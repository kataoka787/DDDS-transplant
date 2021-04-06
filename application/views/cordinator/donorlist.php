<script type="text/javascript">
    $(document).ready(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".list tr:odd").addClass("odd");
        $(".list tr:not(.odd)").hide();
        $(".list tr:first-child").show();

        $(".list tr.odd").click(function() {
            $(this).next("tr").toggle();
        });
    });

    function goPage(d_id) {
        document.list.d_id.value = d_id;
        document.list.submit();
    }
</script>

<p><?php echo anchor('edit/newedit', img(array('src' => 'img/btn011.jpg', 'alt' => 'ドナー情報登録', 'width' => '124', 'height' => '24'))); ?></p>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th>ドナー情報</th>
    </tr>
    <?php foreach ($list as $key => $val) : ?>
        <tr>
            <td><?php echo $val->d_id ?>&nbsp;<?php echo $val->offer_institution_name ?></td>
        </tr>
        <tr>
            <td>
                <ul id="accordion">
                    <li>脳死/心停止
                        <ul>
                            <li class="reqCld"><?php echo $this->Causedeathmst->getCauseDeathNameById($val->cause_death_mst_id) ?></li>
                        </ul>
                    </li>
                </ul>
                <ul id="accordion">
                    <li>提供施設
                        <ul>
                            <li class="reqCld"><?php echo $val->offer_institution_name ?></li>
                        </ul>
                    </li>
                </ul>
                <ul id="accordion">
                    <li>提供施設都道府県
                        <ul>
                            <li class="reqCld"><?php echo $this->Prefmst->getPrefNameById($val->pref_mst_id) ?></li>
                        </ul>
                    </li>
                </ul>
                <ul id="accordion">
                    <li>ドナー氏名カナ
                        <ul>
                            <li class="reqCld"><?php echo $this->Donorbasetbl->getDispName($val->sei, $val->mei, " ") ?></li>
                        </ul>
                    </li>
                </ul>
                <ul id="accordion">
                    <li>性別
                        <ul>
                            <li class="reqCld"><?php echo $val->sex == '1' ? "男性" : "女性" ?></li>
                        </ul>
                    </li>
                </ul>
                <ul id="accordion">
                    <li>年齢
                        <ul>
                            <li class="reqCld"><?php echo $val->age ?>歳</li>
                        </ul>
                    </li>
                </ul>
                <ul id="accordion">
                    <li>データ
                        <ul>
                            <li class="reqCld"><a href="#" onClick="goPage('<?php echo $val->d_id ?>');"><?php echo img(array('src' => 'img/btn015.jpg', 'alt' => 'データ管理', 'width' => '124', 'height' => '24')) ?></a></li>
                        </ul>
                    </li>
                </ul>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


<?php echo form_open('/data', array('name' => 'list')); ?>
<?php echo form_hidden('d_id', '') ?>
<?php echo form_close(); ?>