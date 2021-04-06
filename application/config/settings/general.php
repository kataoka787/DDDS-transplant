<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 共通設定 */
//読み込み共通JS
$config['js'] = array('jquery-3.5.1.min.js', 'jquery.tablesorter.min.js', 'jquery.metadata.js', 'jquery.autoscale.js');

//読み込み共通CSS
$config['css'] = array('style.css');
//読み込み共通CSS
$config['css_mobile'] = array('style_mobile.css');

//ログインチェックを行わないコントローラー
$config['no_login_check'] = array(
    'ajax',
    'auth',
    "errors",
    'cron',
    'init',
    'password',
    'reminder',
);

//ログインチェックを行わないコントローラー
$config['co_no_login_check'] = array(
    'ajax',
    'auth',
    "errors",
    'reminder',
    'password',
);
$config['tp_no_login_check'] = array(
    'ajax',
    'auth',
    "errors",
    'reminder',
    'password',
    'guide',
    'faq',
);
/* Password will expire in 6 month */
$config['password_expired'] = "+6 month";

//開発環境でプロファイラを無効にするコントローラー
$config['no_profiler'] = array('ajax', 'api', 'init', 'detail',);

//事例IDの接頭辞
$config['did_prefix'] = 'D';

//フォルダ(JQUERY用)　比較文字列
$config['folder_name_photo'] = "画像";

//カテゴリを画像選択した場合の年チェック用
$config['validate_photo_date_year'] = 2011;

//ファイルアップロードテンポラリディレクトリ
$config['upload_file_tmp_path'] = "/var/www/html/application/upload/";

//ファイルアップロード最大高さ
$config['upload_file_max_height'] = 3507;

////ファイルアップロード最大幅
$config['upload_file_max_width'] = 2480;

//<-------START transplant ------->
//ファイルアップロード最大高さ
$config['tp_upload_file_max_height'] = 0;
////ファイルアップロード最大幅
$config['tp_upload_file_max_width'] = 0;
//<-------END transplant ------->

////ファイルアップロード幅、高さ　制限なし
$config['upload_file_size_zero'] = 0;

//画像ファイルパス
$config['img_path'] = "/var/www/html/web/img/";

//ファイルアップロード確認画面、preview画面で表示するファイル
$config['conf_xls_img'] = "excel.jpg";
$config['conf_pdf_img'] = "pdf.jpg";
$config['conf_doc_img'] = "word.jpg";
$config["image_ext"]    = array("jpg", "jpeg");
$config["ext_icon"]     = array(
    "xls"       => "excel.jpg",
    "xlsx"      => "excel.jpg",
    "doc"       => "word.jpg",
    "docx"      => "word.jpg",
    "pdf"       => "pdf.jpg",
    "undefined" => "undefined.jpg",
);

//<-------START cordinator ------->
//ドナー一覧ページ件数
$config['donor_list_count'] = 10;
//<-------END cordinator ------->
/* アプリケーション別 */
//ZIPファイル用ディレクトリ
$config['zip_download_file_tmp_path'] = "./download/";

//ファイルダウンロード用接頭辞
$config['download_cordinator_prefix']      = "cordinator_";
$config['download_inspection_prefix']      = "inspection_";
$config['download_transplant_prefix']      = "transplant_";
$config['download_donor_prefix']           = "donor_";
$config['download_account_history_prefix'] = "account_history_";
$config['download_accesslog_prefix']       = "accesslog_";

//ドナー検索・データ一覧ページ件数
$config['donor_search_list_count'] = 10;

//コーディネーター検索・データ一覧ページ件数
$config['cordinator_search_list_count'] = 10;

//移植施設検索・データ一覧ページ件数
$config['transplant_search_list_count'] = 10;

//移植施設　ユーザ検索・データ一覧ページ件数
$config['transplant_user_search_list_count'] = 10;

//アカウント変更履歴管理一覧ページ件数
$config['account_history_search_list_count'] = 10;

//アクセスログ管理一覧ページ件数
$config['accesslog_search_list_count'] = 10;

//ユーザ一括登録ファイルの拡張子
$config['user_regist_file_ext'] = "csv";

//アカウント変更履歴管理一覧のFROM TO　年
$config['account_history_search_year_ago'] = 2;

//アクセスログ管理一覧のFROM TO　年
$config['accesslog_search_year_ago'] = 2;

//PREVIEW表示件数
$config['preview_count'] = 1;

//CSVフォーマットダウンロードファイル
$config['csv_template_path'] = "/var/www/html/application/csv_format/";
$config['csv_template_1']    = "inspection.csv";
$config['csv_template_2']    = "transplant.csv";

//一括ダウンロード用ディレクトリ
$config['admin_path']                           = "/var/www/html/application/controllers/";
$config['admin_download_donor_delete_tmp_path'] = "donorData";
$config['admin_download_donor_csv']             = "donorData.csv";
$config['admin_download_upload_csv']            = "upload.csv";
$config['admin_download_download_csv']          = "download.csv";
$config['admin_download_receipt_csv']           = "receipt.csv";
$config['admin_download_cordinator_path']       = "cordinator";
$config['admin_download_inspection_path']       = "inspection";
$config['admin_download_transplant_path']       = "transplant";
$config['admin_download_upload_path']           = "upload";
$config['admin_download_download_path']         = "download";
$config['admin_download_receipt_path']          = "receipt";

/* アプリケーション別 */
//申込書
$config['ud_no_profiler']    = "";
$config['form_head_honbu']   = array("address" => "あっせん事業部", "post_number" => "107-0052", "adr" => "", "tel" => "03-5446-8806", "fax" => "03-5446-8818");
$config['form_head_higashi'] = array("address" => "東日本支部", "post_number" => "107-0052", "adr" => "", "tel" => "03-5446-8820", "fax" => "03-5446-8822");
$config['form_head_naka']    = array("address" => "中日本支部", "post_number" => "453-0014", "adr" => "", "tel" => "052-453-1409", "fax" => "052-453-1408");
$config['form_head_nishi']   = array("address" => "西日本支部", "post_number" => "530-0003", "adr" => "", "tel" => "06-6455-0504", "fax" => "06-6455-2841");
$config['form_organs']       = "腎臓";

/* BOX access token save path */
$config["access_token_path"] = APPPATH . "/cache/token.json";

/* Password history */
$config["password_history_limit"] = 6;

/* Donor */
$config["donor_expired"] = "-60 day";

/* Transplant user */
$config["request_expired"]        = "+60 day";
$config["tp_user_expired"]        = "+4000 day";
$config["random_password_pool"]   = array("0123456789", "abcdefghijklmnopqrstuvwxyz", "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
$config["random_password_length"] = 8;
$config["request_expired"]        = "+60 day";
$config["tp_user_expired"]        = "+4000 day";

/* Must check controller */
$config['check_user_work'] = array('managementMenu', 'csvLoad', 'search', 'detail', 'pdfReport', 'csvReport');
$config['check_admin']     = array('menu', 'managementMenu', 'csvLoad');

