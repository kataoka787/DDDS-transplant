<?php if ($resultCount == 0) : ?>
    <tr>
        <td colspan="2" class="pCenter">該当データが存在しません。</td>
    </tr>
<?php else : ?>
    <?php foreach ($result as $value) : ?>
        <tr class="clickable-row" id="<?= $value->SISETU_CD ?>" kubun="<?= $value->institution_kubun ?>">
            <td><?= $value->institution_kubun_name ?></td>
            <td><?= $value->institution_name ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif ?>