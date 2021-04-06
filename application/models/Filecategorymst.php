<?php
class Filecategorymst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCategoryData($folderId, $uploadId = 0, $downloadId = 0)
    {
        if ((!$uploadId && !$downloadId) || !$folderId) {
            return null;
        }
        $this->db->where('folder_mst_id', $folderId);
        $uploadId && $this->db->where('upload_id', $uploadId);
        $downloadId && $this->db->where('download_id', $downloadId);
        return $this->db->get(FILE_CATEGORY_MST)->result();
    }

    public function getCategoryDataById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(FILE_CATEGORY_MST)->row();
    }
}
