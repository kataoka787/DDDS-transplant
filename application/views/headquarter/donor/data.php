<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        const cordinatorImgUrl = $(".cordinatorBtn").attr('src');
        const transplantImgUrl = $(".transplantBtn").attr('src');
        const onCordinatorImgUrl = $(".cordinatorBtn").attr('src').replace(/\.jpg/ig, 'on.jpg');
        const onTransplantImgUrl = $(".transplantBtn").attr('src').replace(/\.jpg/ig, 'on.jpg');
        $(".cordinator").click(function() {
            $('#listMenu').empty();

            $(".cordinatorBtn").attr('src', onCordinatorImgUrl);
            $(".transplantBtn").attr('src', transplantImgUrl);
            $.ajax({
                type: "POST",
                url: "/donor/ajax/menu",
                data: "key=cordinator",
                success: function(data) {
                    $('#listMenu').empty();
                    $('#listMenu').append(data);
                }
            });
        });
        $(".transplant").click(function() {
            $('#listMenu').empty();
            $(".cordinatorBtn").attr('src', cordinatorImgUrl);
            $(".transplantBtn").attr('src', onTransplantImgUrl);

            $.ajax({
                type: "POST",
                url: "/donor/ajax/menu",
                data: "key=transplant",
                success: function(data) {
                    $('#listMenu').empty();
                    $('#listMenu').append(data);
                }
            });
        });

        $("#delete").click(function() {
            if (window.confirm('削除します。よろしいですか？')) {
                $("#delete_form").submit();
            } else {
                window.alert('キャンセルされました');
            }
        });

    });
</script>
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
        <td>連絡事項</td>
        <td><?php echo nl2br(html_escape($message)) ?></td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li id="cordinator" class="cordinator cursorPointer"><?php echo img(array('src' => 'img/btn029.jpg', 'alt' => '現地コーディネーター', 'width' => '124', 'height' => '24', 'id' => 'cordinatorBtn', 'class' => 'cordinatorBtn')) ?></li>
        <li id="transplant" class="transplant cursorPointer"><?php echo img(array('src' => 'img/btn031.jpg', 'alt' => '移植施設', 'width' => '124', 'height' => '24', 'class' => 'transplantBtn', 'id' => 'transplantBtn')) ?></li>
        <?php if ($this->session->userdata('admin_flg')) : ?>
            <li><?php echo anchor('admin/donorDataDownload', img(array('src' => 'img/btn032.jpg', 'alt' => '一括ダウンロード', 'width' => '124', 'height' => '24'))) ?></li>
            <li id="delete" class="cursorPointer">
                <?php echo img(array('src' => 'img/btn033.jpg', 'alt' => '削除', 'width' => '124', 'height' => '24')) ?>
            </li>
        <?php endif; ?>
        <li><?php echo anchor('donor/searchlist', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'class' => 'back', 'id' => 'back'))) ?></li>
    </ul>
</div>

<div id="listMenu" class="listMenu"></div>
<?= form_open("admin/donorDataDelete", array("id" => "delete_form")) ?>
<?= form_hidden("d_id", $d_id) ?>
<?= form_close() ?>