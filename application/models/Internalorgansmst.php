<?php
class Internalorgansmst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getInternalOrgansMst()
    {
        return $this->db->get(INTERNAL_ORGAN_MST)->result();
    }

    public function getInternalOrgansMstById($id)
    {
        $this->db->where("id", $id);
        return $this->db->get(INTERNAL_ORGAN_MST)->row();
    }

    public function getInternalOrgansMstByIds($ids)
    {
        $this->db->where_in("id", $ids);
        return $this->db->get(INTERNAL_ORGAN_MST)->result();
    }
}
