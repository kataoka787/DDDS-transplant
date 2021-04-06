<script>
    $(document).ajaxStart(function() {
        $(document.body).addClass('wait-cursor');
    }).ajaxStop(function() {
        $(document.body).removeClass('wait-cursor');
    });

    $(document).ready(function() {
        const userType = '<?= $userType ?>';
        const accTypeCo = <?= ACC_TYPE_CO ?>;
        const unfinished = '<?= INPUT_STATUS_UNFINISHED ?>';
        const transplantKubun = '<?= INSTITUTION_KUBUN_TRANSPLANT ?>';
        const postTransplantKubun = '<?= INSTITUTION_KUBUN_TRANSFER ?>';
        const patientOutcome = '<?= $info->RECIPIENT_TENKI ?>';
        const organOutcome = '<?= $info->ZOKI_TENKI ?>';
        const diffYear = <?= $diffYear ?>;
        const maxTableColumn = <?= config_item('inspection_max_table_column') ?>;
        const maxCycleColumn = <?= config_item('inspection_max_cycle_column') ?>;
        const inspection = <?= json_encode($inspection) ?>;
        let currentColumn = inspection['tableSetting']['maxColumn'];

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


        $('#transplantFacilityClass').prop('disabled', true);
        $('#postTransplantFacilityClass').prop('checked', true);

        $('#searchIcon').click(function() {
            if (userType == accTypeCo) {
                $('#searchDialog').show();
                if (!$('#postTransplant').val() == '') {
                    $('#transplantDialog').val($('#postTransplant option:selected').text());
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
            $('#postTransplantFacilityClass').prop('checked', true);
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

        $('.collapse-btn').click(function() {
            if ($(this).hasClass('collapsed')) {
                $(this).children(":first").attr('class', 'fa fa-minus-circle');
            } else {
                $(this).children(":first").attr('class', 'fa fa-plus-circle')
            }
        });

        $('#causeOfDeathMajor').change(function() {
            $('#causeOfDeathSubclass').empty()
            if ($(this).val() != '') {
                $('#causeOfDeathSubclass').attr('disabled', true).addClass('wait-cursor');
                $.ajax({
                    type: "POST",
                    url: 'ajax/causeOfDeathSubclass',
                    data: {
                        major: $(this).val()
                    },
                    success: function(res) {
                        res = JSON.parse(res);
                        let options = '<option value=""></option>';
                        $.each(res, function(key, value) {
                            options += `<option value="${value.CODE}">${value.VALUE}</option>`;
                        });
                        $('#causeOfDeathSubclass').append(options);
                    }
                }).always(() => {
                    $('#causeOfDeathSubclass').removeAttr('disabled').removeClass('wait-cursor');
                });
            };
        });

        function deleteRow(element) {
            const row = $(element).parent().parent();
            row.find('[name*="isDeleted"]').val('1');
            row.addClass('d-none');
            const tbody = row.parent().children().not('.d-none');
            tbody.each(function(index, value) {
                $(value).find('td:first').text(index + 1);
                const ordinalInput = $(value).find('[name*="ordinal"]');
                const rejectionType = ordinalInput.attr('rejectionType');
                if (typeof rejectionType !== typeof undefined && rejectionType !== false) {
                    ordinalInput.val(rejectionType + '_' + (index + 1));
                } else {
                    ordinalInput.val(index + 1);
                }
            });
            if (row.attr('name') == 'newRow') {
                row.remove();
            };
        }

        $('[name="deleteLine"]').click(function() {
            deleteRow(this);
        });

        function addRow(element) {
            const rows = $(element).find('tbody > tr');
            const rowIndex = parseInt(rows.first().attr('rowIndex') ?? -1) + 1;
            const row = rows.last().clone().removeClass('d-none');
            row.attr('rowIndex', rowIndex)
            row.find('[disabled]').removeAttr('disabled');
            row.find('[name*="deleteLine"]').click(function() {
                deleteRow(this)
            });
            row.find('[name*="rowIndex"]').each(function(index, value) {
                $(value).attr('name', $(value).attr('name').replace('rowIndex', rowIndex));
            });
            rows.first().before(row);
            $(element).find('tbody > tr').not('.d-none').each(function(index, value) {
                $(value).find('td:first').text(index + 1);
                const ordinalInput = $(value).find('[name*="ordinal"]');
                const rejectionType = ordinalInput.attr('rejectionType');
                if (typeof rejectionType !== typeof undefined && rejectionType !== false) {
                    ordinalInput.val(rejectionType + '_' + (index + 1));
                } else {
                    ordinalInput.val(index + 1);
                }
            });
        }

        $('[name="addLine"]').click(function() {
            addRow($(this).next('table'));
        });

        $('#addLineLivingConditions').click(function() {
            addRow($(this).parent().next('table'));
        });

        $('#addColumn').click(function() {
            if (currentColumn == maxCycleColumn) return;
            let tables = $('[name=inspectionTable]');
            let numbersOfTable = Math.ceil(currentColumn / maxTableColumn);
            if (currentColumn % maxTableColumn === 0) {
                tables.parent().append(tables.first().clone().removeClass('d-none'));
                tables = $('[name=inspectionTable]');
                numbersOfTable++;
            }
            const table = tables[numbersOfTable];
            const columnHeader = $(table).find('thead > tr > .disabled').first();
            columnHeader.append(inspection['cycle'][currentColumn]['value']);
            columnHeader.removeClass('disabled');

            const cycle = inspection['cycle'][currentColumn]['code'];
            const rows = $(table).find('tbody > tr');
            rows.each(function(index, value) {
                const cell = $(value).find('.disabled:first');
                const cellData = $(value).find('.d-none:last').children().clone().removeAttr('disabled');
                cellData.attr('name', cellData.attr('name').replace('cycleKey', cycle));
                if (cellData.hasClass('input-status')) {
                    cellData.change(function() {
                        changeBgInputStatus(this);
                    });
                };
                cell.html(cellData);
                cell.removeClass('disabled');
            });
            currentColumn++;
        });

        $('.input-status').each(function() {
            if ($(this).val() == unfinished) {
                $(this).addClass('bg-pink');
            }
        });

        function changeBgInputStatus(element) {
            if ($(element).val() == unfinished) {
                $(element).addClass('bg-pink');
            } else {
                $(element).removeClass('bg-pink');
            };
        };

        $('.input-status').change(function() {
            changeBgInputStatus(this);
        });

        $('#backBtn').click(function() {
            window.location.href = 'search';
        });

        $('#saveBtn').click(function() {
            let isSubmit = true;
            const data = $('#detailForm').serializeArray();
            $.each(data, function() {
                if ((this.name == 'patientOutcome' && this.value != '1' && patientOutcome == '1') ||
                    (this.name == 'organOutcome' && this.value != '1' && organOutcome == '1')) {
                    isSubmit = confirm('転帰（患者・臓器）が「生存「生着」以外に変更されました、変更後は他の入力項目の変更は出来ません。このまま修正してよろしいですか？');
                } else if ((this.name == 'patientOutcome' && this.value != patientOutcome && diffYear == 0) ||
                    (this.name == 'patientOutcome' && this.value != patientOutcome && diffYear == 0)) {
                    isSubmit = confirm('1年以内の転帰変更には書類の提出が必要となりますので、以下のURLからダウンロード頂きご提出をお願いします。');
                }
            });
            if (isSubmit) {
                $(this).attr('disabled', true).addClass('wait-cursor');
                $.ajax({
                    type: "POST",
                    url: "<?= base_url() . "detail/save" ?>",
                    data: $('#detailForm').serialize(),
                    success: function(res) {
                        res = JSON.parse(res);
                        if (res["status"] == 200) {
                            location.reload();
                        } else {
                            $("#systemMessageModalBody").empty();
                            $("#systemMessageModalBody").append(res["message"]);
                            $("#systemMessageModal").modal("show");
                        }
                    }
                }).always(() => {
                    $(this).removeAttr('disabled').removeClass('wait-cursor');
                });
            };
        });
    });

    const primaryKeys = "<?= "$info->RECIPIENT_ID,$info->ZOKI_CODE,$info->ISYOKU_CNT" ?>";

    function pdfPrintDetail() {
        $("#printDetail").val(JSON.stringify([primaryKeys]));
        $("#pdfDetailForm").submit();
    }

    function pdfPrintEntry() {
        $("#printEntry").val(JSON.stringify([primaryKeys]));
        $("#pdfEntryForm").submit();
    }
</script>