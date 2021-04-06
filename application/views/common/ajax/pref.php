<option value="">選択してください</option>
<?php foreach ($pref as $key => $val) : ?>
    <option value="<?php echo $val->id ?>"><?php echo $val->pref_name ?></option>
<?php endforeach;  ?>