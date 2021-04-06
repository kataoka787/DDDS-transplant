<?php
class Tliving extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $inputDate = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($inputDate) || $this->db->where('INPUT_DATE', $inputDate);
        $this->db->where('DEL_FLG', IN_USE_FLG);
        $this->db->order_by("
            (CASE
                WHEN (CYCLE IS NULL) OR (CYCLE = '') THEN 0
                ELSE 1
            END)"
        );
        $this->db->order_by('INPUT_DATE', 'DESC');
        return $this->db->get(T_LIVING)->result();
    }

    public function getOneByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $inputDate = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($inputDate) || $this->db->where('INPUT_DATE', $inputDate);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        return $this->db->get(T_LIVING)->row();
    }

    public function deleteByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $inputDate = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($inputDate) || $this->db->where('INPUT_DATE', $inputDate);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        $this->db->delete(T_LIVING);
    }

    public function getByPrimaryKeyCycle($recipientId = null, $zokiCode = null, $isyokuCnt = null, $cycle = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($cycle) || $this->db->where('CYCLE', $cycle);
        $this->db->where('DEL_FLG', IN_USE_FLG);
        return $this->db->get(T_LIVING)->row();
    }

    public function getTLivingDownloadCSV($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->where('T_LIVING.RECIPIENT_ID', $recipientId);
        $this->db->where('T_LIVING.ZOKI_CODE', $zokiCode);
        $this->db->where('T_LIVING.ISYOKU_CNT', $isyokuCnt);

        return $this->db->get(T_LIVING)->result();
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
        return $this->db->get(T_LIVING)->result();
    }

    public function insert($data)
    {
        $insertData = array();
        foreach (getAllColumnNameAndDefaultValue($this->db, T_LIVING) as $column => $defaultValue) {
            $insertData[$column] = $data[$column] ?? $defaultValue;
        }
        $insertData["DEL_FLG"] = empty($data['DEL_FLG']) ? IN_USE_FLG : $data['DEL_FLG'];
        $insertData["INS_DATE"] = $insertData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->insert(T_LIVING, $insertData);
    }

    public function update($data)
    {
        $updateData = array();
        $columnNameAndDefaultValue = getAllColumnNameAndDefaultValue($this->db, T_LIVING);
        foreach (array_intersect_key($data, $columnNameAndDefaultValue) as $key => $value) {
            $updateData[$key] = $value ?? $columnNameAndDefaultValue[$key];
        }
        $updateData["UPD_DATE"] = date(DATE_TIME_LONG);
        $updateData["DEL_FLG"] = empty($data['SHOULD_DELETE']) ? IN_USE_FLG : DELETED_FLG;
        $this->db->where(array(
            "RECIPIENT_ID" => $data["RECIPIENT_ID"],
            "ZOKI_CODE" => $data["ZOKI_CODE"],
            "ISYOKU_CNT" => $data["ISYOKU_CNT"],
            "INPUT_DATE" => $data["ORIGINAL_INPUT_DATE"] ?? $data["INPUT_DATE"],
        ));

        $this->db->update(T_LIVING, $updateData);
    }

    public function getColumnName()
    {
        return $this->db->list_fields(T_LIVING);
    }

}
