<script type="text/javascript">
    function searchPage(id) {
        document.getElementById("form1").action = "/admin/accesslog/" + id;
        document.forms['form1'].submit();
    }

    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".search").click(function() {
            $('#form1').attr('action', '/admin/accesslog');
            $('#hidden_btn').attr('value', '1');
            $('#form1').submit();
        });

        $(".csv").click(function() {
            $('#form1').attr('action', '/admin/accesslog/csv');
            $('#form1').submit();
        });

        $('#from_year').change(function() {
            $('#from_day').empty();
            if ($('#from_year').val() && $('#from_month').val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/day",
                    data: "data=" + $('#from_year').val() + "_" + $('#from_month').val(),
                    success: function(data) {
                        $('#from_day').empty();
                        $('#from_day').append(data);
                    }
                });
            }
        });

        $('#from_month').change(function() {
            $('#from_day').empty();
            if ($('#from_year').val() && $('#from_month').val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/day",
                    data: "data=" + $('#from_year').val() + "_" + $('#from_month').val(),
                    success: function(data) {
                        $('#from_day').empty();
                        $('#from_day').append(data);
                    }
                });
            }
        });
        
        $('#to_year').change(function() {
            $('#to_day').empty();
            if ($('#to_year').val() && $('#to_month').val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/day",
                    data: "data=" + $('#to_year').val() + "_" + $('#to_month').val(),
                    success: function(data) {
                        $('#to_day').empty();
                        $('#to_day').append(data);
                    }
                });
            }
        });

        $('#to_month').change(function() {
            $('#to_day').empty();
            if ($('#to_year').val() && $('#to_month').val()) {
                $.ajax({
                    type: "POST",
                    url: "/admin/ajax/day",
                    data: "data=" + $('#to_year').val() + "_" + $('#to_month').val(),
                    success: function(data) {
                        $('#to_day').empty();
                        $('#to_day').append(data);
                    }
                });
            }
        });
    });
</script>
<div class="sTable2">
    <?php if ($error or validation_errors()) : ?>
        <div class="err">
            <?php if (validation_errors()) : ?>
                <?php echo validation_errors('<span>', '</span><br />'); ?>
            <?php endif; ?>
            <?php if ($error) : ?>
                <?php foreach ($error as $key => $val) : ?>
                    <span>期間指定<?php echo str_replace("{field}", $val, sprintf($this->lang->line('valid_value'))) ?></span><br />
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php echo form_open('', array('name' => 'form1', 'id' => 'form1', 'class' => 'form1')); ?>
    <table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
        <tr>
            <th colspan="2">検索</th>
        </tr>
        <tr>
            <td width="30%">ユーザ区分</td>
            <td width="70%">
                <select name="kbn">
                    <?php foreach ($kbn as $key => $val) : ?>
                        <option value="<?php echo $key ?>" <?php echo set_select('kbn',  $key); ?>><?php echo $val ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="30%">姓(全角カナ)</td>
            <td width="70%"><?php echo form_input(array('name' => 'sei_kana', 'value' => set_value('sei_kana'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td width="30%">名(全角カナ)</td>
            <td width="70%"><?php echo form_input(array('name' => 'mei_kana', 'value' => set_value('mei_kana'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td width="30%">事例ID</td>
            <td width="70%"><?php echo form_input(array('name' => 'd_id', 'value' => set_value('d_id'), 'class' => 'iText')); ?></td>
        </tr>
        <tr>
            <td width="30%">期間指定From</td>
            <td width="70%">
                <select name="from_year" class="from_year" id="from_year">
                    <option value="">選択してください</option>
                    <?php foreach ($year as $key => $val) : ?>
                        <option value="<?php echo $key ?>" <?php echo set_select('from_year', $key) ?>><?php echo $val ?></option>
                    <?php endforeach; ?>
                </select>年
                <select name="from_month" class="from_month" id="from_month">
                    <option value="">選択してください</option>
                    <?php foreach ($month as $key => $val) : ?>
                        <option value="<?php echo $key ?>" <?php echo set_select('from_month', $key) ?>><?php echo $val ?></option>
                    <?php endforeach; ?>
                </select>月
                <select name="from_day" class="from_day" id="from_day">
                    <option value="">選択してください</option>
                    <?php if ($from_day) : ?>
                        <?php foreach ($from_day as $key => $val) : ?>
                            <option value="<?php echo $key ?>" <?php echo set_select('from_day', $key) ?>><?php echo $val ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>日
            </td>
        </tr>
        <tr>
            <td width="30%">期間指定To</td>
            <td width="70%">
                <select name="to_year" class="to_year" id="to_year">
                    <option value="">選択してください</option>
                    <?php foreach ($year as $key => $val) : ?>
                        <option value="<?php echo $key ?>" <?php echo set_select('to_year', $key) ?>><?php echo $val ?></option>
                    <?php endforeach; ?>
                </select>年
                <select name="to_month" class="to_month" id="to_month">
                    <option value="">選択してください</option>
                    <?php foreach ($month as $key => $val) : ?>
                        <option value="<?php echo $key ?>" <?php echo set_select('to_month', $key) ?>><?php echo $val ?></option>
                    <?php endforeach; ?>
                </select>月
                <select name="to_day" class="to_day" id="to_day">
                    <option value="">選択してください</option>
                    <?php if ($to_day) : ?>
                        <?php foreach ($to_day as $key => $val) : ?>
                            <option value="<?php echo $key ?>" <?php echo set_select('to_day', $key) ?>><?php echo $val ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>日
            </td>
        </tr>
    </table>
    <input type="hidden" name="hidden_btn" id="hidden_btn" value="0">
    <?php echo form_close(); ?>

    <div class="btnArea">
        <ul>
            <li><a href="#" id="csv" class="csv"><?php echo img(array('src' => 'img/btn036.jpg', 'alt' => 'CSVダウンロード', 'width' => '124', 'height' => '24')) ?></a></li>
            <li><a href="#" id="search" class="search"><?php echo img(array('src' => 'img/btn028.jpg', 'alt' => '検索', 'width' => '124', 'height' => '24')) ?></a></li>
            <li><?php echo anchor('menu', img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24', 'id' => 'back', 'class' => 'back'))) ?></li>
        </ul>
    </div>
</div>

<div class="frBtnArea">
    <div class="fArea">
        <?php if ($prev['flg']) : ?><a href="#" onClick='searchPage("<?php echo $prev['link'] ?>");'>前へ</a><?php endif; ?>
    </div>
    <div class="bArea">
        <?php if ($next['flg']) : ?><a href="#" onClick='searchPage("<?php echo $next['link'] ?>");'>次へ</a><?php endif; ?>
    </div>
</div>
<br class="clear" />

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th>日時</th>
        <th>ユーザー区分</th>
        <th>ユーザ名</th>
        <th>URL</th>
        <th>アクセス元</th>
        <th>パラメータ</th>
    </tr>
    <?php foreach ($list as $key => $val) : ?>
        <tr>
            <td><?php echo $val->created_at ?></td>
            <td><?php echo $val->account_type ?></td>
            <td><?php echo $val->sei . " " . $val->mei ?></td>
            <td><?php echo $val->url ?></td>
            <td><?php echo $val->ip_address ?><br /><?php echo $val->user_agent ?>
            </td>
            <td>
                [GET]<br />
                <?php echo $val->get_param ?><br />
                [POST]<br />
                <?php echo $val->post_param ?><br />
            </td>
        </tr>
    <?php endforeach; ?>
</table>