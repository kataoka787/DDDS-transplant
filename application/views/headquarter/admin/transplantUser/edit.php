<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });

    function isPasswordInputable() {
        const isAdmin = $("input[name=doctor_type_id]:checked").val();
        const isHadPassword = <?= json_encode($this->session->userdata("isHadPassword")) ?>;
        const isEdit = <?= json_encode($this->session->userdata("isEdit")) ?>;
        let isPasswordInputable = false;
        if (isEdit && isHadPassword) {
            if (isAdmin == <?= IS_ADMIN ?>) {
                isPasswordInputable = true;
            } else {
                let works = [];
                $("input[name='works[]']:checked").each((index, element) => {
                    works.push($(element).val());
                });
                if (works.includes("<?= WORK_FOLLOW_UP ?>")) {
                    isPasswordInputable = true;
                }
            }
        }
        isPasswordInputable ? $("#password").show() : $("#password").hide();
    }

    $(function() {
        $(".conf").click(function() {
            $('#form1').submit();
        });

        $('#pref').change(function() {
            $('#institution').empty();
            $("#kubun_name").empty();
            $("#organs").empty();
            if ($(this).val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/institution",
                    data: {
                        pref_id: $(this).val()
                    },
                    success: data => $('#institution').append(data)
                });
            }
        });

        $('#institution').change(function() {
            $("#kubun_name").empty();
            $("#organs").empty();
            if ($(this).val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/institution_kubun",
                    data: {
                        institution_id: $(this).val()
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        $('#kubun_name').append(data["kubun_name"]);
                        $('#institution_kubun').val(data["kubun_id"]);
                        if (data["kubun_id"] == <?= INSTITUTION_KUBUN_TRANSFER ?>) {
                            $("input[name='works[]'][value=<?= WORK_DDDS ?>]").attr('disabled', 'disabled');
                            $("input[name='works[]'][value=<?= WORK_DDDS ?>]").prop('checked', false);
                        } else {
                            $("input[name='works[]'][value=<?= WORK_DDDS ?>]").removeAttr('disabled');
                        }
                        isPasswordInputable();
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/institution_organ",
                    data: {
                        institution_id: $(this).val()
                    },
                    success: data => $("#organs").append(data)
                })
            }
        })

        $("input[name='works[]']").change(() => isPasswordInputable());
        $("input[name=doctor_type_id]").change(() => isPasswordInputable());
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?= validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?= form_open('admin/transplantUser/conf', array('name' => 'form1', 'class' => 'form1', 'id' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">移植施設 ユーザ情報変更</th>
    </tr>
    <tr>
        <td width="30%">都道府県</td>
        <td width="70%">
            <select id="pref" name="pref_id">
                <option value="">選択してください</option>
                <?php foreach ($prefList as $pref) : ?>
                    <option value="<?php echo $pref->id ?>" <?php echo set_select('pref_id', $pref->id); ?>><?php echo $pref->pref_name ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="30%">施設</td>
        <td width="30%">
            <select class="institution" id="institution" name="institution">
                <option value="">選択してください</option>
                <?php if ($institution) : ?>
                    <?php foreach ($institution as $val) : ?>
                        <option value="<?php echo $val->id ?>" <?php echo set_select('institution', $val->id); ?>><?php echo $val->institution_name ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="30%">施設区分</td>
        <td width="70%">
            <span id="kubun_name"><?= set_value("institution_kubun") ? INSTITUTION_KUBUN[set_value("institution_kubun")] : "" ?></span>
            <?= form_input(array("id" => "institution_kubun", "name" => "institution_kubun", "value" => set_value("institution_kubun"), "type" => "hidden")) ?>
        </td>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="70%" id="organs">
            <?php foreach ($organs as $organ) : ?>
                <input type="checkbox" name="organs[]" id="<?= "organ$organ->id" ?>" value="<?php echo $organ->id ?>" <?php echo set_checkbox('organs[]', $organ->id); ?> />
                <?php echo form_label($organ->organ_name, "organ$organ->id"); ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td width="30%">利用者権限</td>
        <td width="30%">
            <?php foreach (DOCTOR_TYPE as $doctorTypeId => $doctorTypeVal) : ?>
                <?= form_radio(array("name" => "doctor_type_id", "id" => "doctor_type$doctorTypeId", "value" => $doctorTypeId, "checked" => $doctorTypeId == set_value("doctor_type_id"))) ?>
                <?= form_label($doctorTypeVal, "doctor_type$doctorTypeId") ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td width="30%">業務権限</td>
        <td width="70%">
            <?php foreach ($works as $work) {
                if (set_value("institution_kubun") == INSTITUTION_KUBUN_TRANSFER && $work->id == INSTITUTION_KUBUN_TRANSPLANT) {
                    echo form_checkbox(array("name" => "works[]", "id" => "work$work->id", "value" => $work->id, "checked" => false, "disabled" => "disabled"));
                } else {
                    echo form_checkbox(array("name" => "works[]", "id" => "work$work->id", "value" => $work->id, "checked" => set_checkbox("works[]", $work->id)));
                }
                echo form_label($work->work_name, "work$work->id");
            } ?>
        </td>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%">
            姓<?= form_input(array('name' => 'sei', 'value' => set_value('sei'), 'class' => 'iText', "maxlength" => 80)); ?>
            名<?= form_input(array('name' => 'mei', 'value' => set_value('mei'), 'class' => 'iText', "maxlength" => 80)); ?>
        </td>
    </tr>
    <tr>
        <td width="30%">フリガナ</td>
        <td width="70%">
            セイ<?= form_input(array('name' => 'sei_kana', 'value' => set_value('sei_kana'), 'class' => 'iText', "maxlength" => 80)); ?>
            メイ<?= form_input(array('name' => 'mei_kana', 'value' => set_value('mei_kana'), 'class' => 'iText', "maxlength" => 80)); ?>
        </td>
    </tr>
    <tr>
        <td width="30%">メールアドレス</td>
        <td width="70%">
            <?php if ($this->session->userdata("isEdit")) : ?>
                <?= form_hidden("mail", set_value("mail")) ?>
                <?= set_value("mail") ?>
            <?php else : ?>
                <?= form_input(array('name' => 'mail', 'value' => set_value('mail'), 'class' => 'iText', "maxlength" => 80)); ?>
            <?php endif ?>
        </td>
    </tr>
    <?php if ($this->session->userdata("isEdit")) : ?>
        <?= form_hidden("account_id", set_value("account_id")); ?>
        <?php if ($isPasswordInputable) : ?>
            <tr id="password">
                <td width="30%">パスワード</td>
                <td width="70%">
                    <?= form_input(array("name" => "password", "value" => set_value("password"), "class" => "iText", "maxlength" => 16)); ?>
                </td>
            </tr>
        <?php else : ?>
            <tr id="password" style="display: none;">
                <td width="30%">パスワード</td>
                <td width="70%">
                    <?php echo form_input(array("name" => "password", "value" => set_value("password"), "class" => "iText", "maxlength" => 16)); ?>
                </td>
            </tr>
        <?php endif ?>
    <?php endif ?>
</table>

<div class="btnArea">
    <ul>
        <li><?= anchor('admin/transplantUser', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
        <li><a href="#" id="conf" class="conf"><?= img(array('src' => 'img/btn007.jpg', 'alt' => '確認', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?= form_close(); ?>