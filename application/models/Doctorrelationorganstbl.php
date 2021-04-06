<?php
class Doctorrelationorganstbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertDoctorRelationOrgansTblData($data)
    {
        $insert = array(
            "doctor_tbl_id" => $data['doctor_tbl_id'],
            "internal_organs_mst_id" => $data['internal_organs_mst_id'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(DOCTOR_ORGAN_TBL, $insert);
        return $this->db->insert_id();
    }

    public function deleteDoctorRelationOrgansTblData($doctorId)
    {
        $this->db->where('doctor_tbl_id', $doctorId);
        $this->db->delete(DOCTOR_ORGAN_TBL);
    }
}
