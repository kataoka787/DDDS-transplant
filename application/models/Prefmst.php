<?php
class Prefmst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPrefMst()
    {
        return $this->db->get(PREF_MST)->result();
    }

    public function getPrefMstById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(PREF_MST)->row();
    }

    public function getPrefMstByAffiliation($affiliation_mst_id)
    {
        $this->db->select('prefMst.id,prefMst.pref_name');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'prefMst.block_mst_id = affiliationBlockTbl.id');
        $this->db->join(BLOCK_MST, 'affiliationBlockTbl.block_mst_id  = blockMst.id');
        if ($affiliation_mst_id == '2' || $affiliation_mst_id == '3' || $affiliation_mst_id == '4') {
            $this->db->where('affiliationBlockTbl.affiliation_mst_id', $affiliation_mst_id);
        }
        return $this->db->get(PREF_MST)->result();
    }

    public function getAffiliationIdByPrefId($id)
    {
        $this->db->select('affiliationBlockTbl.affiliation_mst_id');
        $this->db->join(BLOCK_MST, 'prefMst.block_mst_id = blockMst.id');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'affiliationBlockTbl.block_mst_id = blockMst.id');
        $this->db->where('prefMst.id', $id);
        return $this->db->get(PREF_MST)->row();
    }

    public function getPrefNameById($id)
    {
        $this->db->where('id', $id);
        $pref = $this->db->get(PREF_MST)->row();
        if ($pref) {
            return $pref->pref_name;
        }
        return $pref;
    }

    public function getPrefMstByBlockId($blockId)
    {
        $this->db->select('prefMst.id,prefMst.pref_name');
        $this->db->where('block_mst_id', $blockId);
        return $this->db->get(PREF_MST)->result();
    }
}
