<?php
class Affiliationcordinatortbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertAffiliationCordinatorTblData($data)
    {
        $insert = array(
            "cordinator_tbl_id" => $data["cordinator_tbl_id"],
            "affiliation_mst_id" => $data["affiliation_mst_id"],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(AFFILIATION_CORDINATOR_TBL, $insert);
    }

    public function updateAffiliationCordinatorTblData($data, $cordinator_tbl_id)
    {
        $this->db->where('cordinator_tbl_id', $cordinator_tbl_id);
        $this->db->update(AFFILIATION_CORDINATOR_TBL, $data);
    }

    public function deleteAffiliationcordinatortblBycordinatorTblId($cordinator_tbl_id)
    {
        $this->db->where('cordinator_tbl_id', $cordinator_tbl_id);
        $this->db->delete(AFFILIATION_CORDINATOR_TBL);
    }

}
