<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ドナーデータ伝送システム</title>
    <meta name="viewport" content="width=device-width, user-scalable=yes">
    <?php foreach (config_item('js') as $key => $val) : ?>
        <script type="text/javascript" src="/js/<?php echo $val ?>"></script>
    <?php endforeach; ?>

    <?php if (isset($js)) : ?>
        <?php foreach ($js as $key => $val) : ?>
            <script type="text/javascript" src="/js/<?php echo $val ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script type="text/javascript">
        jQuery.browser = {};
        (function() {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            var trident = ua.indexOf('Trident/');

            if (msie > 0) {
                jQuery.browser.msie = true;
                jQuery.browser.version = parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
            } else if (trident > 0) {
                var rv = ua.indexOf('rv:');
                jQuery.browser.version = parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
            }
        })();
        const csrfTokenName = "<?= config_item("csrf_token_name") ?>";
    </script>
    <script>
        $(function($) {
            $(window).autoscale('.autoscale_100', "100");
            $(window).autoscale('.autoscale_90', "90");
            $(window).autoscale('.autoscale_80', "80");
            $(window).autoscale('.autoscale_70', "70");
            $(window).autoscale('.autoscale_60', "60");
            $(window).autoscale('.autoscale_50', "50");
            $(window).autoscale('.autoscale_40', "40");
            $("form").on("submit", function() {
                resetCSRFToken();
            })
        });

        function getCSRFToken() {
            const cookie = document.cookie.match(/csrf_cookie_name=([a-z0-9]+)/);
            return cookie && cookie.length === 2 ? cookie[1] : "";
        }

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (settings.data.indexOf(`${csrfTokenName}`) === -1) {
                    settings.data += `&${csrfTokenName}=` + encodeURIComponent(getCSRFToken());
                }
            }
        });

        function resetCSRFToken() {
            $(`input[name='${csrfTokenName}']`).val(getCSRFToken());
        }

        $(document).ajaxComplete(function() {
            resetCSRFToken();
        });
    </script>
    <?php foreach (config_item('css_mobile') as $key => $val) : ?>
        <link href="/css/<?php echo $val ?>" type="text/css" rel="stylesheet" media="screen" />
    <?php endforeach; ?>

    <?php if (isset($css)) : ?>
        <?php foreach ($css as $key => $val) : ?>
            <link href="/css/<?php echo $val['css'] ?>" type="text/css" rel="stylesheet" media="<?php echo $val['media'] ?>" />
        <?php endforeach; ?>
    <?php endif; ?>


</head>

<body>
    <div id="wrapper">
        <div id="pWrapper">
            <div id="header">
                <div id="headerLogo">
                    <?php echo anchor('/', img(array('src' => 'img/logo02.jpg', 'alt' => '', 'width' => '82', 'height' => '42'))); ?>
                </div>
                <div id="headerBtn">
                    <?php echo $this->session->userdata('is_login') ? anchor('auth/logout', img(array('src' => 'img/btn004.jpg', 'alt' => 'ログアウト', 'width' => '124', 'height' => '24'))) : "" ?>
                </div>
            </div>
            <div id="contents">
                <h2><?php echo isset($page_title) ? $page_title : ""; ?></h2>