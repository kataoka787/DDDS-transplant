<?php defined('BASEPATH') or exit('No direct script access allowed');

class Preview extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('data')) {
            redirect('download');
        }
        $this->data = $this->session->userdata('data');       
        $this->data['css'] = array(array('css' => 'print.css', 'media' => 'print'));
        $this->data['page_title'] = "";
        $this->load->library('box_api');
    }

    public function index()
    {
        $boxFileIdArr = array();
        $fileIdArr = array();
        foreach ($this->Upfiletbl->getUpFileDownloadByDid($this->data['d_id']) as $file) {
            array_push($boxFileIdArr, $file->boxfile_id);
            $fileIdArr[$file->boxfile_id] = $file->id;
        }
        count($fileIdArr) === 0 && redirect("download");
        $this->data['boxFileIdArr'] = $boxFileIdArr;
        $this->data['fileIdArr'] = $fileIdArr;
        $this->data["accessToken"] = $this->box_api->loadToken();

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('preview');
        $this->load->view('footer');
    }

    /**
     * Insert file access log
     *
     * @param string $fileId
     * @return void
     */
    public function file_access_log($fileId = null)
    {
        if ($fileId !== null) {
            $accId = $this->session->userdata("account")->accountId;
            if (!$this->Filedownloadlogtbl->getFiledownloadLogByUpfileTblIdAccountTblId($accId, $fileId)) {
                $insert = array(
                    'account_tbl_id' => $accId,
                    'upfile_tbl_id' => $fileId,
                );
                $this->Filedownloadlogtbl->insertFiledownloadLog($insert);
            }
        }
    }
}
