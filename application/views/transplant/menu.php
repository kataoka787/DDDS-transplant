<ul>
    <?php if (in_array(WORK_FOLLOW_UP, $works)) : ?>
        <li>
            <?= anchor("/managementMenu", "移植後経過情報管理") ?>
        </li>
    <?php endif ?>
    <?php if ($this->session->userdata('admin_flg')) : ?>
        <li>管理業務</li>
        <ul>
            <li>
                <?= anchor("/doctor", "移植施設/移植後経過情報入力施設 ユーザ管理業務") ?>
            </li>
        </ul>
    <?php endif ?>
</ul>
