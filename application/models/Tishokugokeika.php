<?php
class Tishokugokeika extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getOneByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($kensaName) || $this->db->where('KENSA_NAME', $kensaName);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        return $this->db->get(T_ISHOKUGO_KEIKA)->row();
    }

    public function insert($data)
    {
        $insertData = array();
        foreach (getAllColumnNameAndDefaultValue($this->db, T_ISHOKUGO_KEIKA) as $column => $defaultValue) {
            $insertData[$column] = $data[$column] ?? $defaultValue;
        }
        $insertData["INS_DATE"] = $insertData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->insert(T_ISHOKUGO_KEIKA, $insertData);
    }

    public function deleteByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        $this->db->delete(T_ISHOKUGO_KEIKA);
    }

    public function getTIshokugoKeika()
    {
        $this->db->select('T_ISHOKUGO_KEIKA.*, T_DONOR.KANJI_NAME as DONOR_KANJI_NAME, T_DONOR.DONOR_TODOFUKEN as DONOR_TODOFUKEN');
        $this->db->select('transplant.institution_name as transplant_name, transfer_destination.institution_name as transfer_destination_name');
        $this->db->select('T_LIVING.INPUT_DATE as living_conditions, T_KENSA.KENSA_NAME as inspection_item');
        $this->db->join('T_DONOR', 'T_ISHOKUGO_KEIKA.DONOR_ID = T_DONOR.DONOR_ID', 'left');
        $this->db->join('institutionMst as transplant', 'T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD = transplant.SISETU_CD', 'left');
        $this->db->join('institutionMst as transfer_destination', 'T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD = transfer_destination.SISETU_CD', 'left');
        $this->db->join('T_LIVING', 'T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_LIVING.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_LIVING.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_LIVING.ISYOKU_CNT', 'left');
        $this->db->join('T_KENSA', 'T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_KENSA.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_KENSA.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_KENSA.ISYOKU_CNT', 'left');
        $this->db->where('T_ISHOKUGO_KEIKA.DEL_FLG', IN_USE_FLG);

        return $this->db->get(T_ISHOKUGO_KEIKA)->result();
    }

    public function getTIshokugoKeikaDownloadCSVZoki($zokiCode)
    {
        $this->db->where('T_ISHOKUGO_KEIKA.ZOKI_CODE', $zokiCode);

        return $this->db->get(T_ISHOKUGO_KEIKA)->result();
    }

    public function getTIshokugoKeikaInfo($recipientId, $zokiCode, $isyokuCnt)
    {
        $organMCD = CODE_TYPE['ORGAN'];
        $sexMCD = CODE_TYPE['SEX'];
        $gensikkanMCD = CODE_TYPE['ORIGINAL_DISEASE'];
        $isyokuNaiyoMCD = CODE_TYPE['PORTING_CONTENT'][$zokiCode];
        $donorSexMCD = CODE_TYPE['DONOR_SEX'];

        $this->db->select(
            "T_ISHOKUGO_KEIKA.*,
            organ.VALUE as organ,
            T_ISHOKUGO_KEIKA.KANJI_NAME as kanji_name,
            sex.VALUE as sex,
            DATE_FORMAT(CAST(T_ISHOKUGO_KEIKA.BIRTHDAY as date), '%Y/%m/%d') as birthday,
            TIMESTAMPDIFF(YEAR, T_ISHOKUGO_KEIKA.BIRTHDAY, now()) AS age,
            DATE_FORMAT(CAST(T_ISHOKUGO_KEIKA.ISYOKU_DATE as date), '%Y/%m/%d') as isyoku_date,
            gensikkan.VALUE as gensikkan,
            (CASE
                WHEN T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_KBN = 1 THEN '移植施設'
                WHEN T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_KBN = 2 THEN '管理施設'
            END) AS ishokugo_keikajyouhou_sisetu_kbn"
        );
        $this->db->select(
            "T_DONOR.*,
            isyoku_naiyo.VALUE as isyoku_naiyo,
            T_DONOR.KANJI_NAME as donor_kanji_name,
            donor_sex.VALUE as donor_sex,
            DATE_FORMAT(CAST(T_DONOR.BIRTHDAY as date), '%Y/%m/%d') as donor_birthday,
            TIMESTAMPDIFF(YEAR, T_DONOR.BIRTHDAY, now()) AS donor_age"
        );
        $this->db->select('transplant.id as transplantId, transplant.institution_name as transplant_name');
        $this->db->select('post_transplant.id as post_transplant_id, post_transplant.institution_name as post_transplant_name');

        $this->db->join('T_DONOR', 'T_ISHOKUGO_KEIKA.DONOR_ID = T_DONOR.DONOR_ID', 'left');
        $this->db->join('T_GAPPEI', 'T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_GAPPEI.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_GAPPEI.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_GAPPEI.ISYOKU_CNT', 'left');
        $this->db->join('T_KENSA', 'T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_KENSA.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_KENSA.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_KENSA.ISYOKU_CNT', 'left');
        $this->db->join('T_LIVING', 'T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_LIVING.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_LIVING.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_LIVING.ISYOKU_CNT', 'left');
        $this->db->join('institutionMst as transplant', 'T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD = transplant.SISETU_CD', 'left');
        $this->db->join('institutionMst as post_transplant', 'T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD = post_transplant.SISETU_CD', 'left');
        $this->db->join('M_CD as organ', "organ.CODE_TYPE = $organMCD AND organ.CODE = T_ISHOKUGO_KEIKA.ZOKI_CODE", 'left');
        $this->db->join('M_CD as sex', "sex.CODE_TYPE = $sexMCD AND sex.CODE = T_ISHOKUGO_KEIKA.SEX", 'left');
        $this->db->join('M_CD as gensikkan', "gensikkan.CODE_TYPE = $gensikkanMCD AND gensikkan.CODE = T_ISHOKUGO_KEIKA.GENSIKKAN", 'left');

        $this->db->join('M_CD as isyoku_naiyo', "isyoku_naiyo.CODE_TYPE = $isyokuNaiyoMCD AND isyoku_naiyo.CODE = T_DONOR.ISYOKU_NAIYO", 'left');
        $this->db->join('M_CD as donor_sex', "donor_sex.CODE_TYPE = $donorSexMCD AND donor_sex.CODE = T_DONOR.SEX", 'left');

        $this->db->where('T_ISHOKUGO_KEIKA.RECIPIENT_ID', $recipientId);
        $this->db->where('T_ISHOKUGO_KEIKA.ZOKI_CODE', $zokiCode);
        $this->db->where('T_ISHOKUGO_KEIKA.ISYOKU_CNT', $isyokuCnt);
        $this->db->where('T_ISHOKUGO_KEIKA.DEL_FLG', IN_USE_FLG);

        $query = $this->db->get('T_ISHOKUGO_KEIKA');
        return $query->row();
    }

    public function getAllRecipientWithDifferentOrganStatus($recipientId, $zokiCode, $recipientTenki)
    {
        $this->db->where(array(
            "RECIPIENT_ID" => $recipientId,
            "ZOKI_CODE !=" => $zokiCode,
            "RECIPIENT_TENKI !=" => $recipientTenki,
            "DEL_FLG" => IN_USE_FLG,
        ));
        return $this->db->get(T_ISHOKUGO_KEIKA)->result();
    }

    public function getForPdfPrint($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->select("T_DONOR.*");
        $this->db->select("T_DONOR.KANJI_NAME as DONOR_KANJI_NAME, T_DONOR.DONOR_TODOFUKEN as DONOR_TODOFUKEN, T_DONOR.SEX as DONOR_SEX");
        $this->db->select("T_DONOR.BIRTHDAY as DONOR_BIRTHDAY, T_DONOR.BLOOD_ABO as DONOR_BLOOD_ABO, T_DONOR.BLOOD_RH as DONOR_BLOOD_RH");
        $this->db->select("T_DONOR.SHIIN_NAI as DONOR_SHIIN_NAI, T_DONOR.SHIIN_GAI as DONOR_SHIIN_GAI");
        $this->db->select("TRANSPLANT_INSTITUTION.institution_name as TRANSPLANT_INSTITUTION_NAME");
        $this->db->select("FOLLOW_UP_INSTITUTION.institution_name as FOLLOW_UP_INSTITUTION_NAME");
        $this->db->select("T_ISHOKUGO_KEIKA.*");
        $this->db->select("TIMESTAMPDIFF(YEAR, ISYOKU_DATE , SUBDATE(NOW(), 1)) as YEAR_DIFF");

        $this->db->join(T_DONOR, "T_ISHOKUGO_KEIKA.DONOR_ID = T_DONOR.DONOR_ID", "left");
        $this->db->join(INSTITUTION_MST . " as TRANSPLANT_INSTITUTION", "T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD = TRANSPLANT_INSTITUTION.SISETU_CD", "left");
        $this->db->join(INSTITUTION_MST . " as FOLLOW_UP_INSTITUTION", "T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD = FOLLOW_UP_INSTITUTION.SISETU_CD", "left");

        $this->db->where(array(
            "T_ISHOKUGO_KEIKA.RECIPIENT_ID" => $recipientId,
            "T_ISHOKUGO_KEIKA.ZOKI_CODE" => $zokiCode,
            "T_ISHOKUGO_KEIKA.ISYOKU_CNT" => $isyokuCnt,
            "T_ISHOKUGO_KEIKA.DEL_FLG" => IN_USE_FLG,
        ));

        return $this->db->get(T_ISHOKUGO_KEIKA)->row();
    }

    public function getForSendAlertEmail($reportDeadlineFrom, $reportDeadlineTo, $fromCycle = null, $toCycle = null)
    {
        $this->db->select("*");
        $this->db->select("(
            CASE
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 3 THEN 'M1'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 6 THEN 'M3'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 12 THEN 'M6'
                ELSE LPAD(TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)), 2, 0)
            END
        ) AS CYCLE");
        $this->db->select("(
            CASE
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 3 THEN ADDDATE(ISYOKU_DATE, INTERVAL 1 MONTH)
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 6 THEN ADDDATE(ISYOKU_DATE, INTERVAL 3 MONTH)
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 12 THEN ADDDATE(ISYOKU_DATE, INTERVAL 6 MONTH)
                ELSE ADDDATE(ISYOKU_DATE, INTERVAL TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)) YEAR)
            END
        ) AS REPORT_DEADLINE");

        $this->db->where("TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) >=", 1);
        /* 生存 and 生着 */
        $this->db->where('RECIPIENT_TENKI', '1');
        $this->db->where('RECIPIENT_TENKI_DETAIL', '1');
        /* Query by cycle (in year) */
        empty($fromCycle) || $this->db->where("TIMESTAMPDIFF(YEAR, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) >=", $fromCycle);
        empty($toCycle) || $this->db->where("TIMESTAMPDIFF(YEAR, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) <=", $toCycle);

        $fromSql = $this->db->get_compiled_select(T_ISHOKUGO_KEIKA);

        $this->db->select('T_ISHOKUGO_KEIKA.RECIPIENT_ID, T_ISHOKUGO_KEIKA.ZOKI_CODE, T_ISHOKUGO_KEIKA.ISYOKU_CNT, T_ISHOKUGO_KEIKA.CYCLE, T_ISHOKUGO_KEIKA.REPORT_DEADLINE');
        $this->db->select('T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD, T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD');
        $this->db->from("($fromSql) T_ISHOKUGO_KEIKA");
        $this->db->join(T_KENSA, "T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_KENSA.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_KENSA.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_KENSA.ISYOKU_CNT AND T_KENSA.KENSA_NAME = '入力状況'", 'left');
        $this->db->join(T_LIVING, 'T_ISHOKUGO_KEIKA.RECIPIENT_ID = T_LIVING.RECIPIENT_ID AND T_ISHOKUGO_KEIKA.ZOKI_CODE = T_LIVING.ZOKI_CODE AND T_ISHOKUGO_KEIKA.ISYOKU_CNT = T_LIVING.ISYOKU_CNT AND T_ISHOKUGO_KEIKA.CYCLE = T_LIVING.CYCLE AND T_LIVING.DEL_FLG !=' . DELETED_FLG, 'left');

        /* Query inspection input status depend on cycle */
        $inspectionCase = "
                WHEN T_ISHOKUGO_KEIKA.CYCLE = 'M1' THEN T_KENSA.KENSA_VALUE_M1
                WHEN T_ISHOKUGO_KEIKA.CYCLE = 'M3' THEN T_KENSA.KENSA_VALUE_M3
                WHEN T_ISHOKUGO_KEIKA.CYCLE = 'M6' THEN T_KENSA.KENSA_VALUE_M6";
        for ($i = 1; $i < config_item("max_cycle_year") + 1; $i++) {
            $cycle = sprintf("%02d", $i);
            $inspectionCase .= "
                WHEN T_ISHOKUGO_KEIKA.CYCLE = $cycle THEN T_KENSA.KENSA_VALUE_$cycle";
        }
        $this->db->group_start();
        $this->db->where("
            CASE
                $inspectionCase
            END = '未完了'
        ");
        $this->db->or_where("
            CASE
                $inspectionCase
            END IS NULL
        ");
        $this->db->or_where("INPUT_DATE", null);
        $this->db->group_end();

        empty($reportDeadlineFrom) || $this->db->where('REPORT_DEADLINE >=', $reportDeadlineFrom);
        $this->db->where('REPORT_DEADLINE <=', $reportDeadlineTo);
        $this->db->where('T_ISHOKUGO_KEIKA.DEL_FLG', IN_USE_FLG);

        return $this->db->get()->result();
    }

    public function getTIshokugoKeikaByRecipientInfo($conditions)
    {
        $maxCycleYear = config_item("max_cycle_year");
        $reportDeadlineTo = !empty($conditions['reportDeadlineDate']['to']) ? $conditions['reportDeadlineDate']['to'] : date(DATE_TIME_DEFAULT);
        $this->db->select("*");
        $this->db->select("(
            CASE
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 1 THEN '-'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 3 THEN 'M1'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 6 THEN 'M3'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 12 THEN 'M6'
                WHEN TIMESTAMPDIFF(YEAR, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) > $maxCycleYear THEN '$maxCycleYear'
                ELSE LPAD(TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)), 2, 0)
            END
        ) AS CYCLE");
        $this->db->select("(
            CASE
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 3 THEN ADDDATE(ISYOKU_DATE, INTERVAL 1 MONTH)
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 6 THEN ADDDATE(ISYOKU_DATE, INTERVAL 3 MONTH)
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 12 THEN ADDDATE(ISYOKU_DATE, INTERVAL 6 MONTH)
                WHEN TIMESTAMPDIFF(YEAR, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) > $maxCycleYear THEN ADDDATE(ISYOKU_DATE, INTERVAL 35 YEAR)
                ELSE ADDDATE(ISYOKU_DATE, INTERVAL TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)) YEAR)
            END
        ) AS REPORT_DEADLINE");

        /* Search by 対象経過期間 */
        $lessOneYear = $conditions['elapsedPeriod']['lessOneYear'] ?? null;
        $overTwoYear = $conditions['elapsedPeriod']['overTwoYear'] ?? null;
        if (isset($lessOneYear) && isset($overTwoYear)) {
            $this->db->where("TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) >=", 1);
        } elseif (isset($lessOneYear)) {
            $this->db->where("TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) >=", 1);
            $this->db->where("TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)) <=", 1);
        } elseif (isset($overTwoYear)) {
            $this->db->where("TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)) >=", 2);
        }
        /* Get sql statement */
        $fromSql = $this->db->get_compiled_select(T_ISHOKUGO_KEIKA);

        $sexMCD = CODE_TYPE['SEX'];
        $organMCD = CODE_TYPE['ORGAN'];
        $organOutcomeMCD = CODE_TYPE['ORGAN_OUTCOME'];
        $patientOutcomeMCD = CODE_TYPE['PATIENT_OUTCOME'];

        $this->db->select('T_KENSA.*');
        $this->db->select('T_LIVING.INPUT_DATE');
        $this->db->select(
            'T_ISHOKUGO_KEIKA.*,
            sex.VALUE as sex,
            organ.VALUE as organ,
            organ_outcome.VALUE as organ_outcome,
            patient_outcome.VALUE as patient_outcome,
            T_DONOR.KANJI_NAME as DONOR_KANJI_NAME,
            T_DONOR.DONOR_TODOFUKEN as DONOR_TODOFUKEN'
        );

        $this->db->select('transplant.institution_name as transplant_name, transfer_destination.institution_name as transfer_destination_name');
        $this->db->from("($fromSql) " . T_ISHOKUGO_KEIKA);

        $this->db->join(T_DONOR, 'T_ISHOKUGO_KEIKA.DONOR_ID = T_DONOR.DONOR_ID', 'left');
        $this->db->join('institutionMst as transplant', 'T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD = transplant.SISETU_CD', 'left');
        $this->db->join('institutionMst as transfer_destination', 'T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD = transfer_destination.SISETU_CD', 'left');
        $this->db->join('M_CD as sex', "sex.CODE_TYPE = $sexMCD AND sex.CODE = T_ISHOKUGO_KEIKA.SEX", 'left');
        $this->db->join('M_CD as organ', "organ.CODE_TYPE = $organMCD AND organ.CODE = T_ISHOKUGO_KEIKA.ZOKI_CODE", 'left');
        $this->db->join('M_CD as organ_outcome', "organ_outcome.CODE_TYPE = $organOutcomeMCD AND organ_outcome.CODE = T_ISHOKUGO_KEIKA.ZOKI_TENKI", 'left');
        $this->db->join('M_CD as patient_outcome', "patient_outcome.CODE_TYPE = $patientOutcomeMCD AND patient_outcome.CODE = T_ISHOKUGO_KEIKA.RECIPIENT_TENKI", 'left');

        $this->db->join(T_KENSA, "T_KENSA.RECIPIENT_ID = T_ISHOKUGO_KEIKA.RECIPIENT_ID AND T_KENSA.ZOKI_CODE = T_ISHOKUGO_KEIKA.ZOKI_CODE AND T_KENSA.ISYOKU_CNT = T_ISHOKUGO_KEIKA.ISYOKU_CNT AND AND T_KENSA.KENSA_NAME = '入力状況'", 'left');
        $this->db->join(T_LIVING, 'T_LIVING.RECIPIENT_ID = T_ISHOKUGO_KEIKA.RECIPIENT_ID AND T_LIVING.ZOKI_CODE = T_ISHOKUGO_KEIKA.ZOKI_CODE AND T_LIVING.ISYOKU_CNT = T_ISHOKUGO_KEIKA.ISYOKU_CNT AND T_ISHOKUGO_KEIKA.CYCLE = T_LIVING.CYCLE AND T_LIVING.DEL_FLG !=' . DELETED_FLG, 'left');

        /* Search by 報告期限日 */
        empty($conditions['reportDeadlineDate']['from']) || $this->db->where("T_ISHOKUGO_KEIKA.REPORT_DEADLINE >=", $conditions['reportDeadlineDate']['from']);
        empty($conditions['reportDeadlineDate']['to']) || $this->db->where("T_ISHOKUGO_KEIKA.REPORT_DEADLINE <=", $conditions['reportDeadlineDate']['to']);

        /* Search by 入力状況 */
        $notEntered = $conditions['inputStatus']['notEntered'] ?? null;
        $done = $conditions['inputStatus']['done'] ?? null;
        /* Inspection (検査項目) */
        if (isset($conditions['checkTarget']['inspectionItem'])) {
            /* Query inspection input status depend on cycle */
            $inspectionCase = "
                WHEN T_ISHOKUGO_KEIKA.CYCLE = 'M1' THEN T_KENSA.KENSA_VALUE_M1
                WHEN T_ISHOKUGO_KEIKA.CYCLE = 'M3' THEN T_KENSA.KENSA_VALUE_M3
                WHEN T_ISHOKUGO_KEIKA.CYCLE = 'M6' THEN T_KENSA.KENSA_VALUE_M6";
            for ($i = 1; $i < config_item("max_cycle_year") + 1; $i++) {
                $cycle = sprintf("%02d", $i);
                $inspectionCase .= "
                    WHEN T_ISHOKUGO_KEIKA.CYCLE = $cycle THEN T_KENSA.KENSA_VALUE_$cycle";
            }
            if (empty($notEntered) != empty($done)) {
                if (!empty($notEntered)) {
                    $this->db->group_start();
                    $this->db->where("CASE $inspectionCase END = '未完了'");
                    $this->db->or_where("CASE $inspectionCase END IS NULL");
                    $this->db->group_end();
                } else {
                    $this->db->where("CASE $inspectionCase END = '完了'");
                }
            }
        }

        /* Living conditions (生活状況) */
        if (isset($conditions['checkTarget']['livingConditions'])) {
            if (empty($notEntered) != empty($done)) {
                if (!empty($notEntered)) {
                    $this->db->where('T_LIVING.INPUT_DATE', null);
                } else {
                    $this->db->where('T_LIVING.INPUT_DATE !=', null);
                }
            }
        }

        // Search by organ (or)
        empty($conditions['organ']) || $this->db->where_in('T_ISHOKUGO_KEIKA.ZOKI_CODE', $conditions['organ']);
        if ($this->session->userdata('account_type_mst_id') == ACC_TYPE_TP) {
            $this->db->where_in('T_ISHOKUGO_KEIKA.ZOKI_CODE', $this->session->userdata('organsAvailable'));
        }

        if (!empty($conditions['simultaneousTransplantation'])) {
            $this->db->group_start();
            $organName = array();
            foreach ($conditions['simultaneousTransplantation'] as $value) {
                $organName = array_merge(explode(",", $value), $organName);
            }
            foreach ($organName as $key => $value) {
                $key === 0 ? $this->db->like("T_ISHOKUGO_KEIKA.DOUJI_ISHOKU", $value) : $this->db->or_like("T_ISHOKUGO_KEIKA.DOUJI_ISHOKU", $value);
            }
            $this->db->group_end();
        }
        // Search by registrantID
        empty($conditions['registrantID']) || $this->db->where('LPAD(T_ISHOKUGO_KEIKA.RECIPIENT_ID, 7, 0) =', str_pad($conditions['registrantID'], 7, 0, STR_PAD_LEFT));
        // Search by fullName
        if ($conditions['fullName']) {
            switch ($conditions['charType']) {
                case 1:
                    $this->db->like('T_ISHOKUGO_KEIKA.KANJI_NAME', $conditions['fullName']);
                    break;
                case 2:
                    $kanaConverted = mb_convert_kana($conditions['fullName'], 'kh');
                    $this->db->like('T_ISHOKUGO_KEIKA.KANA_NAME', $kanaConverted);
                    break;
            }
        }
        // Search by transplant and postTransplant
        if ($this->session->userdata('account_type_mst_id') == ACC_TYPE_TP) {
            switch ($this->session->userdata('account')->institution_kubun) {
                case INSTITUTION_KUBUN_TRANSPLANT:
                    $this->db->where('T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD', $this->session->userdata('account')->SISETU_CD);
                    break;
                case INSTITUTION_KUBUN_TRANSFER:
                    $this->db->where('T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD', $this->session->userdata('account')->SISETU_CD);
                    break;
            }
        }
        empty($conditions['transplant']) || $this->db->where('T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD', $conditions['transplant']);
        empty($conditions['postTransplant']) || $this->db->where('T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD', $conditions['postTransplant']);
        // Search by transplantDate
        empty($conditions['transplantDate']['from']) || $this->db->where('T_ISHOKUGO_KEIKA.ISYOKU_DATE >=', date_format(date_create($conditions['transplantDate']['from']), 'Ymd'));
        empty($conditions['transplantDate']['to']) || $this->db->where('T_ISHOKUGO_KEIKA.ISYOKU_DATE <=', date_format(date_create($conditions['transplantDate']['to']), 'Ymd'));
        // Search by organOutcome
        empty($conditions['organOutcome']) || $this->db->where_in('T_ISHOKUGO_KEIKA.ZOKI_TENKI', $conditions['organOutcome']);
        // Search by patientOutcome
        empty($conditions['patientOutcome']) || $this->db->where_in('T_ISHOKUGO_KEIKA.RECIPIENT_TENKI', $conditions['patientOutcome']);
        empty($conditions['patientOutcomeDetails']) || $this->db->where_in('T_ISHOKUGO_KEIKA.RECIPIENT_TENKI_DETAIL', $conditions['patientOutcomeDetails']);
        // Search by dischargeDateSet
        if ($conditions['dischargeDateSet']) {
            $this->db->where("T_ISHOKUGO_KEIKA.TAIIN_DATE <>", "");
        } else {
            $this->db->group_start();
            $this->db->where("T_ISHOKUGO_KEIKA.TAIIN_DATE", null);
            $this->db->or_where("T_ISHOKUGO_KEIKA.TAIIN_DATE", "");
            $this->db->group_end();
        }
        $this->db->where('T_ISHOKUGO_KEIKA.DEL_FLG', IN_USE_FLG);

        $this->db->limit(config_item('max_search_result') + 1);
        $this->db->order_by('CAST(T_ISHOKUGO_KEIKA.RECIPIENT_ID AS SIGNED)');
        return $this->db->get()->result();
    }

    public function getTIshokugoKeikaByDonorInfo($conditions)
    {
        $maxCycleYear = config_item("max_cycle_year");
        $reportDeadlineTo = !empty($conditions['reportDeadlineDate']['to']) ? $conditions['reportDeadlineDate']['to'] : date(DATE_TIME_DEFAULT);
        $this->db->select("*");
        $this->db->select("(
            CASE
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 1 THEN '-'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 3 THEN 'M1'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 6 THEN 'M3'
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 12 THEN 'M6'
                WHEN TIMESTAMPDIFF(YEAR, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) > $maxCycleYear THEN '$maxCycleYear'
                ELSE LPAD(TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)), 2, 0)
            END
        ) AS CYCLE");
        $this->db->select("(
            CASE
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 3 THEN ADDDATE(ISYOKU_DATE, INTERVAL 1 MONTH)
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 6 THEN ADDDATE(ISYOKU_DATE, INTERVAL 3 MONTH)
                WHEN TIMESTAMPDIFF(MONTH, ISYOKU_DATE , SUBDATE('$reportDeadlineTo', 1)) < 12 THEN ADDDATE(ISYOKU_DATE, INTERVAL 6 MONTH)
                ELSE ADDDATE(ISYOKU_DATE, INTERVAL TIMESTAMPDIFF(YEAR, ISYOKU_DATE, SUBDATE('$reportDeadlineTo', 1)) YEAR)
            END
        ) AS REPORT_DEADLINE");
        $fromSql = $this->db->get_compiled_select(T_ISHOKUGO_KEIKA);

        $sexMCD = CODE_TYPE['SEX'];
        $organMCD = CODE_TYPE['ORGAN'];
        $organOutcomeMCD = CODE_TYPE['ORGAN_OUTCOME'];
        $patientOutcomeMCD = CODE_TYPE['PATIENT_OUTCOME'];

        $this->db->select(
            'T_ISHOKUGO_KEIKA.*,
            sex.VALUE as sex,
            organ.VALUE as organ,
            organ_outcome.VALUE as organ_outcome,
            patient_outcome.VALUE as patient_outcome,
            T_DONOR.KANJI_NAME as DONOR_KANJI_NAME,
            T_DONOR.DONOR_TODOFUKEN as DONOR_TODOFUKEN'
        );
        $this->db->select('transplant.institution_name as transplant_name, transfer_destination.institution_name as transfer_destination_name');
        $this->db->from("($fromSql) " . T_ISHOKUGO_KEIKA);

        $this->db->join('T_DONOR', 'T_ISHOKUGO_KEIKA.DONOR_ID = T_DONOR.DONOR_ID', 'left');
        $this->db->join('institutionMst as transplant', 'T_ISHOKUGO_KEIKA.ISYOKU_ISYOKUSISETU_CD = transplant.SISETU_CD', 'left');
        $this->db->join('institutionMst as transfer_destination', 'T_ISHOKUGO_KEIKA.ISHOKUGO_KEIKAJYOUHOU_SISETU_CD = transfer_destination.SISETU_CD', 'left');
        $this->db->join('M_CD as sex', "sex.CODE_TYPE = $sexMCD AND sex.CODE = T_ISHOKUGO_KEIKA.SEX", 'left');
        $this->db->join('M_CD as organ', "organ.CODE_TYPE = $organMCD AND organ.CODE = T_ISHOKUGO_KEIKA.ZOKI_CODE", 'left');
        $this->db->join('M_CD as organ_outcome', "organ_outcome.CODE_TYPE = $organOutcomeMCD AND organ_outcome.CODE = T_ISHOKUGO_KEIKA.ZOKI_TENKI", 'left');
        $this->db->join('M_CD as patient_outcome', "patient_outcome.CODE_TYPE = $patientOutcomeMCD AND patient_outcome.CODE = T_ISHOKUGO_KEIKA.RECIPIENT_TENKI", 'left');

        // Search by donorID
        empty($conditions['donorID']) || $this->db->where('LPAD(T_DONOR.DONOR_ID, 7, 0) =', str_pad($conditions['donorID'], 7, 0, STR_PAD_LEFT));
        // Search by donorName
        empty($conditions['donorName']) || $this->db->like('T_DONOR.KANJI_NAME', $conditions['donorName']);
        // Search by organ (or)
        empty($conditions['organ']) || $this->db->where_in('T_ISHOKUGO_KEIKA.ZOKI_CODE', $conditions['organ']);
        if (!empty($conditions['simultaneousTransplantation'])) {
            $this->db->group_start();
            $organName = array();
            foreach ($conditions['simultaneousTransplantation'] as $value) {
                $organName = array_merge(explode(",", $value), $organName);
            }
            foreach ($organName as $key => $value) {
                $key === 0 ? $this->db->like("T_ISHOKUGO_KEIKA.DOUJI_ISHOKU", $value) : $this->db->or_like("T_ISHOKUGO_KEIKA.DOUJI_ISHOKU", $value);
            }
            $this->db->group_end();
        }
        // Search by surgeryStartDate
        empty($conditions['surgeryStartDate']['from']) || $this->db->where('T_DONOR.SYUJUTU_START_DATETIME >=', $conditions['surgeryStartDate']['from']);
        empty($conditions['surgeryStartDate']['to']) || $this->db->where('T_DONOR.SYUJUTU_START_DATETIME <=', $conditions['surgeryStartDate']['to']);
        // Search by providedFacilityName
        empty($conditions['providedFacilityName']) || $this->db->like('T_DONOR.TEIKYOSISETU_NAME', $conditions['providedFacilityName']);
        // Search by caseNo
        empty($conditions['caseNo']) || $this->db->where('T_DONOR.JIREI_NO', $conditions['caseNo']);
        // Search by organDonationStatus (or)
        empty($conditions['organDonationStatus']) || $this->db->where_in('T_DONOR.TEKISYUTU_JOKEN', $conditions['organDonationStatus']);
        $this->db->where('T_ISHOKUGO_KEIKA.DEL_FLG', IN_USE_FLG);

        $this->db->limit(config_item('max_search_result') + 1);
        $this->db->order_by('CAST(RECIPIENT_ID AS UNSIGNED)');
        return $this->db->get()->result();
    }

    public function isInstitutionCodeInUse($institutionCode)
    {
        $this->db->where("ISYOKU_ISYOKUSISETU_CD", $institutionCode);
        $this->db->or_where("ISHOKUGO_KEIKAJYOUHOU_SISETU_CD", $institutionCode);
        return !empty($this->db->count_all_results(ISHOKUGO_KEIKA));
    }

    public function getUpdateToken($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->select('UPD_DATE');
        $this->db->where(array(
            'RECIPIENT_ID' => $recipientId,
            'ZOKI_CODE' => $zokiCode,
            'ISYOKU_CNT' => $isyokuCnt,
            'DEL_FLG' => IN_USE_FLG,
        ));
        return $this->db->get(T_ISHOKUGO_KEIKA)->result();
    }

    public function update($data)
    {
        $updateData = array();
        $excludedColumn = array("RECIPIENT_ID", "ZOKI_CODE", "ISYOKU_CNT");
        $columnNameAndDefaultValue = getAllColumnNameAndDefaultValue($this->db, T_ISHOKUGO_KEIKA, $excludedColumn);
        foreach (array_intersect_key($data, $columnNameAndDefaultValue) as $key => $value) {
            $updateData[$key] = $value ?? $columnNameAndDefaultValue[$key];
        }
        $updateData["DEL_FLG"] = empty($data["DEL_FLG"]) ? IN_USE_FLG : $data["DEL_FLG"];
        $updateData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->where(array(
            "RECIPIENT_ID" => $data["RECIPIENT_ID"],
            "ZOKI_CODE" => $data["ZOKI_CODE"],
            "ISYOKU_CNT" => $data["ISYOKU_CNT"],
        ));
        $this->db->update(ISHOKUGO_KEIKA, $updateData);
    }

    public function getColumnName()
    {
        return $this->db->list_fields(T_ISHOKUGO_KEIKA);
    }
}
