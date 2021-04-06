<?php
class Blockmst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBlockmst()
    {
        return $this->db->get(BLOCK_MST)->result();
    }

    public function getBlockMstById($id)
    {
        $this->db->where("id", $id);
        return $this->db->get(BLOCK_MST)->row();
    }

    public function getBlockMstAffiliationMstIdByBlockMstId($id)
    {
        $this->db->select('affiliationBlockTbl.affiliation_mst_id');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'blockMst.id = affiliationBlockTbl.block_mst_id');
        $this->db->where("blockMst.id", $id);
        return $this->db->get(BLOCK_MST)->row();
    }

    public function getBlockmstByAffiliation($affiliation_mst_id)
    {
        $this->db->select('blockMst.id, blockMst.block_name');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'affiliationBlockTbl.block_mst_id  = blockMst.id');

        if ($affiliation_mst_id == '2' || $affiliation_mst_id == '3' || $affiliation_mst_id == '4') {
            $this->db->where('affiliationBlockTbl.affiliation_mst_id', $affiliation_mst_id);
        }
        return $this->db->get(BLOCK_MST)->result();
    }



    public function getBlockmstByAffiliationCheck($id)
    {
        $this->db->select('blockMst.id,blockMst.block_name');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'affiliationBlockTbl.block_mst_id = blockMst.id');
        $this->db->where('blockMst.id', $id);
        return $this->db->get(BLOCK_MST)->num_rows();
    }
}
