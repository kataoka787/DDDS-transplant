<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<?php if ($isEdit) : ?>
    <p>移植施設　ユーザ情報の変更が完了致しました。</p>
<?php else : ?>
    <?php if (in_array(WORK_FOLLOW_UP, $workIds)) : ?>
        施設ユーザのメールアドレスにパスワード登録用URLを送信致しました。
    <?php else : ?>
        施設ユーザの登録が完了致しました。
    <?php endif ?>
<?php endif ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">移植施設 ユーザ情報変更</th>
    </tr>
    <tr>
        <td>都道府県</td>
        <td>
            <?php echo $pref ?>
        </td>
    </tr>
    <tr>
        <td width="30%">移植施設</td>
        <td width="30%">
            <?php echo $institution ?>
        </td>
    </tr>
    <tr>
        <td width="30%">臓器</td>
        <td width="30%">
            <?= $organs ?>
        </td>
    </tr>
    <tr>
        <td width="30%">利用者権限</td>
        <td width="70%"><?php echo $doctor_type_name ?></td>
    </tr>
    <tr>
        <td width="30%">業務権限</td>
        <td width="70%">
            <?= $works ?>
        </td>
    </tr>
    <tr>
        <td width="30%">氏名</td>
        <td width="70%"><?php echo $name ?></td>
    </tr>
    <tr>
        <td width="30%">フリガナ</td>
        <td width="70%"><?php echo $kana ?></td>
    </tr>
    <tr>
        <td width="30%">メールアドレス</td>
        <td width="70%"><?php echo $mail ?></td>
    </tr>
    <?php if ($isPasswordInputable) : ?>
        <tr>
            <td width="30%">パスワード</td>
            <td width="70%">**********</td>
        </tr>
    <?php endif ?>
</table>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('admin/transplantUser', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>