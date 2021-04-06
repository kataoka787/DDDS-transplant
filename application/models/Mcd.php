<?php
class Mcd extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByCodeTypeCode($codeType, $code)
    {
        $this->db->where(array(
            "CODE_TYPE" => $codeType,
            "CODE" => $code,
        ));
        return $this->db->get(M_CD)->row();
    }

    public function getCodeByCodeTypeValue($codeType, $value)
    {
        $this->db->where(array(
            "CODE_TYPE" => $codeType,
            "VALUE" => $value,
        ));
        return $this->db->get(M_CD)->row();
    }

    public function getByCodeType($codeType, $value2 = null)
    {
        $this->db->where('CODE_TYPE', $codeType);
        empty($value2) || $this->db->where('VALUE2', $value2);
        $this->db->order_by('DSPNO');
        return $this->db->get(M_CD)->result();
    }

    public function getCodeValueArrayByCodeType($codeType, $value2 = null)
    {
        $this->db->where('CODE_TYPE', $codeType);
        empty($value2) || $this->db->where('VALUE2', $value2);
        $this->db->order_by('DSPNO');
        $result = array();
        foreach ($this->db->get(M_CD)->result() as $item) {
            $result[$item->CODE] = $item->VALUE;
        }
        return $result;
    }

    public function getMcd()
    {
        return $this->db->get(M_CD)->result();
    }
}
