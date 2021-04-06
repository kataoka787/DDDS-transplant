<?php defined('BASEPATH') or exit('No direct script access allowed');

class Download extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('data')) {
            redirect('donor/searchlist');
        }
        $this->data = $this->session->userdata('data');        
        $this->data['page_title'] = config_item('page_co_donor_data');
        $this->load->library('box_api');
    }

    public function index()
    {
        $this->data['fileList'] = $this->Upfiletbl->getUpfileCoDownloadByDid($this->data['d_id'], $this->session->userdata("affiliation_mst_id"));
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('cordinator/download');
        $this->load->view('footer');
    }

    public function package()
    {
        $ids = $this->input->post('id');
        $files = array(
            "fileIds" => array(),
            "boxFileIds" => array(),
        );
        foreach ($ids as $id) {
            $file = $this->Upfiletbl->getUpFileByUpFileTblIdAccountId($id, 2);
            if ($file) {
                array_push($files["fileIds"], $file->id);
                array_push($files["boxFileIds"], $file->boxfile_id);
            }
        }
        $zipFile = $this->box_api->downloadZip($files["boxFileIds"]);
        if ($zipFile["success"]) {
            /* Update file access log */
            $accId = $this->session->userdata('account')->accountId;
            foreach ($files["fileIds"] as $fileId) {
                if (!$this->Filedownloadlogtbl->getFiledownloadLogByUpfileTblIdAccountTblId($accId, $fileId)) {
                    $insert = array(
                        "account_tbl_id" => $accId,
                        "upfile_tbl_id" => $fileId,
                    );
                    $this->Filedownloadlogtbl->insertFiledownloadLog($insert);
                }
            }
            /* Do download */
            force_download(config_item('download_cordinator_prefix') . date('YmdHis') . '.zip', $zipFile["data"]["content"]);
        }
        redirect("errors/file_can_not_download");
    }
}
