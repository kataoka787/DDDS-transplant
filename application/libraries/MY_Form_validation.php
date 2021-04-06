<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY Form Validation Class for Japanese
 *
 * @package
 * @subpackage Libraries
 * @category Validation
 * @author Copyright (c) 2011, AIDREAM.
 * @link
 */
class MY_Form_validation extends CI_Form_validation
{
    /**
     * Constructor
     */
    public function __construct($rules = array())
    {
        parent::__construct($rules);
    }

    // --------------------------------------------------------------------

    /**
     * 入力値の整形（変換）
     *
     * @access public
     * @param string
     * @param string
     * @return string
     *
     *
     */
    public function convert($str, $val)
    {
        if ($str == '') {
            return '';
        }
        switch ($val) {

            case 'single': // 半角文字列
                return mb_convert_kana($str, 'ras');
                break;

            case 'double': // 全角文字列
                return $val = mb_convert_kana($str, 'ASKV');
                break;

            case 'hiragana': // ひらがな
                return mb_convert_kana($str, 'HVc');
                break;

            case 'katakana': // 全角カタカナ
                return mb_convert_kana($str, 'KVC');
                break;

            case 'single_katakana': // 半角カタカナ
                return mb_convert_kana($str, 'kh');
                break;

            case 'phone': // 電話番号
                $str = mb_convert_kana($str, 'ras');
                return str_replace(array('ー', '―', '‐'), '-', $str);
                break;

            case 'postal': // 郵便番号
                $str = mb_convert_kana($str, 'ras');
                $str = str_replace(array('ー', '―', '‐'), '-', $str);
                if (strlen($str) == 7 and preg_match("/^[0-9]+$/", $str)) {
                    $str = substr($str, 0, 3) . '-' . substr($str, 3);
                }
                return $str;
                break;

            case 'ymd': // 西暦年月日
                $str = mb_convert_kana($str, 'ras');
                $str = str_replace('/', '-', $str);
                if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $str) and strlen($str) != 10) {
                    $tmp = explode('-', $str);
                    return vsprintf("%4d-%02d-%02d", $tmp); // 月日の箇所をゼロ詰めに整形
                }
                break;

            case 'html': // HTMLタグからXSSなどの悪意のあるコードを除外
                $CI = &get_instance();
                $CI->load->helper('escape_helper'); // escape_helper.php については http://blog.aidream.jp/?p=1479 を参照ください
                $clean_html = purify($str);
                return ($clean_html == '<p></p>' . PHP_EOL) ? '' : $clean_html; // TinyMCEヘルパを使用している場合の対策
                break;
        }
    }

    // --------------------------------------------------------------------

    /**
     * 半角チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function single($str)
    {
        if ($str == '') {
            return true;
        }

        return (strlen($str) != mb_strlen($str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 全角チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function double($str)
    {
        if ($str == '') {
            return true;
        }
        $ratio = (mb_detect_encoding($str) == 'UTF-8') ? 3 : 2;

        return (strlen($str) != mb_strlen($str) * $ratio) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * ひらがな チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function hiragana($str)
    {
        if ($str == '') {
            return true;
        }
        $str = mb_convert_encoding($str, 'UTF-8');

        return (!preg_match("/^(?:\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93]|ー)+$/", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 全角カタカナ チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function katakana($str)
    {
        if ($str == '') {
            return true;
        }
        $str = mb_convert_encoding($str, 'UTF-8');

        return (!preg_match("/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|ー)+$/", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 半角カタカナ チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function single_katakana($str)
    {
        if ($str == '') {
            return true;
        }
        $str = mb_convert_encoding($str, 'UTF-8');

        return (!preg_match("/^(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])+$/", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * メールアドレス チェックの拡張（空の場合はバリデーションを通さない）
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function valid_email($str)
    {
        if ($str == '') {
            return true;
        }

        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 入力値一致チェックの拡張（POST値の配列は5階層まで対応可）
     *
     * @access    public
     * @param    string
     * @param    field
     * @return    bool
     *
     */
    public function matches($str, $field)
    {
        if ($str == '') {
            return true;
        }

        if (strpos($field, '[') !== false and preg_match_all('/\[(.*?)\]/', $field, $matches)) {
            $x = explode('[', $field);
            $indexes[] = current($x);
            for ($i = 0; $i < count($matches['0']); $i++) {
                if ($matches['1'][$i] != '') {
                    $indexes[] = $matches['1'][$i];
                }
            }

            switch (count($indexes)) {
                case 2:
                    return isset($_POST[$indexes[0]][$indexes[1]]) and $str == $_POST[$indexes[0]][$indexes[1]];
                    break;

                case 3:
                    return isset($_POST[$indexes[0]][$indexes[1]][$indexes[2]]) and ($str == $_POST[$indexes[0]][$indexes[1]][$indexes[2]]);
                    break;

                case 4:
                    return isset($_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]]) and ($str == $_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]]);
                    break;

                case 5:
                    return isset($_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]][$indexes[4]]) and ($str == $_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]][$indexes[4]]);
                    break;
            }
        } else {
            if (!isset($_POST[$field])) {
                return false;
            }

            return ($str != $_POST[$field]) ? false : true;
        }
    }

    // --------------------------------------------------------------------

    /**
     * 電話番号チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function phone($str)
    {
        if ($str == '') {
            return true;
        }

        return (!preg_match("/^\d{2,5}\-\d{1,4}\-\d{1,4}$/", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 郵便番号チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function postal($str)
    {
        if ($str == '') {
            return true;
        }

        return (!preg_match("/^\d{3}\-\d{4}$/", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * クレジットカード 名義チェック（英字大文字）
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function creditcard_name($str)
    {
        if ($str == '') {
            return true;
        }

        return (!preg_match("/^[A-Z]+[\s|　]+[A-Z]+[\s|　]*[A-Z]+$/", $str)) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * YYYY-MM-DD形式のチェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function ymd($str)
    {
        if ($str == '') {
            return true;
        }
        $tmp = explode('-', $str);
        if (count($tmp) != 3) {
            return false;
        }
        $tmp = array_map('intval', $tmp);

        return (!checkdate($tmp[1], $tmp[2], $tmp[0])) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 環境依存文字・旧漢字などJISに変換できない文字チェック
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function jis($str)
    {
        if ($str == '') {
            return true;
        }
        $str = str_replace(array('～', 'ー', '－', '∥', '￠', '￡', '￢'), '', $str);
        $str2 = mb_convert_encoding($str, 'iso-2022-jp', $encoding);
        $str2 = mb_convert_encoding($str2, $encoding, 'iso-2022-jp');

        return ($str != $str2) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * 対になっているフィールドの値が存在するかチェック
     *
     * @access public
     * @param string
     * @param string
     * @return bool
     *
     */
    public function pair($str, $field)
    {
        if ($str == '') {
            return true;
        }

        if (strpos($field, '[') !== false and preg_match_all('/\[(.*?)\]/', $field, $matches)) {
            $x = explode('[', $field);
            $indexes[] = current($x);
            for ($i = 0; $i < count($matches['0']); $i++) {
                if ($matches['1'][$i] != '') {
                    $indexes[] = $matches['1'][$i];
                }
            }

            switch (count($indexes)) {
                case 2:
                    return isset($_POST[$indexes[0]][$indexes[1]]) and ($_POST[$indexes[0]][$indexes[1]] != '');
                    break;

                case 3:
                    return isset($_POST[$indexes[0]][$indexes[1]][$indexes[2]]) and ($_POST[$indexes[0]][$indexes[1]][$indexes[2]] != '');
                    break;

                case 4:
                    return isset($_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]]) and ($_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]] != '');
                    break;

                case 5:
                    return isset($_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]][$indexes[4]]) and ($_POST[$indexes[0]][$indexes[1]][$indexes[2]][$indexes[3]][$indexes[4]] != '');
                    break;
            }
        } else {
            if (!isset($_POST[$field])) {
                return false;
            }

            return (isset($_POST[$field]) and ($_POST[$field] != ''));
        }
    }

    /**
     * Check if pref id is valid
     *
     * @param string $prefId
     * @return boolean
     */
    public function pref_id($prefId)
    {
        if (empty($this->CI->Prefmst->getPrefMstById($prefId))) {
            $this->set_message("pref_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if organ id is valid
     *
     * @param string $organId
     * @return boolean
     */
    public function organ_id($organId)
    {
        if (empty($this->CI->Internalorgansmst->getInternalOrgansMstById($organId))) {
            $this->set_message("organ_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if block id is valid
     *
     * @param string $blockId
     * @return boolean
     */
    public function block_id($blockId)
    {
        $isValid = !empty($this->CI->Blockmst->getBlockmstByAffiliationCheck($blockId));
        $isValid || $this->set_message('block_id', lang('valid_value'));
        return $isValid;
    }

    public function status_id($statusId)
    {
        $isValid = !empty($this->CI->Statusmst->getStatusMstById($statusId));
        $isValid || $this->set_message('status_id', lang('valid_value'));
        return $isValid;
    }

    /**
     * Check if sex id is valid and not empty
     *
     * @param string $sexId
     * @return boolean
     */
    public function sex_id($sexId)
    {
        $isValid = empty($sexId) || !empty(SEX[$sexId]);
        $isValid || $this->set_message("sex_id", lang("valid_value"));
        return $isValid;
    }

    public function institution_id($institutionId)
    {
        $isValid = !empty($this->CI->Institutionmst->getTransplantInstitutionMstById($institutionId));
        $isValid || $this->set_message('institution_id', lang('valid_value'));
        return $isValid;
    }

    /**
     * Check if institution name is unique
     *
     * @param string $institutionName
     * @return boolean
     */
    public function institution_unique_name($institutionName)
    {
        $prefId = $this->CI->input->post("pref_id");
        $institutionId = $this->CI->session->userdata('id');
        if ($this->CI->Institutionmst->isInstitutionNameUniqe($prefId, $institutionName, $institutionId)) {
            return true;
        }
        $this->set_message('institution_unique_name', lang('registed'));
        return false;
    }

    /**
     * Check if institution code (SISETU_CD) is unique
     *
     * @param string $institutionCode
     * @return boolean
     */
    public function institution_unique_code($institutionCode)
    {
        $institutionId = $this->CI->session->userdata('id');
        if ($this->CI->Institutionmst->isInstitutionCodeUniqe($institutionCode, $institutionId)) {
            return true;
        }
        $this->set_message("institution_unique_code", lang("registed"));
        return false;
    }

    /**
     * Check if institution code (SISETU_CD) is foreign key of T_ISHOKUGO_KEIKA
     *
     * @param string $institutionCode
     * @return boolean
     */
    public function foreign_key_of_ishokugo_keika($institutionCode)
    {
        $institutionId = $this->CI->session->userdata('id');
        empty($institutionId) || $institution = $this->CI->Institutionmst->getTransplantInstitutionMstById($institutionId);
        if (!empty($institution) && $institution->SISETU_CD != $institutionCode && $this->CI->Ishokugokeika->isInstitutionCodeInUse($institution->SISETU_CD)) {
            $this->set_message("foreign_key_of_ishokugo_keika", lang("registed"));
            return false;
        }
        return true;
    }

    /**
     * Check if email is registered
     *
     * @param string $mail
     * @return boolean
     */
    public function is_registered($mail)
    {
        if (empty($this->CI->Accounttbl->getAccountDataByMail($mail))) {
            $this->set_message("is_registered", lang("reminder"));
            return false;
        }
        return true;
    }

    /**
     * Check if transplant user (doctor) email is not already registered
     *
     * @param string $email
     * @return boolean
     */
    public function doctor_email($email)
    {
        /* Do not check when editing transplant user */
        $isValid = $this->CI->input->post("account_id") != null || empty($this->CI->Doctortbl->getTransplantDoctorByMail($email));
        $isValid || $this->set_message("doctor_email", lang("registed"));
        return $isValid;
    }

    /**
     * Check if cordinator email is not already registered
     *
     * @param string $email
     * @return boolean
     */
    public function cordinator_email($email)
    {
        $co = $this->CI->Accounttbl->getAccountDataByMail($email, ACC_TYPE_CO, ACC_STT_CONF);
        if ($co && $this->CI->session->userdata('id') != $co->accountId) {
            $this->set_message("cordinator_email", lang("registed"));
            return false;
        }
        return true;
    }

    /**
     * Check if doctor name is not already registered
     *
     * @param string $sei
     * @return boolean $isValid
     */
    public function doctor_name($sei)
    {
        $institution = $this->CI->input->post("institution");
        $mei = $this->CI->input->post("mei");
        $accId = $this->CI->input->post("account_id");
        $isValid = true;
        $doctor = $this->CI->Doctortbl->getDoctorByName($institution, $sei, $mei);
        $doctor && $isValid = $doctor->id == $accId || $doctor->sei != $sei || $doctor->mei != $mei || $doctor->institution_mst_id != $institution;
        $isValid || $this->set_message("doctor_name", str_replace("{field}", "この氏名は", lang("registed")));
        return $isValid;
    }

    /**
     * Check if password is used
     *
     * @param string $password
     * @return boolean
     */
    public function not_recent_password($password, $accIdField)
    {
        $accId = $this->CI->input->post($accIdField);
        $accId && $recentPasswords = $this->CI->Passwordhistorytbl->getRecentPassword($accId, config_item("password_history_limit"));
        if (!empty($recentPasswords)) {
            foreach ($recentPasswords as $recentPassword) {
                if (password_verify($password, $recentPassword->password)) {
                    $this->set_message("not_recent_password", lang("recent_password"));
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check if account type is valid
     *
     * @param string $accTypeId
     * @return boolean
     */
    public function account_type_id($accTypeId)
    {
        $isValid = empty($accTypeId) || !empty($this->CI->Accounttypemst->getAccountTypeById($accTypeId));
        $isValid || $this->set_message("account_type_id", lang("valid_value"));
        return $isValid;
    }

    /**
     * Check if password is secured
     *
     * @param string $password
     * @return boolean
     */
    public function secured_password($password)
    {
        /* Password has at least 1 lowercase, 1 uppercase, 1 number character */
        if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/", $password)) {
            return true;
        }
        $this->set_message("secured_password", lang("secured_password"));
        return false;
    }

    /**
     * Check if password is inputable when register/update transplant user
     *
     * @param string $password
     * @return boolean
     */
    public function password_inputable()
    {
        $accId = $this->CI->input->post("account_id");
        $doctorType = $this->CI->input->post("doctor_type_id");
        $works = $this->CI->input->post("works");
        $isPasswordInputable = false;
        if (!empty($accId)) {
            $account = $this->CI->Accounttbl->getAccountById($accId);
            $isHadPassword = !empty($account->password);
            /* Password inputable when update a admin or follow-up transplant user (not first time) */
            $isPasswordInputable = $isHadPassword && ($doctorType == IS_ADMIN || in_array(WORK_FOLLOW_UP, $works));
        }
        $isPasswordInputable || $this->set_message("password_inputable", lang("password_inputable"));
        return $isPasswordInputable;
    }

    /**
     * Check if inputed memo not include forbidden character
     *
     * @param string $memo
     * @return boolean
     */
    public function memo_check($memo)
    {
        for ($i = 0; $i < mb_strlen($memo); $i++) {
            $str2 = mb_substr($memo, $i, 1);
            if (preg_match('/^(\xe2\x91[\xa0-\xb3])/', $str2)) {
                $this->set_message('memo_check', lang('valid_value'));
                return false;
            }
        }
        return true;
    }

    /* TODO Add more common validate */
    /**
     * Check if number is unsigned
     *
     * @param string $memo
     * @return boolean
     */
    public function unsigned_check($unsigned)
    {
        if (intval($unsigned) > 0) {
            return true;
        }
        return false;
    }

    /* TODO Add more common validate */

    // --------------------------------------------------------------------

    /**
     * Override CI3 default prepare rules function
     *
     * @param    array  $rules
     * @return   array $rules
     */
    protected function _prepare_rules($rules)
    {
        return $rules;
    }

    // --------------------------------------------------------------------

}

// END MY Form Validation Class

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */
