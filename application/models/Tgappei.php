<?php
class Tgappei extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $gappei = null, $nyuinDate = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($gappei) || $this->db->where('GAPPEI', $gappei);
        empty($nyuinDate) || $this->db->where('NYUIN_DATE', $nyuinDate);
        $this->db->where('DEL_FLG', IN_USE_FLG);
        $this->db->order_by('NYUIN_DATE DESC', 'GAPPEI');
        return $this->db->get(T_GAPPEI)->result();
    }

    public function getOneByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $gappei = null, $nyuinDate = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($gappei) || $this->db->where('GAPPEI', $gappei);
        empty($nyuinDate) || $this->db->where('NYUIN_DATE', $nyuinDate);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        return $this->db->get(T_GAPPEI)->row();
    }

    public function deleteByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $gappei = null, $nyuinDate = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($gappei) || $this->db->where('GAPPEI', $gappei);
        empty($nyuinDate) || $this->db->where('NYUIN_DATE', $nyuinDate);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        $this->db->delete(T_GAPPEI);
    }

    public function getTGappeiDownloadCSV($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->where('T_GAPPEI.RECIPIENT_ID', $recipientId);
        $this->db->where('T_GAPPEI.ZOKI_CODE', $zokiCode);
        $this->db->where('T_GAPPEI.ISYOKU_CNT', $isyokuCnt);

        return $this->db->get(T_GAPPEI)->result();
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
        return $this->db->get(T_GAPPEI)->result();
    }

    public function insert($data)
    {
        $insertData = array();
        foreach (getAllColumnNameAndDefaultValue($this->db, T_GAPPEI) as $column => $defaultValue) {
            $insertData[$column] = $data[$column] ?? $defaultValue;
        }
        $insertData["DEL_FLG"] = empty($data['DEL_FLG']) ? IN_USE_FLG : $data['DEL_FLG'];
        $insertData["INS_DATE"] = $insertData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->insert(T_GAPPEI, $insertData);
    }

    public function update($data)
    {
        $updateData = array();
        $columnNameAndDefaultValue = getAllColumnNameAndDefaultValue($this->db, T_GAPPEI);
        foreach (array_intersect_key($data, $columnNameAndDefaultValue) as $key => $value) {
            $updateData[$key] = $value ?? $columnNameAndDefaultValue[$key];
        }
        $updateData["UPD_DATE"] = date(DATE_TIME_LONG);
        $updateData["DEL_FLG"] = empty($data['SHOULD_DELETE']) ? IN_USE_FLG : DELETED_FLG;
        $this->db->where(array(
            "RECIPIENT_ID" => $data["RECIPIENT_ID"],
            "ZOKI_CODE" => $data["ZOKI_CODE"],
            "ISYOKU_CNT" => $data["ISYOKU_CNT"],
            "GAPPEI" => $data["ORIGINAL_GAPPEI"] ?? $data["GAPPEI"],
            "NYUIN_DATE" => $data["ORIGINAL_NYUIN_DATE"] ?? $data["NYUIN_DATE"],
        ));
        $this->db->update(T_GAPPEI, $updateData);
    }

    public function getColumnName()
    {
        return $this->db->list_fields(T_GAPPEI);
    }

}
