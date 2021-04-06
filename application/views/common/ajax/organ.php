<?php foreach ($organs as $organ) : ?>
    <input type="checkbox" name="organs[]" id="<?= "organ$organ->id" ?>" value="<?php echo $organ->id ?>"/>
    <?php echo form_label($organ->organ_name, "organ$organ->id"); ?>
<?php endforeach; ?>