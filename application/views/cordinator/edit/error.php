<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $("#donor_data").click(function() {
            $('.donor_form').submit();
        });
    });
</script>


<p>ドナー登録に失敗しました。</p>

<div class="btnArea">
    <ul>
        <li><?php echo anchor('donorlist', img(array('src' => 'img/btn010.jpg', 'alt' => 'ドナー一覧画面へ', 'width' => '124', 'height' => '24', 'id' => 'donor_list', 'class' => 'donor_list'))) ?></li>
    </ul>
</div>

<?php echo form_open('/data', array('name' => 'list', 'id' => 'donor_form', 'class' => 'donor_form')); ?>
<?php echo form_hidden('d_id', $d_id) ?>
<?php echo form_close(); ?>