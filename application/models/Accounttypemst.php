<?php
class Accounttypemst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAccountType()
    {
        return $this->db->get(ACC_TYPE_MST)->result();
    }
    public function getAccountTypeById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(ACC_TYPE_MST)->row();
    }
}
