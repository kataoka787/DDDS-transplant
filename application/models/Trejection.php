<?php
class Trejection extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $rejectionType = null, $sindanDate = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($rejectionType) || $this->db->where('REJECTION_TYPE', $rejectionType);
        empty($sindanDate) || $this->db->where('SINDAN_DATE', $sindanDate);
        $this->db->where('DEL_FLG', IN_USE_FLG);
        $this->db->order_by('SINDAN_DATE', 'DESC');
        return $this->db->get(T_REJECTION)->result();
    }

    public function getOneByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $rejectionType = null, $sindanDate = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($rejectionType) || $this->db->where('REJECTION_TYPE', $rejectionType);
        empty($sindanDate) || $this->db->where('SINDAN_DATE', $sindanDate);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        $this->db->order_by('SINDAN_DATE', 'DESC');
        return $this->db->get(T_REJECTION)->result();
    }

    public function deleteByPrimaryKeys($recipientId = null, $zokiCode = null, $isyokuCnt = null, $rejectionType = null, $sindanDate = null, $isDeleted = null)
    {
        empty($recipientId) || $this->db->where('RECIPIENT_ID', $recipientId);
        empty($zokiCode) || $this->db->where('ZOKI_CODE', $zokiCode);
        empty($isyokuCnt) || $this->db->where('ISYOKU_CNT', $isyokuCnt);
        empty($rejectionType) || $this->db->where('REJECTION_TYPE', $rejectionType);
        empty($sindanDate) || $this->db->where('SINDAN_DATE', $sindanDate);
        isset($isDeleted) && $this->db->where('DEL_FLG', $isDeleted);
        $this->db->delete(T_REJECTION);
    }

    public function getTRejectionDownloadCSV($recipientId, $zokiCode, $isyokuCnt)
    {
        $this->db->where('T_REJECTION.RECIPIENT_ID', $recipientId);
        $this->db->where('T_REJECTION.ZOKI_CODE', $zokiCode);
        $this->db->where('T_REJECTION.ISYOKU_CNT', $isyokuCnt);

        return $this->db->get(T_REJECTION)->result();
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
        return $this->db->get(T_REJECTION)->result();
    }

    public function insert($data)
    {
        $insertData = array();
        foreach (getAllColumnNameAndDefaultValue($this->db, T_REJECTION) as $column => $defaultValue) {
            $insertData[$column] = $data[$column] ?? $defaultValue;
        }
        $insertData["DEL_FLG"] = empty($data['DEL_FLG']) ? IN_USE_FLG : $data['DEL_FLG'];
        $insertData["INS_DATE"] = $insertData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->insert(T_REJECTION, $insertData);
    }

    public function update($data)
    {
        $updateData = array();
        $columnNameAndDefaultValue = getAllColumnNameAndDefaultValue($this->db, T_REJECTION);
        foreach (array_intersect_key($data, $columnNameAndDefaultValue) as $key => $value) {
            $updateData[$key] = $value ?? $columnNameAndDefaultValue[$key];
        }
        $updateData["UPD_DATE"] = date(DATE_TIME_LONG);
        $updateData["DEL_FLG"] = empty($data['SHOULD_DELETE']) ? IN_USE_FLG : DELETED_FLG;

        $this->db->where(array(
            "RECIPIENT_ID" => $data["RECIPIENT_ID"],
            "ZOKI_CODE" => $data["ZOKI_CODE"],
            "ISYOKU_CNT" => $data["ISYOKU_CNT"],
            "REJECTION_TYPE" => $data["REJECTION_TYPE"],
            "SINDAN_DATE" => $data["ORIGINAL_SINDAN_DATE"] ?? $data["SINDAN_DATE"],
        ));

        $this->db->update(T_REJECTION, $updateData);
    }

    public function getColumnName()
    {
        return $this->db->list_fields(T_REJECTION);
    }
}
