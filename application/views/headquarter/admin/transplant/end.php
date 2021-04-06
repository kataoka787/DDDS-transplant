<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<p>移植施設<?php echo $id ? "情報の変更" : "登録" ?>が完了致しました。</p>

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
        <li><?php echo anchor('admin/transplant', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>