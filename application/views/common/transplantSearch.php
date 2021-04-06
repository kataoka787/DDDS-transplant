<div id="searchDialog" class="search-dialog">
    <div class="search-dialog-content">
        <div class="search-dialog-header">
            <h2 class="mb-2">施設検索</h2>
        </div>
        <div class="search-dialog-body">
            <?= form_open('ajax/transplantSearch', array('id' => 'transplantSearchForm')) ?>
            <div class="row m-0">
                <p class="w-30">ブロック</p>
                <?= form_dropdown(array('name' => 'blockDialog', 'id' => 'blockDialog', 'class' => 'input-h24 w-40'), $block) ?>
            </div>
            <div class="row m-0">
                <p class="w-30">都道府県</p>
                <?= form_dropdown(array('name' => 'prefDialog', 'id' => 'prefDialog', 'class' => 'input-h24 w-40')) ?>
            </div>
            <div class="row m-0">
                <p class="w-30">施設名</p>
                <?= form_input(array('name' => 'transplantDialog', 'id' => 'transplantDialog', 'value' => '', 'class' => 'input-h24 w-70', 'maxlength' => 80)) ?>
            </div>
            <div class="row m-0">
                <p class="w-30">施設区分</p>
                <div class="w-70">
                    <div class="custom-control custom-checkbox checkbox-lg">
                        <?= form_checkbox(array('id' => 'transplantFacilityClass', 'name' => 'facilityClass[]', 'value' => '1', 'class' => 'custom-control-input')) ?>
                        <?= form_label('移植施設', '', array('for' => 'transplantFacilityClass', 'class' => 'custom-control-label')) ?>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-lg">
                        <?= form_checkbox(array('id' => 'postTransplantFacilityClass', 'name' => 'facilityClass[]', 'value' => '2', 'class' => 'custom-control-input')) ?>
                        <?= form_label('移植後経過情報管理施設', '', array('for' => 'postTransplantFacilityClass', 'class' => 'custom-control-label')) ?>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
            <div class="mt-3 d-flex justify-content-end">
                <?= form_button(array('class' => 'bg-btn mr-2 w-25', 'id' => 'clearConditionsDialog', 'name' => 'clearConditionsDialog', 'content' => '条件クリア')); ?>
                <?= form_button(array('class' => 'bg-btn w-25', 'id' => 'searchBtnDialog', 'name' => 'searchBtnDialog', 'content' => '検索')); ?>
            </div>
            <p class="mt-2 mb-1 d-flex justify-content-end" id="countTpSearchResult">000&nbsp件表示</p>
            <table class="w-100 table-bordered table-hover" id="tpSearchTable">
                <thead>
                    <tr>
                        <th>施設区分</th>
                        <th>施設名</th>
                    </tr>
                </thead>
                <tbody id="tpSearchResult"></tbody>
            </table>
            <div class="row m-0">
                <?= form_button(array('class' => 'bg-btn w-25', 'id' => 'chooseBtn', 'name' => '', 'content' => '選択')); ?>
                <div class="w-50"></div>
                <?= form_button(array('class' => 'bg-btn w-25', 'id' => 'closeBtn', 'name' => '', 'content' => '閉じる')); ?>
            </div>
        </div>
    </div>
</div>
