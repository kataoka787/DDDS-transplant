<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $("#confirm").click(function() {
            $('#form1').submit();
        });
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?php echo validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?php echo form_open('admin/cordinator/conf', array('name' => 'form1', 'class' => 'form1', 'id' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?= isset($accountId) ? "コーディネーター情報変更" : "コーディネーター新規登録" ?></th>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%">
            姓<?php echo form_input(array('name' => 'sei', 'value' => set_value('sei'), "maxlength" => 80)); ?>
            名<?php echo form_input(array('name' => 'mei', 'value' => set_value('mei'), 'class' => 'iText', "maxlength" => 80)); ?>
        </td>
    </tr>

    <tr>
        <td>フリガナ</td>
        <td>
            セイ<?php echo form_input(array('name' => 'sei_kana', 'value' => set_value('sei_kana'), "maxlength" => 80)); ?>
            メイ<?php echo form_input(array('name' => 'mei_kana', 'value' => set_value('mei_kana'), 'class' => 'iText', "maxlength" => 80)); ?>
        </td>
    </tr>

    <tr>
        <td>メールアドレス(携帯)</td>
        <td><?php echo form_input(array('name' => 'mail', 'value' => set_value('mail'), 'class' => 'iText', "maxlength" => 80)); ?></td>
    </tr>

    <tr>
        <td>パスワード</td>
        <td><?php echo form_input(array('name' => 'password', 'value' => set_value('password'), 'class' => 'iText', "maxlength" => 16)); ?></td>
    </tr>

    <tr>
        <td>Co区分</td>
        <td>
            NW
        </td>
    </tr>
    <tr>
        <td>所属</td>
        <td>本部</td>
    </tr>
    <tr class="nw_row">
        <td>権限</td>
        <td>
            <input type="radio" name="admin_flg" id="admin_flg0" value="0" <?php echo ($admin_flg == '0') ? "checked=checked" : "" ?>>
            <?php echo form_label("一般", "admin_flg0"); ?>
            <input type="radio" name="admin_flg" id="admin_flg1" value="1" <?php echo ($admin_flg == '1') ? "checked=checked" : "" ?>>
            <?php echo form_label("管理", "admin_flg1"); ?>
        </td>
    </tr>
    <tr>
        <td>業務権限</td>
        <td>
            <?php foreach ($works as $work) : ?>
                <input type="checkbox" name="works[]" id="<?= "work$work->id" ?>" value="<?= $work->id ?>" <?php echo set_checkbox('works[]', $work->id); ?> />
                <?php echo form_label($work->work_name, "work$work->id"); ?>
            <?php endforeach ?>
        </td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('admin/cordinator', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
        <li><a href="#" id="confirm"><?php echo img(array('src' => 'img/btn007.jpg', 'alt' => '確認', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>