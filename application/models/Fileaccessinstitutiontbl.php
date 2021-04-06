<?php
class Fileaccessinstitutiontbl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertFileAccessTransplant($data)
    {

        $insert = array(
            "upfile_tbl_id" => $data["upfile_tbl_id"],
            "donor_institution_organs_tbl_id" => $data["donor_institution_organs_tbl_id"],
            "up_account_tbl_id" => $data["up_account_tbl_id"],
            "boxfile_id" => $data["boxfile_id"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        );
        $this->db->insert(FILE_ACCESS_INSTITUTION_TBL, $insert);
        return $this->db->insert_id();
    }

    public function deleteFileAccessTransplantByDId($d_id)
    {
        $this->db->select("fileAccessInstitutionTbl.id");
        $this->db->join(UPFILE_TBL, "fileAccessInstitutionTbl.upfile_tbl_id = upFileTbl.id");
        $this->db->where("upFileTbl.d_id", $d_id);
        $ids = array();
        foreach ($this->db->get(FILE_ACCESS_INSTITUTION_TBL)->result() as $file) {
            $ids[] = $file->id;
        }
        if ($ids) {
            $this->db->where_in("id", $ids);
            $this->db->delete(FILE_ACCESS_INSTITUTION_TBL);
        }
    }

    /**
     * Get requested (copied) file info
     *
     * @param string $upfileId
     * @param string $institutionId
     * @param string $organId
     * @return object $record
     * @return null if not found
     */
    public function getFileInfo($upfileId, $donorInstitutionOrganId)
    {
        $this->db->where(array(
            "upfile_tbl_id" => $upfileId,
            "donor_institution_organs_tbl_id" => $donorInstitutionOrganId,
        ));
        return $this->db->get(FILE_ACCESS_INSTITUTION_TBL)->row();
    }

    /**
     * Get requested (copied) files bu upfile id
     *
     * @param string $upfileId
     * @return object $record
     * @return null if not found
     */
    public function getFilesInfo($upfileId)
    {
        $this->db->where("upfile_tbl_id", $upfileId);
        return $this->db->get(FILE_ACCESS_INSTITUTION_TBL)->result();
    }

    /**
     * Delete file by it"s id
     *
     * @param string $id
     * @return void
     */
    public function deleteById($id)
    {
        $this->db->where("id", $id);
        $this->db->delete(FILE_ACCESS_INSTITUTION_TBL);
    }

}
