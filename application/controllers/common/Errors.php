<?php defined('BASEPATH') or exit('No direct script access allowed');

class Errors extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($error = "")
    {
        $this->load->view("header");
        $this->load->view("error/index", array("error" => lang($error)));
        $this->load->view("footer");
    }

}
