<?php
class Upfiletbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFileNamePrefixByDidFileName($d_id, $file_name)
    {
        $this->db->select_max('file_name_prefix');
        $this->db->where('d_id', $d_id);
        $this->db->where('file_name', $file_name);
        return $this->db->get(UPFILE_TBL)->row();
    }

    public function insertUpfileData($data)
    {
        $insert = array(
            "d_id" => $data['d_id'],
            "file_category_mst_id" => $data['file_category_mst_id'],
            "file_name" => $data['file_name'],
            "file_name_prefix" => $data['file_name_prefix'],
            "boxfile_id" => $data['boxfile_id'],
            "account_tbl_id" => $data['account_tbl_id'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(UPFILE_TBL, $insert);
    }

    public function getUpFileUploadByDid($d_id)
    {
        $this->db->select('upFileTbl.id, upFileTbl.file_name_prefix, upFileTbl.file_name, upFileTbl.file_category_mst_id, upFileTbl.created_at');
        $this->db->join("(SELECT `fileCategoryMst`.`id`,fileCategoryMst.category_name FROM (`folderCategoryManagementTbl`) JOIN `folderMst` ON `folderCategoryManagementTbl`.`folder_mst_id` = `folderMst`.`id` JOIN `fileCategoryMst` ON `folderCategoryManagementTbl`.`file_category_mst_id` = `fileCategoryMst`.`id` JOIN (select * from userMst where name like '%現地%') a ON `folderCategoryManagementTbl`.`up_user_mst_id` = `a`.`id` GROUP BY `fileCategoryMst`.`id`,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        $this->db->order_by('upFileTbl.created_at desc');
        return $this->db->get(UPFILE_TBL)->result();
    }

    public function getUpFileDownloadByDid($dId, $offset = 0, $limit = 0)
    {
        $this->db->select('upFileTbl.id, upFileTbl.boxfile_id, upFileTbl.file_name, upFileTbl.file_name_prefix, upFileTbl.file_category_mst_id, upFileTbl.created_at');
        $this->db->join("(SELECT `fileCategoryMst`.`id`,fileCategoryMst.category_name FROM (`folderCategoryManagementTbl`) JOIN `folderMst` ON `folderCategoryManagementTbl`.`folder_mst_id` = `folderMst`.`id` JOIN `fileCategoryMst` ON `folderCategoryManagementTbl`.`file_category_mst_id` = `fileCategoryMst`.`id` JOIN (select * from userMst where name like '%現地%') a ON `folderCategoryManagementTbl`.`down_user_mst_id` = `a`.`id` GROUP BY `fileCategoryMst`.`id`,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $dId);
        $this->db->order_by('upFileTbl.created_at desc');
        $limit && $this->db->limit($limit);
        $offset && $this->db->offset($offset);
        return $this->db->get(UPFILE_TBL)->result();
    }

    public function getUpFileDownloadByDidCount($d_id)
    {
        $this->db->select('upFileTbl.id');
        $this->db->join("(SELECT `fileCategoryMst`.`id`,fileCategoryMst.category_name FROM (`folderCategoryManagementTbl`) JOIN `folderMst` ON `folderCategoryManagementTbl`.`folder_mst_id` = `folderMst`.`id` JOIN `fileCategoryMst` ON `folderCategoryManagementTbl`.`file_category_mst_id` = `fileCategoryMst`.`id` JOIN (select * from userMst where name like '%現地%') a ON `folderCategoryManagementTbl`.`down_user_mst_id` = `a`.`id` GROUP BY `fileCategoryMst`.`id`,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        return $this->db->count_all_results(UPFILE_TBL);
    }

    public function getUpFileByUpFileTblIdCordinatorId($id, $type = 1)
    {
        if ($type == '1') {
            $column = "up_user_mst_id";
        } else {
            $column = "down_user_mst_id";
        }
        $this->db->select('upFileTbl.id, upFileTbl.file_name, upFileTbl.file_name_prefix, upFileTbl.boxfile_id');
        $this->db->join("(SELECT fileCategoryMst.id, fileCategoryMst.category_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (SELECT * FROM userMst WHERE name like '%現地%' ) a ON folderCategoryManagementTbl." . $column . " = a.id GROUP BY fileCategoryMst.id, fileCategoryMst.category_name ) c", "c.id = upFileTbl.file_category_mst_id");
        $this->db->where('upFileTbl.id', $id);
        return $this->db->get(UPFILE_TBL)->row();
    }

    public function getUpfileCoUploadByDid($d_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id,upFileTbl.file_name_prefix,upFileTbl.file_name,upFileTbl.boxfile_id,upFileTbl.file_category_mst_id,upFileTbl.created_at');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%" . $user . "%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%現地%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        $this->db->order_by('upFileTbl.created_at desc');
        $query = $this->db->get('upFileTbl');
        return $query->result();
    }

    public function getUpfileTransplantUploadByDid($d_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id, upFileTbl.boxfile_id, upFileTbl.file_name, upFileTbl.file_name_prefix, upFileTbl.file_category_mst_id, upFileTbl.created_at, c.folderId, c.folder_name');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name,folderMst.id as folderId,folderMst.folder_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%" . $user . "%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%移植施設%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name,folderId,folderMst.folder_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        $this->db->order_by('upFileTbl.created_at desc');
        return $this->db->get(UPFILE_TBL)->result();
    }

    public function getUpfileInspectionUploadByDidCheck($d_id, $affiliation_mst_id, $id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name,folderMst.id as folderId,folderMst.folder_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%" . $user . "%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%検査センター%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name,folderId,folderMst.folder_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        $this->db->where('upFileTbl.id', $id);
        return $this->db->count_all_results(UPFILE_TBL);
    }

    public function getUpfileTransplantUploadByDidCheck($d_id, $affiliation_mst_id, $id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name,folderMst.id as folderId,folderMst.folder_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%" . $user . "%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%移植施設%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name,folderId,folderMst.folder_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        $this->db->where('upFileTbl.id', $id);
        return $this->db->count_all_results(UPFILE_TBL);
    }

    public function getUpfileCoDownloadByDid($d_id, $affiliation_mst_id, $offset = 0, $limit = 0)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id,upFileTbl.boxfile_id,upFileTbl.file_name_prefix,upFileTbl.file_name,upFileTbl.file_category_mst_id,upFileTbl.created_at,accountTbl.sei,accountTbl.mei');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%現地%' or name like '%本部%' or name like '%支部%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%" . $user . "%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->join("accountTbl", "upFileTbl.account_tbl_id = accountTbl.id");
        $this->db->order_by('upFileTbl.created_at desc');
        $this->db->where('upFileTbl.d_id', $d_id);
        if (!$limit) {
            $query = $this->db->get('upFileTbl');
        } else {
            $query = $this->db->get('upFileTbl', $limit, $offset);
        }
        return $query->result();
    }

    public function getUpfileTransplantDownloadByDid($dId, $affiliation_mst_id, $offset = 0, $limit = 0)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id,upFileTbl.file_name_prefix,upFileTbl.file_name,upFileTbl.file_category_mst_id ,upFileTbl.created_at,accountTbl.sei,accountTbl.mei');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%移植施設%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%" . $user . "%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->join("accountTbl", "upFileTbl.account_tbl_id = accountTbl.id");
        $this->db->where('upFileTbl.d_id', $dId);
        $this->db->order_by('upFileTbl.created_at desc');
        $limit && $this->db->limit($limit);
        $offset && $this->db->offset($offset);
        return $this->db->get(UPFILE_TBL)->result();
    }

    public function getUpfileTransplantDownloadByDidCount($d_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%移植施設%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%" . $user . "%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->join(ACC_TBL, "upFileTbl.account_tbl_id = accountTbl.id");
        $this->db->where('upFileTbl.d_id', $d_id);
        return $this->db->count_all_results(UPFILE_TBL);
    }

    public function getUpFileDataByDidUpload($d_id, $upload_id)
    {
        $this->db->select('upFileTbl.id,upFileTbl.file_name,upFileTbl.photo_datetime,upFileTbl.created_at');
        $this->db->join('fileCategoryMst', 'upFileTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->where('fileCategoryMst.upload_id', $upload_id);
        $this->db->where('upFileTbl.d_id', $d_id);
        return $this->db->get(UPFILE_TBL)->result();
    }

    public function getUpFileListByUpFileTblIdAccountId($id, $account_tbl_id)
    {
        $this->db->select('upFileTbl.*');
        $this->db->join('fileCategoryMst', 'upFileTbl.file_category_mst_id = fileCategoryMst.id');
        $this->db->where('upFileTbl.id', $id);
        $this->db->where('cordinatorTbl.account_tbl_id', $account_tbl_id);
        return $this->db->get(UPFILE_TBL)->row();
    }

    public function deleteUpFileById($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(UPFILE_TBL);
    }

    public function deleteUpFileByDId($d_id)
    {
        $this->db->where('d_id', $d_id);
        $this->db->delete(UPFILE_TBL);
    }

    public function getUpFileByUpFileTblIdAccountId($id, $type = 1)
    {
        if ($type == '1') {
            $column = "up_user_mst_id";
        } else {
            $column = "down_user_mst_id";
        }
        $this->db->select('upFileTbl.id, upFileTbl.boxfile_id, upFileTbl.file_name, upFileTbl.file_name_prefix');
        $this->db->join("(SELECT fileCategoryMst.id, fileCategoryMst.category_name FROM folderCategoryManagementTbl JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (SELECT * FROM userMst WHERE name like '%本部%' ) a ON folderCategoryManagementTbl." . $column . " = a.id GROUP BY fileCategoryMst.id, fileCategoryMst.category_name ) c", "c.id = upFileTbl.file_category_mst_id");
        $this->db->where('upFileTbl.id', $id);
        $this->db->group_by("upFileTbl.id, upFileTbl.file_name");
        return $this->db->get(UPFILE_TBL)->row();
    }

    public function getUpFileTblById($id)
    {
        $this->db->where('upFileTbl.id', $id);
        return $this->db->get(UPFILE_TBL)->row();
    }

}
