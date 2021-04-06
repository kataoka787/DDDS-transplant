<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });
    });
</script>

<p>コラボレーションの作成に失敗</p>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('transplant/request', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24'))) ?></li>
    </ul>
</div>