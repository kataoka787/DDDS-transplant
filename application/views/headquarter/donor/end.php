<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<p><?php echo $id ? "ドナー情報変更" : "ドナー登録" ?>が完了致しました。</p>

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
        <li><?php echo anchor('donor/searchlist', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>