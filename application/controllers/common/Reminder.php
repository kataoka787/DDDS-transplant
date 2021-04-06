<?php defined('BASEPATH') or exit('No direct script access allowed');

class Reminder extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "branch" => config_item('branch'),
            "page_title" => config_item('page_title'),
            "error" => false,
        );
        $this->load->library('encryption');
    }

    public function index()
    {
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('reminder/index');
        $this->load->view('footer');
    }

    public function send()
    {
        $mail = $this->input->post('id');
        if ($this->form_validation->run('reminder')) {
            $mail = $this->input->post("id");
            /* Send mail */
            $url = base_url() . "password/reset?code=";
            $code = array(
                "mail" => $mail,
                "date" => date("Y-m-d H:i:s"),
            );
            $mailConfig = config_item("mail");
            $remiderMailConfig = $mailConfig["reminder"];
            $this->Mailsend->setFromName($mailConfig["from_name"]);
            $this->Mailsend->setFrom($mailConfig["from_address"]);
            $this->Mailsend->setBody($remiderMailConfig["template"]);
            $this->Mailsend->setTo($mail);
            $this->Mailsend->setSubject($remiderMailConfig["subject"]);
            $this->Mailsend->str_replace("URL", $url . urlencode($this->encryption->encrypt(json_encode($code))));
            $this->Mailsend->send();
            redirect('reminder/end');
        }

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('reminder/index');
        $this->load->view('footer');
    }

    public function end()
    {
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('reminder/end');
        $this->load->view('footer');
    }
}
