<?= form_open("PdfReport/printList", array("id" => "pdfListForm", "method" => "POST", "target" => "_blank", "display")) ?>
<input id="pdfListData" name="data" type="text" hidden>
<?= form_close() ?>

<?= form_open("PdfReport/printDetail", array("id" => "pdfDetailForm", "method" => "POST", "target" => "_blank", "display")) ?>
<input id="printDetail" name="data" type="text" hidden>
<?= form_close() ?>

<?= form_open("PdfReport/printEntry", array("id" => "pdfEntryForm", "method" => "POST", "target" => "_blank", "display")) ?>
<input id="printEntry" name="data" type="text" hidden>
<?= form_close() ?>

<?= form_open("CsvReport/downloadCsvbasic", array("id" => "csvListForm", "method" => "POST", "display")) ?>
<input id="csvListData" name="data" type="text" hidden>
<?= form_close() ?>

<?= form_open("CsvReport/downloadCsvAll", array("id" => "csvDetailForm", "method" => "POST", "display")) ?>
<input id="csvDetailOrgan" name="csvDetailOrgan" type="text" hidden>
<input id="csvDetailSimultaneousTransplantation" name="csvDetailSimultaneousTransplantation" type="text" hidden>
<?= form_close() ?>

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-recipient-tab" data-toggle="tab" href="#nav-recipient" role="tab" aria-controls="nav-recipient" aria-selected="true">レシピエント情報</a>
        <?php if ($userType == ACC_TYPE_CO) : ?>
            <a class="nav-item nav-link" id="nav-donor-tab" data-toggle="tab" href="#nav-donor" role="tab" aria-controls="nav-donor" aria-selected="false">ドナー情報</a>
        <?php endif ?>
    </div>
</nav>
<div class="tab-content border pt-2 px-2 pb-3" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-recipient" role="tabpanel" aria-labelledby="nav-recipient-tab">
        <?= form_open('ajax/recipientInfoSearch', array('id' => 'recipientForm')) ?>
        <div class="w-60">
            <fieldset class="w-100 fieldset-border p-2">
                <legend class="w-auto lead">移植臓器</legend>
                <ul class="row m-0 list-unstyled">
                    <?php foreach (ORGAN as $key => $value) : ?>
                        <li class="col-2 custom-control custom-checkbox checkbox-lg">
                            <?= form_checkbox(array('id' => 'organ' . $key, 'name' => 'organ[]', 'value' => $key, 'class' => 'organ custom-control-input', 'disabled' => true)) ?>
                            <?= form_label($value, '', array('for' => 'organ' . $key, 'class' => 'custom-control-label')) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
                <ul class="row m-0 list-unstyled">
                    <?php foreach (SIMULTANEOUS_TRANSPLANTATION as $key => $value) : ?>
                        <li class="col-2 custom-control custom-checkbox checkbox-lg">
                            <?= form_checkbox(array('id' => 'simultaneousTransplantation' . $key, 'name' => 'simultaneousTransplantation[]', 'value' => $value, 'class' => 'custom-control-input')) ?>
                            <?= form_label($key, '', array('for' => 'simultaneousTransplantation' . $key, 'class' => 'custom-control-label')) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </fieldset>
        </div>

        <div class="row m-0 mt-4">
            <div class="w-25 pr-2">
                <div class="row m-0">
                    <p class="w-40">登録者ID</p>
                    <?= form_input(array('id' => 'registrantID', 'name' => 'registrantID', 'value' => '', 'class' => 'input-h24 w-60', 'maxlength' => 7)) ?>
                </div>
                <div class="row m-0">
                    <p class="w-40 m-0">氏名</p>
                    <?= form_input(array('id' => 'fullName', 'name' => 'fullName', 'value' => '', 'class' => 'input-h24 w-60')) ?>
                </div>
                <div class="row mt-2 mr-0 d-flex justify-content-end">
                    <label class="radio-inline mr-3"><?= form_radio(array('id' => 'kanji', 'name' => 'charType', 'value' => '1', 'class' => 'mr-1', 'checked' => true)) ?>漢字</label>
                    <label class="radio-inline mr-3"><?= form_radio(array('id' => 'kana', 'name' => 'charType', 'value' => '2', 'class' => 'mr-1')) ?>カナ</label>
                </div>
            </div>
            <div class="w-35 pl-3">
                <div class="row m-0">
                    <p class="w-35">移植施設</p>
                    <div class="input-group w-65 h-25">
                        <?php if ($userType == ACC_TYPE_CO) : ?>
                            <?= form_dropdown(array('name' => 'transplant', 'id' => 'transplant', 'class' => 'input-h24 w-90'), $transplant) ?>
                            <div class="input-group-append d-flex justify-content-end">
                                <button id="searchIcon" class="btn btn-secondary h24px py-0 px-1" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        <?php else : ?>
                            <?php if ($institutionKubun == INSTITUTION_KUBUN_TRANSPLANT) : ?>
                                <?= form_dropdown(array('name' => 'transplant', 'id' => 'transplant', 'class' => 'input-h24', 'disabled' => true), $transplant) ?>
                            <?php else : ?>
                                <?= form_dropdown(array('name' => 'transplant', 'id' => 'transplant', 'class' => 'input-h24'), $transplant) ?>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row m-0">
                    <p class="w-50 text-nowrap">移植後経過情報管理施設</p>
                    <?php if ($userType == ACC_TYPE_TP && $institutionKubun == INSTITUTION_KUBUN_TRANSFER) : ?>
                        <?= form_dropdown(array('name' => 'postTransplant', 'id' => 'postTransplant', 'class' => 'input-h24 w-50', 'disabled' => true), $postTransplant) ?>
                    <?php else : ?>
                        <?= form_dropdown(array('name' => 'postTransplant', 'id' => 'postTransplant', 'class' => 'input-h24 w-50'), $postTransplant) ?>
                    <?php endif ?>
                </div>
            </div>
            <div class="w-40 pl-3">
                <div class="row m-0">
                    <p class="w-20">移植実施日</p>
                    <div class="row m-0 w-80 justify-content-between">
                        <?= form_input(array('type' => 'date', 'id' => 'transplantDateFrom', 'name' => 'transplantDate[from]', 'value' => '', 'class' => 'input-h24 w-45')) ?>
                        <p>～</p>
                        <?= form_input(array('type' => 'date', 'id' => 'transplantDateTo', 'name' => 'transplantDate[to]', 'value' => '', 'class' => 'input-h24 w-45')) ?>
                    </div>
                </div>
                <div class="row m-0">
                    <p class="w-20">報告期限日</p>
                    <div class="row m-0 w-80 justify-content-between">
                        <?= form_input(array('type' => 'date', 'id' => 'reportDeadlineDateFrom', 'name' => 'reportDeadlineDate[from]', 'value' => '', 'class' => 'input-h24 w-45')) ?>
                        <p>～</p>
                        <?= form_input(array('type' => 'date', 'id' => 'reportDeadlineDateTo', 'name' => 'reportDeadlineDate[to]', 'value' => '', 'class' => 'input-h24 w-45')) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <fieldset class="w-90 fieldset-border p-2">
                    <legend class="w-auto lead">臓器転帰</legend>
                    <ul class="m-0 list-unstyled d-flex justify-content-between">
                        <?php foreach ($organOutcome as $item) : ?>
                            <li class="custom-control custom-checkbox checkbox-lg">
                                <?= form_checkbox(array('id' => 'organOutcome' . $item->CODE, 'name' => 'organOutcome[]', 'value' => $item->CODE, 'class' => 'custom-control-input')) ?>
                                <?= form_label($item->VALUE, '', array('for' => 'organOutcome' . $item->CODE, 'class' => 'custom-control-label')) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </fieldset>
                <fieldset class="w-100 fieldset-border mt-2 p-2">
                    <legend class="w-auto lead">患者転帰</legend>
                    <ul class="w-90 m-0 list-unstyled d-flex justify-content-between">
                        <?php foreach ($patientOutcome as $item) : ?>
                            <li class="custom-control custom-checkbox checkbox-lg">
                                <?= form_checkbox(array('id' => 'patientOutcome' . $item->CODE, 'name' => 'patientOutcome[]', 'value' => $item->CODE, 'class' => 'custom-control-input')) ?>
                                <?= form_label($item->VALUE, '', array('for' => 'patientOutcome' . $item->CODE, 'class' => 'custom-control-label')) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="m-0 list-unstyled d-flex justify-content-between">
                        （
                        <?php foreach ($patientOutcomeDetails as $item) : ?>
                            <li class="custom-control custom-checkbox checkbox-lg">
                                <?= form_checkbox(array('id' => 'patientOutcomeDetails' . $item->CODE, 'name' => 'patientOutcomeDetails[]', 'value' => $item->CODE, 'class' => 'custom-control-input')) ?>
                                <?= form_label($item->VALUE, '', array('for' => 'patientOutcomeDetails' . $item->CODE, 'class' => 'custom-control-label')) ?>
                            </li>
                        <?php endforeach; ?>
                        )
                    </ul>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset class="w-100 fieldset-border p-2">
                    <legend class="w-auto lead">入力状況</legend>
                    <div class="row m-0">
                        <p class="w-40">チェック対象：</p>
                        <div class="w-60">
                            <ul class="list-unstyled row m-0">
                                <li class="w-50 p-0 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'inspectionItemCheckTarget', 'name' => 'checkTarget[inspectionItem]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                                    <?= form_label('検査項目', '', array('for' => 'inspectionItemCheckTarget', 'class' => 'custom-control-label')) ?>
                                </li>
                                <li class="w-50 p-0 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'livingConditionsCheckTarget', 'name' => 'checkTarget[livingConditions]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                                    <?= form_label('生活状況', '', array('for' => 'livingConditionsCheckTarget', 'class' => 'custom-control-label')) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row m-0">
                        <p class="w-40">入力状況：</p>
                        <div class="w-60">
                            <ul class="list-unstyled row m-0">
                                <li class="w-50 p-0 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'notEnteredInputStatus', 'name' => 'inputStatus[notEntered]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                                    <?= form_label('未入力', '', array('for' => 'notEnteredInputStatus', 'class' => 'custom-control-label')) ?>
                                </li>
                                <li class="w-50 p-0 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'doneInputStatus', 'name' => 'inputStatus[done]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                                    <?= form_label('完了', '', array('for' => 'doneInputStatus', 'class' => 'custom-control-label')) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row m-0">
                        <p class="w-40">対象経過期間：</p>
                        <div class="w-60">
                            <ul class="list-unstyled row m-0">
                                <li class="w-50 p-0 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'oneYearElapsedPeriod', 'name' => 'elapsedPeriod[lessOneYear]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                                    <?= form_label('１ヶ月～１年', '', array('for' => 'oneYearElapsedPeriod', 'class' => 'custom-control-label')) ?>
                                </li>
                                <li class="w-50 p-0 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'twoYearElapsedPeriod', 'name' => 'elapsedPeriod[overTwoYear]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                                    <?= form_label('２年以降', '', array('for' => 'twoYearElapsedPeriod', 'class' => 'custom-control-label')) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </fieldset>
                <div class="row mx-0 mt-4">
                    <p class="w-25">退院日設定有無</p>
                    <?= form_dropdown('dischargeDateSet', array('1' => '有', '0' => '無'), '', array('id' => 'dischargeDateSet', 'class' => 'input-h24 w-25')) ?>
                </div>
            </div>
        </div>
        <?= form_close() ?>
    </div>
