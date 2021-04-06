<?php defined('BASEPATH') or exit('No direct script access allowed');

class Receipt extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();       
        if (!$this->session->userdata('data')) {
            redirect('donor/searchlist');
        }       
        $this->data = $this->session->userdata('data');
        $this->data['page_title'] = config_item('page_co_donor_data');
    }

    public function index()
    {        
        $this->data['fileList'] = $this->Filedownloadlogtbl->getUpfileCoReceiptByDid($this->data['d_id'], $this->session->userdata("affiliation_mst_id"));
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('cordinator/receipt');
        $this->load->view('footer');
    }
}
