<?php
class Passwordhistorytbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get most recent (6 time by default) password
     *
     * @param string $accId
     * @param string $limit
     * @return void
     */
    public function getRecentPassword($accId, $limit)
    {
        $this->db->where("account_tbl_id", $accId);
        $this->db->limit($limit);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get(PASSWORD_HISTORY_TBL)->result();
    }

    public function insert($data, $isHashed = true)
    {
        $insert = array(
            "account_tbl_id" => $data["account_tbl_id"],
            "password" => $data["password"],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        /* Hash password if password wasn't hashed */
        $isHashed || $insert["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
        $this->db->insert(PASSWORD_HISTORY_TBL, $insert);
    }
}
