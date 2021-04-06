<script type="text/javascript">
    function goPage(id) {
        document.list.id.value = id;
        document.forms['list'].submit();
    }

    function searchPage(id) {
        document.getElementById("form1").action = "/admin/cordinator/" + id;
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

    <?php echo form_open('admin/cordinator', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
        <tr>
            <th colspan="2">検索</th>
        </tr>
        <tr>
            <td width="30%">姓(カナ)</td>
            <td width="70%"><?php echo form_input(array('name' => 'sei_kana', 'value' => set_value('sei_kana'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td width="30%">名(カナ)</td>
            <td width="70%"><?php echo form_input(array('name' => 'mei_kana', 'value' => set_value('mei_kana'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td width="30%">メールアドレス</td>
            <td width="70%"><?php echo form_input(array('name' => 'mail', 'value' => set_value('mail'), 'class' => 'iText')); ?></td>
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
            <li><?php echo anchor('admin/cordinator/newedit', img(array('src' => 'img/btn020.jpg', 'alt' => '新規登録', 'width' => '124', 'height' => '24'))) ?></li>
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
        <th>メールアドレス</th>
        <th>データ</th>
    </tr>
    <?php foreach ($list as $key => $val) : ?>
        <tr>
            <td><?php echo $val->sei . " " . $val->mei ?></td>
            <td><?php echo $val->mail ?></td>
            <td><a href="#" onClick='goPage("<?php echo $val->id ?>");'>変更 / 削除</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php echo form_open('admin/cordinator/confirm', array('name' => 'list', 'class' => 'list', 'id' => 'list')); ?>
<input type="hidden" id="id" class="id" name="id" value="">
<?php echo form_close(); ?>