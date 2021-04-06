<?php
class Donorinstitutionorganstbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRequestFolder($dId, $institutionId, $organId)
    {
        $this->db->where(array(
            "d_id" => $dId,
            "institution_mst_id" => $institutionId,
            "internal_organs_mst_id" => $organId,
        ));
        return $this->db->get(DONOR_INSTITUTION_ORGAN_TBL)->row();
    }

    public function getRequestFolders($dId, $institutionId, $organId)
    {
        $dId && $this->db->where("d_id", $dId);
        $institutionId && $this->db->where("institution_mst_id", $institutionId);
        $organId && $this->db->where("internal_organs_mst_id", $organId);
        return $this->db->get(DONOR_INSTITUTION_ORGAN_TBL)->result();
    }

    public function insert($data)
    {
        $insert = array(
            "d_id" => $data["d_id"],
            "institution_mst_id" => $data["institution_mst_id"],
            "internal_organs_mst_id" => $data["internal_organs_mst_id"],
            "parent_boxfolder_id" => $data["parent_boxfolder_id"],
            "donorinfo_boxfolder_id" => $data["donorinfo_boxfolder_id"],
            "jot_offer_boxfolder_id" => $data["jot_offer_boxfolder_id"],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(DONOR_INSTITUTION_ORGAN_TBL, $insert);
        return $this->db->insert_id();
    }

    public function getByDid($dId)
    {
        $this->db->where("d_id", $dId);
        return $this->db->get(DONOR_INSTITUTION_ORGAN_TBL)->result();
    }

    public function deleteByDid($dId)
    {
        $this->db->where("d_id", $dId);
        $this->db->delete(DONOR_INSTITUTION_ORGAN_TBL);
    }

}
