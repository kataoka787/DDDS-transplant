<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
 */
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', true);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
 */
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
 */
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
 */
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| Server domain
|--------------------------------------------------------------------------
 */
defined('HQ_DOMAIN') or define('HQ_DOMAIN', 'hdqrs.ddds.gss-sol.com');
defined('CO_DOMAIN') or define('CO_DOMAIN', 'brnch.ddds.gss-sol.com');
defined('TP_DOMAIN') or define('TP_DOMAIN', 'ddds.ddds.gss-sol.com');

//defined('COMMON_DOMAIN') or define('COMMON_DOMAIN', 'www.ddds.media-trust.com');

/*
|--------------------------------------------------------------------------
| Branch
|--------------------------------------------------------------------------
 */
defined('APP_HEAD') or define('APP_HEAD', 'headquarters');
defined('APP_CORDINATOR') or define('APP_CORDINATOR', 'cordinator');
defined('APP_TRANSPLANT') or define('APP_TRANSPLANT', 'transplant');
defined('APP_USER_DATA') or define('APP_USER_DATA', 'user_data');

/*
|--------------------------------------------------------------------------
| BOX API
|--------------------------------------------------------------------------
 */

defined('BOX_CONFIG') or define('BOX_CONFIG', array(
    "client_id"     => "lxoomjh89olebq2wtryhgkc32ldzs9or",
    "client_secret" => "mq5UjBNwV1cRMhgWzFRVGAT5CRFR7jPO",
    "public_key_id"  => "npag24rz",
    "private_key"   => "-----BEGIN ENCRYPTED PRIVATE KEY-----\nMIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIl5Mi8zIGeQkCAggA\nMBQGCCqGSIb3DQMHBAi9x7UqZnt1QASCBMgzQUfypE4MMT1tInEnVjqHx3vPNb6Y\ntxQNqsPvVi0JjAJ4BrQ1RbuHDwJTnJzhnP9wBw7D3C1b30sJJf/ph9ULagBsG6KT\nHG5d138eYKsVjOvTC0DTaCmrYbUV+eArBFZufVoqryl7+RdfZoQW29Ydgh6H04dK\nJnlWqyVaHPQPmvkyEetKWSrRMO6IpFt1NWC9bDiWBPB9ddipr5PhzPnqm3WTf5Jr\nYn5p39v2xNGEEsvl1B+d1CVesNh7Hb94wptgC37s6k8zahkIwHk5jafXwpMlTRd8\noJvWqdTUboNAHPyMQnos4uJuvVI7F9e3l59L2F28CcnqK+REHMmmFvCm0nSIS55X\nAyNxXjjJ1JvUNKXpgKU+T2OSgW2zqjRY1Y7Kkh9ikAPP4hJjqfvbpfImKRmrpqCw\nzZbwjNLa0kiZaVEYPki6e9NI2JqVnAP6/WQMLMeUEu8np95UhD+t5cZTQfGug52b\nedg436mPyfzVRxZSBT3CzEo/6VsNRTFWr+cY7iOpLw9g9HgcdehRF6L8/Rwqpqox\nET25D5+e3ISZvMmsggMk5wUO6ehbCPEo1OX7Tjq9B8ce39Oc+EnWkt/FgBZ4LXln\n3RrMcdNQSd3uTTF4kRFcOeYqugOGd5ObeHpfdC4wpureWDKOSFocNer9lr50CljJ\nYG0S/dPt5XnmTxclDJbPBdpOL2vkXMcxbFoftbf7VMEMTiUCHALQK6ZJZZcaWDPF\nvZGaTveLv5En1gmYiUXWnCV8VaoKY7ykC8555Ek33LHgTIK45DZHl4XA9U6FcbD/\nn48hc+dfgdr47C4wBmI63tUBFNSxaJVV8mEO8Lf6+QsNcc1v9HrgVBzWzD2nyxBF\nT5RloRmC3dHpeQL4Io13Kq3fFO1iJDEdAaF82MZwU2oVdUMs9JKRMON9ab68QNXB\nUchvJX7aq0zCPWjP6xy7LuCb3aHV8n8BMbaXvrHqCZMay+m2XzHoJkanaZSpXBLh\n7KnQrrC4blDdAhLXE9bwfdu/gZY3/84Cpqmm8INTBBdkGNaOWWBL27lP8qYVZdFo\n+qQdLRKFr6GYeJPJ7LucINSz0IxJSokLxLVZW6xZQ5Cghnp2K+hOQGW0W3bDIDrB\nPRnted58fZX0BcMfW9AFw7NG0+h8kHGlmlyoB4nJsK+X+SzTBq3tUR0XzB6fJAAP\npLPygWuWb3pBUAk53mxvDvHNjGF7U8UaHUqgS0ekjmeIwc7VHgIfShQ9UkUX0EhP\nVUTfhTGPTkYn9RDtRA4yyhnJz4wq/EzEkQ8XN8zPFskPznNB0TIwokCzT5H4YdsU\nSc5YsuhCpQAM9XTnQnBCvj294R9rGRiquI2ZbAeNM/0l+jbhO2NnYW22chO6TEI7\nnsoPle5s9i5MrJvUu2gxccwy8iUih0Y7C0zI7/Ldl1ET3h8QIuBPUiiFhEpwFi95\n0enWMg2mbQWLCM37BK/KDqxuvOEglfkekMyejvOTjC68NXDZ+gHXUddHrTxgUJ0a\noGZCosLz8pQ+q9yaxm9rm+bDs7Sc/Q922Y2jtED/q79OzEkWMffz95c0Sxo775Fi\nvtIHLHq1i1CUGu0FjOa69vRfH8fMa6r/M46mQroBhiM8ltg4FxinLuqoBictyH7E\nPM0=\n-----END ENCRYPTED PRIVATE KEY-----\n",
    "passphrase"    => "824c4748b4d6ab75dd789e5f8d76eb06",
    "enterprise_id" => "795807289",
    "auth_url"      => "https://api.box.com/oauth2/token",
    "api_url"       => "https://api.box.com/2.0",
    "upload_url"    => "https://upload.box.com/api/2.0/files/content",
));
defined("CONTENT_TYPE") or define('CONTENT_TYPE', array(
    "form-urlencoded" => "application/x-www-form-urlencoded",
    "multipart"       => "multipart/form-data",
    "json"            => "application/json",
    "json-patch"      => "application/json-patch+json",
));
defined("TIME_ZONE") or define("TIME_ZONE", "+09:00");

/*
|--------------------------------------------------------------------------
| BOX folder name
|--------------------------------------------------------------------------
 */
defined("TP_USER_ROOT_FOLDER") or define("TP_USER_ROOT_FOLDER", "131237784711");
defined("ROOT_FOLDER") or define("ROOT_FOLDER", "131238240713");
defined("HEAD_CO_FOLDER") or define("HEAD_CO_FOLDER", "JOT⇒提供施設");
defined("HEAD_TP_FOLDER") or define("HEAD_TP_FOLDER", "移植施設向けアップロード");
defined("REQUEST_FOLDER") or define("REQUEST_FOLDER", "ドナー情報");
defined("TP_HEAD_FOLDER") or define("TP_HEAD_FOLDER", "JOT向けアップロード");
defined("CO_FOLDER") or define("CO_FOLDER", "提供施設⇒JOT");
defined("TP_FOLDER") or define("TP_FOLDER", "transplant");
/*
|--------------------------------------------------------------------------
| Datetime format
|--------------------------------------------------------------------------
 */
defined("DATE_TIME_DEFAULT") or define("DATE_TIME_DEFAULT", "Y/m/d");
defined("DATE_TIME_DEFAULT_2") or define("DATE_TIME_DEFAULT_2", "Y-m-d");
defined("DATE_TIME_DEFAULT_WITHOUT_DELEMETER") or define("DATE_TIME_DEFAULT_WITHOUT_DELEMETER", "Ymd");
defined("DATE_TIME_DEFAULT_JP") or define("DATE_TIME_DEFAULT_JP", "Y年m月d日");
defined("DATE_TIME_LONG_WITHOUT_DELEMETER") or define("DATE_TIME_LONG_WITHOUT_DELEMETER", "YmdHis");
defined("DATE_TIME_LONG") or define("DATE_TIME_LONG", "Y/m/d H:i:s");
defined("DATE_TIME_LONG_HIJP") or define("DATE_TIME_LONG_HIJP", "Y/m/d　H時i分");

/*
|--------------------------------------------------------------------------
| Database table name
|--------------------------------------------------------------------------
 */
defined("ACC_CHANGE_HISTORY_TBL") or define("ACC_CHANGE_HISTORY_TBL", "accountChangeHistoryTbl");
defined("ACC_TBL") or define("ACC_TBL", "accountTbl");
defined("ACC_TYPE_MST") or define("ACC_TYPE_MST", "accountTypeMst");
defined("ACC_WORK_TBL") or define("ACC_WORK_TBL", "accountRelationWorkTbl");
defined("ACCESS_LOG_TBL") or define("ACCESS_LOG_TBL", "accessLogTbl");
defined("AFFILIATION_BLOCK_TBL") or define("AFFILIATION_BLOCK_TBL", "affiliationBlockTbl");
defined("AFFILIATION_CORDINATOR_TBL") or define("AFFILIATION_CORDINATOR_TBL", "affiliationCordinatorTbl");
defined("AFFILIATION_MST") or define("AFFILIATION_MST", "affiliationMst");
defined("BLOCK_MST") or define("BLOCK_MST", "blockMst");
defined("CAUSE_DEATCH_MST") or define("CAUSE_DEATCH_MST", "causeDeathMst");
defined("CO_TBL") or define("CO_TBL", "cordinatorTbl");
defined("DOCTOR_ACC_COLLABORARION") or define("DOCTOR_ACC_COLLABORARION", "doctorAccountCollaborationTbl");
defined("DOCTOR_ORGAN_TBL") or define("DOCTOR_ORGAN_TBL", "doctorRelationOrgansTbl");
defined("DOCTOR_TBL") or define("DOCTOR_TBL", "doctorTbl");
defined("DONOR_BASE_TBL") or define("DONOR_BASE_TBL", "donorBaseTbl");
defined("DONOR_INSTITUTION_ORGAN_TBL") or define("DONOR_INSTITUTION_ORGAN_TBL", "donorInstitutionOrgansTbl");
defined("FILE_ACCESS_INSTITUTION_TBL") or define("FILE_ACCESS_INSTITUTION_TBL", "fileAccessInstitutionTbl");
defined("FILE_CATEGORY_MST") or define("FILE_CATEGORY_MST", "fileCategoryMst");
defined("FILE_DOWNLOAD_LOG_TBL") or define("FILE_DOWNLOAD_LOG_TBL", "fileDownLoadLogTbl");
defined("FOLDER_CATEGORY_MANAGEMENT_TBL") or define("FOLDER_CATEGORY_MANAGEMENT_TBL", "folderCategoryManagementTbl");
defined("FOLDER_MST") or define("FOLDER_MST", "folderMst");
defined("INSTITUTION_MST") or define("INSTITUTION_MST", "institutionMst");
defined("INSTITUTION_ORGAN_TBL") or define("INSTITUTION_ORGAN_TBL", "institutionRelationOrgansTbl");
defined("INTERNAL_ORGAN_MST") or define("INTERNAL_ORGAN_MST", "internalOrgansMst");
defined("ISHOKUGO_KEIKA") or define("ISHOKUGO_KEIKA", "T_ISHOKUGO_KEIKA");
defined("M_CD") or define("M_CD", "M_CD");
defined("PASSWORD_HISTORY_TBL") or define("PASSWORD_HISTORY_TBL", "passwordHistoryTbl");
defined("PREF_MST") or define("PREF_MST", "prefMst");
defined("STT_MST") or define("STT_MST", "statusMst");
defined("T_DONOR") or define("T_DONOR", "T_DONOR");
defined("T_GAPPEI") or define("T_GAPPEI", "T_GAPPEI");
defined("T_ISHOKUGO_KEIKA") or define("T_ISHOKUGO_KEIKA", "T_ISHOKUGO_KEIKA");
defined("T_KENSA") or define("T_KENSA", "T_KENSA");
defined("T_LIVING") or define("T_LIVING", "T_LIVING");
defined("T_REJECTION") or define("T_REJECTION", "T_REJECTION");
defined("TEMP_UPFILE_TBL") or define("TEMP_UPFILE_TBL", "tmpUpFileTbl");
defined("UPFILE_TBL") or define("UPFILE_TBL", "upFileTbl");
defined("USER_MST") or define("USER_MST", "userMst");
defined("WORK_MST") or define("WORK_MST", "workMst");

/*
|--------------------------------------------------------------------------
| Database value
|--------------------------------------------------------------------------
 */

/* Account using status */
defined("IN_USE_FLG") or define("IN_USE_FLG", 0);
defined("DELETED_FLG") or define("DELETED_FLG", 1);
/* Co区分 (cordinatorTypeMst) */
defined("NW_CO") or define("NW_CO", 1);
/* 所属 (affiliationMst) */
defined("AFF_NW") or define("AFF_NW", 1);
/* Account type (accountTypeMst) */
defined("ACC_TYPE_CO") or define("ACC_TYPE_CO", 1);
defined("ACC_TYPE_TP") or define("ACC_TYPE_TP", 3);
/* Account status id (statusMst) */
defined("ACC_STT_REG") or define("ACC_STT_REG", 1);
defined("ACC_STT_CONF") or define("ACC_STT_CONF", 3);
/* Account status name */
defined("ACC_STT_NAME") or define("ACC_STT_NAME", array(
    ACC_STT_REG  => "仮登録",
    ACC_STT_CONF => "本登録",
));
/* Institution kubun */
defined("INSTITUTION_KUBUN_TRANSPLANT") or define("INSTITUTION_KUBUN_TRANSPLANT", "1");
defined("INSTITUTION_KUBUN_TRANSFER") or define("INSTITUTION_KUBUN_TRANSFER", "2");
defined("INSTITUTION_KUBUN") or define("INSTITUTION_KUBUN", array(
    INSTITUTION_KUBUN_TRANSPLANT => "移植施設",
    INSTITUTION_KUBUN_TRANSFER   => "転院先",
));
/* Work id (workMst) */
defined("WORK_DDDS") or define("WORK_DDDS", 1);
defined("WORK_FOLLOW_UP") or define("WORK_FOLLOW_UP", 2);
/* Sex */
defined("MALE") or define("MALE", 1);
defined("FEMALE") or define("FEMALE", 2);
defined("SEX") or define("SEX", array(
    MALE   => "男性",
    FEMALE => "女性",
));
defined("SEX_SHORT") or define("SEX_SHORT", array(
    MALE   => "男",
    FEMALE => "女",
));
/* Admin flag */
defined("NOT_ADMIN") or define("NOT_ADMIN", 0);
defined("IS_ADMIN") or define("IS_ADMIN", 1);
/* Doctor type */
defined("DOCTOR_TYPE") or define("DOCTOR_TYPE", array(
    NOT_ADMIN => "担当者",
    IS_ADMIN  => "管理者",
));
/* Cordinator type */
defined("CO_TYPE") or define("CO_TYPE", array(
    NOT_ADMIN => "一般",
    IS_ADMIN  => "管理",
));

/* PDFLib */
defined('ADD_TBL_OPTS_DEFAULT') or define('ADD_TBL_OPTS_DEFAULT', array(
    "colNum"   => 2,
    "rowNum"   => 2,
    "colWidth" => array(
        "header" => array("50%", "50%"),
        "body"   => array("50%", "50%"),
    ),
    "colSpan"  => array(
        "header" => array(1, 1),
        "body"   => array(1, 1),
    ),
    "llx"      => 0,
    "lly"      => 0,
    "urx"      => 500,
    "ury"      => 300,
));

defined('ADD_TBL_DATA_DEFAULT') or define('ADD_TBL_DATA_DEFAULT', array(
    array("No.", "内容"),
    array("1", "テスト"),
));

defined('ADD_LINE_POINT_DEFAULT') or define('ADD_LINE_POINT_DEFAULT', array("x" => 0, "y" => 0));

defined('FONT_REGULAR_10') or define('FONT_REGULAR_10', "fontname=NotoSansCJKjp-Regular encoding=unicode fontsize=10");
defined('FONT_BOLD_10') or define('FONT_BOLD_10', "fontname=NotoSansCJKjp-Bold encoding=unicode fontsize=10");
defined('FONT_BOLD_12') or define('FONT_BOLD_12', "fontname=NotoSansCJKjp-Bold encoding=unicode fontsize=12");
defined('FONT_BOLD_15') or define('FONT_BOLD_15', "fontname=NotoSansCJKjp-Bold encoding=unicode fontsize=15");
defined('FONT_BOLD_20') or define('FONT_BOLD_20', "fontname=NotoSansCJKjp-Bold encoding=unicode fontsize=20");
defined('LINE_HEIGHT') or define('LINE_HEIGHT', 15);

defined('ORGAN_HEART') or define('ORGAN_HEART', 1);
defined('ORGAN_LUNG') or define('ORGAN_LUNG', 2);
defined('ORGAN_LIVER') or define('ORGAN_LIVER', 3);
defined('ORGAN_KIDNEY') or define('ORGAN_KIDNEY', 4);
defined('ORGAN_PANCREAS') or define('ORGAN_PANCREAS', 5);
defined('ORGAN_SMALL_INTENSTINE') or define('ORGAN_SMALL_INTENSTINE', 6);

defined('ORGAN') or define('ORGAN', array(
    ORGAN_HEART            => "心臓",
    ORGAN_LUNG             => "肺",
    ORGAN_LIVER            => "肝臓",
    ORGAN_KIDNEY           => "腎臓",
    ORGAN_PANCREAS         => "膵臓",
    ORGAN_SMALL_INTENSTINE => "小腸",
));

defined('M_CD_CODE_TYPE_189_VALUE_HAVE') or define('M_CD_CODE_TYPE_189_VALUE_HAVE', '有');
defined('M_CD_CODE_TYPE_189_VALUE_NOT_HAVE') or define('M_CD_CODE_TYPE_189_VALUE_NOT_HAVE', '無');

defined('SIMULTANEOUS_TRANSPLANTATION') or define('SIMULTANEOUS_TRANSPLANTATION', array(
    "心肺" => "心,肺", // Cardiopulmonary
    "膵腎" => "膵,腎", // Mutton
    "肝腎" => "肝,腎", // Liver and kidney
    "肝小" => "肝,小", // Small liver
));
defined('USER_TYPE') or define('USER_TYPE', array(
    0 => '本部', // Headquarters user
    1 => '移植施設', // Transplant facility user
));
defined('TIME_CHAR') or define('TIME_CHAR', array(
    'M' => 'カ月',
    'Y' => '年',
));

// Rejection type
defined('REJECTION_COMMON') or define('REJECTION_COMMON', 1);
/* 急性拒否反 */
defined('REJECTION_ACUTE') or define('REJECTION_ACUTE', 1);
/* 慢性拒否反応 */
defined('REJECTION_CHRONIC') or define('REJECTION_CHRONIC', 2);

// Hyphen character (-)
defined('HYPHEN_CHAR') or define('HYPHEN_CHAR', '-');

// Code type
defined('CODE_TYPE') or define('CODE_TYPE', array(
    'ORGAN'                               => '001',
    'SEX'                                 => '002',
    'ORIGINAL_DISEASE'                    => '017',
    'PATIENT_OUTCOME'                     => '077',
    'CAUSE_OF_DEATH_MAJOR'                => '078',
    'CAUSE_OF_DEATH_SUBCLASS'             => '079',
    'THERAPEUTIC_EFFECT_HEART'            => '216',
    'THERAPEUTIC_EFFECT_OTHER'            => '098',
    'GRADE_A'                             => '099',
    'GRADE_B'                             => '100',
    'GRADE_C'                             => '101',
    'GRADE_D'                             => '102',
    'CHRONIC_REJECTION_STAGE'             => '103',
    'CHRONIC_REJECTION_AB'                => '104',
    'ORGAN_OUTCOME'                       => '080',
    'CAUSE_OF_ABOLITION'                  => '086',
    'PATIENT_OUTCOME_DETAILS'             => '081',
    'TREATMENT_METHOD'                    => '097',
    'DIALYSIS_WITHDRAWAL'                 => '087',
    'DIALYSIS_CAUSES_OF_DIALYSIS_FAILURE' => '088',
    'CYCLE'                               => '105',
    'LIVING_CONDITIONS_REPORT_FORM'       => '217',
    'DONOR_SEX'                           => '145',
    'ORGAN_DONATION_STATUS'               => '082',
    'COMPLICATIONS'                       => array(
        ORGAN_HEART            => '090',
        ORGAN_LUNG             => '091',
        ORGAN_LIVER            => '092',
        ORGAN_KIDNEY           => '093',
        ORGAN_PANCREAS         => '094',
        ORGAN_SMALL_INTENSTINE => '095',
    ),
    'REHABILITATION'                      => array(
        ORGAN_HEART            => '212',
        ORGAN_LUNG             => '213',
        ORGAN_LIVER            => '214',
        ORGAN_KIDNEY           => '215',
        ORGAN_PANCREAS         => '215',
        ORGAN_SMALL_INTENSTINE => '215',
    ),
    'PORTING_CONTENT'                     => array(
        ORGAN_HEART            => "229",
        ORGAN_LUNG             => "072",
        ORGAN_LIVER            => "073",
        ORGAN_KIDNEY           => "075",
        ORGAN_PANCREAS         => "076",
        ORGAN_SMALL_INTENSTINE => "230",
    ),
));

// Organ and patient outcome code
defined('ORGAN_OUTCOME_ENGRAFTMENT_CODE') or define('ORGAN_OUTCOME_ENGRAFTMENT_CODE', '1');
defined('PATIENT_OUTCOME_CODE') or define('PATIENT_OUTCOME_CODE', array(
    'SURVIVE' => '1',
    'DEATH'   => '4',
));

defined('INSPECTION_INPUT_STATUS') or define('INSPECTION_INPUT_STATUS', '入力状況');
defined('INSPECTION_DATE') or define('INSPECTION_DATE', '検査日');
defined('INPUT_STATUS_UNFINISHED') or define('INPUT_STATUS_UNFINISHED', '未完了');
defined('INPUT_STATUS_DONE') or define('INPUT_STATUS_DONE', '完了');

/**
 * Inspection row data for each organ
 * value 0 => name
 * value 1 => unit
 * value 2 => isDropdown
 * value 3 => option of dropdown
 */
defined('INSPECTIONS_ROWS_DATA') or define('INSPECTIONS_ROWS_DATA', array(
    ORGAN_HEART            => array(
        array(INSPECTION_INPUT_STATUS, '', true, array(
            INPUT_STATUS_UNFINISHED => INPUT_STATUS_UNFINISHED,
            INPUT_STATUS_DONE       => INPUT_STATUS_DONE,
        )),
        array(INSPECTION_DATE, '', false),
        array('FS', '％', false),
        array('EF', '％', false),
        array('NYHA', '', true, array(
            ''  => '',
            '1' => 'I',
            '2' => 'II',
            '3' => 'III',
            '4' => 'IV',
        )),
        array('拒絶分類', '', true, array(
            ''  => '',
            '1' => '0',
            '2' => '1A',
            '3' => '1B',
            '4' => '2',
            '5' => '3A',
            '6' => '3B',
            '7' => '4',
        )),
    ),
    ORGAN_LUNG             => array(
        array(INSPECTION_INPUT_STATUS, '', true, array(
            INPUT_STATUS_UNFINISHED => INPUT_STATUS_UNFINISHED,
            INPUT_STATUS_DONE       => INPUT_STATUS_DONE,
        )),
        array(INSPECTION_DATE, '', false),
        array('体重', 'kg', false),
        array('Hugh-Jones分類', '', true, array(
            ''  => '',
            '1' => 'I',
            '2' => 'II',
            '3' => 'III',
            '4' => 'IV',
            '5' => 'V',
            '6' => '評価不能',
        )),
        array('NYHA分類', '', true, array(
            ''  => '',
            '1' => 'I',
            '2' => 'II',
            '3' => 'III',
            '4' => 'IV',
        )),
        array('PaO2', 'Torr', false),
        array('PaCO2', 'Torr', false),
        array('VC', 'ml', false),
        array('FEV1', 'ml', false),
        array('クレアチニン', 'mg/dl', false),
        array('CTR', '%', false),
        array('Pam', 'mmHg', false),
        array('CI', 'l/min/m2', false),
        array('6分間歩行距離', 'm', false),
    ),
    ORGAN_LIVER            => array(
        array(INSPECTION_INPUT_STATUS, '', true, array(
            INPUT_STATUS_UNFINISHED => INPUT_STATUS_UNFINISHED,
            INPUT_STATUS_DONE       => INPUT_STATUS_DONE,
        )),
        array(INSPECTION_DATE, '', false),
        array('AST', 'IU/I', false),
        array('ALT', 'IU/I', false),
        array('総ピリルピン', 'mg/dl', false),
        array('HPT', '%', false),
        array('アルブミン', 'g/dl', false),
        array('クレアチニン', 'mg/dl', false),
        array('空腹時血糖値', 'mg/dl', false),
        array('HbA1c', '%', false),
        array('体重', 'kg', false),
    ),
    ORGAN_KIDNEY           => array(
        array(INSPECTION_INPUT_STATUS, '', true, array(
            INPUT_STATUS_UNFINISHED => INPUT_STATUS_UNFINISHED,
            INPUT_STATUS_DONE       => INPUT_STATUS_DONE,
        )),
        array(INSPECTION_DATE, '', false),
        array('クレアチニン', 'mg/dl', false),
        array('尿蛋白(定性)', '', true, array(
            ''  => '',
            '1' => '+++',
            '2' => '++',
            '3' => '+',
            '4' => '±',
            '5' => '-',
        )),
        array('尿蛋白(定量)', 'g/d', false),
    ),
    ORGAN_PANCREAS         => array(
        array(INSPECTION_INPUT_STATUS, '', true, array(
            INPUT_STATUS_UNFINISHED => INPUT_STATUS_UNFINISHED,
            INPUT_STATUS_DONE       => INPUT_STATUS_DONE,
        )),
        array(INSPECTION_DATE, '', false),
        array('空腹時血糖', 'mg/dl', false),
        array('HbA1c', '％', false),
        array('空腹時IRI', 'μU/ml', false),
        array('CPR-前地', 'ng/ml', false),
        array('CPRグルカゴン負荷値', 'ng/ml', false),
        array('インスリン投与量', 'U/日', false),
        array('クレアチニン', 'mg/dl', false),
        array('尿蛋白(定性)', '', true, array(
            ''  => '',
            '1' => '+++',
            '2' => '++',
            '3' => '+',
            '4' => '±',
            '5' => '-',
        )),
        array('尿蛋白(定量)', 'g/d', false),
        array('体重', 'kg', false),
    ),
    ORGAN_SMALL_INTENSTINE => array(
        array(INSPECTION_INPUT_STATUS, '', true, array(
            INPUT_STATUS_UNFINISHED => INPUT_STATUS_UNFINISHED,
            INPUT_STATUS_DONE       => INPUT_STATUS_DONE,
        )),
        array(INSPECTION_DATE, '', false),
        array('体重', 'kg', false),
        array('アルブミン値', 'g/dl', false),
        array('高カロリー輸液', '', true, array(
            ''  => '',
            '有' => '有',
            '無' => '無',
        )),
        array('経腸栄養', '', true, array(
            ''  => '',
            '有' => '有',
            '無' => '無',
        )),
        array('補液', '', true, array(
            ''  => '',
            '有' => '有',
            '無' => '無',
        )),
        array('クレアチニン', 'mg/dl', false),
    ),
));

/*
|--------------------------------------------------------------------------
| PROGRAM_ID in database
|--------------------------------------------------------------------------
 */
defined('PROGRAM_ID') or define('PROGRAM_ID', array(
    6  => 'I0001', //csv import
    7  => 'I0002', //search
    8  => 'I0002', //csv all
    9  => 'I0002', //csv basic
    10 => 'I0002', // pdf list
    11 => 'I0002', //pdf entry
    12 => 'I0002', //pdf info
    13 => 'I0003', //detail
    14 => 'I0004', //popup
    15 => 'I0005', //mail
));
