<div class="card mb-4 collapse-border" id="accordion2">
    <div class="collapse-header row m-0 d-flex justify-content-between">
        <p class="m-0">免疫・拒絶反応</p>
        <a class="collapse-btn" data-toggle="collapse" href="#collapseTwo">
            <i class="fa fa-minus-circle" aria-hidden="true"></i>
        </a>
    </div>
    <div id="collapseTwo" class="collapse show" data-parent="#accordion2">
        <div class="card-body">
            <?php if ($info->ZOKI_CODE == ORGAN_KIDNEY) : ?>
                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead mb-0">透析</legend>
                    <div class="row m-0 justify-content-between">
                        <div class="w-35">
                            <div class="row m-0 w-70 justify-content-around">
                                <?php foreach ($dialysisWithdrawal as $key => $value) : ?>
                                    <label class="radio-inline"><?= form_radio(array('name' => 'dialysisWithdrawal', 'class' => 'mr-1'), $key, strval($key) === $info->TOSEKIRIDATU) ?><?= $value ?></label>
                                <?php endforeach; ?>
                            </div>
                            <div class="row m-0 justify-content-around">
                                <p class="w-30">最終透析日</p>
                                <?= form_input(array('type' => 'date', 'name' => 'finalDialysisDay', 'value' => datetimeToString($info->TOSEKI_LAST_DATE, 'Y-m-d'), 'class' => 'input-h24 w-50')) ?>
                            </div>
                        </div>
                        <div class="w-60">
                            <fieldset class="fieldset-border mb-2 p-2">
                                <legend class="w-auto lead m-0">離脱不能原因</legend>
                                <div class="row m-0 justify-content-between">
                                    <?php foreach ($causesOfDialysisFailure as $key => $value) : ?>
                                        <label class="radio-inline mb-0"><?= form_radio(array('name' => 'causesOfDialysisFailure', 'class' => 'mr-1'), $key, strval($key) === $info->TOSEKIRIDATU_FUNOGENIN) ?><?= $value ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </fieldset>
            <?php elseif ($info->ZOKI_CODE == ORGAN_PANCREAS) : ?>
                <div class="row m-0 justify-content-between">
                    <fieldset class="w-49 fieldset-border ml mb-2 p-2">
                        <legend class="w-auto lead m-0">透析</legend>
                        <div class="row m-0">
                            <div class="w-45">
                                <div class="row m-0 w-70 justify-content-between">
                                    <?php foreach ($dialysisWithdrawal as $key => $value) : ?>
                                        <label class="radio-inline mb-0"><?= form_radio(array('name' => 'dialysisWithdrawal', 'class' => 'mr-1'), $key, strval($key) === $info->TOSEKIRIDATU) ?><?= $value ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="row m-0 w-55">
                                <p class="w-40">最終透析日</p>
                                <?= form_input(array('type' => 'date', 'name' => 'finalDialysisDay', 'value' => datetimeToString($info->TOSEKI_LAST_DATE, 'Y-m-d'), 'class' => 'input-h24 w-60')) ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="w-49 fieldset-border mb-2 p-2">
                        <legend class="w-auto lead m-0">インスリン治療</legend>
                        <div class="row m-0">
                            <div class="w-45">
                                <div class="row m-0 w-70 justify-content-between">
                                    <?php foreach ($insulinTreatment as $key => $value) : ?>
                                        <label class="radio-inline mb-0"><?= form_radio(array('name' => 'insulinTreatment', 'class' => 'mr-1'), $key, strval($key) === $info->INSULIN_FLG) ?><?= $value ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="row m-0 w-55">
                                <p class="w-40">最終投与日</p>
                                <?= form_input(array('type' => 'date', 'name' => 'lastAdministrationDate', 'value' => datetimeToString($info->INSULIN_LAST_DATE, 'Y-m-d'), 'class' => 'input-h24 w-60')) ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <?php endif ?>

            <p class="m-0 text-right">*：製造中止</p>
            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead">免疫抑制剤（導入）</legend>
                <div class="row m-0">
                    <ul class="w-80 m-0 pr-4 list-unstyled d-flex justify-content-between">
                        <?php foreach ($immunosuppressant['introduction'] as $key => $value) : ?>
                            <li class="custom-control custom-checkbox checkbox-lg">
                                <?= form_checkbox(array('id' => 'immunosuppressantIntroduction' . $key, 'name' => 'immunosuppressant[introduction][]', 'value' => $key, 'class' => 'custom-control-input'), '', $value) ?>
                                <?= form_label($key, '', array('for' => 'immunosuppressantIntroduction' . $key, 'class' => 'custom-control-label')) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="row w-20 m-0">
                        <p class="w-30">その他</p>
                        <?= form_input(array('name' => 'immunosuppressantOtherIntro', 'value' => $info->DONYU_ETC, 'class' => 'input-h24 w-70')) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead">免疫抑制剤（維持）</legend>
                <div class="row m-0">
                    <div class="w-80">
                        <ul class="w-60 m-0 pr-4 list-unstyled d-flex justify-content-between">
                            <?php foreach ($immunosuppressant['maintenance'] as $key => $value) : ?>
                                <li class="custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'immunosuppressantMaintenance' . $key, 'name' => 'immunosuppressant[maintenance][]', 'value' => $key, 'class' => 'custom-control-input'), '', $value) ?>
                                    <?= form_label($key, '', array('for' => 'immunosuppressantMaintenance' . $key, 'class' => 'custom-control-label')) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="row w-20 m-0">
                        <p class="w-30">その他</p>
                        <?= form_input(array('name' => 'immunosuppressantOtherMaintenance', 'value' => $info->IJI_ETC, 'class' => 'input-h24 w-70')) ?>
                    </div>
                </div>
            </fieldset>

            <?php if ($info->ZOKI_CODE == ORGAN_HEART) : ?>
                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead mb-0">拒絶反応</legend>
                    <?= form_button(array('class' => 'bg-btn w-15', 'id' => '', 'name' => 'addLine', 'content' => '先頭に行追加')); ?>
                    <table class="mt-1 table-scroll table-bordered">
                        <thead>
                            <tr>
                                <th class="w-5">No.</th>
                                <th class="w-15">診断日</th>
                                <th class="w-40">治療手段</th>
                                <th>治療効果</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $length = count($rejection['data']);
                            foreach ($rejection['data'] as $key => $value) : $length--; ?>
                                <tr rowIndex="<?= $length ?>">
                                    <td class="pCenter w-5"><?= $key + 1 ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][ordinal]", 'value' => $key + 1)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][shouldUpdate]", 'value' => '1')) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][originalDiagnosisDate]", 'value' => $value->SINDAN_DATE)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][type]", 'value' => $value->REJECTION_TYPE, 'readonly' => true)) ?></td>
                                    <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[$length][diagnosisDate]", 'value' => datetimeToString($value->SINDAN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0 w-40"><?= form_dropdown("rejection[$length][treatmentMethod]", $treatmentMethod, $value->TIRYOU_SYUDAN, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="pCenter">
                                        <?php foreach ($therapeuticEffect as $item) : ?>
                                            <label class="radio-inline mx-2 mb-0">
                                                <?= form_radio(
                                                    array('name' => "rejection[$length][therapeuticEffect]", 'class' => 'mr-1'),
                                                    $item->CODE,
                                                    $item->CODE === $value->TIRYOU_KOKA
                                                ) ?>
                                                <?= $item->VALUE ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][isDeleted]", 'value' => $value->DEL_FLG)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="d-none" name="newRow">
                                <td class="pCenter w-5"></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][ordinal]", 'value' => '', 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][shouldUpdate]", 'value' => '0', 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][type]", 'value' => REJECTION_COMMON, 'readonly' => true, 'disabled' => true)) ?></td>
                                <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[rowIndex][diagnosisDate]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0 w-40"><?= form_dropdown("rejection[rowIndex][treatmentMethod]", $treatmentMethod, '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="pCenter">
                                    <?php foreach ($therapeuticEffect as $item) : ?>
                                        <label class="radio-inline mx-2 mb-0">
                                            <?= form_radio(
                                                array('name' => "rejection[rowIndex][therapeuticEffect]", 'class' => 'mr-1', 'disabled' => true),
                                                $item->CODE
                                            ) ?>
                                            <?= $item->VALUE ?>
                                        </label>
                                    <?php endforeach; ?>
                                </td>
                                <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][isDeleted]", 'value' => '0', 'disabled' => true)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            <?php elseif ($info->ZOKI_CODE == ORGAN_LUNG) : ?>
                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead mb-0">急性拒絶反応</legend>
                    <?= form_button(array('class' => 'bg-btn w-15', 'id' => '', 'name' => 'addLine', 'content' => '先頭に行追加')); ?>
                    <table class="mt-1 table-scroll table-bordered">
                        <thead>
                            <tr>
                                <th class="w-5">No.</th>
                                <th class="w-15">診断日</th>
                                <th>GradeA</th>
                                <th>GradeB</th>
                                <th>GradeC</th>
                                <th>GradeD</th>
                                <th class="w-30">治療効果</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $acute = REJECTION_ACUTE;
                            $length = count($rejection['data'][$acute]); ?>
                            <?php foreach ($rejection['data'][$acute] as $key => $value) : $length--; ?>
                                <tr rowIndex="<?= $length ?>">
                                    <td class="pCenter w-5"><?= $key + 1 ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$acute][$length][ordinal]", 'value' => $acute . '_' . ($key + 1), 'rejectionType' => $acute)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$acute][$length][shouldUpdate]", 'value' => '1')) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$acute][$length][originalDiagnosisDate]", 'value' => $value->SINDAN_DATE)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$acute][$length][type]", 'value' => $value->REJECTION_TYPE, 'readonly' => true)) ?></td>
                                    <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[$acute][$length][diagnosisDate]", 'value' => datetimeToString($value->SINDAN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$acute][$length][gradeA]", $grade['a'], $value->GRADEA, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$acute][$length][gradeB]", $grade['b'], $value->GRADEB, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$acute][$length][gradeC]", $grade['c'], $value->GRADEC, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$acute][$length][gradeD]", $grade['d'], $value->GRADED, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0 w-30"><?= form_dropdown("rejection[$acute][$length][therapeuticEffect]", $therapeuticEffect, $value->TIRYOU_KOKA, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$acute][$length][isDeleted]", 'value' => $value->DEL_FLG)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="d-none" name="newRow">
                                <td class="pCenter w-5"></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$acute][rowIndex][ordinal]", 'value' => '', 'rejectionType' => $acute, 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$acute][rowIndex][shouldUpdate]", 'value' => '0', 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$acute][rowIndex][type]", 'value' => $acute, 'readonly' => true, 'disabled' => true)) ?></td>
                                <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[$acute][rowIndex][diagnosisDate]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[$acute][rowIndex][gradeA]", $grade['a'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[$acute][rowIndex][gradeB]", $grade['b'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[$acute][rowIndex][gradeC]", $grade['c'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[$acute][rowIndex][gradeD]", $grade['d'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0 w-30"><?= form_dropdown("rejection[$acute][rowIndex][therapeuticEffect]", $therapeuticEffect, '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$acute][rowIndex][isDeleted]", 'value' => '0', 'disabled' => true)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead mb-0">慢性拒絶反応</legend>
                    <?= form_button(array('class' => 'bg-btn w-15', 'id' => '', 'name' => 'addLine', 'content' => '先頭に行追加')); ?>
                    <table class="mt-1 table-scroll table-bordered">
                        <thead>
                            <tr>
                                <th class="w-5">No.</th>
                                <th class="w-15">診断日</th>
                                <th>Stage</th>
                                <th>a/b</th>
                                <th class="w-40">治療効果</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $chronic = REJECTION_CHRONIC;
                            $length = count($rejection['data'][$chronic]); ?>
                            <?php foreach ($rejection['data'][$chronic] as $key => $value) : $length--; ?>
                                <tr rowIndex="<?= $length ?>">
                                    <td class="pCenter w-5"><?= $key + 1 ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][$length][ordinal]", 'value' => $chronic . '_' . ($key + 1), 'rejectionType' => $chronic)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][$length][shouldUpdate]", 'value' => '1')) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][$length][originalDiagnosisDate]", 'value' => $value->SINDAN_DATE)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][$length][type]", 'value' => $value->REJECTION_TYPE, 'readonly' => true)) ?></td>
                                    <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[$chronic][$length][diagnosisDate]", 'value' => datetimeToString($value->SINDAN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$chronic][$length][stage]", $stageAb['stage'], $value->STAGE, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$chronic][$length][ab]", $stageAb['ab'], $value->A_B, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0 w-40"><?= form_dropdown("rejection[$chronic][$length][therapeuticEffect]", $therapeuticEffect, $value->TIRYOU_KOKA, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][$length][isDeleted]", 'value' => $value->DEL_FLG)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="d-none" name="newRow">
                                <td class="pCenter w-5"></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][rowIndex][ordinal]", 'value' => '', 'rejectionType' => $chronic, 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][rowIndex][shouldUpdate]", 'value' => '0', 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][rowIndex][type]", 'value' => $chronic, 'readonly' => true, 'disabled' => true)) ?></td>
                                <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[$chronic][rowIndex][diagnosisDate]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[$chronic][rowIndex][stage]", $stageAb['stage'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[$chronic][rowIndex][ab]", $stageAb['ab'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0 w-40"><?= form_dropdown("rejection[$chronic][rowIndex][therapeuticEffect]", $therapeuticEffect, '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[$chronic][rowIndex][isDeleted]", 'value' => '0', 'disabled' => true)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            <?php else : ?>
                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead mb-0">拒絶反応</legend>
                    <?= form_button(array('class' => 'bg-btn w-15', 'id' => '', 'name' => 'addLine', 'content' => '先頭に行追加')); ?>
                    <table class="mt-1 table-scroll table-bordered">
                        <thead>
                            <tr>
                                <th class="w-5">No.</th>
                                <th class="w-15">診断日</th>
                                <th>治療効果</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $length = count($rejection['data']);
                            foreach ($rejection['data'] as $key => $value) : $length--; ?>
                                <tr rowIndex="<?= $length ?>">
                                    <td class="pCenter w-5"><?= $key + 1 ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][ordinal]", 'value' => $key + 1)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][shouldUpdate]", 'value' => '1')) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][originalDiagnosisDate]", 'value' => $value->SINDAN_DATE)) ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][type]", 'value' => REJECTION_COMMON, 'readonly' => true)) ?></td>
                                    <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[$length][diagnosisDate]", 'value' => datetimeToString($value->SINDAN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="p-0"><?= form_dropdown("rejection[$length][therapeuticEffect]", $therapeuticEffect, $value->TIRYOU_KOKA, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                    <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                    <td class="d-none"><?= form_input(array('name' => "rejection[$length][isDeleted]", 'value' => $value->DEL_FLG)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="d-none" name="newRow">
                                <td class="pCenter w-5"></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][ordinal]", 'value' => '', 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][shouldUpdate]", 'value' => '0', 'disabled' => true)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][type]", 'value' => REJECTION_COMMON, 'readonly' => true, 'disabled' => true)) ?></td>
                                <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "rejection[rowIndex][diagnosisDate]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="p-0"><?= form_dropdown("rejection[rowIndex][therapeuticEffect]", $therapeuticEffect, '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                                <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                <td class="d-none"><?= form_input(array('name' => "rejection[rowIndex][isDeleted]", 'value' => '0', 'disabled' => true)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            <?php endif ?>
        </div>
    </div>
</div>