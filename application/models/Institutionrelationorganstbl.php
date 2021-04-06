<?php
class Institutionrelationorganstbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertInstitutionRelationOrgansTblData($data)
    {
        $insert = array(
            "institution_mst_id" => $data['institution_mst_id'],
            "internal_organs_mst_id" => $data['internal_organs_mst_id'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );

        $this->db->insert(INSTITUTION_ORGAN_TBL, $insert);
        return $this->db->insert_id();
    }

    public function deleteInstitutionRelationOrgansTblByInstitutionId($institution_mst_id)
    {
        $this->db->where('institution_mst_id', $institution_mst_id);
        $this->db->delete(INSTITUTION_ORGAN_TBL);
    }

}
