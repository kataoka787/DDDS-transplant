<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#regist").click(function() {
            $('.form1').attr("action", "/edit/update");
            $('.form1').submit();

        });
        $("#back").click(function() {
            $('.form1').attr("action", "/edit/index");
            $('.form1').submit();
        });
    });
</script>

<?php echo form_open('', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?php echo $id ? "ドナー情報変更" : "ドナー新規登録" ?></th>
    </tr>
    <tr>
        <td>事例No</td>
    </tr>
    <tr>
        <td><?php echo $d_id ?></td>
    </tr>
    <tr>
        <td>提供施設</td>
    </tr>
    <tr>
        <td><?php echo $dispOfferInstitution ?></td>
    </tr>
    <tr>
        <td>提供施設都道府県</td>
    </tr>
    <tr>
        <td><?php echo $dispOfferInstitutionPref ?></td>
    </tr>
    <tr>
        <td>ドナー氏名(全角カナ)</td>
    </tr>
    <tr>
        <td><?php echo $dispDonorNeme ?></td>
    </tr>
    <tr>
        <td>年齢</td>
    </tr>
    <tr>
        <td><?php echo $dispAge ?>歳</td>
    </tr>
    <tr>
        <td>性別</td>
    </tr>
    <tr>
        <td><?php echo $dispSex == '1' ? "男性" : "女性" ?></td>
    </tr>
    <td>脳死/心停止</td>
    </tr>
    <tr>
        <td><?php echo $dispDeathReason ?></td>
    </tr>
    <tr>
        <td>連絡事項</td>
    </tr>
    <tr>
        <td><?php echo nl2br(form_prep($dispMessage)) ?></td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><a href="#" id="regist" class="regist"><?php echo img(array('src' => 'img/btn008.jpg', 'alt' => '登録', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>