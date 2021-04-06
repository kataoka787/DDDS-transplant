<?php
class Doctoraccountcollaborationtbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get collaboration doctor
     *
     * @param string $accId
     * @param string $donorInstitutionOrganId
     * @return object if found
     * @return null if not found
     */
    public function getCollaborationDoctor($accId, $folderId)
    {
        $this->db->join(DONOR_INSTITUTION_ORGAN_TBL, "donorInstitutionOrgansTbl.id = doctorAccountCollaborationTbl.donor_institution_organs_tbl_id");
        $this->db->where(array(
            "account_tbl_id" => $accId,
            "donorInstitutionOrgansTbl.parent_boxfolder_id" => $folderId,
        ));
        return $this->db->get(DOCTOR_ACC_COLLABORARION)->row();
    }

    /**
     * Insert doctor collaboration
     *
     * @param array $data
     * @return insertedId if success
     */
    public function insert($data)
    {
        $insert = array(
            "donor_institution_organs_tbl_id" => $data["donor_institution_organs_tbl_id"],
            "account_tbl_id" => $data["account_tbl_id"],
            "collaboration_id" => $data["collaboration_id"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        );
        $this->db->insert(DOCTOR_ACC_COLLABORARION, $insert);
        return $this->db->insert_id();
    }

    public function deleteByDonorInstitutionOrgansId($id)
    {
        $this->db->where("donor_institution_organs_tbl_id", $id);
        $this->db->delete(DOCTOR_ACC_COLLABORARION);
    }
}
