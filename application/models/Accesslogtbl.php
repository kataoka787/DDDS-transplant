<?php
class Accesslogtbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertAccessLogTbl($data)
    {
        $insert = array(
            "account_tbl_id" => $data["account_tbl_id"],
            "affiliation_mst_id" => $data["affiliation_mst_id"],
            "account_type_mst_id" => $data["account_type_mst_id"],
            "d_id" => $data["d_id"],
            "url" => $data["url"],
            "ip_address" => $data["ip_address"],
            "user_agent" => $data["user_agent"],
            "get_param" => $data["get_param"],
            "post_param" => $data["post_param"],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(ACCESS_LOG_TBL, $insert);
    }

    public function getAccessLogSearchListCount($accType, $seiKana, $meiKana, $dId, $from, $to)
    {
        $this->db->select("accountTypeMst.account_type,accountTbl.sei,accountTbl.mei,accessLogTbl.url,accessLogTbl.ip_address,accessLogTbl.user_agent,accessLogTbl.get_param,accessLogTbl.post_param,accessLogTbl.d_id,accessLogTbl.created_at");
        $this->db->join("accountTbl", "accessLogTbl.account_tbl_id = accountTbl.id");
        $this->db->join("accountTypeMst", "accountTbl.account_type_mst_id = accountTypeMst.id");
        empty($accType) || $this->db->where("accountTypeMst.id", $accType);
        empty($seiKana) || $this->db->like("accountTbl.sei_kana", $seiKana);
        empty($meiKana) || $this->db->like("accountTbl.mei_kana", $meiKana);
        empty($dId) || $this->db->where("accessLogTbl.d_id", $dId);
        empty($from) || $this->db->where("accessLogTbl.created_at >= ", $from . " 00:00:00");
        empty($to) || $this->db->where("accessLogTbl.created_at <= ", $to . " 23:59:59");
        $this->db->where("accessLogTbl.affiliation_mst_id != ", 0);
        return $this->db->count_all_results(ACCESS_LOG_TBL);
    }

    public function getAccessLogSearchList($accType, $seiKana, $meiKana, $dId, $from, $to, $offset = 0, $limit = 0)
    {
        $this->db->select("accountTypeMst.account_type,accountTbl.sei,accountTbl.mei,accessLogTbl.url,accessLogTbl.ip_address,accessLogTbl.user_agent,accessLogTbl.get_param,accessLogTbl.post_param,accessLogTbl.d_id,accessLogTbl.created_at");
        $this->db->join("accountTbl", "accessLogTbl.account_tbl_id = accountTbl.id");
        $this->db->join("accountTypeMst", "accountTbl.account_type_mst_id = accountTypeMst.id");
        empty($accType) || $this->db->where("accountTypeMst.id", $accType);
        empty($seiKana) || $this->db->like("accountTbl.sei_kana", $seiKana);
        empty($meiKana) || $this->db->like("accountTbl.mei_kana", $meiKana);
        empty($dId) || $this->db->where("accessLogTbl.d_id", $dId);
        empty($from) || $this->db->where("accessLogTbl.created_at >= ", $from . " 00:00:00");
        empty($to) || $this->db->where("accessLogTbl.created_at <= ", $to . " 23:59:59");
        $this->db->where("accessLogTbl.affiliation_mst_id != ", 0);
        $limit && $this->db->limit($limit);
        $offset && $this->db->offset($offset);
        return $this->db->get(ACCESS_LOG_TBL)->result();
    }

}
