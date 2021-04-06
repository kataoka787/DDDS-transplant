<?=$error?>
<br />

<script>
    $(function() {
        $("#close").click(() => {
            window.close();
            window.location.href = "<?= base_url() ?>";
        });
    })
</script>
<div class="btnArea">
    <ul>
        <li>
            <a href="#" id="close">
                <?=img(array('src' => 'img/btn018.jpg', 'alt' => '閉じる', 'width' => '124', 'height' => '24'))?>
            </a>
        </li>
    </ul>
</div>