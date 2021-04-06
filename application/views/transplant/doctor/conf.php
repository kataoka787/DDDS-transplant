<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".update").click(function() {
            $('.form1').attr("action", "<?= base_url() ?>doctor/update");
            $('#form1').submit();
        });

        $("#back").click(function() {
            $('.form1').attr("action", "<?= base_url() ?>/doctor/edit");
            $('.form1').submit();
        });
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?php echo validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?php echo form_open('', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">移植施設 ユーザ情報変更</th>
    </tr>
    <tr>
        <td width="30%">施設区分</td>
        <td width="70%"><?php echo INSTITUTION_KUBUN[$institution_kubun] ?></td>
    </tr>
    <tr>
        <td>都道府県</td>
        <td><?= $prefName ?></td>
    </tr>
    <tr>
        <td width="30%">移植施設</td>
        <td width="30%">
            <?php echo $institution ?>
        </td>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="70%">
            <?= $organs ?>
        </td>
    </tr>
    <tr>
        <td width="30%">利用者権限</td>
        <td width="70%"><?php echo $doctor_type_name ?></td>
    </tr>
    <tr>
        <td width="30%">業務権限</td>
        <td width="70%">
            <?= $works ?>
        </td>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%"><?php echo $name ?></td>
    </tr>
    <tr>
        <td width="30%">フリガナ</td>
        <td width="70%"><?php echo $kana ?></td>
    </tr>
    <tr>
        <td width="30%">メールアドレス</td>
        <td width="70%"><?php echo $mail ?></td>
    </tr>
    <?php if ($this->session->userdata("isEdit")) : ?>
        <tr>
            <td width="30%">パスワード</td>
            <td width="70%">**********</td>
        </tr>
    <?php endif ?>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('doctor/edit', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
        <li><a href="#" id="update" class="update"><?php echo img(array('src' => 'img/btn008.jpg', 'alt' => '登録', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>