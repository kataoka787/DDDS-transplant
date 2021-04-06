<?php defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "page_title" => config_item('page_data'),
        );
        $this->input->post('d_id') || redirect('donorlist');
        $this->session->set_userdata('d_id', $this->input->post('d_id'));
    }

    public function index()
    {
        $donor = $this->Donorbasetbl->getDonorBaseTblByDidCordinatorTblId($this->input->post('d_id'), $this->session->userdata('cordinatorId'), $this->session->userdata('affiliation_mst_id'));
        $donor || redirect('donorlist');
        
        $this->data['d_id'] = $donor->d_id;
        $this->data['offerInstitution'] = $donor->offer_institution_name;
        $this->data['offerInstitutionPref'] = $this->Prefmst->getPrefNameById($donor->pref_mst_id);
        $this->data['donorNeme'] = $donor->sei . " " . $donor->mei;
        $this->data['age'] = $donor->age;
        $this->data['sex'] = $donor->sex;
        $this->data['deathReason'] = $this->Causedeathmst->getCauseDeathNameById($donor->cause_death_mst_id);
        $this->data['message'] = $donor->comment;
        $this->data['affiliation'] = $donor->affiliation_mst_id;

        $this->session->set_userdata('data', $this->data);

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('data');
        $this->load->view('footer');
    }
}
