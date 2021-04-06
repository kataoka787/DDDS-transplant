<?php
class Cordinatortbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAffiliationMstId($id)
    {
        $this->db->join(AFFILIATION_CORDINATOR_TBL, 'affiliationCordinatorTbl.cordinator_tbl_id=cordinatorTbl.id');
        $this->db->where('cordinatorTbl.id', $id);
        return $this->db->get(CO_TBL)->row();
    }

    public function getCordinatorByAffiliationMstId()
    {
        $this->db->select('accountTbl.sei,accountTbl.mei, cordinatorTbl.id ,affiliationCordinatorTbl.affiliation_mst_id');
        $this->db->join(ACC_TBL, 'cordinatorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(AFFILIATION_CORDINATOR_TBL, 'cordinatorTbl.id = affiliationCordinatorTbl.cordinator_tbl_id ');
        $this->db->where('accountTbl.account_type_mst_id', ACC_TYPE_CO);
        $this->db->where('cordinatorTbl.cordinator_type_mst_id', 1);
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        return $this->db->get(CO_TBL)->result();
    }

    public function getPrefCordinatorByAffiliationMstId()
    {
        $this->db->select('accountTbl.sei,accountTbl.mei,cordinatorTbl.id, affiliationBlockTbl.affiliation_mst_id,prefMst.id as prefId');
        $this->db->join(ACC_TBL, 'cordinatorTbl.account_tbl_id=accountTbl.id');
        $this->db->join(BLOCK_MST, 'prefMst.block_mst_id = blockMst.id ');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'affiliationBlockTbl.block_mst_id = blockMst.id');
        $this->db->where('accountTbl.account_type_mst_id', ACC_TYPE_CO);
        $this->db->where('cordinatorTbl.cordinator_type_mst_id', 2);
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        return $this->db->get(CO_TBL)->result();
    }

    public function getAccountByInId($ids)
    {
        $this->db->join(ACC_TBL, 'cordinatorTbl.account_tbl_id=accountTbl.id');
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->where_in('cordinatorTbl.id', $ids);
        return $this->db->get('cordinatorTbl')->result();
    }

    public function getCordinatorSearchList($seiKana, $meiKana, $mail, $offset, $limit)
    {
        $this->db->select('accountTbl.id,accountTbl.sei,accountTbl.mei,accountTbl.mail');
        $this->db->join(ACC_TBL, 'cordinatorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(AFFILIATION_CORDINATOR_TBL, 'cordinatorTbl.id = affiliationCordinatorTbl.cordinator_tbl_id', 'left');
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        empty($seiKana) || $this->db->like('accountTbl.sei_kana', $seiKana);
        empty($meiKana) || $this->db->like('accountTbl.mei_kana', $meiKana);
        empty($mail) || $this->db->like('accountTbl.mail', $mail);
        return $this->db->get(CO_TBL, $limit, $offset)->result();
    }

    public function getCordinatorSearchListCount($seiKana, $meiKana, $mail)
    {
        $this->db->select('accountTbl.id,accountTbl.sei,accountTbl.mei,accountTbl.mail');
        $this->db->join(ACC_TBL, 'cordinatorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(AFFILIATION_CORDINATOR_TBL, 'cordinatorTbl.id = affiliationCordinatorTbl.cordinator_tbl_id', 'left');
        $this->db->where('accountTbl.status_mst_id', 3);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        empty($seiKana) || $this->db->like('accountTbl.sei_kana', $seiKana);
        empty($meiKana) || $this->db->like('accountTbl.mei_kana', $meiKana);
        empty($mail) || $this->db->like('accountTbl.mail', $mail);
        return $this->db->count_all_results(CO_TBL);
    }

    public function getCordinatorById($accId)
    {
        $this->db->select("accountTbl.id,accountTbl.sei, accountTbl.mei, accountTbl.sei_kana, accountTbl.mei_kana, accountTbl.password, accountTbl.mail");
        $this->db->select("accountTbl.admin_flg, accountTbl.delete_flg");
        $this->db->select("GROUP_CONCAT(workMst.id) as work_id");
        $this->db->select("GROUP_CONCAT(workMst.work_name ORDER BY workMst.id ASC) as work_name");
        $this->db->join(ACC_TBL, 'cordinatorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(ACC_WORK_TBL, "accountRelationWorkTbl.account_tbl_id = accountTbl.id");
        $this->db->join(WORK_MST, "workMst.id = accountRelationWorkTbl.work_mst_id");
        $this->db->where('accountTbl.id', $accId);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->group_by("cordinatorTbl.id");
        return $this->db->get(CO_TBL)->row();
    }

    public function getCordinatorByAccountTblId($accId)
    {
        $this->db->where('account_tbl_id', $accId);
        return $this->db->get(CO_TBL)->row();
    }

    public function insertCordinatorData($data)
    {
        $insert = array(
            "account_tbl_id" => $data['account_tbl_id'],
            "cordinator_type_mst_id" => $data['cordinator_type_mst_id'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(CO_TBL, $insert);
        return $this->db->insert_id();
    }

    public function updateCordinatorData($data, $accId)
    {
        $this->db->where('account_tbl_id', $accId);
        $this->db->update(CO_TBL, $data);
    }
}
