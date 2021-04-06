<?php if ($resultCount == 0) : ?>
    <tr>
        <td colspan="19" class="pCenter">該当データが存在しません。</td>
    </tr>
<?php elseif ($resultCount > config_item('max_search_result')) : ?>
    <tr>
        <td colspan="19" class="pCenter">該当データが多すぎるため、レシピエント情報を表示できません。検索条件を増やしてください。</td>
    </tr>
<?php else : ?>
    <?php foreach ($result as $row) : ?>
        <tr>
            <td><?= form_checkbox(array('id' => 'itemSelect_' . $row->RECIPIENT_ID, 'name' => 'itemSelect', 'value' => implode(',', array($row->RECIPIENT_ID, $row->ZOKI_CODE, $row->ISYOKU_CNT)), 'class' => '')) ?></td>
            <td class="pCenter">
                <?= form_open('/detail', array("method" => "POST", "target" => "_blank")) ?>
                <a href="javascript:;" onclick="parentNode.submit();"><?= str_pad($row->RECIPIENT_ID, 7, 0, STR_PAD_LEFT) ?></a>
                <input type="hidden" name="recipientId" value="<?= $row->RECIPIENT_ID ?>" />
                <input type="hidden" name="zokiCode" value="<?= $row->ZOKI_CODE ?>" />
                <input type="hidden" name="isyokuCnt" value="<?= $row->ISYOKU_CNT ?>" />
                <?= form_close() ?>
            </td>
            <td><?= $row->KANA_NAME . $row->KANJI_NAME ?></td>
            <td class="pCenter"><?= $row->elapsedPeriod ?></td>
            <td class="pCenter"><?= isset($row->REPORT_DEADLINE) ? datetimeToString($row->REPORT_DEADLINE) : "-" ?></td>
            <td class="pCenter"><?= $row->living_conditions ?></td>
            <td class="pCenter"><?= $row->inspection_item ?></td>
            <td class="pCenter"><?= datetimeToString($row->BIRTHDAY) ?></td>
            <td class="pCenter"><?= empty($row->BIRTHDAY) ? '' : date_diff(date_create($row->BIRTHDAY), date_create('now'))->y ?></td>
            <td class="pCenter"><?= $row->sex ?></td>
            <td class="pCenter"><?= $row->organ ?></td>
            <td class="pCenter"><?= $row->DOUJI_ISHOKU ?></td>
            <td class="pCenter"><?= datetimeToString($row->ISYOKU_DATE) ?></td>
            <td class="pCenter"><?= $row->ISYOKU_CNT ?></td>
            <td><?= $row->transplant_name ?></td>
            <td><?= $row->transfer_destination_name ?></td>
            <td class="pCenter"><?= $row->organ_outcome ?></td>
            <td class="pCenter"><?= $row->patient_outcome ?></td>
            <?php if ($this->session->userdata('account')->account_type_mst_id == ACC_TYPE_CO) : ?>
                <td class="pCenter"><?= isset($row->DONOR_ID) ? str_pad($row->DONOR_ID, 7, 0, STR_PAD_LEFT) : '' ?></td>
            <?php endif ?>
        </tr>
    <?php endforeach ?>
<?php endif ?>