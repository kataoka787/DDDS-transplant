<script type="text/javascript">
    function pageBack() {
        resetCSRFToken();
        document.form2.submit();
    };

    function download_data() {
        var ids = new Array();
        $('input[type=checkbox]').each(function() {
            if (this.checked && $(this).val()) {
                ids.push($(this).val());
            }
        });
        if (ids.length < 1) {
            window.alert("チェックしてください。");
            return false;
        }
        resetCSRFToken();
        $("#form1").submit();
    };

    function preview() {
        <?php if (count($fileList)) : ?>
            window.open( "<?= base_url() ?>transplant/preview");
        <?php else : ?>
            window.alert("プレビューできるファイルがありません。");
        <?php endif; ?>
    }

    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        <?php if (count($fileList)) : ?>
            $('#myTable').tablesorter({
                widgets: ['zebra'],
                sortList: [
                    [1, 2]
                ],
                headers: {
                    0: {
                        sorter: false
                    },
                    2: {
                        sorter: false
                    }
                }
            });
        <?php endif; ?>
        $(".toggle").click(function() {
            var ids = new Array();
            $('input[type=checkbox]').each(function() {
                if (this.checked && $(this).val()) {
                    ids.push($(this).val());
                }
            });
            if (ids.length > 0) {
                $('.ids').prop('checked', false);
            } else {
                $('.ids').prop('checked', $(this).prop('checked'));
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

<?php echo form_open('transplant/download/package', array('name' => 'form1', 'id' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tablesorter list" id="myTable">
    <caption>ダウンロードファイル一覧</caption>
    <thead>
        <tr>
            <th><?php echo form_checkbox(array('checked' => false, 'class' => 'toggle', 'id' => 'toggle')); ?><?php echo form_label('チェック', 'toggle'); ?></th>
            <th class="cursorPointer">ファイル名 △▽</th>
            <th>拡張子</th>
            <th class="cursorPointer">更新日付 △▽</th>
            <th class="cursorPointer">担当者 △▽</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fileList as $file) : ?>
            <tr>
                <td>
                    <?php echo form_checkbox(array('name' => 'id[]', 'value' => $file->id, 'class' => 'ids')); ?>
                </td>
                <td>
                    <?php
                    $fileName = explode(".", $file->name, -1);
                    $fileName = implode(".", $fileName);
                    echo anchor("output/tp_download?id=" . $file->id . "&file_name=" . $file->name, $fileName, array('target' => '_blank'))
                    ?>
                </td>
                <td>
                    <?php echo $file->extension ?>
                </td>
                <td>
                    <?php echo date('Y-m-d H:i', strtotime($file->created_at)) ?>
                </td>
                <td>
                    <?php echo $file->uploader_display_name ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" onClick="download_data();"><img src="/img/btn014.jpg" alt='全ダウンロード' width=124 height=24></a></li>
        <li><a href="#" onClick="preview();"><img src="/img/btn017.jpg" alt='全ファイルプレビュー' width=124 height=24></a></li>
        <li><a href="#" onClick="pageBack();"><img src="/img/btn003.jpg" alt="戻る" width=124 height=24></a></li>
    </ul>
</div>
<?php echo form_close(); ?>

<?php echo form_open('/donor/data', array('name' => 'form2')); ?>
<?php echo form_hidden('d_id', $d_id) ?>
<?php echo form_close(); ?>