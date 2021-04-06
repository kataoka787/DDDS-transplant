<?php defined('BASEPATH') or exit('No direct script access allowed');

class Download extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();    
        if (!$this->session->userdata('data')) {
            redirect('donor/searchlist');
        }
        $this->data = $this->session->userdata('data');        
        $this->data['page_title'] = config_item('page_transplant_donor_data');
        $this->data['dir'] = "transplant";        
        $this->load->library("box_api");
    }

    public function index()
    {
        $requestFolders = $this->Donorinstitutionorganstbl->getRequestFolders($this->data["d_id"], null, null);
        $fileList = array();
        if ($requestFolders) {
            foreach ($requestFolders as $folder) {
                $folderItems = $this->box_api->getFolderItems($folder->jot_offer_boxfolder_id, "id,extension,created_at,name,uploader_display_name");
                if ($folderItems["success"]) {
                    foreach ($folderItems["data"]->entries as $item) {
                        array_push($fileList, $item);
                    }
                }
            }
        }
        $this->data['fileList'] = $fileList;

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('transplant/download');
        $this->load->view('footer');
    }

    public function package()
    {
        $ids = $this->input->post('id');
        $zipFile = $this->box_api->downloadZip($ids);
        if ($zipFile["success"]) {
            /* Do download */            
            force_download(config_item('download_cordinator_prefix') . date('YmdHis') . '.zip', $zipFile["data"]["content"]);
        }
        redirect("errors/file_can_not_download");
    }
}
