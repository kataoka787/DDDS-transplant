<?php
class Ishokugokeika extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isInstitutionCodeInUse($institutionCode)
    {
        $this->db->where("ISYOKU_ISYOKUSISETU_CD", $institutionCode);
        $this->db->or_where("ISHOKUGO_KEIKAJYOUHOU_SISETU_CD", $institutionCode);       
        return !empty($this->db->count_all_results(ISHOKUGO_KEIKA));
    }
}
