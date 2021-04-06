<?php
$ci = &get_instance();
$ci->load->model("Formvalidation");

$config = array(
    'login' => array(
        array(
            'field' => 'id',
            'label' => 'メールアドレス',
            'rules' => 'required',
        ),
        array(
            'field' => 'pw',
            'label' => 'パスワード',
            'rules' => 'required',
        ),
    ),
    'password' => array(
        array(
            'field' => 'pw',
            'label' => '新パスワード',
            'rules' => 'required|alpha_numeric|min_length[8]|max_length[16]|matches[repw]|not_recent_password[account_id]|secured_password',
        ),
        array(
            'field' => 'repw',
            'label' => '再確認用パスワード',
            'rules' => 'required|alpha_numeric|min_length[8]|max_length[16]',
        ),
    ),
    'reminder' => array(
        array(
            'field' => 'id',
            'label' => 'メールアドレス',
            'rules' => 'required|valid_email|is_registered',
        ),
    ),
    'donor' => array(
        array(
            'field' => 'offerInstitution',
            'label' => '提供施設',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'offerInstitutionPref',
            'label' => '提供施設都道府県',
            'rules' => array(
                array("pref_id", array($ci->Formvalidation, "pref_id")),
            ),
        ),
        array(
            'field' => 'firstName',
            'label' => '氏',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'secondName',
            'label' => '名',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'age',
            'label' => '年齢',
            'rules' => 'required|is_natural',
        ),
        array(
            'field' => 'sex',
            'label' => '性別',
            'rules' => array(
                array("sex_id", array($ci->Formvalidation, "sex_id")),
            ),
        ),
        array(
            'field' => 'deathReasonMstId',
            'label' => '脳死/心停止',
            'rules' => array(
                array("death_reason_id", array($ci->Formvalidation, "death_reason_id")),
            ),
        ),
        array(
            'field' => 'message',
            'label' => '連絡事項',
            'rules' => 'max_length[1000]',
        ),
    ),
    'upload' => array(
        array(
            'field' => 'folder',
            'label' => 'フォルダ',
            'rules' => 'required|callback_folder_check',
        ),
        array(
            'field' => 'category',
            'label' => 'ファイルカテゴリ',
            'rules' => 'required|callback_category_check',
        ),
        array(
            'field' => 'memo',
            'label' => 'ファイルカテゴリメモ',
            'rules' => 'max_length[80]|memo_check',
        ),
    ),
    'donor/search' => array(
        array(
            'field' => 'block_id',
            'label' => 'ブロック',
            'rules' => 'block_id',
        ),
        array(
            'field' => 'offer_institution',
            'label' => '病院名',
            'rules' => 'max_length[80]',
        ),
        array(
            'field' => 'sex',
            'label' => '性別',
            'rules' => 'sex_id',
        ),
        array(
            'field' => 'age',
            'label' => '年齢',
            'rules' => 'is_natural',
        ),

    ),
    'donor/edit' => array(
        array(
            'field' => 'offerInstitution',
            'label' => '提供施設',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'offerInstitutionPref',
            'label' => '提供施設都道府県',
            'rules' => array(
                array("pref_id", array($ci->Formvalidation, "pref_id")),
            ),
        ),
        array(
            'field' => 'firstName',
            'label' => '氏',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'secondName',
            'label' => '名',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'age',
            'label' => '年齢',
            'rules' => 'required|is_natural',
        ),
        array(
            'field' => 'sex',
            'label' => '性別',
            'rules' => array(
                array("sex_id", array($ci->Formvalidation, "sex_id")),
            ),
        ),
        array(
            'field' => 'deathReasonMstId',
            'label' => '脳死/心停止',
            'rules' => array(
                array("death_reason_id", array($ci->Formvalidation, "death_reason_id")),
            ),
        ),
        array(
            'field' => 'message',
            'label' => '連絡事項',
            'rules' => 'max_length[1000]',
        ),
    ),
    'transplant/request' => array(
        array(
            'field' => 'organs',
            'label' => '臓器',
            'rules' => array(
                array("organ_id", array($ci->Formvalidation, "organ_id")),
            ),
        ),
        array(
            'field' => 'pref',
            'label' => '都道府県',
            'rules' => array(
                array("pref_id", array($ci->Formvalidation, "pref_id")),
            ),
        ),
        array(
            'field' => 'institution',
            'label' => '施設',
            'rules' => array(
                array("institution_id", array($ci->Formvalidation, "institution_id")),
            ),
        ),
        array(
            'field' => 'user[]',
            'label' => 'ユーザ',
            'rules' => array(
                array("doctor_account_id", array($ci->Formvalidation, "doctor_account_id")),
            ),
        ),
        array(
            'field' => 'files[]',
            'label' => '依頼ファイル',
            'rules' => array(
                array("request_file_id", array($ci->Formvalidation, "request_file_id")),
            ),
        ),
    ),
    'admin/cordinator/search' => array(
        array(
            'field' => 'sei_kana',
            'label' => '姓(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => '名(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'mail',
            'label' => 'メールアドレス',
            'rules' => 'valid_email',
        ),
    ),
    'admin/cordinator/edit' => array(
        array(
            'field' => 'sei',
            'label' => '氏名 姓',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'mei',
            'label' => '氏名 名',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'sei_kana',
            'label' => 'フリガナ セイ',
            'rules' => 'required|max_length[80]|katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => 'フリガナ メイ',
            'rules' => 'required|max_length[80]|katakana',
        ),
        array(
            'field' => 'mail',
            'label' => 'メールアドレス(携帯)',
            'rules' => 'required|valid_email|cordinator_email',
        ),
        array(
            'field' => 'password',
            'label' => 'パスワード',
            'rules' => 'alpha_numeric|min_length[8]|max_length[16]|not_recent_password[account_id]|secured_password',
        ),
        array(
            'field' => 'admin_flg',
            'label' => '権限',
            'rules' => array(
                array("permission", array($ci->Formvalidation, "permission")),
            ),
        ),
        array(
            'field' => 'works[]',
            'label' => '業務権限',
            'rules' => array(
                array("work_id", array($ci->Formvalidation, "work_id")),
            ),
        ),
    ),
    'admin/cordinator/new' => array(
        array(
            'field' => 'sei',
            'label' => '氏名 姓',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'mei',
            'label' => '氏名 名',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'sei_kana',
            'label' => 'フリガナ セイ',
            'rules' => 'required|max_length[80]|katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => 'フリガナ メイ',
            'rules' => 'required|max_length[80]|katakana',
        ),
        array(
            'field' => 'mail',
            'label' => 'メールアドレス(携帯)',
            'rules' => 'required|valid_email|cordinator_email',
        ),
        array(
            'field' => 'password',
            'label' => 'パスワード',
            'rules' => 'required|alpha_numeric|min_length[8]|max_length[16]|secured_password',
        ),
        array(
            'field' => 'admin_flg',
            'label' => '権限',
            'rules' => array(
                array("permission", array($ci->Formvalidation, "permission")),
            ),
        ),
        array(
            'field' => 'works[]',
            'label' => '業務権限',
            'rules' => array(
                array("work_id", array($ci->Formvalidation, "work_id")),
            ),
        ),
    ),
    'admin/transplant/search' => array(
        array(
            'field' => 'organs[]',
            'label' => '臓器',
            'rules' => 'organ_id',
        ),
        array(
            'field' => 'pref_id',
            'label' => '都道府県',
            'rules' => 'pref_id',
        ),
        array(
            'field' => 'institution',
            'label' => '施設名',
            'rules' => 'max_length[80]',
        ),
    ),
    'admin/transplant/edit' => array(
        array(
            'field' => 'organs[]',
            'label' => '臓器',
            'rules' => array(
                array("organ_id", array($ci->Formvalidation, "organ_id")),
            ),
        ),
        array(
            'field' => 'pref_id',
            'label' => '都道府県',
            'rules' => array(
                array("pref_id", array($ci->Formvalidation, "pref_id")),
            ),
        ),
        array(
            'field' => 'institution',
            'label' => '移植施設名',
            'rules' => 'required|max_length[80]|institution_unique_name',
        ),
        array(
            'field' => 'institution_kubun',
            'label' => '施設区分',
            'rules' => array(
                array("kubun_id", array($ci->Formvalidation, "kubun_id")),
            ),
        ),
        array(
            'field' => 'institution_code',
            'label' => '施設コード',
            'rules' => 'required|max_length[6]|institution_unique_code|foreign_key_of_ishokugo_keika',
        ),
    ),
    'admin/transplantUser/search' => array(
        array(
            'field' => 'pref_id',
            'label' => '都道府県',
            'rules' => "pref_id",
        ),
        array(
            'field' => 'institution',
            'label' => '施設名',
            'rules' => 'institution_id',
        ),
        array(
            'field' => 'sei_kana',
            'label' => '姓(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => '名(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'organs[]',
            'label' => '臓器',
            'rules' => "organ_id",
        ),
        array(
            'field' => 'status',
            'label' => 'ステータス',
            'rules' => 'status_id',
        ),
    ),
    'admin/transplantUser/edit' => array(
        array(
            'field' => 'pref_id',
            'label' => '都道府県',
            'rules' => array(
                array("pref_id", array($ci->Formvalidation, "pref_id")),
            ),
        ),
        array(
            'field' => 'institution',
            'label' => '施設名',
            'rules' => array(
                array("institution_id", array($ci->Formvalidation, "institution_id")),
            ),
        ),
        array(
            'field' => 'organs[]',
            'label' => '臓器',
            'rules' => array(
                array("doctor_organ_id", array($ci->Formvalidation, "doctor_organ_id")),
            ),
        ),
        array(
            'field' => 'doctor_type_id',
            'label' => '利用者権限',
            'rules' => array(
                array("permission", array($ci->Formvalidation, "permission")),
            ),
        ),
        array(
            'field' => 'works[]',
            'label' => '業務権限',
            'rules' => array(
                array("doctor_work_id", array($ci->Formvalidation, "doctor_work_id")),
            ),
        ),
        array(
            'field' => 'sei',
            'label' => '氏名 姓',
            'rules' => 'required|max_length[80]|doctor_name',
        ),
        array(
            'field' => 'mei',
            'label' => '氏名 名',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'sei_kana',
            'label' => 'フリガナ セイ',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'mei_kana',
            'label' => 'フリガナ メイ',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'mail',
            'label' => 'メールアドレス',
            'rules' => 'required|valid_email|doctor_email',
        ),
        array(
            'field' => 'password',
            'label' => 'パスワード',
            'rules' => 'password_inputable|alpha_numeric|min_length[8]|max_length[16]|not_recent_password[account_id]|secured_password',
        ),
    ),
    'admin/accountHistory/search' => array(
        array(
            'field' => 'kbn',
            'label' => 'ユーザ区分',
            'rules' => "account_type_id",
        ),
        array(
            'field' => 'sei_kana',
            'label' => '姓(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => '名(カナ)',
            'rules' => 'katakana',
        ),
    ),
    'admin/accesslog/search' => array(
        array(
            'field' => 'kbn',
            'label' => 'ユーザ区分',
            'rules' => 'account_type_id',
        ),
        array(
            'field' => 'sei_kana',
            'label' => '姓(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => '名(カナ)',
            'rules' => 'katakana',
        ),
    ),
    "transplant/doctor/search" => array(
        array(
            'field' => 'sei_kana',
            'label' => '姓(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'mei_kana',
            'label' => '名(カナ)',
            'rules' => 'katakana',
        ),
        array(
            'field' => 'organs[]',
            'label' => '臓器',
            'rules' => 'organ_id',
        ),
    ),
    "transplant/doctor/edit" => array(
        array(
            'field' => 'institution',
            'label' => '施設名',
            'rules' => array(
                array("institution_id", array($ci->Formvalidation, "institution_id")),
            ),
        ),
        array(
            'field' => 'organs[]',
            'label' => '臓器',
            'rules' => array(
                array("doctor_organ_id", array($ci->Formvalidation, "doctor_organ_id")),
            ),
        ),
        array(
            'field' => 'doctor_type_id',
            'label' => '利用者権限',
            'rules' => array(
                array("permission", array($ci->Formvalidation, "permission")),
            ),
        ),
        array(
            'field' => 'works[]',
            'label' => '業務権限',
            'rules' => array(
                array("doctor_work_id", array($ci->Formvalidation, "doctor_work_id")),
            ),
        ),
        array(
            'field' => 'sei',
            'label' => '氏名 姓',
            'rules' => 'required|max_length[80]|doctor_name',
        ),
        array(
            'field' => 'mei',
            'label' => '氏名 名',
            'rules' => 'required|max_length[80]',
        ),
        array(
            'field' => 'sei_kana',
            'label' => 'フリガナ セイ',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'mei_kana',
            'label' => 'フリガナ メイ',
            'rules' => 'required|katakana|max_length[80]',
        ),
        array(
            'field' => 'mail',
            'label' => 'メールアドレス',
            'rules' => 'required|valid_email|doctor_email',
        ),
        array(
            'field' => 'password',
            'label' => 'パスワード',
            'rules' => 'alpha_numeric|min_length[8]|max_length[16]|not_recent_password[account_id]|secured_password',
        ),
    ),
    'detail/Tgappei' => array(
        array(
            'field' => 'RECIPIENT_ID',
            'label' => '登録者ID',
            'rules' => "required|max_length[7]",
        ),
        array(
            'field' => 'ZOKI_CODE',
            'label' => '移植臓器',
            'rules' => "required|max_length[1]",
        ),
        array(
            'field' => 'ISYOKU_CNT',
            'label' => '移植回数',
            'rules' => "required|max_length[2]",
        ),
        array(
            'field' => 'GAPPEI',
            'label' => '合併症',
            'rules' => 'required|max_length[2]',
        ),
        array(
            'field' => 'NYUIN_DATE',
            'label' => '入院日',
            'rules' => 'required|min_length[8]|max_length[8]',
        ),
        array(
            'field' => 'TAIIN_DATE',
            'label' => '退院日',
            'rules' => 'max_length[8]',
        ),
        array(
            'field' => 'CMNT',
            'label' => 'コメント',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'DEL_FLG',
            'label' => '削除フラグ',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'INS_USER_ID',
            'label' => '登録ユーザID',
            'rules' => 'required|max_length[10]',
        ),
        array(
            'field' => 'INS_PROGRAM_ID',
            'label' => '登録プログラムID',
            'rules' => 'required|max_length[20]',
        ),
        array(
            'field' => 'UPD_USER_ID',
            'label' => '更新ユーザID',
            'rules' => 'max_length[10]',
        ),
        array(
            'field' => 'UPD_PROGRAM_ID',
            'label' => '更新プログラムID',
            'rules' => 'max_length[20]',
        ),

    ),
    'detail/Tishokugokeika' => array(
        array(
            'field' => 'RECIPIENT_ID',
            'label' => '登録者ID',
            'rules' => 'required|max_length[7]',
        ),
        array(
            'field' => 'ZOKI_CODE',
            'label' => '移植臓器',
            'rules' => 'required|max_length[1]',
        ),
        array(
            'field' => 'ISYOKU_CNT',
            'label' => '移植回数',
            'rules' => 'required|integer|max_length[2]',
        ),
        array(
            'field' => 'ISHOKUGO_KEIKAJYOUHOU_SISETU_CD',
            'label' => '移植後経過情報管理施設コード',
            'rules' => 'max_length[6]',
        ),
        array(
            'field' => 'RECIPIENT_TENKI',
            'label' => '患者の転帰',
            'rules' => 'required|max_length[1]',
        ),
        array(
            'field' => 'RECIPIENT_TENKI_DETAIL',
            'label' => '患者の転帰詳細',
            'rules' => 'max_length[1]|single',
        ),
        array(
            'field' => 'SIBO_DATE',
            'label' => '死亡日',
            'rules' => 'min_length[8]|max_length[8]',
        ),
        array(
            'field' => 'SIIN_H',
            'label' => '死因１',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'SIIN_L',
            'label' => '死因２',
            'rules' => 'max_length[4]',
        ),
        array(
            'field' => 'RECIPENT_TENKI_CMNT',
            'label' => '患者の転帰のコメント',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'TAIIN_DATE',
            'label' => '退院年月日',
            'rules' => 'min_length[8]|max_length[8]',
        ),
        array(
            'field' => 'FINAL_LIV_DATE',
            'label' => '最終生存確認日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'ZOKI_TENKI',
            'label' => '臓器転帰',
            'rules' => 'required|max_length[1]',
        ),
        array(
            'field' => 'KINOHAIZETU_DATE',
            'label' => '廃絶日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'ZOKI_TENKI_GENIN',
            'label' => '廃絶原因',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'ZOKI_TENKI_CMNT',
            'label' => '臓器の転帰のコメント',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'DONYU_CSA',
            'label' => 'CsA（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_TAC',
            'label' => 'TAC（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_PS',
            'label' => 'PS（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_MMF',
            'label' => 'MMF（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_BAS',
            'label' => 'Bas（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_ATG',
            'label' => 'ATG（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_AZ',
            'label' => 'AZ（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_MZ',
            'label' => 'MZ（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_EVL',
            'label' => 'EVL（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_DSG',
            'label' => 'DSG（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_ALG',
            'label' => 'ALG（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_OKT3',
            'label' => 'OKT3（導入）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DONYU_ETC',
            'label' => '免疫抑制剤（導入）のその他',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'IJI_CSA',
            'label' => 'CsA（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_TAC',
            'label' => 'TAC（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_PS',
            'label' => 'PS（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_MMF',
            'label' => 'MMF（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_AZ',
            'label' => 'AZ（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_MZ',
            'label' => 'MZ（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_EVL',
            'label' => 'EVL（維持）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'IJI_ETC',
            'label' => '免疫抑制剤（維持）のその他',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'SYAKAIFUKKI',
            'label' => '社会復帰状況',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'SYAKAIFUKKI_DATE',
            'label' => '社会復帰日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'SYAKAIFUKKI_NAIYO',
            'label' => '社会復帰状況のコメント',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'TENKI',
            'label' => '転帰',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'GENSIKKAN',
            'label' => '原疾患',
            'rules' => 'max_length[5]',
        ),
        array(
            'field' => 'GENSIKKAN_DATE',
            'label' => '発症年月日',
            'rules' => 'min_length[8]|max_length[8]',
        ),
        array(
            'field' => 'GENSIKKAN_CMNT',
            'label' => '原疾患コメント',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'GENSIKKAN_H',
            'label' => '原疾患（大分類）',
            'rules' => 'max_length[50]',
        ),
        array(
            'field' => 'GENSIKKAN_L',
            'label' => '原疾患（小分類）',
            'rules' => 'max_length[50]',
        ),
        array(
            'field' => 'ZAITAKUSANSORYOHO_START_DATE',
            'label' => '導入年月日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'ZAITAKUSANSORYOHO_END_DATE',
            'label' => '離脱年月日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'TOSEKIRIDATU',
            'label' => '透析離脱',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'TOSEKIRIDATU_FUNOGENIN',
            'label' => '離脱不能原因',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'TOSEKI_LAST_DATE',
            'label' => '最終透析日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'INSULIN_FLG',
            'label' => 'インスリン治療',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'INSULIN_LAST_DATE',
            'label' => '最終投与日',
            'rules' => 'min_length[8]|max_length[8]|single',
        ),
        array(
            'field' => 'INS_USER_ID',
            'label' => '登録ユーザID',
            'rules' => 'required|max_length[10]',
        ),
        array(
            'field' => 'INS_PROGRAM_ID',
            'label' => '登録プログラムID',
            'rules' => 'required|max_length[20]',
        ),
        array(
            'field' => 'UPD_USER_ID',
            'label' => '更新ユーザID',
            'rules' => 'max_length[10]',
        ),
        array(
            'field' => 'UPD_PROGRAM_ID',
            'label' => '更新プログラムID',
            'rules' => 'max_length[20]',
        ),

    ),
    'detail/Tkensa' => array(
        array(
            'field' => 'RECIPIENT_ID',
            'label' => '登録者ID',
            'rules' => "required|max_length[7]",
        ),
        array(
            'field' => 'ZOKI_CODE',
            'label' => '移植臓器',
            'rules' => "required|max_length[1]",
        ),
        array(
            'field' => 'ISYOKU_CNT',
            'label' => '移植回数',
            'rules' => "required|integer|max_length[2]",
        ),
        array(
            'field' => 'KENSA_NAME',
            'label' => '検査項目',
            'rules' => 'required|max_length[20]',
        ),
        array(
            'field' => 'DSPNO',
            'label' => '検査項目',
            'rules' => 'required|integer|max_length[6]',
        ),
        array(
            'field' => 'KENSA_UNIT',
            'label' => '単位',
            'rules' => 'max_length[10]',
        ),
        array(
            'field' => 'KENSA_VALUE_M1',
            'label' => '検査値M1',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_M3',
            'label' => '検査値M3',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_M6',
            'label' => '検査値M6',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_01',
            'label' => '検査値01年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_02',
            'label' => '検査値02年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_03',
            'label' => '検査値03年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_04',
            'label' => '検査値04年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_05',
            'label' => '検査値05年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_06',
            'label' => '検査値06年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_07',
            'label' => '検査値07年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_08',
            'label' => '検査値08年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_09',
            'label' => '検査値09年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_10',
            'label' => '検査値10年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_11',
            'label' => '検査値11年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_12',
            'label' => '検査値12年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_13',
            'label' => '検査値13年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_14',
            'label' => '検査値14年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_15',
            'label' => '検査値15年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_16',
            'label' => '検査値16年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_17',
            'label' => '検査値17年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_18',
            'label' => '検査値18年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_19',
            'label' => '検査値19年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_20',
            'label' => '検査値20年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_21',
            'label' => '検査値21年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_22',
            'label' => '検査値22年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_23',
            'label' => '検査値23年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_24',
            'label' => '検査値24年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_25',
            'label' => '検査値25年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_26',
            'label' => '検査値26年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_27',
            'label' => '検査値27年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_28',
            'label' => '検査値28年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_29',
            'label' => '検査値29年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_30',
            'label' => '検査値30年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_31',
            'label' => '検査値31年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_32',
            'label' => '検査値32年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_33',
            'label' => '検査値33年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_34',
            'label' => '検査値34年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'KENSA_VALUE_35',
            'label' => '検査値35年',
            'rules' => 'max_length[100]',
        ),
        array(
            'field' => 'INS_USER_ID',
            'label' => '登録ユーザID',
            'rules' => 'required|max_length[10]',
        ),
        array(
            'field' => 'INS_PROGRAM_ID',
            'label' => '登録プログラムID',
            'rules' => 'required|max_length[20]',
        ),
        array(
            'field' => 'UPD_USER_ID',
            'label' => '更新ユーザID',
            'rules' => 'max_length[10]',
        ),
        array(
            'field' => 'UPD_PROGRAM_ID',
            'label' => '更新プログラムID',
            'rules' => 'max_length[20]',
        ),

    ),
    'detail/Tliving' => array(
        array(
            'field' => 'RECIPIENT_ID',
            'label' => '登録者ID',
            'rules' => "required|max_length[7]",
        ),
        array(
            'field' => 'ZOKI_CODE',
            'label' => '移植臓器',
            'rules' => "required|max_length[1]",
        ),
        array(
            'field' => 'ISYOKU_CNT',
            'label' => '移植回数',
            'rules' => "required|max_length[2]",
        ),
        array(
            'field' => 'INPUT_DATE',
            'label' => '記録日',
            'rules' => 'required|min_length[8]|max_length[8]',
        ),
        array(
            'field' => 'KAKUNIN_USER_NAME',
            'label' => '確認者',
            'rules' => 'max_length[40]',
        ),
        array(
            'field' => 'REPORT_USER_NAME',
            'label' => '報告者',
            'rules' => 'max_length[40]',
        ),
        array(
            'field' => 'REPORT_FORM',
            'label' => '報告形式',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'LIVING_NAIYO',
            'label' => '生活状況',
            'rules' => 'max_length[120]',
        ),
        array(
            'field' => 'CYCLE',
            'label' => '経過期間',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'DEL_FLG',
            'label' => '削除フラグ',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'INS_USER_ID',
            'label' => '登録ユーザID',
            'rules' => 'required|max_length[10]',
        ),
        array(
            'field' => 'INS_PROGRAM_ID',
            'label' => '登録プログラムID',
            'rules' => 'required|max_length[20]',
        ),
        array(
            'field' => 'UPD_USER_ID',
            'label' => '更新ユーザID',
            'rules' => 'max_length[10]',
        ),
        array(
            'field' => 'UPD_PROGRAM_ID',
            'label' => '更新プログラムID',
            'rules' => 'max_length[20]',
        ),

    ),
    'detail/Trejection' => array(
        array(
            'field' => 'RECIPIENT_ID',
            'label' => '登録者ID',
            'rules' => "required|max_length[7]",
        ),
        array(
            'field' => 'ZOKI_CODE',
            'label' => '移植臓器',
            'rules' => 'required|max_length[1]',
        ),
        array(
            'field' => 'ISYOKU_CNT',
            'label' => '移植回数',
            'rules' => 'required|integer|max_length[2]',
        ),
        array(
            'field' => 'REJECTION_TYPE',
            'label' => '拒絶反応種別',
            'rules' => 'required|integer|max_length[1]',
        ),
        array(
            'field' => 'SINDAN_DATE',
            'label' => '診断日（一覧）',
            'rules' => 'required|min_length[8]|max_length[8]',
        ),
        array(
            'field' => 'TIRYOU_SYUDAN',
            'label' => '治療手段（一覧）',
            'rules' => 'max_length[2]',
        ),
        array(
            'field' => 'TIRYOU_KOKA',
            'label' => '治療効果（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'GRADEA',
            'label' => 'GradeA（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'GRADEB',
            'label' => 'GradeB（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'GRADEC',
            'label' => 'GradeC（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'GRADED',
            'label' => 'GradeD（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'STAGE',
            'label' => 'Stage（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'A_B',
            'label' => 'a/b（一覧）',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'DEL_FLG',
            'label' => '削除フラグ',
            'rules' => 'max_length[1]',
        ),
        array(
            'field' => 'INS_USER_ID',
            'label' => '登録ユーザID',
            'rules' => 'required|max_length[10]',
        ),
        array(
            'field' => 'INS_PROGRAM_ID',
            'label' => '登録プログラムID',
            'rules' => 'required|max_length[20]',
        ),
        array(
            'field' => 'UPD_USER_ID',
            'label' => '更新ユーザID',
            'rules' => 'max_length[10]',
        ),
        array(
            'field' => 'UPD_PROGRAM_ID',
            'label' => '更新プログラムID',
            'rules' => 'max_length[20]',
        ),
    ),
);
