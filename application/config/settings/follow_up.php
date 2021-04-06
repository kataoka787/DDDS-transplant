<?php defined("BASEPATH") or exit("No direct script access allowed");

/* Japanese table name */
$config["japanese_table_name"] = array(
    "Tishokugokeika" => "移植後経過情報",
    "Tkensa" => "検査項目",
    "Tgappei" => "入院を要する合併症",
    "Tliving" => "生活状況",
    "Trejection" => array(
        0 => "拒絶反応",
        REJECTION_ACUTE => "急性拒絶反応",
        REJECTION_CHRONIC => "慢性拒絶反応",
    ),
);

/* Should update tables and their corresponding key (in post data) */
$config["should_update_tables"] = array(
    "Tishokugokeika" => null,
    "Tkensa" => "inspectionValue",
    "Tgappei" => "complications",
    "Tliving" => "livingConditions",
    "Trejection" => "rejection",
);

/* Detail screen database column <=> input name definition */
$config["Tishokugokeika_input_name_convert_def"] = array(
    "ZAITAKUSANSORYOHO_START_DATE" => array("formatDate", "dateOfIntroduction"),
    "ZAITAKUSANSORYOHO_END_DATE" => array("formatDate", "withdrawalDate"),
    "ISHOKUGO_KEIKAJYOUHOU_SISETU_CD" => "postTransplant",
    "TOSEKIRIDATU" => "dialysisWithdrawal",
    "TOSEKIRIDATU_FUNOGENIN" => "causesOfDialysisFailure",
    "TOSEKI_LAST_DATE" => array("formatDate", "finalDialysisDay"),
    "INSULIN_FLG" => "insulinTreatment",
    "INSULIN_LAST_DATE" => array("formatDate", "lastAdministrationDate"),
    "RECIPIENT_TENKI" => "patientOutcome",
    "ZOKI_TENKI" => "organOutcome",
    "RECIPIENT_TENKI_DETAIL" => "patientOutcomeDetails",
    "SIBO_DATE" => array("formatDate", "dayOfDeath"),
    "RECIPENT_TENKI_CMNT" => "organOutcomeComment",
    "TAIIN_DATE" => array("formatDate", "dateOfDischarge"),
    "FINAL_LIV_DATE" => array("formatDate", "finalLivDate"),
    "KINOHAIZETU_DATE" => array("formatDate", "abolitionDate"),
    "ZOKI_TENKI_GENIN" => "causeOfAbolition",
    "ZOKI_TENKI_CMNT" => "comment",
    "SYAKAIFUKKI" => "rehabilitationStatus",
    "SYAKAIFUKKI_DATE" => array("formatDate", "rehabilitationDate"),
    "SYAKAIFUKKI_NAIYO" => "rehabilitationComment",
);

$config["Trejection_input_name_convert_def"] = array(
    "REJECTION_TYPE" => "type",
    "SINDAN_DATE" => array("formatDate", "diagnosisDate"),
    "ORIGINAL_SINDAN_DATE" => array("formatDate", "originalDiagnosisDate"),
    "TIRYOU_SYUDAN" => "treatmentMethod",
    "TIRYOU_KOKA" => "therapeuticEffect",
    "SHOULD_DELETE" => "isDeleted",
    "SHOULD_UPDATE" => "shouldUpdate",
    "ORDINAL" => "ordinal",
);

$config["Trejection_lung_input_name_convert_def"] = array(
    "GRADEA" => "gradeA",
    "GRADEB" => "gradeB",
    "GRADEC" => "gradeC",
    "GRADED" => "gradeD",
    "STAGE" => "stage",
    "A_B" => "ab",
);

$config["Tkensa_input_name_convert_def"] = array(
    "KENSA_NAME" => "name",
    "KENSA_UNIT" => "unit",
    "DSPNO" => "dspno",
    "KENSA_VALUE_M1" => "M1",
    "KENSA_VALUE_M3" => "M3",
    "KENSA_VALUE_M6" => "M6",
    "KENSA_VALUE_01" => "01",
    "KENSA_VALUE_02" => "02",
    "KENSA_VALUE_03" => "03",
    "KENSA_VALUE_04" => "04",
    "KENSA_VALUE_05" => "05",
    "KENSA_VALUE_06" => "06",
    "KENSA_VALUE_07" => "07",
    "KENSA_VALUE_08" => "08",
    "KENSA_VALUE_09" => "09",
    "KENSA_VALUE_10" => "10",
    "KENSA_VALUE_11" => "11",
    "KENSA_VALUE_12" => "12",
    "KENSA_VALUE_13" => "13",
    "KENSA_VALUE_14" => "14",
    "KENSA_VALUE_15" => "15",
    "KENSA_VALUE_16" => "16",
    "KENSA_VALUE_17" => "17",
    "KENSA_VALUE_18" => "18",
    "KENSA_VALUE_19" => "19",
    "KENSA_VALUE_20" => "20",
    "KENSA_VALUE_21" => "21",
    "KENSA_VALUE_22" => "22",
    "KENSA_VALUE_23" => "23",
    "KENSA_VALUE_24" => "24",
    "KENSA_VALUE_25" => "25",
    "KENSA_VALUE_26" => "26",
    "KENSA_VALUE_27" => "27",
    "KENSA_VALUE_28" => "28",
    "KENSA_VALUE_29" => "29",
    "KENSA_VALUE_30" => "30",
    "KENSA_VALUE_31" => "31",
    "KENSA_VALUE_32" => "32",
    "KENSA_VALUE_33" => "33",
    "KENSA_VALUE_34" => "34",
    "KENSA_VALUE_35" => "35",
);

$config["Tgappei_input_name_convert_def"] = array(
    "GAPPEI" => "type",
    "ORIGINAL_GAPPEI" => "originalType",
    "NYUIN_DATE" => array("formatDate", "dateOfHospitalization"),
    "ORIGINAL_NYUIN_DATE" => "originalDateOfHospitalization",
    "TAIIN_DATE" => array("formatDate", "dischargeDate"),
    "CMNT" => "comment",
    "SHOULD_DELETE" => "isDeleted",
    "SHOULD_UPDATE" => "shouldUpdate",
    "ORDINAL" => "ordinal",
);

$config["Tliving_input_name_convert_def"] = array(
    "INPUT_DATE" => array("formatDate", "recordingDate"),
    "ORIGINAL_INPUT_DATE" => array("formatDate", "originalRecordingDate"),
    "KAKUNIN_USER_NAME" => "confirmer",
    "REPORT_USER_NAME" => "reporter",
    "REPORT_FORM" => "reportForm",
    "LIVING_NAIYO" => "content",
    "CYCLE" => "cycle",
    "SHOULD_DELETE" => "isDeleted",
    "SHOULD_UPDATE" => "shouldUpdate",
    "ORDINAL" => "ordinal",
);

/* Follow up detail screen settings */
$config["inspection_max_table_column"] = 9;
$config["inspection_max_cycle_column"] = 38;

/* Max cycle year */
$config['max_cycle_year'] = '35';

/* 免疫抑制剤 */
$config["immunosuppressant_drugs"] = array(
    "maintenance" => array(
        "CsA" => "IJI_CSA",
        "TAC" => "IJI_TAC",
        "PS" => "IJI_PS",
        "MMF" => "IJI_MMF",
        "AZ" => "IJI_AZ",
        "MZ" => "IJI_MZ",
        "EVL" => "IJI_EVL",
    ),
    "introduction" => array(
        "CsA" => "DONYU_CSA",
        "TAC" => "DONYU_TAC",
        "PS" => "DONYU_PS",
        "MMF" => "DONYU_MMF",
        "Bas" => "DONYU_BAS",
        "ATG" => "DONYU_ATG",
        "AZ" => "DONYU_AZ",
        "MZ" => "DONYU_MZ",
        "EVL" => "DONYU_EVL",
        "DSG" => "DONYU_DSG",
        "ALG*" => "DONYU_ALG",
        "OKT3*" => "DONYU_OKT3",
    ),
);

/* Max search result of search screen (follow up) */
$config['max_search_result'] = 100;
