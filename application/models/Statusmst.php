<?php
class Statusmst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getStatusMst()
    {
        return $this->db->get(STT_MST)->result();
    }

    public function getStatusMstById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(STT_MST)->row();
    }
}
