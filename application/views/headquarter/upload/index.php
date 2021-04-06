<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
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
                    }
                },
            });
        <?php endif; ?>

        $(".back").click(function() {
            document.list.submit();
        });

        $(".upload").click(function() {
            document.form1.submit();
        });

        $(".delete").click(function() {
            if (window.confirm('ファイルを削除します。よろしいですか？')) {
                const id = $(this).attr('id');
                $("#delete_id").val(id);
                $("#delete_form").submit();
            } else {
                window.alert('キャンセルされました');
            }
        });

        $('#folder').change(function() {
            $('#photo').empty();
            $('#category').empty();
            if ($(this).val()) {
                $.ajax({
                    type: "POST",
                    url: "/ajax/fileCategory",
                    data: {
                        id: $(this).val(),
                        sub_branch: "<?= config_item("sub_branch") ?>"
                    },
                    success: function(data) {
                        $('#photo').empty();
                        $('#category').empty();
                        $('#category').append(data);
                    }
                });
            }
        });
    });
</script>

<?php if (isset($error) || (validation_errors())) : ?>
    <div class="err">
        <?php if (validation_errors()) : ?>
            <?php echo validation_errors('<span>', '</span><br />'); ?>
        <?php endif; ?>
        <?php if ($error) : ?>
            <?php echo $error; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php echo form_open_multipart("$subSystem/upload/conf", array('name' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">ドナー データ管理</th>
    </tr>
    <tr>
        <td width="30%">事例No</td>
        <td width="70%"><?php echo $d_id ?></td>
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
    <tr>
        <td>フォルダ</td>
        <td>
            <select id="folder" name="folder">
                <option value=""></option>
                <?php foreach ($folder as $key => $val) : ?>
                    <option value="<?php echo $val->id ?>" <?php echo set_select('folder', $val->id); ?>><?php echo $val->folder_name ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>ファイルカテゴリ</td>
        <td>
            <select id="category" name="category">
                <?php if ($category) : ?>
                    <option value="">選択してください</option>
                    <?php foreach ($category as $key => $val) : ?>
                        <option value="<?php echo $val->id ?>" <?php echo set_select('category', $val->id); ?>><?php echo $val->category_name ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select><br /><br />
            <?php echo form_input(array('name' => 'memo', 'value' => set_value('memo'), "maxlength" => 80)); ?>
        </td>
    </tr>
    <tr>
        <td>ファイル</td>
        <td><input type="file" name="upfile" size="20" /></td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li>
            <a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a>
        </li>
        <li>
            <a href="#" id="upload" class="upload"><?php echo img(array('src' => 'img/btn016.jpg', 'alt' => 'ファイルアップロード', 'width' => '124', 'height' => '24')) ?></a>
        </li>
    </ul>
</div>
<?php echo form_close(); ?>

<?php echo form_open('/donor/data', array('name' => 'list')); ?>
<?php echo form_hidden('d_id', $d_id) ?>
<?php echo form_close(); ?>

<?= form_open(config_item("sub_branch") . "/upload/delete", array("id" => "delete_form")) ?>
<?= form_input(array("id" => "delete_id", "name" => "id", "hidden" => "hidden")) ?>
<?= form_close() ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tablesorter list" id="myTable">
    <caption>アップロードファイル一覧</caption>
    <thead>
        <tr>
            <th class="cursorPointer">ファイル名 △▽</th>
            <th>拡張子</th>
            <th class="cursorPointer">更新日付 △▽</th>
            <th>削除</th>
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
                    echo anchor("output/download/?type=1&id=" . $file->id, $fileName, array('target' => '_blank'))
                    ?>
                </td>
                <td>
                    <?= explode(".", $file->file_name)[1] ?>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($file->created_at)) ?></td>
                <td><a href="#" id="<?php echo $file->id ?>" class="delete">削除</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>