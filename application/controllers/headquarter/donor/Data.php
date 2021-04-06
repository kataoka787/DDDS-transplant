<?php defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array();
    }

    public function index()
    {
        $donor_base_data =  $this->Donorbasetbl->getDonorBaseTblByDid($this->input->post('d_id'));
        if (!$donor_base_data) {
            redirect('donor/searchlist');
        }
        $this->data['page_title'] = config_item('page_donor_data_menu');
        $this->data['d_id'] = $donor_base_data->d_id;
        $this->data['offerInstitution'] = $donor_base_data->offer_institution_name;
        $this->data['offerInstitutionPref'] = $this->Prefmst->getPrefNameById($donor_base_data->pref_mst_id);
        $this->data['donorNeme'] = $donor_base_data->sei . " " . $donor_base_data->mei;
        $this->data['age'] = $donor_base_data->age;
        $this->data['sex'] = $donor_base_data->sex;
        $this->data['deathReason'] = $this->Causedeathmst->getCauseDeathNameById($donor_base_data->cause_death_mst_id);
        $this->data['message'] = $donor_base_data->comment;
        $this->data['affiliation'] = $donor_base_data->affiliation_mst_id;

        $this->session->set_userdata('d_id', $this->input->post('d_id'));
        $this->session->set_userdata('data', $this->data);

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('donor/data');
        $this->load->view('footer');
    }
}
