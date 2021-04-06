<script type="text/javascript">
    $(function() {
        $(".update").click(function() {
            document.form1.submit();
        });
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?php echo validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?php echo form_open('password/change', array('name' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="tableText">
    <tr>
        <td width="30%">
            新しいパスワードの入力</td>
    </tr>
    <tr>
        <td width="30%">
            <ul>
                <li>パスワードに使用できる文字は、半角英数字と記号です。</li>
                <li>アルファベットの大文字、小文字、数字をそれぞれ1文字以上、含めてください。</li>
                <li>8桁以上、16桁以内で設定してください。</li>
                <li>過去に使用したパスワードは利用できません。</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td width="30%">メールアドレス</td>
    </tr>
    <tr>
        <td width="70%"><?php echo $account->mail; ?></td>
    </tr>
    <tr>
        <td>新パスワード</td>
    </tr>
    <tr>
        <td><?php echo form_password(array('name' => 'pw', 'value' => set_value('pw'), 'class' => 'iText', "maxlength" => 16)); ?></td>
    </tr>
    <tr>
        <td>再確認用パスワード</td>
    </tr>
    <tr>
        <td><?php echo form_password(array('name' => 'repw', 'value' => set_value('repw'), 'class' => 'iText', "maxlength" => 16)); ?></td>
    </tr>
    <tr>
        <td><a href="#" id="update" class="update"><?php echo img(array('src' => 'img/btn042.jpg', 'alt' => '更新', 'width' => '124', 'height' => '24')) ?></a></td>
    </tr>
</table>
<?php echo form_close(); ?>