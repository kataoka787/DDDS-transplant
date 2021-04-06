<script type="text/javascript">
    function searchPage(id) {
        document.getElementById("form1").action = "/donor/searchlist/" + id;
        document.forms['form1'].submit();
    }

    function goPage(id) {
        document.getElementById("list").action = "edit";
        document.list.d_id.value = id;
        document.forms['list'].submit();
    }

    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".edit_button").click(function() {
            $('.d_id').val($(this).val());
            document.getElementById("list").action = "/donor/data";
            document.forms['list'].submit();
        });
        $(".search").click(function() {
            document.forms['form1'].submit();
        });
    });
</script>
<div class="sTable2">
    <p><?php echo anchor('donor/edit/newedit', img(array('src' => 'img/btn027.jpg', 'alt' => 'ドナー新規登録', 'width' => '124', 'height' => '24'))); ?></p>

    <?php if (validation_errors()) : ?>
        <div class="err">
            <?php echo validation_errors('<span>', '</span><br />'); ?>
        </div>
    <?php endif; ?>


    <?php echo form_open('donor/searchlist', array('class' => 'form1', 'id' => 'form1', 'name' => 'form1')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
        <tr>
            <th colspan="2">検索</th>
        </tr>
        <tr>
            <td width="30%">事例No<br /></td>
            <td width="70%"><?php echo form_input(array('name' => 'd_id', 'value' => set_value('d_id'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td>ブロック<br /></td>
            <td>
                <select name="block_id">
                    <option value=""></option>
                    <?php foreach ($block_mst as $key => $val) : ?>
                        <option value="<?php echo $val->id ?>" <?php echo set_select('block_id', $val->id); ?>><?php echo $val->block_name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>病院名<br /></td>
            <td><?php echo form_input(array('name' => 'offer_institution', 'value' => set_value('offer_institution'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td>性別<br /></td>
            <td>
                <input type="radio" name="sex" id="sex3" value="" checked> <?php echo form_label("すべて", "sex3"); ?>
                <input type="radio" name="sex" id="sex1" value="<?= MALE ?>" <?php echo set_radio('sex', MALE); ?>><?php echo form_label(SEX[MALE], "sex1"); ?>
                <input type="radio" name="sex" id="sex2" value="<?= FEMALE ?>" <?php echo set_radio('sex', FEMALE); ?>><?php echo form_label(SEX[FEMALE], "sex2"); ?>
            </td>
        </tr>
        <tr>
            <td>年齢</td>
            <td><?php echo form_input(array('name' => 'age', 'value' => set_value('age'))); ?>歳</td>
        </tr>
    </table>
    <?php echo form_close(); ?>

    <div class="btnArea">
        <ul>
            <li><?php echo anchor('menu', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))); ?></li>
            <li><a href="#" id="search" class="search"><?php echo img(array('src' => 'img/btn028.jpg', 'alt' => '検索', 'width' => '124', 'height' => '24')); ?></a></li>
        </ul>
    </div>
</div>



<div class="frBtnArea">
    <div class="fArea">
        <?php if ($prev['flg']) : ?><a href="#" onClick='searchPage("<?php echo $prev['link'] ?>");'>前へ</a><?php endif; ?>
    </div>
    <div class="bArea">
        <?php if ($next['flg']) : ?><a href="#" onClick='searchPage("<?php echo $next['link'] ?>");'>次へ</a><?php endif; ?>
    </div>
</div>
<br class="clear" />

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th width="15%">事例No</th>
        <th width="15%">脳死/心停止</th>
        <th width="15%">提供施設</th>
        <th width="11%">提供施設<br />都道府県</th>
        <th width="15%">ドナー氏名<br />（カナ）</th>
        <th width="8%">性別</th>
        <th width="8%">年齢</th>
        <th width="11%">データ</th>
    </tr>
    <?php foreach ($list as $key => $val) : ?>
        <tr>
            <td><a href="#" onClick='goPage("<?php echo $val->d_id ?>");'><?php echo $val->d_id ?></a></td>
            <td><?php echo $this->Causedeathmst->getCauseDeathNameById($val->cause_death_mst_id) ?></td>
            <td><?php echo $val->offer_institution_name ?></td>
            <td><?php echo $this->Prefmst->getPrefNameById($val->pref_mst_id) ?></td>
            <td><?php echo $this->Donorbasetbl->getDispName($val->sei, $val->mei, " ") ?></td>
            <td><?php echo SEX[$val->sex] ?></td>
            <td><?php echo $val->age ?>歳</td>
            <td><?php echo form_button(array('class' => 'edit_button', 'id' => 'edit_button', 'name' => 'edit_button', 'content' => '送受信', 'value' => $val->d_id)); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php echo form_open('donor/data', array('name' => 'list', 'class' => 'list', 'id' => 'list')); ?>
<input type="hidden" id="d_id" class="d_id" name="d_id" value="">
<?php echo form_close(); ?>