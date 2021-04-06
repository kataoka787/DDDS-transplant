<script>
    $(document).ready(function() {
        $('#backBtn').click(function() {
            window.location.href = '/managementMenu';
        });
        $('#button').click(function() {
            $('#file').click();
        });
        $('#file').change(function(e) {
            const fileName = e.target.files[0]?.name;
            $('#filename').val(fileName ?? "");
        });
        $('#submitCsv').click(function() {
            document.form1.submit();
        });

    });
</script>

<div class='d-flex justify-content-center'>
    <p>移植者情報CSVファイル</p>
    <?= form_open_multipart('CsvLoad/load', array('name' => 'form1', 'id' => 'form1', 'class' => 'ml-3')); ?>
    <?= form_input(array('name' => 'csv_file', 'value' => set_value(''), 'class' => 'input-h24', 'id' => 'filename')); ?>
    <input type="file" id="file" style="display:none;" accept=".csv" name="upfile" size="20" />
    <?= form_button(array('class' => 'bg-btn ml-3', 'id' => 'button', 'name' => '', 'content' => 'ファイルの選択')); ?>

    <div style="color: #FF0000;">
        <?php if (isset($errorFormat)) : ?>
            <?php if ($errorFormat) : ?>
                システムエラーが発生しました。<br>
                不正な形式のファイルが指定されています。
            <?php endif; ?>
        <?php elseif (isset($errorReadFile)) : ?>
            <?php if ($errorReadFile) : ?>
                ファイルが読み込めません
            <?php endif ?>
        <?php else : ?>
            &nbsp;
        <?php endif ?>
    </div>
    <div class="row mt-3 mb-4 pl-5">
        <?= form_button(array('class' => 'bg-btn px-5', 'id' => 'submitCsv', 'name' => '', 'content' => '取込')); ?>
    </div>
    <?= form_close() ?>
</div>

<?php if (!empty($results)) : ?>

    <div class="px-5">
        <p class="mb-0">取込結果</p>
        <ul class="list-group import-result">
            <?php foreach ($results as $result) : ?>
                <li class="list-group-item">
                    <div class="row">
                        <?php if ($result["status"] == "success") : ?>
                            <span style="font-size: 1.5em; color: #3CB371; margin-top: -5px;">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="mb-0">レシピエント登録者ID【<?= $result["recipientId"] ?>】</p>
                                <p class="mb-0 ml-3"><?= $result["message"] ?></p>
                            </div>
                        <?php elseif ($result["status"] == "warning") : ?>
                            <span style="font-size: 1.5em; color: #FFD700; margin-top: -5px;">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="mb-0">レシピエント登録者ID【<?= $result["recipientId"] ?>】</p>
                                <p class="mb-0 ml-3"><?= $result["message"] ?></p>
                            </div>
                        <?php elseif ($result["status"] == "error") : ?>
                            <span style="font-size: 1.5em; color: #FF0000; margin-top: -5px;">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="mb-0">レシピエント登録者ID【<?= $result["recipientId"] ?>】</p>
                                <p class="mb-0 ml-3"><?= $result["message"] ?></p>
                            </div>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif ?>

<div class='mt-3 d-flex justify-content-end'>
    <?= form_button(array('class' => 'bg-btn px-5', 'id' => 'backBtn', 'name' => '', 'content' => '戻る')); ?>
</div>