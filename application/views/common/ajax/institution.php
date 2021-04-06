<option value="">選択してください</option>
<?php foreach ($institution as $key => $val) : ?>
    <option value="<?php echo $val->id ?>"><?php echo $val->institution_name ?></option>
<?php endforeach;  ?>