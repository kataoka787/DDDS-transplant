    <?php if ($userType == ACC_TYPE_CO) : ?>
        <div class="tab-pane fade" id="nav-donor" role="tabpanel" aria-labelledby="nav-donor-tab">
            <?= form_open('ajax/recipientInfoSearch?info=donorInfo', array('id' => 'donorForm')) ?>
            <div class="row m-0">
                <div class="w-35 m-0 mt-4 pr-2">
                    <div class="row m-0">
                        <p class="w-50">ドナーID</p>
                        <?= form_input(array('name' => 'donorID', 'value' => '', 'class' => 'input-h24 w-50')) ?>
                    </div>
                    <div class="row m-0">
                        <p class="w-50">ドナー氏名（漢字）</p>
                        <?= form_input(array('name' => 'donorName', 'value' => '', 'class' => 'input-h24 w-50')) ?>
                    </div>
                </div>
                <div class="w-65 d-flex justify-content-center">
                    <fieldset class="w-85 fieldset-border p-2">
                        <legend class="w-auto lead mb-0">提供臓器</legend>
                        <ul class="row m-0 list-unstyled">
                            <?php foreach (ORGAN as $key => $value) : ?>
                                <li class="col-2 pb-3 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'organDonorTab' . $key, 'name' => 'organ[]', 'value' => $key, 'class' => 'custom-control-input')) ?>
                                    <?= form_label($value, '', array('for' => 'organDonorTab' . $key, 'class' => 'custom-control-label')) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                        <ul class="row m-0 list-unstyled">
                            <?php foreach (SIMULTANEOUS_TRANSPLANTATION as $key => $value) : ?>
                                <li class="col-2 pb-3 custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'simultaneousTransplantationDonorTab' . $key, 'name' => 'simultaneousTransplantation[]', 'value' => $value, 'class' => 'custom-control-input')) ?>
                                    <?= form_label($key, '', array('for' => 'simultaneousTransplantationDonorTab' . $key, 'class' => 'custom-control-label')) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </fieldset>
                </div>
            </div>

            <div class="row m-0 mt-3">
                <div class="w-45">
                    <div class="row m-0">
                        <p class="w-25">手術開始日</p>
                        <div class="row w-75 m-0 justify-content-between">
                            <?= form_input(array('type' => 'date', 'name' => 'surgeryStartDate[from]', 'value' => '', 'class' => 'input-h24 w-45')) ?>
                            <p>～</p>
                            <?= form_input(array('type' => 'date', 'name' => 'surgeryStartDate[to]', 'value' => '', 'class' => 'input-h24 w-45')) ?>
                        </div>
                    </div>
                    <div class="row m-0">
                        <p class="w-25">提供施設名</p>
                        <?= form_input(array('name' => 'providedFacilityName', 'value' => '', 'class' => 'input-h24 w-75')) ?>
                    </div>
                    <div class="row m-0">
                        <p class="w-25">事例No</p>
                        <?= form_input(array('name' => 'caseNo', 'value' => '', 'class' => 'input-h24 w-40')) ?>
                    </div>
                </div>
                <div class="w-25 pl-4">
                    <fieldset class="fieldset-border px-2 py-0">
                        <legend class="w-auto lead">臓器提供状況</legend>
                        <ul class="m-0 list-unstyled d-flex justify-content-around">
                            <?php foreach ($organDonationStatus as $item) : ?>
                                <li class="custom-control custom-checkbox checkbox-lg">
                                    <?= form_checkbox(array('id' => 'organDonationStatus' . $item->CODE, 'name' => 'organDonationStatus[]', 'value' => $item->CODE, 'class' => 'custom-control-input')) ?>
                                    <?= form_label($item->VALUE, '', array('for' => 'organDonationStatus' . $item->CODE, 'class' => 'custom-control-label')) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </fieldset>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    <?php endif ?>
</div>