<?php
class Institutionmst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getInstitutionMst()
    {
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getTransplantInstitutionMstById($id)
    {
        $this->db->where("id", $id);
        return $this->db->get(INSTITUTION_MST)->row();
    }

    public function getTransplantInstitutionMstByBlockId($block_mst_id)
    {
        $this->db->where("block_mst_id", $block_mst_id);
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getTransplantInstitutionMstByPrefId($prefId)
    {
        $this->db->where("pref_mst_id", $prefId);
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getInstitutionByCode($institutionCode)
    {
        $this->db->where("SISETU_CD", $institutionCode);
        return $this->db->get(INSTITUTION_MST)->row();
    }

    public function getAllInstitutionByCodes($institutionCodes)
    {
        $this->db->where_in("SISETU_CD", $institutionCodes);
        return $this->db->get(INSTITUTION_MST)->result()();
    }

    public function isInstitutionNameUniqe($prefId, $institutionName, $institutionId)
    {
        $this->db->join(PREF_MST, "prefMst.id = institutionMst.pref_mst_id");
        $this->db->where('prefMst.id', $prefId);
        $this->db->where('institutionMst.institution_name', $institutionName);
        $institutionId && $this->db->where('institutionMst.id !=', $institutionId);
        return empty($this->db->count_all_results(INSTITUTION_MST));
    }

    public function isInstitutionCodeUniqe($institutionCode, $id)
    {
        $this->db->where("SISETU_CD", $institutionCode);
        $institutionCode && $this->db->where("id !=", $id);
        return empty($this->db->count_all_results(INSTITUTION_MST));
    }

    public function getTransplantInstitutionDataById($id)
    {
        $this->db->select('institutionMst.*, institutionRelationOrgansTbl.internal_organs_mst_id as organId');
        $this->db->join(INSTITUTION_ORGAN_TBL, 'institutionMst.id = institutionRelationOrgansTbl.institution_mst_id');
        $this->db->where('institutionMst.id', $id);

        $data = array();
        foreach ($this->db->get(INSTITUTION_MST)->result() as $val) {
            $data['id']                = $val->id;
            $data['institution_kubun'] = $val->institution_kubun;
            $data['institution_name']  = $val->institution_name;
            $data['institution_code']  = $val->SISETU_CD;
            $data['pref_mst_id']       = $val->pref_mst_id;
            $data['organ_id'][]        = $val->organId;
        }
        return $data;
    }

    /* TODO QA */
    public function getTransplantInstitutionSearchList($organs = array(), $pref_id = null, $institution_name = "", $affiliation_mst_id, $offset = 0, $limit = 0)
    {
        $count = $this->db->count_all('internalOrgansMst');

        $select  = "a.id,a.institution_name,a.SISETU_CD,a.affiliationMstId,a.prefId,a.pref_name,a.institution_kubun,";
        $select2 = "institutionMst.id,institutionMst.institution_name,institutionMst.SISETU_CD,institutionMst.institution_kubun,prefMst.id as prefId, prefMst.pref_name, affiliationMst.id as affiliationMstId,";

        for ($i = 1; $i <= $count; $i++) {
            $select .= "max(a.organ" . $i . ") as organ" . $i;
            $select2 .= "case when internalOrgansMst.id = " . $i . " then 1 else 0 end as organ" . $i;
            if ($i != $count) {
                $select .= ",";
                $select2 .= ",";
            }
        }

        $sql = "select ";
        $sql .= $select;
        $sql .= " from (SELECT " . $select2;
        $sql .= " FROM institutionMst JOIN prefMst ON institutionMst.pref_mst_id = prefMst.id JOIN blockMst ON prefMst.block_mst_id = blockMst.id JOIN affiliationBlockTbl ON blockMst.id = affiliationBlockTbl.block_mst_id JOIN affiliationMst ON affiliationMst.id = affiliationBlockTbl.affiliation_mst_id JOIN institutionRelationOrgansTbl ON institutionMst.id = institutionRelationOrgansTbl.institution_mst_id JOIN internalOrgansMst ON internalOrgansMst.id = institutionRelationOrgansTbl.internal_organs_mst_id ORDER BY prefMst.id, institutionMst.id, internalOrgansMst.id ) as a group by a.prefId ,a.id having a.affiliationMstId in";

        if ($affiliation_mst_id == '1') {
            $sql .= "(2,3,4)";
        } else {
            $sql .= "(" . $affiliation_mst_id . ")";
        }

        if ($pref_id) {
            $sql .= " and a.prefId = " . $pref_id;
        }

        if ($institution_name) {
            $sql .= " and a.institution_name like '%" . $institution_name . "%'";
        }

        if ($organs) {
            $organs_query = " and (";
            $i            = 1;
            foreach ($organs as $key => $val) {
                if ($i != 1) {
                    $organs_query .= " or ";
                }
                $organs_query .= "organ" . $val . "=1";
                $i++;
            }
            $organs_query .= ")";
            $sql .= $organs_query;
        }
        if ($limit) {
            if ($offset) {
                $sql .= " limit " . $offset . "," . $limit;
            } else {
                $sql .= " limit " . $limit;
            }
        }
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTransplantInstitutionSearchListCount($organs = array(), $pref_id, $institution_name, $affiliation_mst_id)
    {
        $this->db->select('institutionMst.id');
        $this->db->join('prefMst', 'institutionMst.pref_mst_id = prefMst.id');
        $this->db->join("blockMst", "prefMst.block_mst_id = blockMst.id");
        $this->db->join('affiliationBlockTbl', 'blockMst.id = affiliationBlockTbl.block_mst_id');
        $this->db->join('affiliationMst', 'affiliationMst.id = affiliationBlockTbl.affiliation_mst_id');
        $this->db->join('institutionRelationOrgansTbl', 'institutionMst.id = institutionRelationOrgansTbl.institution_mst_id');
        $this->db->join('internalOrgansMst', 'internalOrgansMst.id = institutionRelationOrgansTbl.internal_organs_mst_id');

        if ($pref_id) {
            $this->db->where('prefMst.id', $pref_id);
        }

        if ($pref_id) {
            $this->db->where('prefMst.id', $pref_id);
        }

        if ($institution_name) {
            $this->db->like('institutionMst.institution_name', $institution_name);
        }

        if ($organs) {
            $this->db->where_in('internalOrgansMst.id', $organs);
        }

        if ($affiliation_mst_id == '2' || $affiliation_mst_id == '3' || $affiliation_mst_id == '4') {
            $this->db->where('affiliationMst.id', $affiliation_mst_id);
        }
        $this->db->group_by('institutionMst.id');
        $query = $this->db->get('institutionMst');
        return $query->num_rows();
    }

    public function getTransplantByIdOrgansId($id, $organs_id)
    {
        $this->db->join(INSTITUTION_ORGAN_TBL, "institutionMst.id = institution_mst_id");
        $this->db->where("institutionMst.id", $id);
        $this->db->where("institutionRelationOrgansTbl.internal_organs_mst_id", $organs_id);
        return $this->db->get(INSTITUTION_MST)->row();
    }

    public function insertTransplantInstitutionMstData($data)
    {
        $insert = array(
            "SISETU_CD"         => $data["institution_code"],
            "pref_mst_id"       => $data['pref_mst_id'],
            "institution_kubun" => $data["institution_kubun"],
            "institution_name"  => $data['institution_name'],
            "created_at"        => date('Y-m-d H:i:s'),
            "updated_at"        => date('Y-m-d H:i:s'),
        );
        $this->db->insert(INSTITUTION_MST, $insert);
        return $this->db->insert_id();
    }

    public function updateTransplantInstitutionMstData($data, $id)
    {
        $insert = array(
            "SISETU_CD"         => $data["institution_code"],
            "pref_mst_id"       => $data['pref_mst_id'],
            "institution_kubun" => $data["institution_kubun"],
            "institution_name"  => $data['institution_name'],
            "updated_at"        => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $id);
        $this->db->update(INSTITUTION_MST, $insert);
    }

    public function deleteTransplantInstitutionMstById($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(INSTITUTION_MST);
    }

    public function getInstitutionAvailableOrgan()
    {
        $this->db->select('internalOrgansMst.id,internalOrgansMst.organ_name');
        $this->db->join(DOCTOR_TBL, 'institutionMst.id = doctorTbl.institution_mst_id');
        $this->db->join(ACC_TBL, 'doctorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(DOCTOR_ORGAN_TBL, 'doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id');
        $this->db->join(INTERNAL_ORGAN_MST, 'doctorRelationOrgansTbl.internal_organs_mst_id = internalOrgansMst.id');
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->group_by('internalOrgansMst.id');
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getInstitutionOrgans($institutionId)
    {
        $this->db->select("internalOrgansMst.id");
        $this->db->select("internalOrgansMst.organ_name");
        $this->db->join(INSTITUTION_ORGAN_TBL, "institutionRelationOrgansTbl.institution_mst_id = institutionMst.id");
        $this->db->join(INTERNAL_ORGAN_MST, "internalOrgansMst.id = institutionRelationOrgansTbl.internal_organs_mst_id");
        $this->db->where("institutionMst.id", $institutionId);
        $organs = array();
        foreach ($this->db->get(INSTITUTION_MST)->result() as $row) {
            array_push($organs, (object) array("id" => $row->id, "organ_name" => $row->organ_name));
        }
        return $organs;
    }

    public function getPrefByOrgansIdRequest($organId)
    {
        $this->db->select('prefMst.id, prefMst.pref_name');
        $this->db->join(PREF_MST, 'prefMst.id = institutionMst.pref_mst_id');
        $this->db->join(DOCTOR_TBL, 'institutionMst.id = doctorTbl.institution_mst_id');
        $this->db->join(ACC_TBL, 'doctorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(DOCTOR_ORGAN_TBL, 'doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id');
        $this->db->join(INTERNAL_ORGAN_MST, 'doctorRelationOrgansTbl.internal_organs_mst_id = internalOrgansMst.id');
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->where('doctorRelationOrgansTbl.internal_organs_mst_id', $organId);
        $this->db->group_by('prefMst.id');
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getTransplantInstitutionByPrefIdOrgansIdRequest($prefId, $organId)
    {
        $this->db->select('institutionMst.id,institutionMst.institution_name');
        $this->db->join(PREF_MST, 'prefMst.id = institutionMst.pref_mst_id');
        $this->db->join(DOCTOR_TBL, 'institutionMst.id = doctorTbl.institution_mst_id');
        $this->db->join(ACC_TBL, 'doctorTbl.account_tbl_id = accountTbl.id');
        $this->db->join(DOCTOR_ORGAN_TBL, 'doctorRelationOrgansTbl.doctor_tbl_id = doctorTbl.id');
        $this->db->join(INTERNAL_ORGAN_MST, 'doctorRelationOrgansTbl.internal_organs_mst_id = internalOrgansMst.id');
        $this->db->where('accountTbl.status_mst_id', ACC_STT_CONF);
        $this->db->where('accountTbl.delete_flg', IN_USE_FLG);
        $this->db->where('prefMst.id', $prefId);
        $this->db->where('doctorRelationOrgansTbl.internal_organs_mst_id', $organId);
        $this->db->group_by('institutionMst.id');
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getInstitutionMstByKubun($kubun)
    {
        $this->db->where("institution_kubun", $kubun);
        return $this->db->get(INSTITUTION_MST)->result();
    }

    public function getInstitutionMstBySearchConditions($query)
    {
        $this->db->select("institutionMst.id, institutionMst.SISETU_CD, institutionMst.institution_name, institutionMst.institution_kubun");
        $this->db->select(
            "(CASE
                WHEN institution_kubun = 1 THEN '移植施設'
                WHEN institution_kubun = 2 THEN '移植後経過情報管理施設'
            END) AS institution_kubun_name"
        );
        $this->db->join(PREF_MST, "prefMst.id = institutionMst.pref_mst_id");
        $this->db->join(BLOCK_MST, "blockMst.id = prefMst.block_mst_id");
        empty($query['blockDialog']) || $this->db->where("block_mst_id", $query['blockDialog']);
        empty($query['prefDialog']) || $this->db->where("pref_mst_id", $query['prefDialog']);
        empty($query['transplantDialog']) || $this->db->like("institution_name", $query['transplantDialog']);
        if (isset($query['facilityClass'])) {
            $this->db->where_in("institution_kubun", $query['facilityClass']);
        } else {
            return array();
        }
        return $this->db->get(INSTITUTION_MST)->result();
    }
}
