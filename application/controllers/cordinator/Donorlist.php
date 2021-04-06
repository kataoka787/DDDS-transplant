<?php defined('BASEPATH') or exit('No direct script access allowed');

class Donorlist extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            'page_title' => config_item('page_list'),
        );
        $this->session->unset_userdata('d_id');
    }

    public function index()
    {
        $this->data['list'] = $this->Donorbasetbl->getSearchList();
        $this->session->unset_userdata('data');
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('donorlist');
        $this->load->view('footer');
    }
}
