<div class="img_center">
    <p><?php echo $fileName ?></p>
    <div class="img_area">
        <p>
            <?= img(array("src" => "/output?key=$insertedId", "class" => $isImage ? "autoscale_70" : "autoscale_40")) ?>
        </p>
    </div>
</div>
<div class="btnArea">
    <ul>
        <li><?php echo anchor("$subSystem/upload", img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
        <li><?php echo anchor("$subSystem/upload/update", img(array('src' => 'img/btn013.jpg', 'alt' => 'アップロード', 'width' => '124', 'height' => '24', 'id' => 'upload', 'class' => 'upload'))) ?></li>
    </ul>
</div>
<p>&nbsp;</p>