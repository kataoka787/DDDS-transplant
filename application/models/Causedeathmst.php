<?php
class Causedeathmst extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function getCauseDeathMst()
    {
        return $this->db->get(CAUSE_DEATCH_MST)->result();
    }

    function getCauseDeathNameById($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->get(CAUSE_DEATCH_MST)->row();
        if ($result) {
            return $result->cause_death_name;
        }
        return $result;
    }
}
