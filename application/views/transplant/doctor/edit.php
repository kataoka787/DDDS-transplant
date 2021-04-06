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
        $("input[name='works[]']").change(() => isPasswordInputable());
        $("input[name=doctor_type_id]").change(() => isPasswordInputable());
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?php echo validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?php echo form_open('doctor/conf', array('name' => 'form1', 'class' => 'form1', 'id' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">移植施設 ユーザ情報変更</th>
    </tr>
    <tr>
        <td width="30%">都道府県</td>
        <td width="70%"><?= $prefName ?></td>
    </tr>
    <tr>
        <td width="30%">施設</td>
        <td width="30%"><?= $institutionName ?></td>
    </tr>
    <tr>
        <td width="30%">施設区分</td>
        <td width="70%"><?= $institutionKubun ?></td>
        <?= form_input(array("id" => "institution_kubun", "name" => "institution_kubun", "value" => set_value("institution_kubun"), "type" => "hidden")) ?>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="70%" id="organs">
            <?php foreach ($organs as $val) : ?>
                <input type="checkbox" name="organs[]" id="<?= "organ$val->id" ?>" value="<?php echo $val->id ?>" <?php echo set_checkbox('organs[]', $val->id); ?> />
                <?php echo form_label($val->organ_name, "organ$val->id"); ?>
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
            姓<?= form_input(array('name' => 'sei', 'value' => set_value('sei'), 'class' => 'input-h24', "maxlength" => 80)); ?>
            名<?= form_input(array('name' => 'mei', 'value' => set_value('mei'), 'class' => 'input-h24', "maxlength" => 80)); ?>
        </td>
    </tr>
    <tr>
        <td width="30%">フリガナ</td>
        <td width="70%">
            セイ<?= form_input(array('name' => 'sei_kana', 'value' => set_value('sei_kana'), 'class' => 'input-h24', "maxlength" => 80)); ?>
            メイ<?= form_input(array('name' => 'mei_kana', 'value' => set_value('mei_kana'), 'class' => 'input-h24', "maxlength" => 80)); ?>
        </td>
    </tr>
    <tr>
        <td width="30%">メールアドレス</td>
        <td width="70%">
            <?php if ($this->session->userdata("isEdit")) : ?>
                <?= form_hidden("mail", set_value("mail")) ?>
                <?= set_value("mail") ?>
            <?php else : ?>
                <?= form_input(array('name' => 'mail', 'value' => set_value('mail'), 'class' => 'input-h24', "maxlength" => 80)); ?>
            <?php endif ?>
        </td>
    </tr>
    <?php if ($this->session->userdata("isEdit")) : ?>
        <?= form_hidden("account_id", set_value("account_id")); ?>
        <?php if ($isPasswordInputable) : ?>
            <tr id="password">
                <td width="30%">パスワード</td>
                <td width="70%">
                    <?= form_input(array("name" => "password", "value" => set_value("password"), "class" => "input-h24", "maxlength" => 16)); ?>
                </td>
            </tr>
        <?php else : ?>
            <tr id="password" style="display: none;">
                <td width="30%">パスワード</td>
                <td width="70%">
                    <?php echo form_input(array("name" => "password", "value" => set_value("password"), "class" => "input-h24", "maxlength" => 16)); ?>
                </td>
            </tr>
        <?php endif ?>
    <?php endif ?>
</table>

<div class="btnArea">
    <ul>
        <li><?= anchor('doctor', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
        <li><a href="#" id="conf" class="conf"><?php echo img(array('src' => 'img/btn007.jpg', 'alt' => '確認', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?= form_close(); ?>