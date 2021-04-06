<div class="card mb-4 collapse-border" id="accordion3">
    <div class="collapse-header row m-0 d-flex justify-content-between">
        <p class="m-0">検査・合併症</p>
        <a class="collapse-btn" data-toggle="collapse" href="#collapseThree">
            <i class="fa fa-minus-circle" aria-hidden="true"></i>
        </a>
    </div>
    <div id="collapseThree" class="collapse show" data-parent="#accordion3">
        <div class="card-body">
            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead mb-0">検査項目</legend>
                <?= form_button(array('class' => 'bg-btn w-15', 'id' => 'addColumn', 'name' => '', 'content' => '列追加')); ?>
                <table class="w-100 mt-1 table-bordered d-none" name="inspectionTable">
                    <thead>
                        <tr>
                            <th>検査項目</th>
                            <th>単位</th>
                            <?php for ($i = 0; $i < config_item('inspection_max_table_column'); $i++) : ?>
                                <th class="cell-width disabled"></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inspection['items'] as $key => $value) : ?>
                            <tr>
                                <td><?= $value[0] ?></td>
                                <td class="d-none"><?= form_input(array('name' => "inspectionValue[$key][name]", 'value' => $value[0], 'readonly' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "inspectionValue[$key][unit]", 'value' => $value[1], 'readonly' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "inspectionValue[$key][dspno]", 'value' => $key + 1, 'readonly' => true)) ?></td>
                                <td class="pCenter"><?= $value[1] ?></td>
                                <?php for ($i = 0; $i < config_item('inspection_max_table_column'); $i++) : ?>
                                    <td class="pCenter p-0 disabled"></td>
                                <?php endfor; ?>
                                <?php if ($value[2]) : ?>
                                    <td class="pCenter p-0 d-none">
                                        <?= form_dropdown(
                                            "inspectionValue[$key][cycleKey]",
                                            $value[3],
                                            '',
                                            array(
                                                'class' => 'input-h24 w-100 no_border' . ($key === 0 ? ' input-status bg-pink' : ''),
                                                'disabled' => true
                                            )
                                        ) ?>
                                    </td>
                                <?php else : ?>
                                    <td class="pCenter p-0 d-none">
                                        <?= form_input(array(
                                            'name' => "inspectionValue[$key][cycleKey]",
                                            '',
                                            'disabled' => true,
                                            'maxlength' => '100',
                                            'class' => 'input-h24 no_border' . ($key === 1 ? ' no-calendar-icon' : ' w-100'),
                                            'type' => ($key === 1) ? 'date' : 'text',
                                        )) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php for ($t = 0; $t < $inspection['tableSetting']['numbersOfTable']; $t++) : ?>
                    <table class="w-100 mt-1 table-bordered" name="inspectionTable">
                        <thead>
                            <tr>
                                <th>検査項目</th>
                                <th>単位</th>
                                <?php $currentColumn = $t * config_item('inspection_max_table_column');
                                for ($i = 0; $i < config_item('inspection_max_table_column'); $i++) : ?>
                                    <?php if ($currentColumn >= $inspection['tableSetting']['maxColumn']) : ?>
                                        <th class="cell-width disabled"></th>
                                    <?php else : ?>
                                        <th class="cell-width"><?= $inspection['cycle'][$currentColumn]['value'] ?></th>
                                    <?php endif; ?>
                                <?php $currentColumn++;
                                endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inspection['items'] as $key => $value) : ?>
                                <tr>
                                    <td><?= $value[0] ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "inspectionValue[$key][name]", 'value' => $value[0], 'readonly' => true)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "inspectionValue[$key][unit]", 'value' => $value[1], 'readonly' => true)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "inspectionValue[$key][dspno]", 'value' => $key + 1, 'readonly' => true)) ?></td>
                                    <td class="pCenter"><?= $value[1] ?></td>
                                    <?php $currentColumn = $t * config_item('inspection_max_table_column');
                                    for ($i = 0; $i < config_item('inspection_max_table_column'); $i++) : ?>
                                        <?php if ($currentColumn >= $inspection['tableSetting']['maxColumn']) : ?>
                                            <td class="pCenter p-0 disabled"></td>
                                        <?php elseif ($value[2]) : ?>
                                            <?php $columnName = 'KENSA_VALUE_' . $inspection['cycle'][$currentColumn]['code'] ?>
                                            <td class="pCenter p-0">
                                                <?= form_dropdown(
                                                    "inspectionValue[$key][" . $inspection['cycle'][$currentColumn]['code'] . ']',
                                                    $value[3],
                                                    isset($inspection['data'][$value[0]]) ? $inspection['data'][$value[0]]->$columnName : '',
                                                    array('class' => 'input-h24 w-100 no_border' . ($key === 0 ? ' input-status' : ''))
                                                ) ?>
                                            </td>
                                        <?php else : ?>
                                            <?php $columnName = 'KENSA_VALUE_' . $inspection['cycle'][$currentColumn]['code'] ?>
                                            <td class="pCenter p-0">
                                                <?= form_input(array(
                                                    'name' => "inspectionValue[$key][" . $inspection['cycle'][$currentColumn]['code'] . ']',
                                                    'value' => isset($inspection['data'][$value[0]]) ?  $inspection['data'][$value[0]]->$columnName : '',
                                                    'maxlength' => '100',
                                                    'class' => 'input-h24 no_border' . ($key === 1 ? ' no-calendar-icon' : ' w-100'),
                                                    'type' => ($key === 1) ? 'date' : 'text',
                                                )) ?>
                                            </td>
                                        <?php endif; ?>
                                    <?php $currentColumn++;
                                    endfor; ?>
                                    <?php if ($value[2]) : ?>
                                        <td class="pCenter p-0 d-none">
                                            <?= form_dropdown(
                                                "inspectionValue[$key][cycleKey]",
                                                $value[3],
                                                '',
                                                array(
                                                    'class' => 'input-h24 w-100 no_border' . ($key === 0 ? ' input-status bg-pink' : ''),
                                                    'disabled' => true
                                                )
                                            ) ?>
                                        </td>
                                    <?php else : ?>
                                        <td class="pCenter p-0 d-none">
                                            <?= form_input(array(
                                                'name' => "inspectionValue[$key][cycleKey]",
                                                '',
                                                'disabled' => true,
                                                'maxlength' => '100',
                                                'class' => 'input-h24 no_border' . ($key === 1 ? ' no-calendar-icon' : ' w-100'),
                                                'type' => ($key === 1) ? 'date' : 'text',
                                            )) ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endfor; ?>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead mb-0">入院を要する合併症</legend>
                <?= form_button(array('class' => 'bg-btn w-15', 'id' => '', 'name' => 'addLine', 'content' => '先頭に行追加')); ?>
                <table class="mt-1 table-scroll table-bordered">
                    <thead>
                        <tr>
                            <th class="w-5">No.</th>
                            <th class="w-15">合併症</th>
                            <th class="w-15">入院日</th>
                            <th class="w-15">退院日</th>
                            <th>コメント</th>
                            <th class="w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $length = count($complications['data']);
                        foreach ($complications['data'] as $key => $value) : $length--; ?>
                            <tr rowIndex="<?= $length ?>">
                                <td class="pCenter w-5"><?= $key + 1 ?></td>
                                <td class="d-none"><?= form_input(array('name' => "complications[$length][ordinal]", 'value' => $key + 1)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "complications[$length][shouldUpdate]", 'value' => '1')) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "complications[$length][originalType]", 'value' => $value->GAPPEI)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "complications[$length][originalDateOfHospitalization]", 'value' => $value->NYUIN_DATE)) ?></td>
                                <td class="p-0 w-15"><?= form_dropdown("complications[$length][type]", $complications['type'], $value->GAPPEI, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="w-15"><?= form_input(array('type' => 'date', 'name' => "complications[$length][dateOfHospitalization]", 'value' => datetimeToString($value->NYUIN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="w-15"><?= form_input(array('type' => 'date', 'name' => "complications[$length][dischargeDate]", 'value' => datetimeToString($value->TAIIN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <td><?= form_input(array('name' => "complications[$length][comment]", 'value' => $value->CMNT, 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                <td class="d-none"><?= form_input(array('name' => "complications[$length][isDeleted]", 'value' => $value->DEL_FLG)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="d-none" name="newRow">
                            <td class="pCenter w-5"></td>
                            <td class="d-none"><?= form_input(array('name' => "complications[rowIndex][ordinal]", 'value' => '', 'disabled' => true)) ?></td>
                            <td class="d-none"><?= form_input(array('name' => "complications[rowIndex][shouldUpdate]", 'value' => '0', 'disabled' => true)) ?></td>
                            <td class="p-0 w-15"><?= form_dropdown("complications[rowIndex][type]", $complications['type'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="w-15"><?= form_input(array('type' => 'date', 'name' => "complications[rowIndex][dateOfHospitalization]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="w-15"><?= form_input(array('type' => 'date', 'name' => "complications[rowIndex][dischargeDate]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td><?= form_input(array('name' => "complications[rowIndex][comment]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                            <td class="d-none"><?= form_input(array('name' => "complications[rowIndex][isDeleted]", 'value' => '0', 'disabled' => true)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>

            <?php if ($info->ZOKI_CODE == ORGAN_LUNG) : ?>
                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead mb-0">在宅酸素療法</legend>
                    <div class="row w-50 m-0 justify-content-between">
                        <div class="row w-45 m-0">
                            <p class="w-40">導入年月日</p>
                            <?= form_input(array('type' => 'date', 'name' => 'dateOfIntroduction', 'value' => datetimeToString($info->ZAITAKUSANSORYOHO_START_DATE, 'Y-m-d'), 'class' => 'input-h24 w-60')) ?>
                        </div>
                        <div class="row w-45 m-0">
                            <p class="w-40">離脱年月日</p>
                            <?= form_input(array('type' => 'date', 'name' => 'withdrawalDate', 'value' => datetimeToString($info->ZAITAKUSANSORYOHO_END_DATE, 'Y-m-d'), 'class' => 'input-h24 w-60')) ?>
                        </div>
                    </div>
                </fieldset>
            <?php endif; ?>
        </div>
    </div>
</div>