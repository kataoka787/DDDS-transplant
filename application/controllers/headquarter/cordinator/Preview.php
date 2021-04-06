<?php defined('BASEPATH') or exit('No direct script access allowed');

class Preview extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('data')) {
            redirect('cordinator/download');
        }
        $this->data = $this->session->userdata('data');
        $this->data['css'] = array(array('css' => 'print.css', 'media' => 'print'));
        $this->data['page_title'] = "";
        $this->load->library('box_api');
    }

    public function index()
    {
        $fileList = $this->Upfiletbl->getUpfileCoDownloadByDid($this->data['d_id'], $this->session->userdata("affiliation_mst_id"));
        $fileIdArr = array();
        if ($fileList) {
            foreach ($fileList as $file) {
                array_push($fileIdArr, $file->boxfile_id);
            }
        }
        count($fileIdArr) === 0 && redirect("cordinator/download");
        $this->data["fileIdArr"] = $fileIdArr;
        $this->data["accessToken"] = $this->box_api->loadToken();
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('cordinator/preview');
        $this->load->view('footer');
    }
}
