<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $("#delete").click(function() {
            if (window.confirm('削除します。よろしいですか？')) {
                $('.form1').attr("action", "/admin/cordinator/delete");
                $('.form1').submit();
            } else {
                window.alert('キャンセルされました');
            }
        });

        $("#edit").click(function() {
            $('.form1').submit();
        });
    });
</script>

<?php echo form_open('admin/cordinator/edit', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">コーディネーター情報</th>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%"><?php echo $name ?></td>
    </tr>

    <tr>
        <td>フリガナ</td>
        <td><?php echo $kana ?></td>
    </tr>

    <tr>
        <td>メールアドレス(携帯)</td>
        <td><?php echo $mail; ?></td>
    </tr>

    <tr>
        <td>パスワード</td>
        <td>**********</td>
    </tr>

    <tr>
        <td>Co区分</td>
        <td>NW</td>
    </tr>
    <tr>
        <td>所属</td>
        <td>本部</td>
    </tr>    
    <tr class="nw_row">
        <td>権限</td>
        <td><?php echo $admin_flg == '1' ? "管理" : "一般" ?></td>
    </tr>
    <tr>
        <td>業務権限</td>
        <td>
            <?=implode(" | ", explode(",", $workName))?>
        </td>
    </tr>
    <?php echo form_hidden('account_id', $accountId); ?>
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" id="edit" class="edit"><?php echo img(array('src' => 'img/btn019.jpg', 'alt' => '変更', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><?php echo anchor('admin/cordinator', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
        <li><a href="#" id="delete" class="delete"><?php echo img(array('src' => 'img/btn033.jpg', 'alt' => '削除', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>

<?php echo form_close(); ?>