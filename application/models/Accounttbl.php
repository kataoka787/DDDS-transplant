<?php
class Accounttbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAccountById($id)
    {
        $this->db->where("id", $id);
        $this->db->where("accountTbl.status_mst_id", ACC_STT_CONF);
        $this->db->where("accountTbl.delete_flg", IN_USE_FLG);
        return $this->db->get(ACC_TBL)->row();
    }

    public function getAccountByIds($id)
    {
        $this->db->where_in("id", $id);
        $this->db->where("accountTbl.status_mst_id", ACC_STT_CONF);
        $this->db->where("accountTbl.delete_flg", IN_USE_FLG);
        return $this->db->get(ACC_TBL)->result();
    }

    public function getAccountByIdPw($id, $pw)
    {
        $this->db->select("accountTbl.id as accountId, accountTbl.password, accountTbl.account_type_mst_id, accountTbl.mail");
        $this->db->select("accountTbl.password_datetime, accountTbl.admin_flg");
        $this->db->select("GROUP_CONCAT(workMst.id) as work_id");
        if (config_item("branch") === APP_TRANSPLANT) {
            $this->db->select("doctorTbl.institution_mst_id");
            $this->db->select("institutionMst.pref_mst_id, institutionMst.institution_name, institutionMst.institution_kubun, institutionMst.SISETU_CD");
            $this->db->select("prefMst.pref_name");
            $this->db->select("GROUP_CONCAT(internalOrgansMst.id) as organ_id");
            $this->db->select("affiliation_mst_id");
            $this->db->join(DOCTOR_TBL, "account_tbl_id = accountTbl.id");
            $this->db->join(INSTITUTION_MST, "doctorTbl.institution_mst_id = institutionMst.id");
            $this->db->join(PREF_MST, "prefMst.id = institutionMst.pref_mst_id");
            $this->db->join(DOCTOR_ORGAN_TBL, "doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id");
            $this->db->join(INTERNAL_ORGAN_MST, "internalOrgansMst.id = doctorRelationOrgansTbl.internal_organs_mst_id");
            $this->db->join(AFFILIATION_BLOCK_TBL, "affiliationBlockTbl.block_mst_id = prefMst.block_mst_id");
            $this->db->where("accountTbl.account_type_mst_id", ACC_TYPE_TP);
        } else {
            $this->db->select("cordinatorTbl.id as cordinatorId, cordinatorTbl.cordinator_type_mst_id");
            $this->db->select("affiliationCordinatorTbl.affiliation_mst_id");
            $this->db->join(CO_TBL, "cordinatorTbl.account_tbl_id=accountTbl.id");
            $this->db->join(AFFILIATION_CORDINATOR_TBL, "cordinatorTbl.id = affiliationCordinatorTbl.cordinator_tbl_id", "left");
            $this->db->where("accountTbl.account_type_mst_id", ACC_TYPE_CO);
        }
        $this->db->join(ACC_WORK_TBL, "accountRelationWorkTbl.account_tbl_id = accountTbl.id");
        $this->db->join(WORK_MST, "workMst.id = accountRelationWorkTbl.work_mst_id");
        $this->db->where("accountTbl.mail", $id);
        $this->db->where("accountTbl.status_mst_id", ACC_STT_CONF);
        $this->db->where("accountTbl.delete_flg", IN_USE_FLG);
        if (config_item("branch") !== APP_TRANSPLANT) {
            $this->db->group_by("accountTbl.id");
        }
        $acc = $this->db->get(ACC_TBL)->row();
        if ($acc && password_verify($pw, $acc->password)) {
            $acc->password = null;
            return $acc;
        }
        return null;
    }

    public function getAccountDataByMail($mail)
    {
        $this->db->select("accountTbl.id as accountId, accountTbl.mail");
        if (config_item("branch") === APP_TRANSPLANT) {
            $this->db->join(DOCTOR_TBL, "doctorTbl.account_tbl_id = accountTbl.id");
            $this->db->join(DOCTOR_ORGAN_TBL, "doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id");
            $this->db->join(INTERNAL_ORGAN_MST, "internalOrgansMst.id = doctorRelationOrgansTbl.internal_organs_mst_id");
            $this->db->join(INSTITUTION_MST, "institutionMst.id = doctorTbl.institution_mst_id");
            $accType = ACC_TYPE_TP;
        } else {
            $this->db->join(CO_TBL, "cordinatorTbl.account_tbl_id = accountTbl.id");
            $accType = ACC_TYPE_CO;
        }
        $this->db->where("accountTbl.account_type_mst_id", $accType);
        $this->db->where("mail", $mail);
        $this->db->where("accountTbl.status_mst_id", ACC_STT_CONF);
        $this->db->where("accountTbl.delete_flg", IN_USE_FLG);
        return $this->db->get(ACC_TBL)->row();
    }

    /**
     * Insert account info to account tbl,
     * also update password history
     *
     * @param array $data
     * @return string $insertedId
     */
    public function insertAccountTblData($data)
    {
        $insert = array(
            "sei" => $data["sei"],
            "mei" => $data["mei"],
            "sei_kana" => $data["sei_kana"],
            "mei_kana" => $data["mei_kana"],
            "mail" => $data["mail"],
            "account_type_mst_id" => $data["account_type_mst_id"],
            "status_mst_id" => $data["status_mst_id"],
            "admin_flg" => $data["admin_flg"],
            "password_datetime" => $data["password_datetime"],
            "delete_flg" => $data["delete_flg"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        );
        /* Hash password */
        $insert["password"] = isset($data["password"]) ? password_hash($data["password"], PASSWORD_DEFAULT) : "";
        /* Insert to  accountTbl*/
        $this->db->insert(ACC_TBL, $insert);
        $insertedId = $this->db->insert_id();
        /* Insert to passwordHistoryTbl */
        if (isset($data["password"]) && $insertedId) {
            $this->Passwordhistorytbl->insert(array(
                "account_tbl_id" => $insertedId,
                "password" => $insert["password"],
            ));
        }
        return $insertedId;
    }

    /**
     * Update account info,
     * also update password history
     *
     * @param array $data
     * @param string $accountId
     * @return void
     */
    public function updateAccountTblData($data, $accountId, $isDeletePassword = false)
    {
        if ($isDeletePassword) {
            $data["password"] = "";
        } else {
            if (!empty($data["password"])) {
                $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
                /* Insert to passwordHistoryTbl */
                $this->Passwordhistorytbl->insert(array(
                    "account_tbl_id" => $accountId,
                    "password" => $data["password"],
                ));
            } else {
                unset($data["password"]);
            }
        }
        $this->db->where("id", $accountId);
        $this->db->update(ACC_TBL, $data);
    }
}
