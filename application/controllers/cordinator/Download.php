<?php defined('BASEPATH') or exit('No direct script access allowed');

class Download extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session->userdata('data') || redirect("data");
        $this->data = $this->session->userdata('data');
    }

    public function index()
    {
        $this->data['fileList'] = $this->Upfiletbl->getUpFileDownloadByDid($this->data['d_id']);
        $this->data['page_title'] = config_item('page_download');
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('download');
        $this->load->view('footer');
    }
}
