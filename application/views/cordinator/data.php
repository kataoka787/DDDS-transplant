<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $("#donor_edit").click(function() {
            $('.donor_edit_form').submit();
        });
    });
</script>

<?php echo form_open('edit', array('name' => 'form1', 'class' => 'donor_edit_form', 'id' => 'donor_edit_form')); ?>
<?php echo form_hidden('d_id', $d_id); ?>
<?php echo form_close(); ?>

<p><a href="#" id="donor_edit"><?php echo img(array('src' => 'img/btn012.jpg', 'alt' => 'ドナー情報変更', 'width' => '124', 'height' => '24')) ?></a></p>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th>ドナー データ管理</th>
    </tr>
    <tr>
        <td width="30%">事例No</td>
    </tr>
    <tr>
        <td width="70%"><?php echo $d_id ?></td>
    </tr>
    <tr>
        <td>提供施設</td>
    </tr>
    <tr>
        <td><?php echo $offerInstitution ?></td>
    </tr>
    <tr>
        <td>提供施設都道府県</td>
    </tr>
    <tr>
        <td><?php echo $offerInstitutionPref ?></td>
    </tr>
    <tr>
        <td>ドナー氏名(カナ)</td>
    </tr>
    <tr>
        <td><?php echo $donorNeme ?></td>
    </tr>
    <tr>
        <td>性別</td>
    </tr>
    <tr>
        <td><?php echo $sex == '1' ? "男性" : "女性" ?></td>
    </tr>
    <tr>
        <td>年齢</td>
    </tr>
    <tr>
        <td><?php echo $age ?>歳</td>
    </tr>
    <tr>
        <td>脳死/心 停止</td>
    </tr>
    <tr>
        <td><?php echo $deathReason ?></td>
    </tr>
    <tr>
        <td>連絡事項</td>
    </tr>
    <tr>
        <td><?= $message ?> </td>
    </tr>
</table>
<div class="btnArea">
    <ul>
        <li><?php echo anchor('/upload', img(array('src' => 'img/btn013.jpg', 'alt' => 'アップロード', 'width' => '124', 'height' => '24'))) ?></li>
        <li><?php echo anchor('/download', img(array('src' => 'img/btn014.jpg', 'alt' => 'ダウンロード', 'width' => '124', 'height' => '24'))) ?></li>
        <li><?php echo anchor('/donorlist', img(array('src' => 'img/btn010.jpg', 'alt' => 'ドナー一覧画面へ', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>