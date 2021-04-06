<script type="text/javascript">
    $(function() {
        $(".submit").click(function() {
            document.form1.submit();
        });
        $(".back").click(function() {
            location.href = "<?php echo base_url() ?>";
        });
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?= validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?php echo form_open('reminder/send', array('name' => 'form1')); ?>

<div style="padding-left: 9px;">
    パスワードの再設定を行いたいメールアドレスを入力してください。入力されたメールアドレスへパスワード再設定を行うためのURLを送付いたします。
</div>
<br />
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tableText">
    <tr>
        <td width="30%">メールアドレス</td>
        <?php if (config_item("branch") === APP_CORDINATOR) : ?>
            <td width="70%"><?php echo form_input(array('name' => 'id', 'value' => set_value('id'))); ?></td>
        <?php else : ?>
            <td width="70%"><?php echo form_input(array('name' => 'id', 'value' => set_value('id'), "style" => "width: 50%")); ?></td>
        <?php endif ?>
    </tr>
</table>
<?php echo form_close(); ?>
<div class="btnArea">
    <ul>
        <li><a href="#" id="submit" class="submit"><?php echo img(array('src' => 'img/btn002.jpg', 'alt' => '送信', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<br />
<br />
<br />
<br />
<div style="padding-left: 9px;">
    ※パスワード再設定のURLの有効期限は１時間です。１時間以内にアクセスして頂けなかった場合は、再度変更操作を行いURLを取得してください。
</div>