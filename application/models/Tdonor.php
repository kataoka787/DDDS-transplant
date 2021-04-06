<?php
class Tdonor extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllByPrimaryKeys($donorId)
    {
        empty($donorId) || $this->db->where("DONOR_ID", $donorId);
        return $this->db->get(T_DONOR)->result();
    }

    public function getOneByPrimaryKeys($donorId)
    {
        empty($donorId) || $this->db->where("DONOR_ID", $donorId);
        return $this->db->get(T_DONOR)->row();
    }

    public function insert($data)
    {
        $insertData = array();
        foreach (getAllColumnNameAndDefaultValue($this->db, T_DONOR) as $column => $defaultValue) {
            $insertData[$column] = $data[$column] ?? $defaultValue;
        }
        $insertData["INS_DATE"] = $insertData["UPD_DATE"] = date(DATE_TIME_LONG);
        $this->db->insert(T_DONOR, $insertData);
    }

    public function getColumnName()
    {
        return $this->db->list_fields(T_DONOR);
    }

}
