<?= form_open("detail/save", array("id" => "detailForm", "method" => "POST", "display")) ?>
<?= form_hidden('updateToken', $updateToken) ?>

<div class="row m-0">
    <div class="row m-0 w-15">
        <p class="w-50">移植臓器</p>
        <?= form_input(array('name' => 'organ', 'value' => $info->organ, 'class' => 'input-h24 w-50', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-20 d-flex justify-content-end">
        <p class="w40 mr-1 text-right">登録者ID</p>
        <?= form_input(array('name' => 'RECIPIENT_ID', 'value' => str_pad($info->RECIPIENT_ID, 7, 0, STR_PAD_LEFT), 'class' => 'input-h24 w-55', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-20 d-flex justify-content-end">
        <p class="w30 mr-1 text-right">氏名</p>
        <?= form_input(array('name' => 'fullName', 'value' => $info->kanji_name, 'class' => 'input-h24 w-65', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-10">
        <p class="w-55 mr-1 text-right">性別</p>
        <?= form_input(array('name' => 'sex', 'value' => $info->sex, 'class' => 'input-h24 w-40', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-20">
        <p class="w-45 mr-1 text-right">生年月日</p>
        <?= form_input(array('name' => 'birthday', 'value' => $info->birthday, 'class' => 'input-h24 w-50', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-15 justify-content-around">
        <p class="w-45 mr-1 text-right">現在年齢</p>
        <?= form_input(array('name' => 'age', 'value' => $info->age, 'class' => 'input-h24 w-35', 'disabled' => true)) ?>
        <p class="w-15 text-right">歳</p>
    </div>
</div>
<div class="row m-0">
    <div class="row m-0 w-20 justify-content-around">
        <p class="w-50">移植年月日</p>
        <?= form_input(array('name' => 'transplantDate', 'value' => $info->isyoku_date, 'class' => 'input-h24 w-50', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-45 justify-content-center">
        <p class="w30 ml-1">移植実施施設</p>
        <?= form_input(array('name' => 'transplant_name', 'value' => $info->transplant_name, 'class' => 'input-h24 w-70 ml-2', 'disabled' => true)) ?>
    </div>
    <div class="row m-0 w-15 justify-content-around">
        <p class="w-45 mr-1 text-right">移植回数</p>
        <?= form_input(array('name' => 'numberOfTransplants', 'value' => $info->ISYOKU_CNT, 'class' => 'input-h24 w-35', 'disabled' => true)) ?>
        <p class="w-15 text-right">回</p>
    </div>
    <div class="row m-0 w-20 justify-content-around">
        <p class="w-45 mr-1 text-right">移植時年齢</p>
        <?= form_input(array('name' => 'ageAtTransplant', 'value' => $info->ISYOKU_AGE, 'class' => 'input-h24 w-40', 'disabled' => true)) ?>
        <p class="w-10 text-right">歳</p>
    </div>
</div>

<?php if (!($info->ZOKI_CODE == ORGAN_LUNG || $info->ZOKI_CODE == ORGAN_LIVER)) : ?>
    <div class="row m-0 w-40">
        <p class="w-20">原疾患</p>
        <?= form_input(array('name' => 'originalDisease', 'value' => $info->gensikkan, 'class' => 'input-h24 w-80', 'disabled' => true)) ?>
    </div>
<?php endif ?>

<?php if ($info->ZOKI_CODE == ORGAN_LUNG || $info->ZOKI_CODE == ORGAN_LIVER) : ?>
    <div class="row m-0">
        <div class="row m-0 w-40">
            <p class="w-30">原疾患(大分類)</p>
            <?= form_input(array('name' => 'originalDiseaseMajor)', 'value' => $info->GENSIKKAN_H, 'class' => 'input-h24 w-70', 'disabled' => true)) ?>
        </div>
        <div class="row m-0 w-40 ml-4">
            <p class="w-30">原疾患(小分類)</p>
            <?= form_input(array('name' => 'originalDiseaseSub)', 'value' => $info->GENSIKKAN_L, 'class' => 'input-h24 w-70', 'disabled' => true)) ?>
        </div>
    </div>
<?php endif ?>

<div class="row m-0">
    <div class="row m-0 w-40">
        <p class="w-20">コメント</p>
        <?= form_input(array('name' => 'comment', 'value' => $info->GENSIKKAN_CMNT, 'class' => 'input-h24 w-80', 'disabled' => true)) ?>
    </div>
    <?php if ($info->ZOKI_CODE == ORGAN_LUNG || $info->ZOKI_CODE == ORGAN_LIVER || $info->ZOKI_CODE == ORGAN_PANCREAS) : ?>
        <div class="row m-0 w-40 ml-4">
            <p class="w-30">移植内容</p>
            <?= form_input(array('name' => 'portingContent', 'value' => $info->isyoku_naiyo, 'class' => 'input-h24 w-70', 'disabled' => true)) ?>
        </div>
    <?php endif ?>
</div>

<div class="card mb-4 collapse-border" id="accordion">
    <div class="collapse-header row m-0 d-flex justify-content-between">
        <p class="m-0">基本情報</p>
        <a class="collapse-btn" data-toggle="collapse" href="#collapseOne">
            <i class="fa fa-minus-circle" aria-hidden="true"></i>
        </a>
    </div>

    <div id="collapseOne" class="collapse show" data-parent="#accordion">
        <div class="card-body">
            <?php if ($userType == ACC_TYPE_CO) : ?>
                <fieldset class="w-100 fieldset-border mb-2 p-2">
                    <legend class="w-auto lead">ドナー情報</legend>
                    <div class="row m-0 d-flex justify-content-between">
                        <div class="row m-0 w-25">
                            <p class="w-35">ドナーID</p>
                            <?= form_input(array('name' => 'DONOR_ID', 'value' => isset($info->DONOR_ID) ? str_pad($info->DONOR_ID, 7, 0, STR_PAD_LEFT) : '', 'class' => 'input-h24 w-65', 'disabled' => true)) ?>
                        </div>
                        <div class="row m-0 w-25">
                            <p class="w-45">ドナー発生地</p>
                            <?= form_input(array('name' => 'donorOrigin', 'value' => $info->DONOR_TODOFUKEN, 'class' => 'input-h24 w-55', 'disabled' => true)) ?>
                        </div>
                        <div class="row m-0 w-45">
                            <p class="w-20">提供施設</p>
                            <?= form_input(array('name' => 'providedFacilities', 'value' => $info->TEIKYOSISETU_NAME, 'class' => 'input-h24 w-80', 'disabled' => true)) ?>
                        </div>
                    </div>
                    <div class="row m-0">
                        <div class="row m-0 w-25">
                            <p class="w-35">氏名</p>
                            <?= form_input(array('name' => 'donorFullName', 'value' => $info->donor_kanji_name, 'class' => 'input-h24 w-65', 'disabled' => true)) ?>
                        </div>
                        <div class="row m-0 w-10">
                            <p class="w-55 ml-2 text-right">性別</p>
                            <?= form_input(array('name' => 'donorSex', 'value' => $info->donor_sex, 'class' => 'input-h24 w-30 ml-1', 'disabled' => true)) ?>
                        </div>
                        <div class="row m-0 w-20">
                            <p class="w-45 mx-1 text-right">生年月日</p>
                            <?= form_input(array('name' => 'donorBirthday', 'value' => $info->donor_birthday, 'class' => 'input-h24 w-50', 'disabled' => true)) ?>
                        </div>
                        <div class="row ml-4 w-10">
                            <?= form_input(array('name' => 'donorAge', 'value' => $info->donor_age, 'class' => 'input-h24 w-40', 'disabled' => true)) ?>
                            <p class="w-50 ml-1">歳</p>
                        </div>
                    </div>
                </fieldset>
            <?php endif; ?>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead">移植手術情報</legend>
                <div class="row m-0 justify-content-between">
                    <div class="row col-md-4 justify-content-between">
                        <p class="w-40">手術開始日時</p>
                        <?= form_input(array('name' => 'startDayCommencementOfSurgery', 'value' => datetimeToString($info->SYUJUTU_START_DATETIME), 'class' => 'input-h24 w-30', 'disabled' => true)) ?>
                        <?= form_input(array('name' => 'startTimeCommencementOfSurgery', 'value' => datetimeToString($info->SYUJUTU_START_DATETIME, 'H:i:s'), 'class' => 'input-h24 w-25', 'disabled' => true)) ?>
                    </div>
                    <div class="row col-md-4 justify-content-between">
                        <p class="w-40">血流再開日時</p>
                        <?= form_input(array('name' => 'DayResumptionOfBloodFlow', 'value' => datetimeToString($info->KETURYUSAIKAI_DATETIME), 'class' => 'input-h24 w-30', 'disabled' => true)) ?>
                        <?= form_input(array('name' => 'TimeResumptionOfBloodFlow', 'value' => datetimeToString($info->KETURYUSAIKAI_DATETIME, 'H:i:s'), 'class' => 'input-h24 w-25', 'disabled' => true)) ?>
                    </div>
                    <div class="row col-md-4 justify-content-between">
                        <p class="w-40">手術終了日時</p>
                        <?= form_input(array('name' => 'endDayCommencementOfSurgery', 'value' => datetimeToString($info->SYUJUTU_END_DATETIME), 'class' => 'input-h24 w-30', 'disabled' => true)) ?>
                        <?= form_input(array('name' => 'endDayCommencementOfSurgery', 'value' => datetimeToString($info->SYUJUTU_END_DATETIME, 'H:i:s'), 'class' => 'input-h24 w-25', 'disabled' => true)) ?>
                    </div>
                </div>
                <div class="row m-0 justify-content-between">
                    <div class="row col-md-4 justify-content-between">
                        <p class="w-40">温阻血時間</p>
                        <?= form_input(array('name' => 'warmIschemiaTime', 'value' => $info->ONSOKETU_MINUTE, 'class' => 'input-h24 w-50', 'disabled' => true)) ?>
                        <p class="w-5">分</p>
                    </div>
                    <div class="row col-md-4 justify-content-between">
                        <p class="w-40">全阻血時間</p>
                        <?= form_input(array('name' => 'totalBlockTime', 'value' => $info->ZENSOKETU_HOUR, 'class' => 'input-h24 w-15', 'disabled' => true)) ?>
                        <p class="w-15 pCenter">時間</p>
                        <?= form_input(array('name' => 'time', 'value' => $info->ZENSOKETU_MINUTE, 'class' => 'input-h24 w-15', 'disabled' => true)) ?>
                        <p class="w-5 ml-1">分</p>
                    </div>
                    <div class="row col-md-4"></div>
                </div>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto m-0 lead">移植後経過情報管理施設</legend>
                <div class="row m-0">
                    <div class="row m-0 w-60 d-flex align-content-center">
                        <?php if ($info->ZOKI_CODE == ORGAN_HEART) : ?>
                            <div class="row m-0 mb-2 w-100">
                                <p class="w-40"></p>
                                <?= form_input(array('value' => $info->ishokugo_keikajyouhou_sisetu_kbn, 'class' => 'input-h24 w-30', 'disabled' => true)) ?>
                            </div>
                        <?php endif ?>
                        <div class="row m-0 w-100">
                            <p class="w-40 m-0">移植後経過情報管理施設</p>
                            <div class="input-group w-60 h-25">
                                <?= form_dropdown(
                                    array('name' => 'postTransplant', 'id' => 'postTransplant', 'class' => 'input-h24 w-85'),
                                    $postTransplant,
                                    $info->ISHOKUGO_KEIKAJYOUHOU_SISETU_CD
                                ) ?>
                                <?php if ($userType == ACC_TYPE_CO) : ?>
                                    <div class="input-group-append w-15">
                                        <button id="searchIcon" class="btn btn-secondary h24px py-0 px-1" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="m-0 w-40 d-flex justify-content-end">
                        <div>
                            <a href="<?= base_url() . "/detail/downloadApplicationForm" ?>" target="_blank" type="button" class="bg-btn w-100 text-center a-button">
                                施設変更の際は申請書を<br />ダウンロードしJOT迄申請して下さい。
                            </a>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead">臓器の転帰</legend>
                <div class="row m-0">
                    <div class="row m-0 w-25 justify-content-between">
                        <?php if ($userType == ACC_TYPE_TP && $info->ZOKI_TENKI !== ORGAN_OUTCOME_ENGRAFTMENT_CODE) : ?>
                            <?php foreach ($organOutcome as $item) : ?>
                                <label class="radio-inline mb-0"><?= form_radio(array('class' => 'mr-1', 'disabled' => true), '', $item->CODE === $info->ZOKI_TENKI) ?><?= $item->VALUE ?></label>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <?php foreach ($organOutcome as $item) : ?>
                                <label class="radio-inline mb-0"><?= form_radio(array('name' => 'organOutcome', 'class' => 'mr-1'), $item->CODE, $item->CODE === $info->ZOKI_TENKI) ?><?= $item->VALUE ?></label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="row m-0 w-30">
                        <p class="w-30 text-right">廃絶日</p>
                        <?= form_input(array('type' => 'date', 'name' => 'abolitionDate', 'value' => datetimeToString($info->KINOHAIZETU_DATE, 'Y-m-d'), 'class' => 'input-h24 w-70')) ?>
                    </div>
                    <div class="row m-0 w-45">
                        <p class="w-25 pr-2 text-right">廃絶原因</p>
                        <?= form_dropdown('causeOfAbolition', $causeOfAbolition, $info->ZOKI_TENKI_GENIN, array('class' => 'input-h24 w-75')) ?>
                    </div>
                </div>
                <div class="row m-0 w-100">
                    <p class="w-10">コメント</p>
                    <?= form_input(array('name' => 'comment', 'value' => $info->ZOKI_TENKI_CMNT, 'class' => 'input-h24 w-90')) ?>
                </div>
            </fieldset>

            <fieldset class="w-100 fieldset-border mb-2 p-2">
                <legend class="w-auto lead">患者の転帰</legend>
                <div class="row m-0">
                    <div class="row m-0 w-25 justify-content-between">
                        <?php if ($userType == ACC_TYPE_TP && $info->RECIPIENT_TENKI !== PATIENT_OUTCOME_CODE['SURVIVE']) : ?>
                            <?php foreach ($patientOutcome as $item) : ?>
                                <label class="radio-inline mb-0"><?= form_radio(array('class' => 'mr-1', 'disabled' => true), '', $item->CODE === $info->RECIPIENT_TENKI) ?><?= $item->VALUE ?></label>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <?php foreach ($patientOutcome as $item) : ?>
                                <label class="radio-inline mb-0"><?= form_radio(array('name' => 'patientOutcome', 'class' => 'mr-1'), $item->CODE, $item->CODE === $info->RECIPIENT_TENKI) ?><?= $item->VALUE ?></label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="row m-0 w-30">
                        <p class="w-30 mb-0 text-right">死亡日</p>
                        <?= form_input(array('type' => 'date', 'name' => 'dayOfDeath', 'value' => datetimeToString($info->SIBO_DATE, 'Y-m-d'), 'class' => 'input-h24 w-70')) ?>
                    </div>
                    <div class="row m-0 w-45 d-flex justify-content-between">
                        <p class="w-15 mb-0 text-right">死因</p>
                        <?= form_dropdown(array('id' => 'causeOfDeathMajor', 'name' => 'causeOfDeath[major]'), $causeOfDeath['major'], $info->SIIN_H, array('class' => 'input-h24 w-40 ml-2')) ?>
                        <?= form_dropdown(array('id' => 'causeOfDeathSubclass', 'name' => 'causeOfDeath[subclass]'), $causeOfDeath['subclass'], $info->SIIN_L, array('class' => 'input-h24 w-40 ml-2')) ?>
                    </div>
                </div>

                <?php if ($info->ZOKI_CODE == ORGAN_HEART) : ?>
                    <div class="row mx-0 mt-1 w-30 justify-content-between">
                        （
                        <?php if ($userType == ACC_TYPE_TP && $info->RECIPIENT_TENKI !== PATIENT_OUTCOME_CODE['SURVIVE']) : ?>
                            <?php foreach ($patientOutcomeDetails as $item) : ?>
                                <label class="radio-inline mb-0"><?= form_radio(array('class' => 'mr-1', 'disabled' => true), '', $item->CODE === $info->RECIPIENT_TENKI_DETAIL) ?><?= $item->VALUE ?></label>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <?php foreach ($patientOutcomeDetails as $item) : ?>
                                <label class="radio-inline mb-0"><?= form_radio(array('name' => 'patientOutcomeDetails', 'class' => 'mr-1'), $item->CODE, $item->CODE === $info->RECIPIENT_TENKI_DETAIL) ?><?= $item->VALUE ?></label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        ）
                    </div>
                <?php endif ?>

                <div class="row mx-0 mt-2">
                    <?php if ($info->ZOKI_CODE == ORGAN_LIVER) : ?>
                        <div class="row mx-0 mt-2 w-70">
                            <p class="w-15">コメント</p>
                            <?= form_input(array('name' => 'organOutcomeComment', 'value' => $info->RECIPENT_TENKI_CMNT, 'class' => 'input-h24 w-85')) ?>
                        </div>
                        <div class="row mx-0 mt-2 w-30">
                            <p class="w-50 text-right pr-2">最終生存確認日</p>
                            <?= form_input(array('type' => 'date', 'name' => 'finalLivDate', 'value' => datetimeToString($info->FINAL_LIV_DATE, 'Y-m-d'), 'class' => 'input-h24 w-50')) ?>
                        </div>
                    <?php else : ?>
                        <div class="row mx-0 mt-2 w-100">
                            <p class="w-10">コメント</p>
                            <?= form_input(array('name' => 'organOutcomeComment', 'value' => $info->RECIPENT_TENKI_CMNT, 'class' => 'input-h24 w-90')) ?>
                        </div>
                    <?php endif ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>