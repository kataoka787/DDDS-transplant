<?php defined('BASEPATH') or exit('No direct script access allowed');

/* All mail settings */
$config["mail"] = array(
    /* Common */
    "path" => "/var/www/html/application/mail/",
    "from_name" => "DDDS",
    "from_address" => "head@jotnw.or.jp",
    "signature" => "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　【DDDS：公益社団法人日本臓器移植ネットワーク ドナーデータ伝送システム】
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
○登録データの変更、削除は下記より行ってください。（要ログイン）
    %URL1%

○ご利用ガイド
    %URL2%

○FAQ（よくある質問）
    %URL3%",
    /* Reminder */
    "reminder" => array(
        "subject" => "【JOT】DDDSユーザー　パスワード変更手続きのお知らせ",
        "template" => "reminder.txt",
    ),
    /* Cordinator */
    "cordinator" => array(
        "template" => array(
            "register" => "user_regist.txt",
            "edit" => "user_edit.txt",
        ),
        "subject" => array(
            "register" => "【DDDS】ユーザー　登録完了通知",
            "edit" => "【DDDS】ユーザー　変更完了通知",
        ),
        "signature" => "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　【DDDS：公益社団法人日本臓器移植ネットワーク ドナーデータ伝送システム】
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
○ご利用ガイド
    %URL2%

○FAQ（よくある質問）
    %URL3%


ドナーデータ伝送システムはドナー情報の伝達のための専用のサイトです。
個人情報を含みますので、取扱いには十分ご注意ください。
パスワードの管理は利用者ご自身でお願いします。(パスワードは6ヶ月ごとの更新が必要です)

* お問い合わせは下記のアドレスへご連絡ください。(お時間をいただく場合があります)
E-mail ： head@jotnw.or.jp

===============================================================================

公益社団法人日本臓器移植ネットワーク

あっせん事業部　　TEL：03-5446-8806／FAX：03-5446-8818

===============================================================================",
    ),
    /* Transplant user (doctor) */
    "transplant_user" => array(
        "template" => "transplant_user.txt",
        "subject" => array(
            "register" => "【DDDS】ユーザー　登録完了通知",
            "edit" => "【DDDS】ユーザー　変更完了通知",
        ),
        "header" => array(
            "register" => "ドナーデータ伝送システムのユーザー登録が完了致しました。",
            "edit" => "ドナーデータ伝送システムのユーザー変更が完了致しました。",
        ),
        "password_notification" => array(
            "register" => "ドナーデータ伝送システムのアカウントが作成されました、下記のURLからログインしパスワードの変更を行ってください。
 ・DDDSへのログインURL
    %TRANSPLANT_BASE_URL%
※初回ログイン時の仮パスワード： %PASSWORD%",
            "edit" => "ドナーデータ伝送システムのアカウントが作成されました、下記のURLからログインしパスワードの変更を行ってください。
 ・パスワード変更　兼　ログインパスワード
    %TRANSPLANT_BASE_URL%
    仮パスワード：%PASSWORD%",
        ),
        "box_notification" => "また、別途BOXアプリケーションより招待メール送られておりますので、受信内容をご確認頂き、BOXへのユーザー登録をお願いします。\nＢＯＸへのユーザー登録後、以下のURLより、あっせん情報を確認することが出来ます。
    ・ＢｏｘへのログインＵＲＬ（Ｂｏｘへのユーザー登録後にログイン可能）
    　https://app.box.com/login
    ※Ｂｏｘへのユーザー登録時に設定するパスワードは、ドナーデータ伝送システムのパスワードとは個別に管理されている為、それぞれのパスワードを大事に保管ください。
    ※Ｂｏｘへのユーザー登録を行わないと、あっせん情報を受け取ることが出来ません。",
    ),
    /* Request */
    "request" => array(
        "template" => "transplant_request.txt",
        "subject" => "【DDDS】事例No「%DID%」ドナー情報連絡",
    ),
    /* Follow up */
    "follow_up" => array(
        "subject" => array(
            1 => "【DDDS】移植後経過情報入力のお願い",
            2 => "【DDDS】年度末　移植後経過情報入力のお願い（まもなく入力期間が終了します）",
            3 => "【DDDS】移植後経過情報入力のお願い（入力期日が過ぎています）",
            4 => "【DDDS】移植後経過情報入力のお願い（入力期限が過ぎています）",
        ),
        "contact_mail" => "assen-follow@jotnw.or.jp",
        /**
         * 1: 依頼メール (１、３、６ヶ月、１年)
         * 2: 依頼メール (２年目以降)
         * 3: 督促メール(１、３、６ヶ月、１年)
         * 4: 督促メール(２年目以降)
         */
        "type" => array(1, 2, 3, 4),
    ),
);
