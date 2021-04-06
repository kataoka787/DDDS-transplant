<?php
class Foldermst extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFolderData($upload_ids = array(), $download_ids = array())
    {
        if (!$upload_ids && !$download_ids) {
            return null;
        }
        if ($upload_ids) {
            $this->db->where_in('upload_id', $upload_ids);
        }
        if ($download_ids) {
            $this->db->where_in('download_id', $download_ids);
        }
        return $this->db->get(FOLDER_MST)->result();
    }

    public function getFolderDataById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(FOLDER_MST)->row();
    }
}
