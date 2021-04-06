<?php if (config_item("branch") === APP_HEAD) : ?>
    <script type="text/javascript">
        $(function() {
            $(".login").click(function() {
                document.form1.submit();
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

    <?php echo form_open('auth/login', array('name' => 'form1')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="tableText">
        <tr>
            <td width="30%">メールアドレス</td>
            <td width="70%"><?php echo form_input(array('name' => 'id', 'value' => set_value('id'), 'class' => 'iText', "autofocus" => "autofocus")); ?></td>
        </tr>
        <tr>
            <td>パスワード</td>
            <td><?php echo form_password(array('name' => 'pw', 'value' => set_value('pw'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td colspan="2"><a href="#" id="login" class="login"><?php echo img(array('src' => 'img/btn001.jpg', 'alt' => 'ログイン', 'width' => '124', 'height' => '24')) ?></a></td>
        </tr>
        <tr>
            <td colspan="2">※パスワードを忘れた方は<?php echo anchor('reminder', 'こちら'); ?></td>
        </tr>
    </table>
    <?php echo form_close(); ?>
<?php else : ?>
    <script type="text/javascript">
        $(function() {
            $(".login").click(function() {
                document.form1.submit();
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

    <?php echo form_open('auth/login', array('name' => 'form1', 'autocomplete' => 'off')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="tableText">
        <tr>
            <td width="30%">メールアドレス</td>
            <td width="70%"><?php echo form_input(array('name' => 'id', 'value' => set_value('id'), 'class' => 'iText', "autofocus" => "autofocus")); ?></td>
        </tr>
        <tr>
            <td>パスワード</td>
            <td><?php echo form_password(array('name' => 'pw', 'value' => set_value('pw'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td colspan="2"><a href="#" id="login" class="login"><?php echo img(array('src' => 'img/btn001.jpg', 'alt' => 'ログイン', 'width' => '124', 'height' => '24')) ?></a>
            </td>
        </tr>
        <tr>
            <td colspan="2">※パスワードを忘れた方は<?php echo anchor('reminder', 'こちら'); ?></td>
        </tr>
    </table>
    <?php echo form_close(); ?>
    ※システムに関するマニュアルや質問等はこちら<br />
    ・<?= anchor("guide", "操作マニュアル") ?>
    <br />
    ・<?= anchor("faq", "FAQ") ?>
<?php endif ?>
