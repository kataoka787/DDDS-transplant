<div class="card mb-4 collapse-border" id="accordion4">
    <div class="collapse-header row m-0 d-flex justify-content-between">
        <p class="m-0">社会復帰状況</p>
        <a class="collapse-btn" data-toggle="collapse" href="#collapseFour">
            <i class="fa fa-minus-circle" aria-hidden="true"></i>
        </a>
    </div>
    <div id="collapseFour" class="collapse show" data-parent="#accordion4">
        <div class="card-body">
            <fieldset class="w-30 fieldset-border mb-2 p-2">
                <legend class="w-auto lead">退院</legend>
                <div class="row w-100 m-0">
                    <p class="w-40">退院年月日</p>
                    <?= form_input(array('type' => 'date', 'name' => 'dateOfDischarge', 'value' => datetimeToString($info->TAIIN_DATE, 'Y-m-d'), 'class' => 'input-h24 w-60')) ?>
                </div>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto m-0 lead">社会復帰状況</legend>
                <div class="row m-0">
                    <p class="w-10"></p>
                    <button type="button" class="bg-btn w-30 ml-1 mb-2" data-toggle="modal" data-target="#rehabilitationModal">社会復帰状況の入力方法について</button>
                </div>
                <div class="row m-0 d-flex justify-content-between">
                    <div class="row w-70 m-0">
                        <p class="w-15">社会復帰状況</p>
                        <?= form_dropdown('rehabilitationStatus', $rehabilitationStatus, $info->SYAKAIFUKKI, array('class' => 'input-h24 w-85')) ?>
                    </div>
                    <div class="row w-25 m-0">
                        <p class="w-40">社会復帰日</p>
                        <?= form_input(array('type' => 'date', 'name' => 'rehabilitationDate', 'value' => datetimeToString($info->SYAKAIFUKKI_DATE, 'Y-m-d'), 'class' => 'input-h24 w-60')) ?>
                    </div>
                </div>
                <div class="row w-70 m-0">
                    <p class="w-15">コメント</p>
                    <?= form_input(array('name' => 'rehabilitationComment', 'value' => $info->SYAKAIFUKKI_NAIYO, 'class' => 'input-h24 w-85')) ?>
                </div>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead mb-0">生活状況</legend>
                <div class="row m-0">
                    <?= form_button(array('class' => 'bg-btn w-15', 'id' => 'addLineLivingConditions', 'name' => '', 'content' => '先頭に行追加')); ?>
                    <div class="row w-85 m-0 d-flex justify-content-center">
                        <button type="button" class="bg-btn w-30" data-toggle="modal" data-target="#livingCondititonInputMethodModal">
                            生活状況の入力方法について
                        </button>
                    </div>
                </div>
                <table class="mt-1 table-scroll table-bordered">
                    <thead>
                        <tr>
                            <th class="w-5">No.</th>
                            <th class="w-15">記録日</th>
                            <th class="w-10">確認者</th>
                            <th class="w-10">報告者</th>
                            <th class="w-10">報告形式</th>
                            <th class="w-30">生活状況</th>
                            <th class="w-10">経過期間</th>
                            <th class="w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $length = count($livingConditions['data']);
                        foreach ($livingConditions['data'] as $key => $value) : $length--; ?>
                            <tr rowIndex="<?= $length ?>">
                                <td class="pCenter w-5"><?= $key + 1 ?></td>
                                <td class="d-none"><?= form_input(array('name' => "livingConditions[$length][ordinal]", 'value' => $key + 1)) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "livingConditions[$length][shouldUpdate]", 'value' => '1')) ?></td>
                                <td class="d-none"><?= form_input(array('name' => "livingConditions[$length][originalRecordingDate]", 'value' => $value->INPUT_DATE)) ?></td>
                                <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "livingConditions[$length][recordingDate]", 'value' => datetimeToString($value->INPUT_DATE, 'Y-m-d'), 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <?php if ($userType == ACC_TYPE_CO) : ?>
                                    <td class="pCenter w-10"><?= form_input(array('name' => "livingConditions[$length][confirmer]", 'value' => $value->KAKUNIN_USER_NAME, 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <?php else : ?>
                                    <td class="pCenter w-10"><?= $value->KAKUNIN_USER_NAME ?></td>
                                <?php endif; ?>
                                <td class="pCenter w-10"><?= form_input(array('name' => "livingConditions[$length][reporter]", 'value' => $value->REPORT_USER_NAME, 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="p-0 w-10"><?= form_dropdown("livingConditions[$length][reportForm]", $livingConditions['reportForm'], $value->REPORT_FORM, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="w-30"><?= form_input(array('name' => "livingConditions[$length][content]", 'value' => $value->LIVING_NAIYO, 'class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="p-0 w-10"><?= form_dropdown("livingConditions[$length][cycle]", addEmptySelect($cycle['list']), $value->CYCLE, array('class' => 'input-h24 w-100 no_border')) ?></td>
                                <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                                <td class="d-none"><?= form_input(array('name' => "livingConditions[$length][isDeleted]", 'value' => $value->DEL_FLG)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="d-none" name="newRow">
                            <td class="pCenter w-5"></td>
                            <td class="d-none"><?= form_input(array('name' => "livingConditions[rowIndex][ordinal]", 'value' => '', 'disabled' => true)) ?></td>
                            <td class="d-none"><?= form_input(array('name' => "livingConditions[rowIndex][shouldUpdate]", 'value' => '0', 'disabled' => true)) ?></td>
                            <td class="pCenter w-15"><?= form_input(array('type' => 'date', 'name' => "livingConditions[rowIndex][recordingDate]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="pCenter w-10"><?= form_input(array('name' => "livingConditions[rowIndex][confirmer]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="pCenter w-10"><?= form_input(array('name' => "livingConditions[rowIndex][reporter]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="p-0 w-10"><?= form_dropdown("livingConditions[rowIndex][reportForm]", $livingConditions['reportForm'], '', array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="w-30"><?= form_input(array('name' => "livingConditions[rowIndex][content]", 'value' => '', 'class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="p-0 w-10"><?= form_dropdown("livingConditions[rowIndex][cycle]", addEmptySelect($cycle['list']), $currentCycle, array('class' => 'input-h24 w-100 no_border', 'disabled' => true)) ?></td>
                            <td class="pCenter w-10"><?= form_button(array('class' => 'bg-btn w-60', 'id' => '', 'name' => 'deleteLine', 'content' => '削除')); ?></td>
                            <td class="d-none"><?= form_input(array('name' => "livingConditions[rowIndex][isDeleted]", 'value' => '0', 'disabled' => true)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</div>
<?= form_close() ?>

<?= form_open("PdfReport/printDetail", array("id" => "pdfDetailForm", "method" => "POST", "target" => "_blank", "display")) ?>
<input id="printDetail" name="data" type="text" hidden>
<?= form_close() ?>

<?= form_open("PdfReport/printEntry", array("id" => "pdfEntryForm", "method" => "POST", "target" => "_blank", "display")) ?>
<input id="printEntry" name="data" type="text" hidden>
<?= form_close() ?>

<div class="row m-0">
    <button id="saveBtn" class="bg-btn w-15">保存</button>
    <div class="row w-70 m-0 d-flex justify-content-center">
        <button type="button" class="bg-btn w-20 mx-2" onclick="pdfPrintEntry()">
            移植後経過情報<br />記入用紙印刷
        </button>
        <button type="button" class="bg-btn w-20 mx-2" onclick="pdfPrintDetail()">
            移植後経過情報<br />印刷
        </button>
    </div>
    <?= form_button(array('class' => 'bg-btn w-15', 'id' => 'backBtn', 'name' => '', 'content' => '戻る')); ?>
</div>

<div class="modal fade" id="livingCondititonInputMethodModal" tabindex="-1" role="dialog" aria-labelledby="livingCondititonInputMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-light">
                <h5 class="modal-title" id="livingCondititonInputMethodModalLabel">生活状況の入力方法について</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-justify">
                移植後の身体的な変化や活動範囲の変化、小児の場合は発達に関すること、臓器の機能に関することをご記載ください。
                <br>
                退院、復学や復職、入園や入学・進学や就職等、生活に変化があった場合にはレシピエント様のご様子や言動などを教えてください。
                <br>
                また、ドナーのご家族やご家族に対しての思いを話されることがあればご記載ください。
                <br />
                <br />
                <?php
                switch ($info->ZOKI_CODE) {
                    case ORGAN_HEART:
                        echo "<p>心臓移植に関することとして、心臓の拍動に関することや補助人工心臓を離脱に関することなどをご記載ください。</p>";
                        break;
                    case ORGAN_LUNG:
                        echo "<p>肺移植に関することとして、人工呼吸器離脱や酸素離脱に関することなど呼吸に関するご本人の様子をご記載ください。</p>";
                        break;
                    case ORGAN_LIVER:
                        echo "<p>肝臓移植に関することとして、黄疸や倦怠感の改善など、肝機能改善に伴うご本人の様子をご記載ください。</p>";
                        break;
                    case ORGAN_KIDNEY:
                        echo "<p>腎臓移植に関することとして、透析の離脱や排尿に関することなど、ご本人の様子をご記載ください。</p>";
                        break;
                    case ORGAN_PANCREAS:
                        echo "<p>膵臓移植に関することとして、インスリン離脱、低血糖症状の改善などについて、ご本人の様子をご記載ください。</p>";
                        break;
                    case ORGAN_SMALL_INTENSTINE:
                        echo "<p>小腸移植に関することとして、食事開始など、ご本人の様子をご記載ください。</p>";
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rehabilitationModal" tabindex="-1" role="dialog" aria-labelledby="rehabilitationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-light">
                <h5 class="modal-title" id="rehabilitationModalLabel">社会復帰状況の入力方法について</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-justify">
                社会復帰とは、発病前の活動状況に戻ったことが把握できる日（発病前より仕事に従事もしくは学校に通学していた方は、仕事への復帰や学校への復学を確認できた日）をご記載ください。
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="systemMessageModal" tabindex="-1" role="dialog" aria-labelledby="systemMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div id="systemMessageModalHeader" class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="systemMessageModalLabel">システムメッセージ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-justify" id="systemMessageModalBody"></div>
        </div>
    </div>
</div>