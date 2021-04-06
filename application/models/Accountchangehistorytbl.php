<?php
class Accountchangehistorytbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAccountChangeHistorySearchListCount($accType, $seiKana, $meiKana, $from, $to)
    {
        $this->db->select("accountTbl.sei,accountTbl.mei,accountTbl.sei_kana,accountTbl.mei_kana,accountTypeMst.account_type,accountChangeHistoryTbl.contents,accountChangeHistoryTbl.affiliation_mst_id,accountChangeHistoryTbl.created_at");
        $this->db->join(ACC_TBL, "accountChangeHistoryTbl.account_tbl_id = accountTbl.id");
        $this->db->join(ACC_TYPE_MST, "accountTbl.account_type_mst_id = accountTypeMst.id");
        empty($accType) || $this->db->where("accountTypeMst.id", $accType);
        empty($seiKana) || $this->db->like("accountTbl.sei_kana", $seiKana);
        empty($meiKana) || $this->db->like("accountTbl.mei_kana", $meiKana);
        empty($from) || $this->db->where("accountChangeHistoryTbl.created_at >= ", $from . " 00:00:00");
        empty($to) || $this->db->where("accountChangeHistoryTbl.created_at <= ", $to . " 23:59:59");
        return $this->db->count_all_results(ACC_CHANGE_HISTORY_TBL);
    }

    public function getAccountChangeHistorySearchList($accType, $seiKana, $meiKana, $from, $to, $offset = 0, $limit = 0)
    {
        $this->db->select("accountTbl.sei,accountTbl.mei,accountTbl.sei_kana,accountTbl.mei_kana,accountTypeMst.account_type,accountChangeHistoryTbl.contents,accountChangeHistoryTbl.affiliation_mst_id,accountChangeHistoryTbl.created_at");
        $this->db->join(ACC_TBL, "accountChangeHistoryTbl.account_tbl_id = accountTbl.id");
        $this->db->join(ACC_TYPE_MST, "accountTbl.account_type_mst_id = accountTypeMst.id");
        empty($accType) || $this->db->where("accountTypeMst.id", $accType);
        empty($seiKana) || $this->db->like("accountTbl.sei_kana", $seiKana);
        empty($meiKana) || $this->db->like("accountTbl.mei_kana", $meiKana);
        empty($from) || $this->db->where("accountChangeHistoryTbl.created_at >= ", $from . " 00:00:00");
        empty($to) || $this->db->where("accountChangeHistoryTbl.created_at <= ", $to . " 23:59:59");
        $this->db->order_by("accountChangeHistoryTbl.created_at desc");
        $limit && $this->db->limit($limit);
        $offset && $this->db->offset($offset);
        return $this->db->get(ACC_CHANGE_HISTORY_TBL)->result();
    }

    public function insertAccountChangeHistoryTblData($data)
    {
        $insert = array(
            "account_tbl_id" => $data["account_tbl_id"],
            "contents" => $data["contents"],
            "account_type_mst_id" => $data["account_type_mst_id"],
            "affiliation_mst_id" => $data["affiliation_mst_id"],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(ACC_CHANGE_HISTORY_TBL, $insert);
    }
}
