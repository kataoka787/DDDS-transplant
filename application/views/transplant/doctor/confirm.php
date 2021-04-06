<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $("#delete").click(function() {
            if (window.confirm('ユーザを削除するとBOXも削除されます。よろしいですか？')) {
                $('.form1').attr("action", "<?= base_url() ?>doctor/delete");
                $('.form1').submit();
            } else {
                window.alert('キャンセルされました');
            }
        });

        $("#edit").click(function() {
            $('.form1').attr("action", "<?= base_url() ?>doctor/edit");
            $('.form1').submit();
        });
    });
</script>

<?php echo form_open('', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">移植施設　ユーザ情報変更</th>
    </tr>
    <tr>
        <td width="30%">施設区分</td>
        <td width="70%"><?php echo INSTITUTION_KUBUN[$user_data->institution_kubun] ?></td>
    </tr>
    <tr>
        <td width="30%">施設コード</td>
        <td width="70%"><?php echo $user_data->SISETU_CD ?></td>
    </tr>
    <tr>
        <td width="30%">都道府県</td>
        <td width="70%"><?php echo $user_data->pref_name ?></td>
    </tr>
    <tr>
        <td width="30%">施設</td>
        <td width="70%"><?php echo $user_data->institution_name; ?></td>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="70%">
            <?= str_replace(",","　", $user_data->organ_name) ?>            
        </td>
    </tr>
    <tr>
        <td width="30%">利用者権限</td>
        <td width="70%"><?= DOCTOR_TYPE[$user_data->admin_flg] ?></td>
    </tr>
    <tr>
        <td width="30%">業務権限</td>
        <td width="70%">
            <?= str_replace(","," | ", $user_data->work_name) ?>
        </td>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%"><?php echo $user_data->sei . " " . $user_data->mei; ?></td>
    </tr>
    <tr>
        <td width="30%">フリガナ</td>
        <td width="70%"><?php echo $user_data->sei_kana . " " . $user_data->mei_kana; ?></td>
    </tr>
    <tr>
        <td width="30%">メールアドレス</td>
        <td width="70%"><?php echo $user_data->mail; ?></td>
    </tr>
    <tr>
        <td width="30%">パスワード</td>
        <td width="70%">**********</td>
    </tr>
    <?php echo form_hidden('account_id', $user_data->id); ?>
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" id="edit" class="edit"><?php echo img(array('src' => 'img/btn019.jpg', 'alt' => '変更', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><?php echo anchor('doctor', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
        <li><a href="#" id="delete" class="delete"><?php echo img(array('src' => 'img/btn033.jpg', 'alt' => '削除', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>

<?php echo form_close(); ?>