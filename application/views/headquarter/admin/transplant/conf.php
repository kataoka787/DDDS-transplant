<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".update").click(function() {
            $('.form1').attr("action", "/admin/transplant/update");
            $('#form1').submit();
        });

        $("#back").click(function() {
            $('.form1').attr("action", "/admin/transplant/edit");
            $('.form1').submit();
        });
    });
</script>

<?php echo form_open('', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?php echo $id ? "施設情報変更" : "施設新規登録" ?></th>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="70%">
            <?php foreach ($organs as $key => $val) : ?>
                <?= $val->organ_name . "　"?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td width="30%">施設区分</td>
        <td width="70%"><?= INSTITUTION_KUBUN[$institution_kubun] ?></td>
    </tr>
    <tr>
        <td width="30%">施設コード</td>
        <td width="70%"><?= $institution_code ?></td>
    </tr> 
    <tr>
        <td width="30%">都道府県</td>
        <td width="70%"><?php echo $pref ?></td>
    </tr>  
    <tr>
        <td width="30%">施設名</td>
        <td width="70%"><?php echo $institution; ?></td>
    </tr>   
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><a href="#" id="update" class="update"><?php echo img(array('src' => 'img/btn008.jpg', 'alt' => '登録', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>

<?php echo form_close(); ?>