<?php
class Affiliationmst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAffiliationMst()
    {
        return $this->db->get(AFFILIATION_MST)->result();
    }

    public function getAffiliationMstById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(AFFILIATION_MST)->row();
    }
}
