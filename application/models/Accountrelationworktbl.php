<?php
class Accountrelationworktbl extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function delete($accountId)
    {
        $this->db->where("account_tbl_id", $accountId);
        $this->db->delete(ACC_WORK_TBL);
    }

    public function insert($data)
    {
        $insert = array(
            "account_tbl_id" => $data["account_tbl_id"],
            "work_mst_id" => $data["work_mst_id"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        );
        $this->db->insert(ACC_WORK_TBL, $insert);
    }
}
