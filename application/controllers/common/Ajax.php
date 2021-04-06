<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "branch" => config_item("branch"),
        );
    }

    public function fileCategory()
    {
        $id = $this->input->post('id');
        $subBranch = $this->input->post("sub_branch");
        switch (config_item("branch")) {
            case APP_CORDINATOR:
                $category = $this->Foldercategorymanagementtbl->getCategoryData($id);
                break;
            case APP_HEAD:
                $category = $subBranch == APP_CORDINATOR
                ? $this->Foldercategorymanagementtbl->getCategoryCoData($id, $this->session->userdata("affiliation_mst_id"))
                : $this->Foldercategorymanagementtbl->getCategoryTransplantData($id, $this->session->userdata("affiliation_mst_id"));
                break;
        }
        $this->data['category'] = $category;
        $this->load->vars($this->data);
        $this->load->view('ajax/fileCategory');
    }

    public function transplant()
    {
        $id = $this->input->post('id');
        $this->data['institution'] = $this->Institutionmst->getTransplantInstitutionMstByBlockId($id);
        $this->data['type'] = "transplant";
        $this->load->vars($this->data);
        $this->load->view('ajax/institution');
    }

    public function pref()
    {
        $id = $this->input->post('id');
        $pref = $this->Prefmst->getPrefMstByAffiliation($id);
        $this->data['pref'] = $pref;
        $this->load->vars($this->data);
        $this->load->view('ajax/pref');
    }

    public function pref_by_organ()
    {
        $id = $this->input->post('id');
        $this->data['pref'] = $this->Institutionmst->getPrefByOrgansIdRequest($id);
        $this->load->vars($this->data);
        $this->load->view('ajax/pref');
    }

    public function pref_by_block()
    {
        $id = $this->input->post('id');
        $pref = $this->Prefmst->getPrefMstByBlockId($id);
        $this->data['pref'] = $pref;
        $this->load->vars($this->data);
        $this->load->view('ajax/pref');
    }

    public function institution()
    {
        $prefId = $this->input->post('pref_id');
        $institution = $this->Institutionmst->getTransplantInstitutionMstByPrefId($prefId);
        $this->data['institution'] = $institution;

        $this->load->vars($this->data);
        $this->load->view('ajax/institution');
    }

    public function institution_by_pref_organ()
    {
        $prefId = $this->input->post('pref_id');
        $organId = $this->input->post('organs_id');
        $institution = $this->Institutionmst->getTransplantInstitutionByPrefIdOrgansIdRequest($prefId, $organId);
        $this->data['institution'] = $institution;

        $this->load->vars($this->data);
        $this->load->view('ajax/institution');
    }

    public function institution_kubun()
    {
        $institutionId = $this->input->post("institution_id");
        if ($institutionId !== null) {
            $kubunId = $this->Institutionmst->getTransplantInstitutionMstById($institutionId)->institution_kubun;
            $kubun = array(
                "kubun_id" => $kubunId,
                "kubun_name" => INSTITUTION_KUBUN[$kubunId],
            );
            echo (json_encode($kubun));
        }
    }

    public function institution_organ()
    {
        $institutionId = $this->input->post("institution_id");
        if ($institutionId !== null) {
            $organs = $this->Institutionmst->getInstitutionOrgans($institutionId);
            $this->load->view("ajax/organ", array("organs" => $organs));
        }
    }

    public function day()
    {
        $data = explode("_", $this->input->post('data'));
        $last_day = date("t", mktime(0, 0, 0, $data[1], 1, $data[0]));

        $this->data['last_day'] = $last_day;
        $this->load->vars($this->data);
        $this->load->view('ajax/day');
    }

    public function menu()
    {
        /* XSS clean */
        $key = $this->input->post('key', true);
        $this->data['key'] = $key;
        $this->load->vars($this->data);
        $this->load->view('ajax/menu');
    }

    /**
     * Get can accept request doctor
     *
     * @return view $doctorList
     */
    public function user()
    {
        /* XSS clean */
        $institution_id = $this->input->post('institution_id', true);
        $pref_id = $this->input->post('pref_id');
        $organs_id = $this->input->post('organs_id');
        $doctor = $this->Doctortbl->getCanAcceptRequestDoctor($pref_id, $organs_id, $institution_id);
        $this->data['users'] = $doctor;
        $this->load->vars($this->data);
        $this->load->view('ajax/user');
    }

    public function transplantSearch()
    {
        $result = $this->Institutionmst->getInstitutionMstBySearchConditions($this->input->post());
        $resultCount = count($result);
        
        $this->data['result'] = $result;
        $this->data['resultCount'] = $resultCount;

        echo json_encode(array(
            'view' => $this->load->view('ajax/transplantSearchResult', $this->data, true),
            'count' => $resultCount,
        ));
    }

    public function recipientInfoSearch()
    {
        $searchConditions = $this->input->post();
        $result = ($this->input->get('info') == 'donorInfo' && $this->session->userdata('account_type_mst_id') == ACC_TYPE_CO)
        ? $this->Tishokugokeika->getTIshokugoKeikaByDonorInfo($searchConditions)
        : $this->Tishokugokeika->getTIshokugoKeikaByRecipientInfo($searchConditions);

        $resultCount = count($result);

        if ($resultCount > config_item('max_search_result')) {
            $this->data['resultCount'] = $resultCount;
            echo json_encode(array(
                'view' => $this->load->view('ajax/searchResult', $this->data, true),
                'count' => 0,
            ));
            return;
        }

        $cycleName = array(HYPHEN_CHAR => HYPHEN_CHAR) + $this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CYCLE']);
        foreach ($result as &$row) {
            $row->elapsedPeriod = $cycleName[$row->CYCLE] ?? "-";
            if (isset($searchConditions['checkTarget']['inspectionItem'])) {
                $inspectionValueCycle = "KENSA_VALUE_" . $row->CYCLE;
                $row->inspection_item = $row->$inspectionValueCycle ?? '未完了';
            } else {
                $row->inspection_item = "-";
            }
            if (isset($searchConditions['checkTarget']['livingConditions'])) {
                $row->living_conditions = empty($row->INPUT_DATE) ? "なし" : "あり";
            } else {
                $row->living_conditions = "-";
            }
        }

        $this->data['result'] = $result;
        $this->data['resultCount'] = $resultCount;

        echo json_encode(array(
            'view' => $this->load->view('ajax/searchResult', $this->data, true),
            'result' => $result,
            'count' => $resultCount,
        ));
    }

    public function causeOfDeathSubclass()
    {
        $major = $this->input->post('major');
        if (empty($major)) {
            $result = array();
        } else {
            $result = $this->Mcd->getByCodeType(CODE_TYPE['CAUSE_OF_DEATH_SUBCLASS'], $major);
        }

        echo json_encode($result);
    }
}
