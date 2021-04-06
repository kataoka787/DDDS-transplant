<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#next").click(function() {
            $('.form1').submit();
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

<?php echo form_open('edit/conf', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?php echo $id ? "ドナー情報変更" : "ドナー新規登録" ?></th>
    </tr>
    <tr>
        <td>提供施設</td>
    </tr>
    <tr>
        <td><?php echo form_input(array('name' => 'offerInstitution', 'value' => set_value('offerInstitution'), 'class' => 'iText', "maxlength" => 80)); ?></td>
    </tr>
    <tr>
        <td>提供施設都道府県</td>
    </tr>
    <tr>
        <td>
            <select name="offerInstitutionPref">
                <option value="--">選択してください</option>
                <?php foreach ($prefMst as $key => $val) : ?>
                    <option value="<?php echo $val->id ?>" <?php echo set_select('offerInstitutionPref',  $val->id); ?>><?php echo $val->pref_name ?></option>
                <?php endforeach;  ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>ドナー氏名(全角カナ)</td>
    </tr>
    <tr>
        <td>
            氏<?php echo form_input(array('name' => 'firstName', 'value' => set_value('firstName'), 'class' => 'iText', "maxlength" => 80)); ?>
            <br />
            名<?php echo form_input(array('name' => 'secondName', 'value' => set_value('secondName'), 'class' => 'iText', "maxlength" => 80)); ?>
        </td>
    </tr>
    <tr>
        <td>年齢</td>
    </tr>
    <tr>
        <td><?php echo form_input(array('name' => 'age', 'value' => set_value('age'), 'maxlength' => '2')); ?></td>
    </tr>
    <tr>
        <td>性別</td>
    </tr>
    <tr>
        <td>
            <input type="radio" name="sex" id="sex1" value="1" <?php echo set_radio('sex', '1'); ?>><?php echo form_label("男性", "sex1"); ?>
            <input type="radio" name="sex" id="sex2" value="2" <?php echo set_radio('sex', '2'); ?>><?php echo form_label("女性", "sex2"); ?>
        </td>
    </tr>
    <td>脳死/心停止</td>
    </tr>
    <tr>
        <td>
            <select name="deathReasonMstId">
                <option value=""></option>
                <?php foreach ($causeDeathMst as $key => $val) : ?>
                    <option value="<?php echo $val->id ?>" <?php echo set_select('deathReasonMstId',  $val->id); ?>><?php echo $val->cause_death_name ?></option>
                <?php endforeach;  ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>連絡事項</td>
    </tr>
    <tr>
        <td><?php echo form_textarea(array('name' => 'message', 'value' => set_value('message'), 'cols' => 30, 'rows' => 4, "maxlength" => 1000)); ?></td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('/data', img(array('src' => 'img/btn010.jpg', 'alt' => 'ドナー一覧画面へ', 'width' => '124', 'height' => '24'))) ?></li>
        <li><a href="#" id="next" class="next"><?php echo img(array('src' => 'img/btn006.jpg', 'alt' => '次へ', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>