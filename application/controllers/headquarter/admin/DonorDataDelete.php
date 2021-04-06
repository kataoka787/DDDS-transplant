<?php defined('BASEPATH') or exit('No direct script access allowed');

class DonorDataDelete extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('box_api');
    }

    public function index()
    {
        $dId = $this->input->post('d_id');
        if ($this->session->userdata('admin_flg') && $dId) {
            /* Donor info */
            $donor = $this->Donorbasetbl->getDonorBaseTblByDid($dId);
            /* Delete donor box folder */
            $deleteFolder = $this->box_api->deleteFolder($donor->donor_boxfolder_id);
            /* Update database */
            if ($deleteFolder["success"]) {
                $this->Fileaccessinstitutiontbl->deleteFileAccessTransplantByDId($dId);
                $donorInstitutionOrgans = $this->Donorinstitutionorganstbl->getByDid($dId);
                if (!empty($donorInstitutionOrgans)) {
                    foreach ($donorInstitutionOrgans as $donorInstitutionOrgan) {
                        $this->Doctoraccountcollaborationtbl->deleteByDonorInstitutionOrgansId($donorInstitutionOrgan->id);
                    }
                }
                $this->Donorinstitutionorganstbl->deleteByDid($dId);
                $this->Filedownloadlogtbl->deleteFiledownloadLogByDId($dId);
                $this->Tmpupfiletbl->deleteTmpUpFileByDid($dId);
                $this->Upfiletbl->deleteUpFileByDId($dId);
                $this->Donorbasetbl->deleteDonorData($dId);
                redirect("donor/searchlist");
            }
            redirect("errors/folder_can_not_delete");
        }
        redirect("donor/searchlist");
    }
}
