<?php foreach ($users as $key => $val) : ?>
    <input type="checkbox" name="user[]" id="user<?php echo $key ?>" value="<?php echo $val->id ?>" <?php echo set_checkbox('user[]', $key); ?> /><?php echo form_label($val->sei . " " . $val->mei, "user" . $key); ?><br />
<?php endforeach;  ?>