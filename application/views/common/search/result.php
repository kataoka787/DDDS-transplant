<div class="mt-3 d-flex justify-content-end">
    <div class="w-25 d-flex">
        <?= form_button(array('class' => 'bg-btn px-3 mr-2 w-50', 'id' => 'clearConditions', 'name' => 'clearConditions', 'content' => '条件クリア')); ?>
        <?= form_button(array('class' => 'bg-btn px-3 w-50', 'id' => 'searchBtn', 'name' => 'searchBtn', 'content' => '検索')); ?>
    </div>
</div>

<div class="w-25 d-flex">
    <?= form_button(array('class' => 'bg-btn w-50', 'id' => 'selectAll', 'name' => '', 'content' => '全選択')); ?>
    <?= form_button(array('class' => 'bg-btn ml-2 w-50', 'id' => 'deselectAll', 'name' => '', 'content' => '全選択解除')); ?>
</div>

<p class="my-0 d-flex justify-content-end" id="countSearchResult">000 件表示</p>

<table class="table-bordered">
    <thead>
        <tr>
            <th rowspan="2"></th>
            <th rowspan="2">登録者ID</th>
            <th rowspan="2">カナ氏名漢字氏名</th>
            <th rowspan="2">入力対象経過期間</th>
            <th rowspan="2">報告期限日</th>
            <th colspan="2">入力状況</th>
            <th rowspan="2">生年月日</th>
            <th rowspan="2">年齢</th>
            <th rowspan="2">性別</th>
            <th rowspan="2">移植臓器</th>
            <th rowspan="2">同時移植</th>
            <th rowspan="2">移植実施日</th>
            <th rowspan="2">移植回数</th>
            <th rowspan="2">移植施設</th>
            <th rowspan="2">移植後経過情報管理施設</th>
            <th rowspan="2">臓器転帰</th>
            <th rowspan="2">患者転帰</th>
            <?php if ($userType == ACC_TYPE_CO) : ?>
                <th rowspan="2">ドナーID</th>
            <?php endif ?>
        </tr>
        <tr>
            <th>生活状況</th>
            <th>検査項目</th>
        </tr>
    </thead>
    <tbody id="searchResult">

    </tbody>
</table>

<div class="d-flex justify-content-end">
    <div class="row m-0 w-90 d-flex justify-content-end">
        <?php if ($userType == ACC_TYPE_CO) : ?>
            <?= form_button(array('class' => 'bg-btn w-15', 'id' => 'allCsvBtn', 'name' => '', 'content' => 'CSV出力<br>(全件 全項目)')); ?>
        <?php endif ?>
        <?= form_button(array('class' => 'bg-btn ml-2 w-15', 'id' => 'listPdfBtn', 'name' => '', 'content' => '移植後経過情報<br>一覧印刷')); ?>
        <?= form_button(array('class' => 'bg-btn ml-2 w-15', 'id' => 'infoPdfBtn', 'name' => '', 'content' => '移植後経過情報<br>印刷')); ?>
        <?= form_button(array('class' => 'bg-btn ml-2 w-15', 'id' => 'entryPdfBtn', 'name' => '', 'content' => '移植後経過情報<br>記入用紙印刷')); ?>
        <?= form_button(array('class' => 'bg-btn ml-2 w-15', 'id' => 'basicCsvBtn', 'name' => '', 'content' => 'CSV出力<br>(基本情報のみ)')); ?>
        <?= form_button(array(
            'class' => 'bg-btn ml-2 w-15',
            'id' => 'backBtn',
            'content' => '戻る',
            'redirect' => ($userType == ACC_TYPE_TP && $adminFlg != IS_ADMIN) ? '/auth/logout' : '/managementMenu'
        )); ?>
    </div>
</div>