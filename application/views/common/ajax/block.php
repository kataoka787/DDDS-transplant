<option value="">選択してください</option>
<?php foreach ($block as $key => $val) : ?>
    <option value="<?php echo $val->id ?>"><?php echo $val->block_name ?></option>
<?php endforeach;  ?>