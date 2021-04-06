<?php defined('BASEPATH') or exit('No direct script access allowed');

class ManagementMenu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "page_title" => "メニュー",
            "works" => explode(",", $this->session->userdata("account")->work_id),
        );
    }

    public function index()
    {
        $this->load->vars($this->data);
        $this->load->view("header");
        $this->load->view("managementMenu");
        $this->load->view("footer");
    }
}
