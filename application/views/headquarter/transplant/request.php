<script type="text/javascript">
    $(function() {
        $("table").each(function() {
            jQuery(this).find("tr:even").addClass("even");
        });

        $(".back").click(function() {
            document.list.submit();
        });
        $(".confirm").click(function() {
            $("#form1").submit();
        });

        $('#organs').change(function() {
            $('#pref').empty();
            $('#institution').empty();
            $('#user_area').empty();
            if ($(this).val()) {
                $.ajax({
                    type: "POST",
                    url: "/transplant/ajax/pref_by_organ",
                    data: "id=" + $(this).val(),
                    success: function(data) {
                        $('#institution').empty();
                        $('#user_area').empty();
                        $('#pref').append(data);
                    }
                });
            }
        });

        $('#pref').change(function() {
            $('#institution').empty();
            $('#user_area').empty();
            if ($(this).val() && $('#organs').val()) {
                $.ajax({
                    type: "POST",
                    url: "/transplant/ajax/institution_by_pref_organ",
                    data: {
                        "pref_id": $(this).val(),
                        "organs_id": $('#organs').val(),
                    },
                    success: function(data) {
                        $('#institution').append(data);
                    }
                });
            }
        });

        $('#institution').change(function() {
            $('#user_area').empty();
            if ($(this).val() && $('#organs').val() && $('#pref').val()) {
                $.ajax({
                    type: "POST",
                    url: "/transplant/ajax/user",
                    data: {
                        "institution_id": $(this).val(),
                        "pref_id": $('#pref').val(),
                        "organs_id": $('#organs').val(),
                    },
                    success: function(data) {
                        $('#user_area').empty();
                        $('#user_area').append(data);
                    }
                });
            }
        });

        $.fn.accordion = function(settings) {
            settings = jQuery.extend({
                first: true,
                open: 0,
                animation: true,
                speed: 200,
                params: {
                    height: "toggle"
                },
                action: "click"
            }, settings);

            var fchild = $(this).contents();
            if (settings.first == true) {
                fchild.not(":eq(" + settings.open + ")").contents().next().hide();
                fchild.contents().next().not(":hidden").parent().addClass("open");
            } else {
                fchild.contents().next().hide();
            }

            fchild.find(":first").bind(settings.action, function() {
                var _this = $(this).next();
                var p = $(this).parent();
                if (_this.get(0)) {
                    if (_this.is(":hidden")) {
                        p.addClass("open");
                    } else {
                        p.removeClass("open");
                    }
                    sh(p.parent().find(_this.get(0).tagName + ":visible"));
                    sh(_this);
                    return false;
                }
            });

            function sh(_this) {
                if (settings.animation == true) {
                    _this.animate(settings.params, settings.speed);
                } else {
                    _this.toggle();
                }
            }
        }
        $('#accordion').accordion();
    });
</script>

<?php if (validation_errors()) : ?>
    <div class="err">
        <?php if (validation_errors()) : ?>
            <?php echo validation_errors('<span>', '</span><br />'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>


<?php echo form_open('transplant/request/conf', array('name' => 'form1', 'class' => 'form1', 'id' => 'form1')); ?>

<table width="100%" border="0" cellpadding="6" cellspacing="3" class="list">
    <tr>
        <th colspan="2">ドナー データ管理</th>
    </tr>
    <tr>
        <td width="30%">事例No</td>
        <td width="70%"><?php echo $d_id ?></td>
    </tr>
    <tr>
        <td>提供施設</td>
        <td><?php echo $offerInstitution ?></td>
    </tr>
    <tr>
        <td>提供施設都道府県</td>
        <td><?php echo $offerInstitutionPref ?></td>
    </tr>
    <tr>
        <td>ドナー氏名(カナ)</td>
        <td><?php echo $donorNeme ?></td>
    </tr>
    <tr>
        <td>性別</td>
        <td><?php echo $sex == '1' ? "男性" : "女性" ?></td>
    </tr>
    <tr>
        <td>年齢</td>
        <td><?php echo $age ?>歳</td>
    </tr>
    <tr>
        <td>脳死/心 停止</td>
        <td><?php echo $deathReason ?></td>
    </tr>
    <tr>
        <td>臓器</td>
        <td>
            <select id="organs" name="organs">
                <option value=""></option>
                <?php foreach ($organs as $key => $val) : ?>
                    <option value="<?php echo $val->id ?>" <?php echo set_select('organs',  $val->id); ?>><?php echo $val->organ_name ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>都道府県</td>
        <td>
            <select id="pref" name="pref">
                <?php if ($pref) : ?>
                    <option value="">選択してください</option>
                    <?php foreach ($pref as $key => $val) : ?>
                        <option value="<?php echo $val->id ?>" <?php echo set_select('pref',  $val->id); ?>><?php echo $val->pref_name ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>施設</td>
        <td>
            <select id="institution" name="institution">
                <?php if ($institution) : ?>
                    <option value="">選択してください</option>
                    <?php foreach ($institution as $key => $val) : ?>
                        <option value="<?php echo $val->id ?>" <?php echo set_select('institution',  $val->id); ?>><?php echo $val->institution_name ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
    </tr>

    <tr>
        <td>ユーザ</td>
        <td>
            <span id="user_area" name="user_area">
                <?php if ($user) : ?>
                    <?php foreach ($user as $key => $val) : ?>
                        <input type="checkbox" name="user[]" id="user<?php echo $key ?>" value="<?php echo $val->id ?>" <?php echo set_checkbox('user[]', $val->id); ?> /><?php echo form_label($val->sei . " " . $val->mei, "user" . $key); ?><br />
                    <?php endforeach;  ?>
                <?php endif; ?>
            </span>
        </td>
    </tr>
    <tr>
        <td>依頼ファイル</td>
        <td>
            <ul id="accordion">
                <?php foreach ($list as $key => $val) : ?>
                    <li><span class="reqBgBtn"><?php echo $val['folder_name'] ?></span>
                        <ul>
                            <?php foreach ($val['file'] as $key => $val) : ?>
                                <li class="reqCld"><input type="checkbox" name="files[]" id="files<?php echo $key ?>" value="<?php echo $key ?>" <?php echo set_checkbox('files[]', $key); ?> /><?php echo form_label($val, "files" . $key); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>

<div class="btnArea">
    <ul>
        <li><a href="#" id="back" class="back"><?php echo img(array('src' => 'img/btn003.jpg', 'alt' => '戻る', 'width' => '124', 'height' => '24')) ?></a></li>
        <li><a href="#" id="confirm" class="confirm"><?php echo img(array('src' => 'img/btn034.jpg', 'alt' => '依頼', 'width' => '124', 'height' => '24')) ?></a></li>
    </ul>
</div>
<?php echo form_close(); ?>

<?php echo form_open('/donor/data', array('name' => 'list')); ?>
<?php echo form_hidden('d_id', $d_id) ?>
<?php echo form_close(); ?>