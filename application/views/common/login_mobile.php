<script type="text/javascript">
    $(function() {
        $(".login").click(function() {
            document.loginForm.submit();
        });
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?= validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>
<?php if (isset($error_message)) : ?>
    <div class="err">
        <?= $error_message ?>
    </div>
<?php endif ?>


<?php echo form_open('auth/login', array('name' => 'loginForm', 'id' => 'loginForm')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tableText">
    <tr>
        <td>メールアドレス</td>
    </tr>
    <tr>
        <td><?php echo form_input(array('name' => 'id', 'value' => set_value('id'), 'class' => 'iText')); ?></td>
    </tr>
    <tr>
        <td>パスワード</td>
    </tr>
    <tr>
        <td><?php echo form_password(array('name' => 'pw', 'value' => set_value('pw'), 'class' => 'iText')); ?></td>
    </tr>
    <tr>
        <td>
            <a href="#" class="login" id="login">
                <?php echo img(array('src' => 'img/btn001.jpg', 'alt' => 'ログイン', 'width' => '124', 'height' => '24')) ?>
            </a>
        </td>
    </tr>
    <tr>
        <td>※パスワードを忘れた方は<?php echo anchor('reminder', 'こちら'); ?></td>
    </tr>
</table>
<?php echo form_close(); ?>