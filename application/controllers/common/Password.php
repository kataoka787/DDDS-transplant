<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Password extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "branch" => config_item('branch'),
            "page_title" => "",
            "pw" => "",
            "repw" => "",
            "error" => false,
        );
        $this->load->library('encryption');
        $this->load->library("box_api");
    }

    public function index()
    {
        if ($this->session->flashdata('account')) {
            $this->data['account'] = $this->session->flashdata('account');
            $this->session->keep_flashdata('account');
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('password/password');
            $this->load->view('footer');
            return;
        }
        redirect(base_url());

    }

    /**
     * Reset password
     *
     * @return view reset password screen if reset code is valid
     * @return redirect if reset code is not valid
     */
    public function reset()
    {
        $code = $this->input->get("code");
        if ($code) {
            $decryptedCode = $this->encryption->decrypt($code);
            $jsonDecodedCode = $decryptedCode ? json_decode($decryptedCode) : false;
            $isCodeValid = $jsonDecodedCode && (strtotime("-1 day") < strtotime($jsonDecodedCode->date));
            if ($isCodeValid) {
                $account = $this->Accounttbl->getAccountDataByMail($jsonDecodedCode->mail);
                if ($account) {
                    $this->session->set_flashdata('account', $account);
                    $this->data["account"] = $account;
                    $this->load->vars($this->data);
                    $this->load->view('header');
                    $this->load->view('password/password');
                    $this->load->view('footer');
                    return;
                }
                redirect("errors/account_not_found");
            }
        }
        return redirect("errors/invalid_reset_code");
    }

    public function change()
    {
        if ($this->session->flashdata('account')) {
            $account = $this->session->flashdata('account');
            $this->session->keep_flashdata('account');

            $this->data['account'] = $account;
            $this->data["accId"] = $account->accountId;
            $pw = $this->input->post('pw');
            $repw = $this->input->post('repw');
            $_POST["account_id"] = $account->accountId;
            if ($this->form_validation->run('password') === true) {
                $update = array(
                    "updated_at" => date('Y-m-d H:i:s'),
                    "password_datetime" => date('Y-m-d'),
                    "status_mst_id" => ACC_STT_CONF,
                    "password" => $pw,
                );
                $this->Accounttbl->updateAccountTblData($update, $this->data["accId"]);
                redirect('password/end');
            }
            $this->data['pw'] = $pw;
            $this->data['repw'] = $repw;

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('password/password');
            $this->load->view('footer');
        } else {
            redirect(base_url());
        }
    }

    public function end()
    {
        $this->session->sess_destroy();
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('password/password_end');
        $this->load->view('footer');
    }
}
