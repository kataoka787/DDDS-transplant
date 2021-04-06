<?php
if ($key == 'cordinator') {
    $class = "listMenu01";
} else if ($key == 'transplant') {
    $class = "listMenu03";
}
if ($this->session->userdata('admin_flg')) {
    $class .= "_delete";
}
?>

<div class="<?php echo $class ?>">
    <ul>
        <li>■<?php echo anchor('/' . $key . '/upload', 'アップロード'); ?></li>
        <li>■<?php echo anchor('/' . $key . '/download', 'ダウンロード'); ?></li>
        <?php if ($key != 'cordinator') : ?>
            <li>■<?php echo anchor('/' . $key . '/request', '依頼'); ?></li>
        <?php endif; ?>
        <li>■<?php echo anchor('/' . $key . '/receipt', '受取確認'); ?></li>
    </ul>
</div>