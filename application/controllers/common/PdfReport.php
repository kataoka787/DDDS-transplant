<?php defined('BASEPATH') or exit('No direct script access allowed');

class PdfReport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function printList()
    {
        $data = $this->input->post("data");
        $data == null && redirect(base_url());
        $data = json_decode($data);
        try {
            $buf = $this->Printlist->createPdf($data);
            $fileName = '移植後経過情報一覧.pdf';
            $len = strlen($buf);
            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: inline; filename=$fileName");
            print $buf;
        } catch (PDFlibException $e) {
            redirect("errors/pdf_can_not_print");
            exit(1);
        } catch (Exception $e) {
            redirect("errors/pdf_can_not_print");
            exit(1);
        }
    }

    public function printEntry()
    {
        $data = $this->input->post("data");
        $data == null && redirect(base_url());
        $data = json_decode($data);
        try {
            $buf = $this->Printentry->createPdf($data);
            $fileName = '移植後経過情報（記入用紙）.pdf';
            $len = strlen($buf);
            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: inline; filename=$fileName");
            print $buf;
        } catch (PDFlibException $e) {
            redirect("errors/pdf_can_not_print");
            exit(1);
        } catch (Exception $e) {
            redirect("errors/pdf_can_not_print");
            exit(1);
        }
    }

    public function printDetail()
    {
        $data = $this->input->post("data");
        $data == null && redirect(base_url());
        $data = json_decode($data);
        $accType = $this->session->userdata("account")->account_type_mst_id;
        try {
            $buf = $this->Printdetail->createPdf($data, $accType == ACC_TYPE_CO);
            $fileName = '移植後経過情報（個人票）.pdf';
            $len = strlen($buf);
            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: inline; filename=$fileName");
            print $buf;
        } catch (PDFlibException $e) {
            redirect("errors/pdf_can_not_print");
            exit(1);
        } catch (Exception $e) {
            redirect("errors/pdf_can_not_print");
            exit(1);
        }
    }
}
