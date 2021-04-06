<?php
class Doctortbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTransplantDoctorOrgansByDoctorId($account_tbl_id)
    {
        $this->db->select('doctorTbl.institution_mst_id,doctorRelationOrgansTbl.internal_organs_mst_id');
        $this->db->join('doctorRelationOrgansTbl', 'doctorRelationOrgansTbl.doctor_tbl_id =  doctorTbl.id');
        $this->db->where('doctorTbl.account_tbl_id', $account_tbl_id);
        return $this->db->get(DOCTOR_TBL)->result();
    }

    public function getTransplantDoctorTblDataByAccountId($account_tbl_id)
    {
        $this->db->where('account_tbl_id', $account_tbl_id);
        return $this->db->get(DOCTOR_TBL)->row();
    }

    public function getTransplantDoctorTblData($accId)
    {
        $this->db->select("doctorTbl.institution_mst_id, doctorTbl.boxfolder_id");
        $this->db->select("accountTbl.id, accountTbl.sei, accountTbl.mei, accountTbl.sei_kana, accountTbl.mei_kana, accountTbl.password, accountTbl.mail");
        $this->db->select("accountTbl.status_mst_id, accountTbl.admin_flg");
        $this->db->select("institutionMst.institution_name, institutionMst.pref_mst_id, institutionMst.institution_kubun, institutionMst.SISETU_CD");
        $this->db->select("prefMst.pref_name");
        $this->db->select("GROUP_CONCAT(DISTINCT (internalOrgansMst.id)) as organ_id");
        $this->db->select("GROUP_CONCAT(DISTINCT (internalOrgansMst.organ_name) ORDER BY internalOrgansMst.id ASC) as organ_name");
        $this->db->select("GROUP_CONCAT(DISTINCT (workMst.id)) as work_id");
        $this->db->select("GROUP_CONCAT(DISTINCT (workMst.work_name) ORDER BY workMst.id ASC) as work_name");
        $this->db->join(ACC_TBL, "accountTbl.id = doctorTbl.account_tbl_id");
        /* Account is in-use */
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        /* Search by account id */
        $this->db->where("accountTbl.id", $accId);
        $this->db->join(INSTITUTION_MST, "institutionMst.id = doctorTbl.institution_mst_id");
        $this->db->join(PREF_MST, "prefMst.id = institutionMst.pref_mst_id");
        $this->db->join(DOCTOR_ORGAN_TBL, "doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id");
        $this->db->join(INTERNAL_ORGAN_MST, "internalOrgansMst.id = doctorRelationOrgansTbl.internal_organs_mst_id");
        $this->db->join(ACC_WORK_TBL, "accountRelationWorkTbl.account_tbl_id = accountTbl.id");
        $this->db->join(WORK_MST, "workMst.id = accountRelationWorkTbl.work_mst_id");
        /* Group by account id */
        $this->db->group_by("accountTbl.id");
        return $this->db->get(DOCTOR_TBL)->row();
    }

    public function insertTransplantDoctorTblData($data)
    {
        $insert = array(
            "account_tbl_id" => $data['account_tbl_id'],
            "institution_mst_id" => $data['institution_mst_id'],
            "boxfolder_id" => $data["boxfolder_id"],
            "box_collaboration_id" => $data["box_collaboration_id"],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );

        $this->db->insert(DOCTOR_TBL, $insert);
        return $this->db->insert_id();
    }

    public function getTransplantDoctorByMail($mail)
    {
        $this->db->join('accountTbl', 'accountTbl.id = doctorTbl.account_tbl_id');
        $this->db->where('accountTbl.mail', $mail);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->where('accountTbl.account_type_mst_id', ACC_TYPE_TP);
        return $this->db->get(DOCTOR_TBL)->row();
    }

    public function getTransplantDoctorTblDataByAccountIds($account_tbl_ids)
    {
        $count = $this->db->count_all('internalOrgansMst');

        $select = "a.id,a.sei,a.mei,a.sei_kana,a.mei_kana,a.mail,a.password,a.institution_name,a.account_type_mst_id,a.block_name,a.block_mst_id,a.institution_mst_id,a.status_mst_id,a.status,";
        $select2 = "accountTbl.id,accountTbl.sei,accountTbl.mei,accountTbl.sei_kana,accountTbl.mei_kana,accountTbl.mail,accountTbl.password,accountTbl.account_type_mst_id,institutionMst.id as institution_mst_id,institutionMst.institution_name,blockMst.id as block_mst_id,blockMst.block_name,statusMst.id as status_mst_id ,statusMst.status,";

        for ($i = 1; $i <= $count; $i++) {
            $select .= "max(a.organ" . $i . ") as organ" . $i;
            $select2 .= "case when doctorRelationOrgansTbl.internal_organs_mst_id = " . $i . " then 1 else 0 end as organ" . $i;
            if ($i != $count) {
                $select .= ",";
                $select2 .= ",";
            }
        }
        $sql = "select ";
        $sql .= $select;
        $sql .= " from ( select " . $select2;
        $sql .= " from doctorTbl join accountTbl on doctorTbl.account_tbl_id = accountTbl.id join institutionMst on institutionMst.id = doctorTbl.institution_mst_id join blockMst on blockMst.id = institutionMst.block_mst_id join doctorRelationOrgansTbl on doctorTbl.id = doctor_tbl_id join statusMst on accountTbl.status_mst_id = statusMst.id where accountTbl.delete_flg = '0')as a group by a.id ";
        $count = count($account_tbl_ids);
        $ids = "";
        foreach ($account_tbl_ids as $key => $val) {
            $ids .= $val;
            if ($key != ($count - 1)) {
                $ids .= ",";
            }
        }
        $sql .= "having a.id in( " . $ids . ")";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTransplantDoctorTblSearch($pref_mst_id, $institution_mst_id, $organs = array(), $sei_kana, $mei_kana, $status_mst_id, $affiliation_mst_id, $offset = 0, $limit = 0)
    {
        $count = $this->db->count_all('internalOrgansMst');

        $select = "a.id,a.status,a.status_mst_id,a.sei,a.mei,a.sei_kana,a.mei_kana,a.mail,a.password,a.SISETU_CD,a.institution_name,a.institution_kubun,a.account_type_mst_id,a.pref_name,a.pref_mst_id,a.institution_mst_id,";
        $select2 = "accountTbl.status_mst_id,accountTbl.id,accountTbl.sei,accountTbl.mei,accountTbl.sei_kana,accountTbl.mei_kana,accountTbl.mail,accountTbl.password,accountTbl.account_type_mst_id,institutionMst.id as institution_mst_id,institutionMst.institution_name,institutionMst.SISETU_CD,institutionMst.institution_kubun,prefMst.id as pref_mst_id,prefMst.pref_name,statusMst.status,";

        for ($i = 1; $i <= $count; $i++) {
            $select .= "max(a.organ" . $i . ") as organ" . $i;
            $select2 .= "case when doctorRelationOrgansTbl.internal_organs_mst_id = " . $i . " then 1 else 0 end as organ" . $i;
            if ($i != $count) {
                $select .= ",";
                $select2 .= ",";
            }
        }
        $sql = "select ";
        $sql .= $select;
        $sql .= " from ( select " . $select2;
        $sql .= " from doctorTbl join accountTbl on doctorTbl.account_tbl_id = accountTbl.id join institutionMst on institutionMst.id = doctorTbl.institution_mst_id join prefMst on prefMst.id = institutionMst.pref_mst_id join doctorRelationOrgansTbl on doctorTbl.id = doctor_tbl_id join statusMst on statusMst.id = accountTbl.status_mst_id where accountTbl.delete_flg = '0')as a group by a.id ";
        $having = "";
        if ($pref_mst_id) {
            $having .= " having a.pref_mst_id =" . $pref_mst_id;
        }
        if ($sei_kana) {
            $having .= empty($having) ? " having a.sei_kana like '%" . $sei_kana . "%'" : " and  a.sei_kana like '%" . $sei_kana . "%'";
        }
        if ($mei_kana) {
            $having .= empty($having) ? " having  a.mei_kana like '%" . $mei_kana . "%'" : " and  a.mei_kana like '%" . $mei_kana . "%'";
        }
        if ($status_mst_id) {
            $having .= empty($having) ? " having  a.status_mst_id =" . $status_mst_id : " and  a.status_mst_id =" . $status_mst_id;
        }
        if ($institution_mst_id) {
            $having .= empty($having) ? " having a.institution_mst_id = " . $institution_mst_id : " and a.institution_mst_id = " . $institution_mst_id;
        }

        if ($organs) {
            $count = count($organs);
            $having .= empty($having) ? "having (" : " and (";
            foreach ($organs as $key => $val) {
                $having .= "organ" . $val . "=1";
                if ($key != ($count - 1)) {
                    $having .= " or ";
                }
            }
            $having .= ")";
        }
        $sql .= $having;
        if ($limit) {
            $sql .= " limit " . $offset . "," . $limit;
        }

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTransplantDoctorTblSearchCount($pref_mst_id, $institution_mst_id, $organs = array(), $sei_kana, $mei_kana, $status_mst_id, $affiliation_mst_id)
    {
        $count = $this->db->count_all('internalOrgansMst');

        $select = "a.id,a.status,a.status_mst_id,a.sei,a.mei,a.sei_kana,a.mei_kana,a.mail,a.password,a.SISETU_CD,a.institution_name,a.institution_kubun,a.account_type_mst_id,a.pref_name,a.pref_mst_id,a.institution_mst_id,";
        $select2 = "accountTbl.status_mst_id,accountTbl.id,accountTbl.sei,accountTbl.mei,accountTbl.sei_kana,accountTbl.mei_kana,accountTbl.mail,accountTbl.password,accountTbl.account_type_mst_id,institutionMst.id as institution_mst_id,institutionMst.institution_name,institutionMst.SISETU_CD,institutionMst.institution_kubun,prefMst.id as pref_mst_id,prefMst.pref_name,statusMst.status,";

        for ($i = 1; $i <= $count; $i++) {
            $select .= "max(a.organ" . $i . ") as organ" . $i;
            $select2 .= "case when doctorRelationOrgansTbl.internal_organs_mst_id = " . $i . " then 1 else 0 end as organ" . $i;
            if ($i != $count) {
                $select .= ",";
                $select2 .= ",";
            }
        }
        $sql = "select ";
        $sql .= $select;
        $sql .= " from ( select " . $select2;
        $sql .= " from doctorTbl join accountTbl on doctorTbl.account_tbl_id = accountTbl.id join institutionMst on institutionMst.id = doctorTbl.institution_mst_id join prefMst on prefMst.id = institutionMst.pref_mst_id join doctorRelationOrgansTbl on doctorTbl.id = doctor_tbl_id join statusMst on statusMst.id = accountTbl.status_mst_id where accountTbl.delete_flg = '0')as a group by a.id ";
        if ($pref_mst_id) {
            $sql .= " and a.pref_mst_id =" . $pref_mst_id;
        }
        if ($sei_kana) {
            $sql .= " and  a.sei_kana like '%" . $sei_kana . "%'";
        }
        if ($mei_kana) {
            $sql .= " and  a.mei_kana like '%" . $mei_kana . "%'";
        }
        if ($status_mst_id) {
            $sql .= " and  a.status_mst_id =" . $status_mst_id;
        }

        if ($institution_mst_id) {
            $sql .= " and a.institution_mst_id = " . $institution_mst_id;
        }
        if ($organs) {
            $count = count($organs);
            $sql .= " and (";
            foreach ($organs as $key => $val) {
                $sql .= "organ" . $val . "=1";
                if ($key != ($count - 1)) {
                    $sql .= " or ";
                }
            }
            $sql .= ")";
        }

        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    public function getDoctorByAccountId($account_tbl_id)
    {
        $this->db->join(ACC_TBL, "doctorTbl.account_tbl_id = accountTbl.id");
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->where('accountTbl.id', $account_tbl_id);
        return $this->db->get(DOCTOR_TBL)->row();
    }

    public function getDoctorByName($institutionId, $sei, $mei)
    {
        $this->db->join(INSTITUTION_MST, "institutionMst.id = doctorTbl.institution_mst_id");
        $this->db->join(ACC_TBL, "accountTbl.id = doctorTbl.account_tbl_id");
        empty($sei) || $this->db->where("accountTbl.sei", $sei);
        empty($mei) || $this->db->where("accountTbl.mei", $mei);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        empty($institutionId) || $this->db->where("institutionMst.id", $institutionId);
        return $this->db->get(DOCTOR_TBL)->row();
    }

    /**
     * Get can accept request doctor (ddds transplant user)
     *
     * @param string $prefId
     * @param string $organId
     * @param string $institutionId
     * @return array $doctors
     */
    public function getCanAcceptRequestDoctor($prefId, $organId, $institutionId)
    {
        $this->db->select("accountTbl.id,accountTbl.sei,accountTbl.mei");
        $this->db->join(ACC_TBL, "doctorTbl.account_tbl_id = accountTbl.id");
        $this->db->join(INSTITUTION_MST, "doctorTbl.institution_mst_id = institutionMst.id");
        $this->db->join(DOCTOR_ORGAN_TBL, "doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id");
        $this->db->join(ACC_WORK_TBL, "accountRelationWorkTbl.account_tbl_id = accountTbl.id");
        $this->db->where("doctorRelationOrgansTbl.internal_organs_mst_id", $organId);
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->where("doctorTbl.institution_mst_id", $institutionId);
        $this->db->where("institutionMst.pref_mst_id", $prefId);
        $this->db->where("work_mst_id", WORK_DDDS);
        return $this->db->get(DOCTOR_TBL)->result();
    }

    public function updateTransplantDoctorTblData($data, $accId)
    {
        $this->db->where('account_tbl_id', $accId);
        $this->db->update(DOCTOR_TBL, $data);
    }

    public function getForSendAlertEmail($institionCode, $organId)
    {
        $this->db->select("accountTbl.mail");
        $this->db->join(ACC_TBL, "accountTbl.id = doctorTbl.account_tbl_id");
        $this->db->join(INSTITUTION_MST, "institutionMst.id = doctorTbl.institution_mst_id");
        $this->db->join(DOCTOR_ORGAN_TBL, "doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id");
        $this->db->join(ACC_WORK_TBL, "accountRelationWorkTbl.account_tbl_id = accountTbl.id");
        $this->db->where("SISETU_CD", $institionCode);
        $this->db->where("internal_organs_mst_id", $organId);
        $this->db->where('work_mst_id', WORK_FOLLOW_UP);
        $this->db->group_by("doctorTbl.id");
        return $this->db->get(DOCTOR_TBL)->result();
    }
}
