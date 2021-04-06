<?php defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "page_title" => config_item('page_login'),
            "works" => explode(",", $this->session->userdata("account")->work_id),
        );
    }

    public function index()
    {
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('menu/index');
        $this->load->view('footer');
    }
}
