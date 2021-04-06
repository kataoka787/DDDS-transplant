<ul>
    <?php if (in_array(WORK_DDDS, $works)) : ?>
        <li><?php echo anchor('donor/searchlist', 'ドナーデータ伝送システム'); ?></li>
    <?php endif ?>
    <?php if (in_array(WORK_FOLLOW_UP, $works)) : ?>
        <li><?php echo anchor('managementMenu', '移植後経過情報管理'); ?></li>
    <?php endif ?>
    <?php if ($this->session->userdata('admin_flg')) : ?>
        <li>管理業務</li>
        <ul>
            <li><?php echo anchor('admin/cordinator', 'コーディネーター管理業務'); ?></li>
            <li><?php echo anchor('admin/transplant', '移植施設/移植後経過情報入力施設 データ管理業務'); ?></li>
            <li><?php echo anchor('admin/transplantUser', '移植施設/移植後経過情報入力施設 ユーザ管理業務'); ?></li>
            <li><?php echo anchor('admin/accountHistory', 'アカウント変更履歴管理業務'); ?></li>
            <li><?php echo anchor('admin/accesslog', 'アクセスログ'); ?></li>
        </ul>
    <?php endif; ?>
</ul>
