<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        <?php if (count($fileList)) : ?>
            $('#myTable').tablesorter({
                widgets: ['zebra'],
                sortList: [
                    [0, 1, 2]
                ],
                headers: {
                    1: {
                        sorter: false
                    }
                }
            });
        <?php endif; ?>
        $(".donor_data").click(function() {
            document.list.submit();
        });
    });
</script>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th>ドナー データ管理</th>
    </tr>
    <tr>
        <td>事例No</td>
    </tr>
    <tr>
        <td><?php echo $d_id ?></td>
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
</table>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tablesorter list" id="myTable">
    <caption>ダウンロードファイル一覧</caption>
    <thead>
        <tr>
            <th class="cursorPointer">ファイル名 △▽</th>
            <th>拡張</th>
            <th class="cursorPointer">更新日付 △▽</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fileList as $file) : ?>
            <tr>
                <td>
                    <span class="pNoDisp"><?php echo sprintf('%06d', $file->file_category_mst_id) ?></span>
                    <?php
                    $fileName = explode(".", $file->file_name)[0];
                    $fileName = $file->file_name_prefix ? $fileName . "(" . $file->file_name_prefix . ")" : $fileName;
                    echo anchor("output/download/?type=2&id=" . $file->id, $fileName, array('target' => '_blank'))
                    ?>
                </td>
                <td><?= explode(".", $file->file_name)[1] ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($file->created_at)) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('/preview', img(array('src' => 'img/btn017.jpg', 'alt' => '全ファイルプレビュー', 'width' => '124', 'height' => '24')), array('target' => '_blank')) ?></li>
        <li><a href="#" id="donor_data" class="donor_data"><?php echo img(array('src' => 'img/btn009.jpg', 'alt' => 'ドナーデータ管理画面へ', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>

<?php echo form_open('/data', array('name' => 'list')); ?>
<?php echo form_hidden('d_id', $d_id) ?>
<?php echo form_close(); ?>