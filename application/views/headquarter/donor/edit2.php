<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $("#confirm").click(function() {
            $('.form1').attr("action", "/donor/edit/conf");
            $('.form1').submit();

        });

        $("#back").click(function() {
            $('.form1').attr("action", "/donor/edit");
            $('.form1').submit();
        });

        $(".toggle_container").hide();
        $("tr.trigger").click(function() {
            $(this).toggleClass("active").next().slideToggle("slow");
            return false;
        });
    });
</script>


<?php if (validation_errors()) : ?>
    <div class="err">
        <?php if (validation_errors()) : ?>
            <?php echo validation_errors('<span>', '</span><br />'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php echo form_open('', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?php echo $id ? "ドナー情報変更" : "ドナー新規登録" ?></th>
    </tr>
    <tr class="trigger">
        <td>東日本支部<br />Co</td>
    </tr>
    <tr class="toggle_container">
        <td>
            <?php foreach ($list[2] as $key => $val) : ?>
                <input type="checkbox" name="co[]" id="co<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('co[]', $key); ?> /><?php echo form_label($val['sei'] . " " . $val['mei'], "co" . $key); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
    <tr class="trigger">
        <td>東日本支部<br />都道府県Co</td>
    </tr>
    <tr class="toggle_container">
        <td>
            <?php foreach ($prefList[2] as $key => $val) : ?>
                <input type="checkbox" name="co[]" id="co<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('co[]', $key); ?> /><?php echo form_label($val['sei'] . " " . $val['mei'], "co" . $key); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
    <tr class="trigger">
        <td>中日本支部<br />Co</td>
    </tr>
    <tr class="toggle_container">
        <td>
            <?php foreach ($list[3] as $key => $val) : ?>
                <input type="checkbox" name="co[]" id="co<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('co[]', $key); ?> /><?php echo form_label($val['sei'] . " " . $val['mei'], "co" . $key); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
    <tr class="trigger">
        <td>中日本支部<br />都道府県Co</td>
    </tr>
    <tr class="toggle_container">
        <td>
            <?php foreach ($prefList[3] as $key => $val) : ?>
                <input type="checkbox" name="co[]" id="co<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('co[]', $key); ?> /><?php echo form_label($val['sei'] . " " . $val['mei'], "co" . $key); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
    <tr class="trigger">
        <td>西日本支部<br />Co</td>
    </tr>
    <tr class="toggle_container">
        <td>
            <?php foreach ($list[4] as $key => $val) : ?>
                <input type="checkbox" name="co[]" id="co<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('co[]', $key); ?> /><?php echo form_label($val['sei'] . " " . $val['mei'], "co" . $key); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
    <tr class="trigger">
        <td>西日本支部<br />都道府県Co</td>
    </tr>
    <tr class="toggle_container">
        <td>
            <?php foreach ($prefList[4] as $key => $val) : ?>
                <input type="checkbox" name="co[]" id="co<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('co[]', $key); ?> /><?php echo form_label($val['sei'] . " " . $val['mei'], "co" . $key); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><a href="#" id="confirm" class="confirm"><?php echo img(array('src' => 'img/btn007.jpg', 'alt' => '確認', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>