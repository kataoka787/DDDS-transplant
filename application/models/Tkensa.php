<?php
class Tkensa extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $kensaName = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($kensaName) || $this->db->where('KENSA_NAME', $kensaName);
        $this->db->order_by('DSPNO');
        return $this->db->get(T_KENSA)->result();
    }

    public function getOneByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $kensaName = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($kensaName) || $this->db->where('KENSA_NAME', $kensaName);
        return $this->db->get(T_KENSA)->row();
    }

    public function getOneByInspectionValueCycle($inspectionValue, $cycle, $recipientId = null, $zokiCode = null, $isyokuCnt = null)
    {
        $this->db->where("KENSA_VALUE_$cycle", $inspectionValue);
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        return $this->db->get(T_KENSA)->row();
    }

    public function getAllByInspectionValueCycle($inspectionValue, $cycle, $recipientId = null, $zokiCode = null, $isyokuCnt = null)
    {
        $this->db->where("KENSA_VALUE_$cycle", $inspectionValue);
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        return $this->db->get(T_KENSA)->result();
    }

    public function getOneByPrimaryKeysCycleValue($recipientId = null, $zokiCode = null, $isyokuCnt = null, $kensaName = null, $cycle = null, $value = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($kensaName) || $this->db->where('KENSA_NAME', $kensaName);
        empty($cycle) || empty($value) || $this->db->where("KENSA_VALUE_$cycle", $value);
        return $this->db->get(T_KENSA)->row();
    }

    public function deleteByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $kensaName = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($kensaName) || $this->db->where('REJECTION_TYPE', $kensaName);
        $this->db->delete(T_KENSA);
    }

    public function getMaxCycle($recipientId, $zokiCode, $isyokuCnt)
    {
        $cycleSelect = "
            SELECT  RECIPIENT_ID, ZOKI_CODE, ISYOKU_CNT, KENSA_VALUE_M1 as KENSA_VALUE, 1 as CYCLE
            FROM T_KENSA
            UNION ALL
            SELECT  RECIPIENT_ID, ZOKI_CODE, ISYOKU_CNT, KENSA_VALUE_M3 as KENSA_VALUE, 2
            FROM T_KENSA
            UNION ALL
            SELECT RECIPIENT_ID, ZOKI_CODE, ISYOKU_CNT, KENSA_VALUE_M6 as KENSA_VALUE, 3
            FROM T_KENSA
            UNION ALL
        ";
        $maxCycleYear = config_item('max_cycle_year');
        $maxCycle = $maxCycleYear + 3;
        for ($i = 1; $i < $maxCycleYear; $i++) {
            $kensaYear = sprintf('%02d', $i);
            $kensaCycle = $kensaYear + 3;
            $cycleSelect .= "
                SELECT RECIPIENT_ID, ZOKI_CODE, ISYOKU_CNT, KENSA_VALUE_$kensaYear as KENSA_VALUE, $kensaCycle
                FROM T_KENSA
                UNION ALL
            ";
        }

        $cycleSelect .= "
            SELECT RECIPIENT_ID, ZOKI_CODE, ISYOKU_CNT, KENSA_VALUE_$maxCycleYear as KENSA_VALUE, $maxCycle
            FROM T_KENSA
        ";

        $sql = "
            SELECT MAX(CYCLE) as MAX_CYCLE
                FROM (
                    $cycleSelect
                ) kensa
            WHERE
                RECIPIENT_ID = ?
                AND ZOKI_CODE = ?
                AND ISYOKU_CNT = ?
                AND KENSA_VALUE IS NOT NULL
                AND KENSA_VALUE != ''
        ";
        $maxCycle = $this->db->query($sql, array($recipientId, $zokiCode, $isyokuCnt))->row()->MAX_CYCLE;
        empty($maxCycle) && $maxCycle = 1;
        return $maxCycle;
    }

    public function getTKensaDownloadCSV($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->where('T_KENSA.RECIPIENT_ID', $recipientId);
        $this->db->where('T_KENSA.ZOKI_CODE', $zokiCode);
        $this->db->where('T_KENSA.ISYOKU_CNT', $isyokuCnt);

        return $this->db->get(T_KENSA)->result();
    }

    public function getColumnName()
    {
        return $this->db->list_fields(T_KENSA);
    }

    public function getUpdateToken($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->select('UPD_DATE');
        $this->db->where(array(
            'RECIPIENT_ID' => $recipientId,
            'ZOKI_CODE' => $zokiCode,
            'ISYOKU_CNT' => $isyokuCnt,
        ));
        return $this->db->get(T_KENSA)->result();
    }

    public function insert($data)
    {
        $insertData = array();
        foreach (getAllColumnNameAndDefaultValue($this->db, T_KENSA) as $column => $defaultValue) {
            $insertData[$column] = $data[$column] ?? $defaultValue;
        }
        $insertData["INS_DATE"] = $insertData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->insert(T_KENSA, $insertData);
    }

    public function update($data)
    {
        $updateData = array();
        $columnNameAndDefaultValue = getAllColumnNameAndDefaultValue($this->db, T_KENSA);
        foreach (array_intersect_key($data, $columnNameAndDefaultValue) as $key => $value) {
            $updateData[$key] = $value ?? $columnNameAndDefaultValue[$key];
        }
        $updateData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->where(array(
            "RECIPIENT_ID" => $data["RECIPIENT_ID"],
            "ZOKI_CODE" => $data["ZOKI_CODE"],
            "ISYOKU_CNT" => $data["ISYOKU_CNT"],
            "KENSA_NAME" => $data["KENSA_NAME"],
        ));

        $this->db->update(T_KENSA, $updateData);
    }
}
