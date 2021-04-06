<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".confirm").click(function() {
            $('#form1').submit();
        });
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?php echo validation_errors('<span>', '</span><br />'); ?>
    </div>
<?php endif; ?>

<?php echo form_open('admin/transplant/conf', array('name' => 'form1', 'class' => 'form1', 'id' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?php echo $id ? "施設情報変更" : "施設新規登録" ?></th>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="70%">
            <?php foreach ($organsMst as $val) : ?>
                <input type="checkbox" name="organs[]" id="<?php echo $val->id ?>" value="<?php echo $val->id ?>" <?php echo set_checkbox('organs[]', $val->id); ?> /><?php echo form_label($val->organ_name, $val->id); ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td width="30%">施設区分</td>
        <td width="70%">
            <select name="institution_kubun">
                <option value="">選択してください</option>
                <?php foreach (INSTITUTION_KUBUN as $id => $display) : ?>
                    <option value="<?php echo $id ?>" <?php echo set_select('institution_kubun', $id); ?>><?php echo $display ?></option>
                <?php endforeach; ?>
            </select>

        </td>
    </tr>
    <tr>
        <td width="30%">施設コード</td>
        <td width="70%"><?php echo form_input(array('name' => 'institution_code', 'value' => set_value('institution_code'), 'class' => 'iText', "maxlength" => 6)); ?></td>
    </tr>
    <tr>
        <td width="30%">都道府県</td>
        <td width="70%">
            <select name="pref_id">
                <option value="">選択してください</option>
                <?php foreach ($prefList as $pref) : ?>
                    <option value="<?php echo $pref->id ?>" <?php echo set_select('pref_id',  $pref->id); ?>><?php echo $pref->pref_name ?></option>
                <?php endforeach;  ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="30%">施設名</td>
        <td width="70%"><?php echo form_input(array('name' => 'institution', 'value' => set_value('institution'), 'class' => 'iText', "maxlength" => 80)); ?></td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('admin/transplant', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
        <li><a href="#" id="confirm" class="confirm"><?php echo img(array('src' => 'img/btn007.jpg', 'alt' => '確認', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>