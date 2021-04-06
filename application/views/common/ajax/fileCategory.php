<option value="">選択してください</option>
<?php foreach ($category as $key => $val) : ?>
    <option value="<?php echo $val->id ?>"><?php echo $val->category_name ?></option>
<?php endforeach;  ?>