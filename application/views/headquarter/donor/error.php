<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<p>ドナー登録に失敗しました。</p>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('donor/searchlist', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>