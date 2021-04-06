<?php
class Tmpupfiletbl extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function insertUpfileData($data)
    {
        $insert = array(
            "d_id" => $data['d_id'],
            "file_category_mst_id" => $data['file_category_mst_id'],
            "file_name" => $data['file_name'],
            "file" => file_get_contents(config_item('upload_file_tmp_path') . $data['file']),
            "account_tbl_id" => $data['account_tbl_id'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(TEMP_UPFILE_TBL, $insert);
        return $this->db->insert_id();
    }

    public function getTmpUpFileByIdAccountId($id, $account_mst_id)
    {
        $this->db->where('tmpUpFileTbl.id', $id);
        $this->db->where('tmpUpFileTbl.account_tbl_id', $account_mst_id);
        return $this->db->get(TEMP_UPFILE_TBL)->row();
    }

    public function deleteTmpUpFileById($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(TEMP_UPFILE_TBL);
    }

    public function deleteTmpUpFile()
    {
        $this->db->where('created_at <=', date('Y-m-d H:i:s', strtotime('-1 hour')));
        $this->db->delete(TEMP_UPFILE_TBL);
    }

    public function deleteTmpUpFileByDid($dId)
    {
        $this->db->where('d_id', $dId);
        $this->db->delete(TEMP_UPFILE_TBL);
    }
}
