<?php defined('BASEPATH') or exit('No direct script access allowed');

class Faq extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array();
        $this->data['js'] = array();
        $this->data['css'] = array();
        $this->data['page_title'] = config_item('page_faq');
    }

    public function index()
    {
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('faq');
        $this->load->view('footer');
    }
}
