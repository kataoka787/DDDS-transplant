<?php
class Donorbasetbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSearchList($query = array(), $offset = null, $limit = null)
    {
        $this->db->select('donorBaseTbl.*');
        $this->db->join(PREF_MST, 'donorBaseTbl.pref_mst_id = prefMst.id');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'prefMst.block_mst_id = affiliationBlockTbl.id');
        $this->db->join(BLOCK_MST, 'affiliationBlockTbl.block_mst_id  = blockMst.id');
        /* Extract query to variable */
        extract($query);
        empty($dId) || $this->db->where('donorBaseTbl.d_id', $dId);
        empty($blockId) || $this->db->where('blockMst.id', $blockId);
        empty($offerInstitution) || $this->db->like('donorBaseTbl.offer_institution_name', $offerInstitution);
        empty($sex) || $this->db->where('donorBaseTbl.sex', $sex);
        (isset($age) && $age !== "") && $this->db->where('donorBaseTbl.age', $age);
        $this->db->order_by("donorBaseTbl.d_id desc");
        $limit && $this->db->limit($limit);
        $offset && $this->db->offset($offset);
        $this->db->where("delete_flg", IN_USE_FLG);
        $this->db->where("donorBaseTbl.created_at >=", date('Y-m-d H:i:s', strtotime(config_item("donor_expired"))));
        return $this->db->get(DONOR_BASE_TBL)->result();
    }

    public function getSearchListCount($query = array())
    {
        $this->db->select('donorBaseTbl.*');
        $this->db->join(PREF_MST, 'donorBaseTbl.pref_mst_id = prefMst.id');
        $this->db->join(AFFILIATION_BLOCK_TBL, 'prefMst.block_mst_id = affiliationBlockTbl.id');
        $this->db->join(BLOCK_MST, 'affiliationBlockTbl.block_mst_id  = blockMst.id');
        /* Extract query to variable */
        extract($query);
        empty($dId) || $this->db->where('donorBaseTbl.d_id', $dId);
        empty($blockId) || $this->db->where('blockMst.id', $blockId);
        empty($offerInstitution) || $this->db->like('donorBaseTbl.offer_institution_name', $offerInstitution);
        empty($sex) || $this->db->where('donorBaseTbl.sex', $sex);
        (isset($age) && $age !== "") && $this->db->where('donorBaseTbl.age', $age);
        $this->db->where("delete_flg", IN_USE_FLG);
        $this->db->where("donorBaseTbl.created_at >=", date('Y-m-d H:i:s', strtotime(config_item("donor_expired"))));
        return $this->db->count_all_results(DONOR_BASE_TBL);
    }

    public function getDonorBaseTblByDid($dId)
    {
        $this->db->where('d_id', $dId);
        $this->db->where("delete_flg", IN_USE_FLG);
        return $this->db->get(DONOR_BASE_TBL)->row();
    }

    public function getDonorBaseTblByDidCordinatorTblId($dId)
    {
        $this->db->select('donorBaseTbl.*');
        $this->db->join('prefMst', 'donorBaseTbl.pref_mst_id = prefMst.id');
        $this->db->join('affiliationBlockTbl', 'prefMst.block_mst_id = affiliationBlockTbl.id');
        $this->db->join('blockMst', 'affiliationBlockTbl.block_mst_id  = blockMst.id');
        $this->db->where("delete_flg", IN_USE_FLG);
        $this->db->where('donorBaseTbl.d_id', $dId);
        $this->db->group_by('donorBaseTbl.id');
        return $this->db->get(DONOR_BASE_TBL)->row();
    }

    public function getDonorBaseTblByDateAffiliationMstIdCount($date, $affiliation_mst_id)
    {
        $this->db->where('date_format(donorBaseTbl.created_at,"%Y-%m-%d")', $date);
        $this->db->where('affiliation_mst_id', $affiliation_mst_id);
        return $this->db->count_all_results(DONOR_BASE_TBL);
    }

    public function getDispName($sei, $mei, $string)
    {
        return $sei . $string . $mei;
    }

    public function insertDonorData($data)
    {
        $insert = array(
            "d_id" => $data["d_id"],
            "sei" => $data["firstName"],
            "mei" => $data["secondName"],
            "age" => $data["age"],
            "sex" => $data["sex"],
            "offer_institution_name" => $data["offerInstitution"],
            "pref_mst_id" => $data["offerInstitutionPref"],
            "cause_death_mst_id" => $data["deathReasonMstId"],
            "comment" => $data["message"],
            "affiliation_mst_id" => $data['affiliation_mst_id'],
            "donor_boxfolder_id" => $data['donor_boxfolder_id'],
            "offer_institution_boxfolder_id" => $data['offer_institution_boxfolder_id'],
            "jot_boxfolder_id" => $data['jot_boxfolder_id'],
            "institution_boxfolder_id" => $data['institution_boxfolder_id'],
            "delete_flg" => IN_USE_FLG,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(DONOR_BASE_TBL, $insert);
        return $this->db->trans_status();
    }

    public function updateDonorData($data, $d_id)
    {
        $update = array(
            'sei' => $data['firstName'],
            'mei' => $data['secondName'],
            'age' => $data['age'],
            'sex' => $data['sex'],
            'offer_institution_name' => $data['offerInstitution'],
            'pref_mst_id' => $data['offerInstitutionPref'],
            'cause_death_mst_id' => $data['deathReasonMstId'],
            'comment' => $data['message'],
            'affiliation_mst_id' => $data['affiliation_mst_id'],
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $this->db->where('d_id', $d_id);
        $this->db->update(DONOR_BASE_TBL, $update);
    }

    public function deleteDonorData($d_id)
    {
        $this->db->where('d_id', $d_id);
        $this->db->update("donorBaseTbl", array(
            "donor_boxfolder_id" => null,
            "offer_institution_boxfolder_id" => null,
            "jot_boxfolder_id" => null,
            "institution_boxfolder_id" => null,
            "delete_flg" => DELETED_FLG,
        ));
    }

}
