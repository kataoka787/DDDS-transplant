<?php
class Foldercategorymanagementtbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFolderData()
    {
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join('folderMst', 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join('fileCategoryMst', 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%現地%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->group_by('folderMst.id,folderMst.folder_name');

        $query = $this->db->get('folderCategoryManagementTbl');
        return $query->result();
    }

    public function getFolderDataCheckById($id)
    {
        $this->db->join('folderMst', 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join('fileCategoryMst', 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%現地%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->where("folderMst.id", $id);
        $query = $this->db->get('folderCategoryManagementTbl');
        return $query->num_rows();
    }

    public function getCategoryData($folder_mst_id)
    {
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join('folderMst', 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join('fileCategoryMst', 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%現地%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->where('folderMst.id', $folder_mst_id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');

        $query = $this->db->get('folderCategoryManagementTbl');
        return $query->result();
    }

    public function getCategoryDataCheckById($id)
    {
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join('folderMst', 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join('fileCategoryMst', 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%現地%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->where('fileCategoryMst.id', $id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');

        $query = $this->db->get('folderCategoryManagementTbl');
        return $query->num_rows();
    }

    public function getFolderDataById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(FOLDER_MST)->row();
    }

    public function getFolderCoData($affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join('folderMst', 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join('fileCategoryMst', 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%現地%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->group_by('folderMst.id,folderMst.folder_name');

        $query = $this->db->get('folderCategoryManagementTbl');
        return $query->result();
    }

    public function getFolderInspectionData($affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%検査センター%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->group_by('folderMst.id,folderMst.folder_name');
        return $this->db->get(FOLDER_CATEGORY_MANAGEMENT_TBL)->result();
    }

    public function getFolderTransplantData($affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%移植施設%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->group_by('folderMst.id,folderMst.folder_name');
        return $this->db->get(FOLDER_CATEGORY_MANAGEMENT_TBL)->result();
    }

    public function getCategoryCoData($folder_mst_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%現地%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where('folderMst.id', $folder_mst_id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');
        return $this->db->get(FOLDER_CATEGORY_MANAGEMENT_TBL)->result();
    }

    public function getCategoryInspectionData($folder_mst_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%検査センター%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where('folderMst.id', $folder_mst_id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');
        return $this->db->get(FOLDER_CATEGORY_MANAGEMENT_TBL)->result();
    }

    public function getCategoryTransplantData($folder_mst_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%移植施設%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where('folderMst.id', $folder_mst_id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');
        return $this->db->get(FOLDER_CATEGORY_MANAGEMENT_TBL)->result();
    }

    public function getFolderCoDataCheckById($id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%現地%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where("folderMst.id", $id);
        $this->db->group_by('folderMst.id,folderMst.folder_name');
        return $this->db->count_all_results(FOLDER_CATEGORY_MANAGEMENT_TBL);
    }

    public function getFolderInspectionDataCheckById($id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%検査センター%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where("folderMst.id", $id);
        $this->db->group_by('folderMst.id,folderMst.folder_name');
        return $this->db->count_all_results(FOLDER_CATEGORY_MANAGEMENT_TBL);
    }

    public function getFolderTransplantDataCheckById($id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('folderMst.id,folderMst.folder_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%移植施設%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->group_by('folderMst.id,folderMst.folder_name');
        $this->db->where("folderMst.id", $id);
        return $this->db->count_all_results(FOLDER_CATEGORY_MANAGEMENT_TBL);
    }

    public function getCategoryCoDataCheckById($id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%現地%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where('fileCategoryMst.id', $id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');
        return $this->db->count_all_results(FOLDER_CATEGORY_MANAGEMENT_TBL);
    }

    public function getCategoryInspectionDataCheckById($id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%検査センター%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where('fileCategoryMst.id', $id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');
        return $this->db->count_all_results(FOLDER_CATEGORY_MANAGEMENT_TBL);
    }

    public function getCategoryTransplantDataCheckById($id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('fileCategoryMst.id,fileCategoryMst.category_name');
        $this->db->join(FOLDER_MST, 'folderCategoryManagementTbl.folder_mst_id = folderMst.id');
        $this->db->join(FILE_CATEGORY_MST, 'folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->join("(select * from userMst where name like '%" . $user . "%') a", 'folderCategoryManagementTbl.up_user_mst_id = a.id');
        $this->db->join("(select * from userMst where name like '%移植施設%') b", 'folderCategoryManagementTbl.down_user_mst_id = b.id');
        $this->db->where('fileCategoryMst.id', $id);
        $this->db->group_by('fileCategoryMst.id,fileCategoryMst.category_name');
        return $this->db->count_all_results(FOLDER_CATEGORY_MANAGEMENT_TBL);
    }
}
