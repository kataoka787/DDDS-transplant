<?php defined('BASEPATH') or exit('No direct script access allowed');

class Search extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            'page_title' => '移植後経過情報管理システム',
            'js' => array('bootstrap.min.js', 'bootstrap.bundle.min.js'),
            'css' => array(
                array('css' => 'tp_style.css'),
                array('css' => 'bootstrap.min.css'),
                array('css' => 'font-awesome.min.css'),
            ),
            'userType' => $this->session->userdata('account')->account_type_mst_id,
            'adminFlg' => $this->session->userdata('admin_flg')
        );
    }

    public function index()
    {
        $this->data['institutionKubun'] = $this->session->userdata('account')->institution_kubun ?? null;
        $organsAvailable = array();
        foreach ($this->Institutionmst->getInstitutionOrgans($this->session->userdata('institution_mst_id')) as $item) {
            array_push($organsAvailable, $item->id);
        }
        $this->data['organsAvailable'] = $organsAvailable;
        $this->session->set_userdata('organsAvailable', $organsAvailable);

        $block = array('' => '選択してください');
        foreach ($this->Blockmst->getBlockmst() as $value) {
            $block[$value->id] = $value->block_name;
        }

        if ($this->data['userType'] == ACC_TYPE_TP && $this->data['institutionKubun'] == INSTITUTION_KUBUN_TRANSPLANT) {
            $transplant = array('' => $this->session->userdata('account')->institution_name);
        } else {
            $transplant = array('' => '選択してください');
            foreach ($this->Institutionmst->getInstitutionMstByKubun(INSTITUTION_KUBUN_TRANSPLANT) as $value) {
                $transplant[$value->SISETU_CD] = $value->institution_name;
            }
        }

        if ($this->data['userType'] == ACC_TYPE_TP && $this->data['institutionKubun'] == INSTITUTION_KUBUN_TRANSFER) {
            $postTransplant = array('' => $this->session->userdata('account')->institution_name);
        } else {
            $postTransplant = array('' => '選択してください');
            foreach ($this->Institutionmst->getInstitutionMstByKubun(INSTITUTION_KUBUN_TRANSFER) as $value) {
                $postTransplant[$value->SISETU_CD] = $value->institution_name;
            }
        }

        $patientOutcome = $this->Mcd->getByCodeType(CODE_TYPE['PATIENT_OUTCOME']);
        array_splice($patientOutcome, 1, 0, array($patientOutcome[3]));
        unset($patientOutcome[4]);

        $this->data = array_merge($this->data, array(
            'block' => $block,
            'transplant' => $transplant,
            'postTransplant' => $postTransplant,
            'organOutcome' => $this->Mcd->getByCodeType(CODE_TYPE['ORGAN_OUTCOME']),
            'patientOutcome' => $patientOutcome,
            'patientOutcomeDetails' => $this->Mcd->getByCodeType(CODE_TYPE['PATIENT_OUTCOME_DETAILS']),
            'organDonationStatus' => $this->Mcd->getByCodeType(CODE_TYPE['ORGAN_DONATION_STATUS'])
        ));

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('search/script');
        $this->load->view('search/recipient');
        $this->load->view('search/donor');
        $this->load->view('search/result');
        $this->load->view('transplantSearch');
        $this->load->view('footer');
    }
}
