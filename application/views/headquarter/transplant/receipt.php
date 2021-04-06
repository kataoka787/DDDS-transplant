<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        setInterval(function() {
            location.reload();
        }, 300000);

        $(".back").click(function() {
            document.form2.submit();
        });

        $('#myTable').tablesorter({
            widgets: ['zebra'],
            sortList: [
                [0, 1]
            ],
            headers: {
                0: {
                    sorter: true
                },
                1: {
                    sorter: false
                },
                2: {
                    sorter: false
                },
                3: {
                    sorter: false
                },
                4: {
                    sorter: false
                }
            }
        });
    });
</script>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">ドナー データ管理</th>
    </tr>
    <tr>
        <td>事例No</td>
        <td><?php echo $d_id ?></td>
    </tr>
    <tr>
        <td>提供施設</td>
        <td><?php echo $offerInstitution ?></td>
    </tr>
    <tr>
        <td>提供施設都道府県</td>
        <td><?php echo $offerInstitutionPref ?></td>
    </tr>
    <tr>
        <td>ドナー氏名(カナ)</td>
        <td><?php echo $donorNeme ?></td>
    </tr>
    <tr>
        <td>性別</td>
        <td><?php echo $sex == '1' ? "男性" : "女性" ?></td>
    </tr>
    <tr>
        <td>年齢</td>
        <td><?php echo $age ?>歳</td>
    </tr>
    <tr>
        <td>脳死/心 停止</td>
        <td><?php echo $deathReason ?></td>
    </tr>
</table>


<div class="btnArea">
    <ul>
        <li><a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>

<div style="text-align:center"><b>【受取確認】</b></div>

<?php echo form_open('/donor/data', array('name' => 'form2')); ?>
<?php echo form_hidden('d_id', $d_id) ?>
<?php echo form_close(); ?>

<div class="btnArea">
    <ul>
        <?php foreach ($organs as $key => $val) : ?>
            <?php if ($val->id != $id) : ?>
                <li><a href="receipt?id=<?php echo $val->id ?>"><?php echo $val->organ_name; ?></a></li>
            <?php else : ?>
                <li><?php echo $val->organ_name; ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tablesorter list" id="myTable">
    <caption>ダウンロードファイル一覧</caption>
    <thead>
        <tr>
            <th class="cursorPointer">ファイル名 △▽</th>
            <th>拡張子</th>
            <th>施設名</th>
            <th>最終受取確認</th>
            <th>受取回数</th>
            <th class="cursorPointer">更新日付 △▽</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fileList as $file) : ?>
            <tr>
                <td>
                    <?php
                    $fileName = explode(".", $file->name, -1);
                    $fileName = implode(".", $fileName);
                    echo anchor("output/tp_download?id=" . $file->id . "&file_name=" . $file->name, $fileName, array('target' => '_blank'))
                    ?>
                </td>
                <td>
                    <?= $file->extension ?>
                </td>
                <td>
                    <?= $file->institution ?>
                </td>
                <td>
                    <?= date("Y-m-d H:i", strtotime($file->modified_at)) ?>
                </td>
                <td>
                    <?= $file->shared_link->preview_count ?>-<?= $file->shared_link->download_count ?>
                </td>

                <td>                   
                    <?= date("Y-m-d H:i", strtotime($file->modified_at)) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <tbody>
</table>