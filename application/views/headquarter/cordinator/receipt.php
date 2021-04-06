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

        <?php if (count($fileList)) : ?>
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
                    }
                }
            });
        <?php endif; ?>
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


<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tablesorter list" id="myTable">
    <caption>ダウンロードファイル一覧</caption>
    <thead>
        <tr>
            <th class="cursorPointer">ファイル名 △▽</th>
            <th>拡張子 </th>
            <th>受取確認</th>
            <th class="cursorPointer">更新日付 △▽</th>
        </tr>
    </thead>
    <tbody>        
        <?php foreach ($fileList as $key => $val) : ?>
            <?php if ($val['file_name_prefix']) : $file_name = $val['file_name'] . "(" . $val['file_name_prefix'] . ")";
            else : $file_name = $val['file_name'];
            endif; ?>
            <tr>
                <td>
                    <?php echo anchor("output/download?type=1&id=" . $key, $file_name, array('target' => '_blank')) ?>
                </td>
                <td>
                    <?= $val["ext"] ?>
                </td>
                <td>
                    <?php foreach ($val['user'] as $key => $val2) : ?>
                        <?php echo $val2['name'] ?>　
                        <?php if ($val2['download_datetime']) : echo date('Y-m-d H:i', strtotime($val2['download_datetime']));
                        endif; ?>
                        <br />
                    <?php endforeach; ?>
                </td>
                <td><?php if ($val['updated_at']) : echo date('Y-m-d H:i', strtotime($val['updated_at']));
                    endif; ?></td>
            </tr>
        <?php endforeach; ?>
    <tbody>
</table>