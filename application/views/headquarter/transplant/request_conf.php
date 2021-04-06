<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".file_request").click(function() {
            $("#form1").submit();
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

<?php echo form_open('transplant/request/update', array('name' => 'form1', 'class' => 'form1', 'id' => 'form1')); ?>
<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">ドナー データ管理</th>
    </tr>
    <tr>
        <td width="30%">事例No</td>
        <td width="70%"><?php echo $d_id ?></td>
    </tr>
    <tr>
        <td>提供施設</td>
        <td><?php echo $offerInstitution ?></td>
    </tr>
    <tr>
        <td>提供施設都道府県</td>
        <td><?php echo $offerInstitutionPref ?></td>
    </tr>
    <tr>
        <td>ドナー氏名(カナ)</td>
        <td><?php echo $donorNeme ?></td>
    </tr>
    <tr>
        <td>性別</td>
        <td><?php echo $sex == '1' ? "男性" : "女性" ?></td>
    </tr>
    <tr>
        <td>年齢</td>
        <td><?php echo $age ?>歳</td>
    </tr>
    <tr>
        <td>脳死/心 停止</td>
        <td><?php echo $deathReason ?></td>
    </tr>

    <tr>
        <td>臓器</td>
        <td><?php echo $organ->organ_name ?></td>
    </tr>


    <tr>
        <td>都道府県</td>
        <td><?php echo $pref->pref_name ?></td>
    </tr>
    <tr>
        <td>施設</td>
        <td><?php echo $institution->institution_name ?></td>
    </tr>

    <tr>
        <td>ユーザ</td>
        <td>
            <?php foreach ($user as $key => $val) : ?>
                <?php echo $val->sei ?> <?php echo $val->mei ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td>依頼ファイル</td>
        <td>
            <ul id="accordion">
                <?php foreach ($list as $key => $val) : ?>
                    <li><span class="reqBgBtn"><?php echo $val['folder_name'] ?></span>
                        <ul>
                            <?php foreach ($val['file'] as $key => $val) : ?>
                                <li class="reqCld"><?php echo $val ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('transplant/request', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
        <li><a href="#" id="file_request" class="file_request"><?php echo img(array('src' => 'img/btn034.jpg', 'alt' => '依頼', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>