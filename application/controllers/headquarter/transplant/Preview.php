<?php defined('BASEPATH') or exit('No direct script access allowed');

class Preview extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('data')) {
            redirect('transplant/download');
        }
        $this->data = $this->session->userdata('data');
        $this->data['css'] = array(array('css' => 'print.css', 'media' => 'print'));
        $this->data['page_title'] = "";
        $this->load->library('box_api');
    }

    public function index()
    {
        $requestFolders = $this->Donorinstitutionorganstbl->getRequestFolders($this->data["d_id"], null, null);
        $fileIdArr = array();
        if ($requestFolders) {
            foreach ($requestFolders as $folder) {
                $folder = $this->box_api->getFolderItems($folder->jot_offer_boxfolder_id, "id,extension,created_at,name,uploader_display_name");
                if ($folder["success"]) {
                    $items = $folder["data"]->entries;
                    foreach ($items as $item) {
                        array_push($fileIdArr, $item->id);
                    }
                }
            }
        }
        count($fileIdArr) === 0 && redirect("transplant/download");
        $this->data["fileIdArr"] = $fileIdArr;
        $this->data["accessToken"] = $this->box_api->loadToken();
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('transplant/preview');
        $this->load->view('footer');
    }
}
