<script type="text/javascript">
    function goPage(id) {
        resetCSRFToken();
        document.list.id.value = id;
        document.forms['list'].submit();
    }

    function searchPage(id) {
        resetCSRFToken();
        document.getElementById("form1").action = "<?= base_url() ?>doctor/" + id;
        document.forms['form1'].submit();
    }

    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".search").click(function() {
            resetCSRFToken();
            $('#form1').submit();
        });

        $(".notice").click(function() {
            resetCSRFToken();
            $('#regist_form').submit();
        });

        $('#pref').change(function() {
            $('#institution').empty();
            if ($(this).val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/institution",
                    data: {
                        pref_id: $(this).val()
                    },
                    success: function(data) {
                        $('#institution').append(data);
                    }
                });
            }
        });
    });
</script>

<div class="sTable2">
    <?php if (validation_errors()) : ?>
        <div class="err">
            <?php echo validation_errors('<span>', '</span><br />'); ?>
        </div>
    <?php endif; ?>

    <?php echo form_open('/doctor', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
        <tr>
            <th colspan="2">検索</th>
        </tr>
        <tr>
            <td width="30%">都道府県</td>
            <td width="70%"><?= $prefName  ?></td>
        </tr>
        <tr>
            <td width="30%">施設名</td>
            <td width="70%"><?= $institutionName ?></td>
        </tr>
        <tr>
            <td width="30%">姓(全角カナ)</td>
            <td width="70%"><?php echo form_input(array('name' => 'sei_kana', 'value' => set_value('sei_kana'), 'class' => 'input-h24')); ?></td>
        </tr>
        <tr>
            <td width="30%">名(全角カナ)</td>
            <td width="70%"><?php echo form_input(array('name' => 'mei_kana', 'value' => set_value('mei_kana'), 'class' => 'input-h24')); ?></td>
        </tr>

        <tr>
            <td width="30%">臓器</td>
            <td width="70%">
                <?php foreach ($organs as $key => $val) : ?>
                    <input type="checkbox" name="organs[]" id="<?php echo $val->id ?>" value="<?php echo $val->id ?>" <?php echo set_checkbox('organs[]', $val->id); ?> />
                    <?php echo form_label($val->organ_name, $val->id); ?>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php echo form_close(); ?>

    <div class="btnArea">
        <ul>
            <li><?php echo anchor('doctor/edit', img(array('src' => 'img/btn020.jpg', 'alt' => '新規登録', 'width' => '124', 'height' => '24'))) ?></li>
            <li><?php echo anchor('menu', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
            <li><a href="#" class="search" id="search"><img src="/img/btn028.jpg" alt="検索" width=124 height=24></a></li>
        </ul>
    </div>
    <div class="btnArea">
        <ul>

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
        <th>氏名</th>
        <th>臓器</th>
        <th>ステータス</th>
        <th>メールアドレス</th>
        <th>データ</th>
    </tr>
    <?php foreach ($list as $val) : ?>
        <tr>
            <td><?php echo $val->sei . " " . $val->mei ?></td>
            <td>
                <?php foreach ($organs as $organKey => $organVal) : ?>
                    <?php $name = "organ" . $organVal->id; ?>
                    <?php if ($val->$name) : ?>
                        <?php echo $organVal->organ_name . "　" ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </td>
            <td><?php echo $val->status ?></td>
            <td><?php echo $val->mail ?></td>
            <td>
                <a href="#" onClick='goPage("<?php echo $val->id ?>");'>
                    変更/削除
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php echo form_close(); ?>

<br class="clear" />

<?php echo form_open('doctor/confirm', array('name' => 'list', 'class' => 'list', 'id' => 'list')); ?>
<input type="hidden" id="id" class="id" name="id" value="">
<?php echo form_close(); ?>