<?php
class Workmst extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getWorkMst()
    {
        return $this->db->get(WORK_MST)->result();
    }

    public function getWorkMstById($id)
    {
        $this->db->where('workMst.id', $id);
        return $this->db->get(WORK_MST)->row();
    }
}
