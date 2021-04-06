<?php
class Filedownloadlogtbl extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUpfileCoReceiptByDid($d_id, $affiliation_mst_id)
    {
        if ($affiliation_mst_id == '1') {
            $user = "本部";
        } else {
            $user = "支部";
        }
        $this->db->select('upFileTbl.id,upFileTbl.file_name,upFileTbl.boxfile_id,d.sei,d.mei,d.download_datetime,upFileTbl.file_name_prefix,upFileTbl.created_at');
        $this->db->join("(SELECT fileCategoryMst.id,fileCategoryMst.category_name FROM (folderCategoryManagementTbl) JOIN folderMst ON folderCategoryManagementTbl.folder_mst_id = folderMst.id JOIN fileCategoryMst ON folderCategoryManagementTbl.file_category_mst_id = fileCategoryMst.id JOIN (select * from userMst where name like '%" . $user . "%') a ON folderCategoryManagementTbl.up_user_mst_id = a.id JOIN (select * from userMst where name like '%現地%') b ON folderCategoryManagementTbl.down_user_mst_id = b.id GROUP BY fileCategoryMst.id,fileCategoryMst.category_name) c", "upFileTbl.file_category_mst_id = c.id");
        $this->db->join("(SELECT accountTbl.sei, accountTbl.mei, fileDownLoadLogTbl.download_datetime, upFileTbl.file_name, upFileTbl.id, upFileTbl.created_at FROM (fileDownLoadLogTbl) JOIN accountTbl ON fileDownLoadLogTbl.account_tbl_id = accountTbl.id JOIN upFileTbl ON upfile_tbl_id = upFileTbl.id where upFileTbl.d_id =  '" . $d_id . "') d", "upFileTbl.id = d.id", "left");
        $this->db->where("upFileTbl.d_id", $d_id);
        $this->db->order_by('upFileTbl.created_at desc');
        $query = $this->db->get('upFileTbl');

        $result = $query->result();

        $file = array();
        foreach ($result as $key => $val) {
            $file[$val->id]['file_name'] = explode(".", $val->file_name)[0];
            $file[$val->id]['file_name_prefix'] = $val->file_name_prefix;
            $file[$val->id]['ext'] = explode(".", $val->file_name)[1];
            $file[$val->id]['user'][$key]['name'] = $val->sei . " " . $val->mei;
            $file[$val->id]['user'][$key]['download_datetime'] = $val->download_datetime;
            $file[$val->id]['updated_at'] = $val->created_at;
        }
        return $file;
    }

    public function insertFiledownloadLog($data)
    {
        $insert = array(
            "account_tbl_id" => $data['account_tbl_id'],
            "upfile_tbl_id" => $data['upfile_tbl_id'],
            "download_datetime" => date('Y-m-d H:i:s'),
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        );
        $this->db->insert(FILE_DOWNLOAD_LOG_TBL, $insert);
        return $this->db->insert_id();
    }

    public function getFiledownloadLogByUpfileTblIdAccountTblId($accId, $upFileId)
    {
        $this->db->where('account_tbl_id', $accId);
        $this->db->where('upfile_tbl_id', $upFileId);
        return $this->db->get(FILE_DOWNLOAD_LOG_TBL)->row();
    }

    public function deleteFiledownloadLogByDId($dId)
    {
        $this->db->select("fileDownLoadLogTbl.id");
        $this->db->join(UPFILE_TBL, 'fileDownLoadLogTbl.upfile_tbl_id = upFileTbl.id');
        $this->db->where('upFileTbl.d_id', $dId);

        $ids = array();
        foreach ($this->db->get(FILE_DOWNLOAD_LOG_TBL)->result() as $downloadLog) {
            array_push($ids, $downloadLog->id);
        }
        if ($ids) {
            $this->db->where_in('id', $ids);
            $this->db->delete(FILE_DOWNLOAD_LOG_TBL);
        }
    }

    public function deleteByUpfileId($upfileId)
    {
        $this->db->where("upfile_tbl_id", $upfileId);
        $this->db->delete(FILE_DOWNLOAD_LOG_TBL);
    }
}
