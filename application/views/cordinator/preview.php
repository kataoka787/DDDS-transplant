<link rel="stylesheet" href="https://cdn01.boxcdn.net/platform/preview/2.57.0/en-US/preview.css">
<script src="https://cdn01.boxcdn.net/platform/preview/2.57.0/en-US/preview.js"></script>

<div class="textCenter">
    <div class="preview-container" id="preview-container" style="height:400px; width:100%;"></div>
</div>

<div class="btnArea">
    <ul>
        <li>
            <a href="#" onClick="window.close();">
                <?php echo img(array('src' => 'img/btn018.jpg', 'alt' => '閉じる', 'width' => '124', 'height' => '24')) ?>
            </a>
        </li>
    </ul>
</div>

<script>
    const fileIdArr = <?= json_encode($fileIdArr) ?>;
    const boxFileIdArr = <?= json_encode($boxFileIdArr) ?>;
    const accessToken = <?= json_encode($accessToken) ?>;
    const preview = new Box.Preview();

    preview.addListener("load", (event) => {
        const loadedBoxFileId = event["file"]["id"];
        const fileId = fileIdArr[loadedBoxFileId];
        fetch(`/preview/file_access_log/${fileId}`);
    });

    const options = {
        container: "#preview-container",
        collection: boxFileIdArr,
        showDownload: true,
    };
    preview.show(boxFileIdArr[0], accessToken, options);
</script>
