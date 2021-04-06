<script>
    $(document).ajaxStart(function() {
        $(document.body).addClass('wait-cursor');
    }).ajaxStop(function() {
        $(document.body).removeClass('wait-cursor');
    });

    let result = null;
    $(document).ready(function() {
        const userType = '<?= $userType ?>';
        const accTypeCo = <?= ACC_TYPE_CO ?>;
        const institutionKubun = '<?= $institutionKubun ?>';
        const transplantKubun = '<?= INSTITUTION_KUBUN_TRANSPLANT ?>';
        const postTransplantKubun = '<?= INSTITUTION_KUBUN_TRANSFER ?>';
        const organsAvailable = <?= json_encode($organsAvailable) ?>;

        if (userType == accTypeCo) {
            $('.organ').prop('disabled', false);
        } else {
            $.each(organsAvailable, function(key, value) {
                $('#organ' + value).prop('disabled', false);
            });
        };

        function initSearch() {
            $('[name="organOutcome[]"]:first').prop('checked', true);
            $('[name="patientOutcome[]"]:first').prop('checked', true);
            $('[name="patientOutcomeDetails[]"]:first').prop('checked', true);
            $('#transplantFacilityClass').prop('checked', true);
            $('#postTransplantFacilityClass').prop('checked', false);
            $('#inspectionItemCheckTarget').prop('checked', true);
            $('#livingConditionsCheckTarget').prop('checked', true);
            $('#notEnteredInputStatus').prop('checked', true);
            if (userType == accTypeCo) {
                $('#doneInputStatus').prop('checked', true);
            }
        }

        initSearch();

        $('#clearConditions').click(function() {
            $('input[type=text], input[type=date], select').val('');
            $('input[type=checkbox').prop('checked', false);
            $('#prefDialog').val('').empty();
            $('#countTpSearchResult').text('000 件表示');
            $('#tpSearchResult').empty();
            $('#countSearchResult').text('000 件表示');
            $('#searchResult').empty();
            $('#dischargeDateSet').val(1);
            initSearch();
        });

        $('#searchBtn').click(function() {
            $(this).attr('disabled', true).addClass('wait-cursor');
            $('#searchResult').empty();
            const form = $($('#nav-tab').find('.active')[0].id == 'nav-recipient-tab' ? '#recipientForm' : '#donorForm');
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data: form.serialize(),
                success: function(res) {
                    res = JSON.parse(res);
                    result = res['result'];
                    $('#searchResult').append(res['view']);
                    $('#countSearchResult').text(res['count'].toString().padStart(3, '0') + ' 件表示');
                }
            }).always(() => {
                $(this).removeAttr('disabled').removeClass('wait-cursor');
            });
        });

        $('#transplantSearchForm').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        function transplantSearch() {
            $('#searchBtnDialog').attr('disabled', true).addClass('wait-cursor');
            $('#tpSearchResult').empty();
            $('#countTpSearchResult').text('000 件表示');
            $.ajax({
                type: "POST",
                url: $('#transplantSearchForm').attr("action"),
                data: $('#transplantSearchForm').serialize(),
                success: function(res) {
                    res = JSON.parse(res);
                    result = res['result'];
                    $('#tpSearchResult').append(res['view']);
                    $('#countTpSearchResult').text(res['count'].toString().padStart(3, '0') + ' 件表示');
                },
            }).always(() => {
                $('#searchBtnDialog').removeAttr('disabled').removeClass('wait-cursor');
            });
        };

        $('#searchIcon').click(function() {
            if (userType == accTypeCo) {
                $('#searchDialog').show();
                if (!$('#transplant').val() == '') {
                    $('#transplantDialog').val($('#transplant option:selected').text());
                }
                transplantSearch();
            }
        });

        $('#blockDialog').change(function() {
            $('#prefDialog').empty();
            if ($(this).val()) {
                $('#prefDialog').attr('disabled', true).addClass('wait-cursor');
                $.ajax({
                    type: "POST",
                    url: "/transplant/ajax/pref_by_block",
                    data: "id=" + $(this).val(),
                    success: function(data) {
                        $('#prefDialog').append(data);
                    }
                }).always(() => {
                    $('#prefDialog').removeAttr('disabled').removeClass('wait-cursor');
                });
            };
        });

        $('#clearConditionsDialog').click(function() {
            $('#blockDialog').val('');
            $('#prefDialog').val('').empty();
            $('#transplantDialog').val('');
            $('#tpSearchResult').empty();
            $('#transplantFacilityClass').prop('checked', true);
            $('#postTransplantFacilityClass').prop('checked', false);
            $('#countTpSearchResult').text('000 件表示');
        });

        $('#searchBtnDialog').click(function() {
            transplantSearch();
        });

        $('#tpSearchTable').on('click', '.clickable-row', function(event) {
            $(this).addClass('row-selected').siblings().removeClass('row-selected');
        });

        $('#chooseBtn').click(function() {
            let rowSelected = $('#tpSearchTable .row-selected');
            if (rowSelected.length === 1) {
                switch ($(rowSelected).attr('kubun')) {
                    case transplantKubun:
                        $('#transplant').val($(rowSelected).attr('id'));
                        break;
                    case postTransplantKubun:
                        $('#postTransplant').val($(rowSelected).attr('id'));
                        break;
                };
            }
            $('#searchDialog').hide();
        });

        $('#closeBtn').click(function() {
            $('#searchDialog').hide();
        });

        $('#nav-tab').click(function(event) {
            if (event.target.id == 'nav-donor-tab') {
                $('.recipient-btn').css('display', 'contents');
            } else {
                $('.recipient-btn').css('display', 'block');
            }
        });

        $('#selectAll').click(function() {
            $('[name=itemSelect]').prop('checked', true);
        });

        $('#deselectAll').click(function() {
            $('[name=itemSelect]').prop('checked', false);
        });

        $('#allCsvBtn').click(function() {
            const form = $($('#nav-tab').find('.active')[0].id == 'nav-recipient-tab' ? '#recipientForm' : '#donorForm');
            var organ = [];
            $(form).find("input[name='organ[]']:checked").each(function(index, obj) {
                organ.push($(this).val());
            });
            var simultaneousTransplantation = [];
            $(form).find("input[name='simultaneousTransplantation[]']:checked").each(function(index, obj) {
                simultaneousTransplantation.push($(this).val());
            });
            let total = organ.length + simultaneousTransplantation.length;
            if (organ.length == 1 && simultaneousTransplantation.length == 0) {
                $("#csvDetailOrgan").val(JSON.stringify(organ));
                $("#csvDetailSimultaneousTransplantation").val(JSON.stringify(simultaneousTransplantation));
                $("#csvDetailForm").submit();
            } else {
                alert('CSV出力(全件 全項目)は１つの臓器のみ選択して下さい。');
            }
        });

        $('#basicCsvBtn').click(function() {
            if (result != null) {
                let selectedId = [];
                $("input:checkbox[name=itemSelect]:checked").each(function() {
                    selectedId.push($(this).val());
                });
                let selectedData = result.filter((item) => selectedId.includes(`${item["RECIPIENT_ID"]},${item["ZOKI_CODE"]},${item["ISYOKU_CNT"]}`));
                if (selectedData.length !== 0) {
                    $("#csvListData").val(JSON.stringify(selectedData));
                    $("#csvListForm").submit();
                } else {
                    alertError("<?= lang("empty_export_data") ?>");
                }
            } else {
                alertError("<?= lang("empty_export_data") ?>");
            }
        });

        $('#listPdfBtn').click(function() {
            if (result != null) {
                let selectedId = [];
                $("input:checkbox[name=itemSelect]:checked").each(function() {
                    selectedId.push($(this).val());
                });
                if (selectedId.length === 0) {
                    alertError("<?= lang("empty_export_data") ?>");
                }
                let selectedData = result.filter((item) => selectedId.includes(`${item["RECIPIENT_ID"]},${item["ZOKI_CODE"]},${item["ISYOKU_CNT"]}`));
                if (selectedData.length !== 0) {
                    $("#pdfListData").val(JSON.stringify(selectedData));
                    $("#pdfListForm").submit();
                }
            } else {
                alertError("<?= lang("empty_export_data") ?>");
            }

        });

        $('#infoPdfBtn').click(function() {
            if (result != null) {
                let selectedId = [];
                $("input:checkbox[name=itemSelect]:checked").each(function() {
                    selectedId.push($(this).val());
                });
                if (selectedId.length === 0) {
                    return alertError("<?= lang("empty_export_data") ?>");
                }
                let selectedData = result.map((item) => {
                    const id = `${item["RECIPIENT_ID"]},${item["ZOKI_CODE"]},${item["ISYOKU_CNT"]}`;
                    if (selectedId.includes(id)) {
                        return id;
                    }
                });
                if (selectedData.length !== 0) {
                    $("#printDetail").val(JSON.stringify(selectedData));
                    $("#pdfDetailForm").submit();
                }
            } else {
                alertError("<?= lang("empty_export_data") ?>");
            }
        });

        $('#entryPdfBtn').click(function() {
            if (result != null) {
                let selectedId = [];
                $("input:checkbox[name=itemSelect]:checked").each(function() {
                    selectedId.push($(this).val());
                });
                if (selectedId.length === 0) {
                    return alertError("<?= lang("empty_export_data") ?>");
                }
                let selectedData = result.map((item) => {
                    const id = `${item["RECIPIENT_ID"]},${item["ZOKI_CODE"]},${item["ISYOKU_CNT"]}`;
                    if (selectedId.includes(id)) {
                        return id;
                    }
                });
                if (selectedData.length !== 0) {
                    $("#printEntry").val(JSON.stringify(selectedData));
                    $("#pdfEntryForm").submit();
                }
            } else {
                alertError("<?= lang("empty_export_data") ?>");
            }
        });

        function alertError(message) {
            return alert("出力するデータを選択してください。");
        }

        $('#backBtn').click(function() {
            window.location.href = $(this).attr('redirect');
        });
    });
</script>