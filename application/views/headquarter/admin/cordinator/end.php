<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<p>コーディネーター <?= isset($accountId) ? "変更" : "登録" ?>が完了致しました。<br />
    メール送信完了しました。
</p>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2"><?= isset($accountId) ? "コーディネーター情報変更" : "コーディネーター新規登録" ?></th>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%"><?php echo $sei . "" . $mei ?></td>
    </tr>

    <tr>
        <td width="30%">フリガナ</td>
        <td width="70%"><?php echo $sei_kana . "" . $mei_kana ?></td>
    </tr>

    <tr>
        <td width="30%">メールアドレス(携帯)</td>
        <td width="70%"><?php echo $mail; ?></td>
    </tr>

    <tr>
        <td width="30%">パスワード</td>
        <td width="70%">*********</td>
    </tr>

    <tr>
        <td width="30%">Co区分</td>
        <td width="70%">NW</td>
    </tr>
    <tr>
        <td width="30%">所属</td>
        <td width="70%">本部</td>
    </tr>
    <tr class="nw_row">
        <td width="30%">権限</td>
        <td width="70%"><?php echo $admin_flg == '1' ? "管理" : "一般" ?></td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('admin/cordinator', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>