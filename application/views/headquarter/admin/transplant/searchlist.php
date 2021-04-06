<script type="text/javascript">
    function edit(id) {
        $("#edit_id").val(id);
        $("#edit").submit();
    }

    function searchPage(id) {
        document.getElementById("form1").action = "/admin/transplant/" + id;
        document.forms['form1'].submit();
    }

    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".search").click(function() {
            $('#form1').submit();
        });
    });
</script>

<div class="sTable2">
    <?php if (validation_errors()) : ?>
        <div class="err">
            <?php echo validation_errors('<span>', '</span><br />'); ?>
        </div>
    <?php endif; ?>

    <?php echo form_open('admin/transplant', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
        <tr>
            <th colspan="2">検索</th>
        </tr>
        <tr>
            <td width="30%">臓器</td>
            <td width="70%">
                <?php foreach ($organs as $key => $val) : ?>
                    <input type="checkbox" name="organs[]" id="<?php echo $val->id ?>" value="<?php echo $val->id ?>" <?php echo set_checkbox('organs[]', $val->id); ?> /><?php echo form_label($val->organ_name, $val->id); ?>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <td width="30%">都道府県</td>
            <td width="70%">
                <select name="pref_id">
                    <option value="">選択してください</option>
                    <?php foreach ($prefList as $pref) : ?>
                        <option value="<?php echo $pref->id ?>" <?php echo set_select('pref_id', $pref->id); ?>><?php echo $pref->pref_name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="30%">施設名</td>
            <td width="70%"><?php echo form_input(array('name' => 'institution', 'value' => set_value('institution'), 'class' => 'iText')); ?></td>
        </tr>
    </table>
    <?php echo form_close(); ?>

    <div class="btnArea">
        <ul>
            <li><?php echo anchor('menu', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
            <li><a href="#" class="search" id="search"><img src="/img/btn028.jpg" alt="検索" width=124 height=24></a></li>
        </ul>
    </div>

    <div class="btnArea">
        <ul>
            <li><?php echo anchor('admin/transplant/newedit', img(array('src' => 'img/btn020.jpg', 'alt' => '新規登録', 'width' => '124', 'height' => '24'))) ?></li>
            <li><?php echo anchor('admin/transplant/csv', img(array('src' => 'img/btn036.jpg', 'alt' => 'CSVダウンロード', 'width' => '124', 'height' => '24'))) ?></li>
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
        <th>都道府県</th>
        <th>施設名</th>
        <th>臓器</th>
        <th>データ</th>
    </tr>
    <?php foreach ($institutionList as $key => $institution) : ?>
        <tr>
            <td><?php echo $institution->pref_name ?></td>
            <td><?php echo $institution->institution_name ?></td>
            <td>
                <?php foreach ($organs as $organKey => $organVal) : ?>
                    <?php $name = "organ" . $organVal->id; ?>
                    <?php if ($institution->$name) : ?>
                        <?php echo $organVal->organ_name . "　"?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </td>
            <td><a href="#" onClick='edit("<?php echo $institution->id ?>");'>変更</a></td>
        </tr>
    <?php endforeach ?>
</table>

<?php echo form_open('admin/transplant/edit', array('id' => 'edit')); ?>
<input type="hidden" id="edit_id" name="id" value="">
<?php echo form_close(); ?>