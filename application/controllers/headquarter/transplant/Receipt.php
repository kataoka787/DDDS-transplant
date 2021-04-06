<?php defined('BASEPATH') or exit('No direct script access allowed');

class Receipt extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = $this->session->userdata('data');
        $this->data["page_title"] = config_item('page_transplant_donor_data');
        if (!$this->session->userdata('data')) {
            redirect('donor/searchlist');
        }
        $this->load->library("box_api");
    }

    public function index()
    {
        /* Get organ id */
        $id = $this->input->get('id');
        if ($id === null) {
            $id = "1";
        }
        $this->data['id'] = $id;
        $dId = $this->data['d_id'];
        $fileList = array();
        /* Get all requested folder by organ id */
        $requestFolders = $this->Donorinstitutionorganstbl->getRequestFolders($dId, null, $id);
        if ($requestFolders) {
            foreach ($requestFolders as $requestFolder) {
                $headTpFolder = $this->box_api->getFolderItems($requestFolder->donorinfo_boxfolder_id, "id,extension,name,modified_at,shared_link");
                if ($headTpFolder["success"]) {
                    foreach ($headTpFolder["data"]->entries as $item) {
                        $item->institution = $this->Institutionmst->getTransplantInstitutionMstById($requestFolder->institution_mst_id)->institution_name;
                        $item->organ = $this->Internalorgansmst->getInternalOrgansMstById($requestFolder->internal_organs_mst_id)->organ_name;
                        array_push($fileList, $item);
                    }
                } else {
                    redirect("errors/folder_can_not_get_item");
                }
            }
        }
        $this->data['fileList'] = $fileList;
        $this->data['organs'] = $this->Internalorgansmst->getInternalOrgansMst();

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('transplant/receipt');
        $this->load->view('footer');
    }
}
