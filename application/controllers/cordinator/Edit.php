<?php defined('BASEPATH') or exit('No direct script access allowed');

class Edit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array();
    }

    public function newedit()
    {
        redirect('edit');
    }

    public function index()
    {
        $this->data['causeDeathMst'] = $this->Causedeathmst->getCauseDeathMst();
        $this->data['prefMst'] = $this->Prefmst->getPrefMstByAffiliation($this->session->userdata('affiliation_mst_id'));
        $this->data['id'] = "";

        if ($this->input->post('d_id')) {
            $this->data['id'] = $this->input->post('d_id');
            $donor = $this->Donorbasetbl->getDonorBaseTblByDidCordinatorTblId($this->input->post('d_id'), $this->session->userdata('cordinatorId'), $this->session->userdata('affiliation_mst_id'));
            if (!$donor) {
                redirect("donorlist");
            }
            $_POST['offerInstitution'] = $donor->offer_institution_name;
            $_POST['offerInstitutionPref'] = $donor->pref_mst_id;
            $_POST['firstName'] = $donor->sei;
            $_POST['secondName'] = $donor->mei;
            $_POST['age'] = $donor->age;
            $_POST['sex'] = $donor->sex;
            $_POST['deathReasonMstId'] = $donor->cause_death_mst_id;
            $_POST['message'] = $donor->comment;
        } else if ($this->session->flashdata('input_data')) {
            $this->data['id'] = $this->session->flashdata('id');
            $input_data = $this->session->flashdata('input_data');
            $_POST['offerInstitution'] = $input_data['offerInstitution'];
            $_POST['offerInstitutionPref'] = $input_data['offerInstitutionPref'];
            $_POST['firstName'] = $input_data['firstName'];
            $_POST['secondName'] = $input_data['secondName'];
            $_POST['age'] = $input_data['age'];
            $_POST['sex'] = $input_data['sex'];
            $_POST['deathReasonMstId'] = $input_data['deathReasonMstId'];
            $_POST['message'] = $input_data['message'];
        }

        $this->session->set_flashdata('causeDeathMst', $this->data['causeDeathMst']);
        $this->session->set_flashdata('prefMst', $this->data['prefMst']);
        $this->session->set_flashdata('id', $this->data['id']);
        $this->form_validation->run('donor');

        if ($this->input->post('d_id') || $this->session->flashdata('id')) {
            $this->data['page_title'] = $this->config->item('page_donor_edit');
        } else {
            $this->data['page_title'] = $this->config->item('page_donor_regist');
        }

        $this->load->vars($this->data);

        $this->load->view('header');
        $this->load->view('edit/index');
        $this->load->view('footer');
    }

    public function conf()
    {
        $this->data['causeDeathMst'] = $this->session->flashdata('causeDeathMst');
        $this->data['prefMst'] = $this->session->flashdata('prefMst');
        $this->data['id'] = $this->session->flashdata('id');
        $this->session->keep_flashdata('id');
        $this->session->keep_flashdata('prefMst');
        $this->session->keep_flashdata('causeDeathMst');

        if ($this->form_validation->run('donor') == true) {

            $input_data = $this->input->post();
            $this->data = array_merge($this->data, array(
                "dispOfferInstitution" => $input_data['offerInstitution'],
                "dispOfferInstitutionPref" => $this->Prefmst->getPrefNameById($input_data['offerInstitutionPref']),
                "dispDonorNeme" => $this->Donorbasetbl->getDispName($input_data['firstName'], $input_data['secondName'], " "),
                "dispAge" => $input_data['age'],
                "dispSex" => $input_data['sex'],
                "dispDeathReason" => $this->Causedeathmst->getCauseDeathNameById($input_data['deathReasonMstId']),
                "dispMessage" => $input_data['message'],
            ));

            $_POST['offerInstitution'] = $input_data['offerInstitution'];
            $_POST['offerInstitutionPref'] = $input_data['offerInstitutionPref'];
            $_POST['firstName'] = $input_data['firstName'];
            $_POST['secondName'] = $input_data['secondName'];
            $_POST['age'] = $input_data['age'];
            $_POST['sex'] = $input_data['sex'];
            $_POST['deathReasonMstId'] = $input_data['deathReasonMstId'];
            $_POST['message'] = $input_data['message'];

            $this->session->set_flashdata('input_data', $input_data);
            $this->session->set_flashdata('edit_data', $this->data);

            $affiliation = $this->Prefmst->getAffiliationIdByPrefId($this->input->post('offerInstitutionPref'));
            $this->data['affiliation'] = $affiliation->affiliation_mst_id;
            $this->session->set_flashdata('affiliation', $affiliation->affiliation_mst_id);

            $this->session->set_flashdata('input_data', $this->input->post());
            $this->session->set_flashdata('donor_conf', true);
            if (!$this->session->flashdata('id')) {
                $donorCnt = $this->Donorbasetbl->getDonorBaseTblByDateAffiliationMstIdCount(date('Y-m-d'), $this->session->flashdata('affiliation'));
                $d_id = $this->config->item('did_prefix') . date('Ymd') . "-" . ($this->session->flashdata('affiliation') - 1) . sprintf("%02d", ($donorCnt + 1));
            } else {
                $d_id = $this->session->flashdata('id');
            }
            $this->data['d_id'] = $d_id;
            $this->session->set_flashdata('d_id', $d_id);

            if ($this->session->flashdata('id')) {
                $this->data['page_title'] = $this->config->item('page_donor_edit_conf');
            } else {
                $this->data['page_title'] = $this->config->item('page_donor_regist_conf');
            }

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('edit/conf');
            $this->load->view('footer');
        } else {

            if ($this->session->flashdata('id')) {
                $this->data['page_title'] = $this->config->item('page_donor_edit');
            } else {
                $this->data['page_title'] = $this->config->item('page_donor_regist');
            }

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('edit/index');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        if ($this->session->flashdata('donor_conf')) {
            $dId = $this->session->flashdata('d_id');
            $inputData = $this->session->flashdata('input_data');
            $insert = array(
                "d_id" => $dId,
                "firstName" => $inputData['firstName'],
                "secondName" => $inputData['secondName'],
                "age" => $inputData['age'],
                "sex" => $inputData['sex'],
                "offerInstitution" => $inputData['offerInstitution'],
                "offerInstitutionPref" => $inputData['offerInstitutionPref'],
                "deathReasonMstId" => $inputData['deathReasonMstId'],
                "message" => $inputData['message'],
                'affiliation_mst_id' => $this->session->flashdata('affiliation'),
            );

            if ($this->session->flashdata('id')) {
                /* Get donor info */
                $donor = $this->Donorbasetbl->getDonorBaseTblByDid($dId);
                /* Update donor folder name if need */
                if ($donor->offer_institution_name != $insert["offerInstitution"]) {
                    $newFolderName = $insert["d_id"] . "_" . $insert["offerInstitution"];
                    $this->updateFolderName($donor->donor_boxfolder_id, $newFolderName);
                }
                /* Update metadata */
                $this->updateMetadata($donor, $inputData);
                /* Update donor info */
                $this->Donorbasetbl->updateDonorData($insert, $dId);
            } else {
                /* Create donor main folder */
                $name = $dId . "_" . $inputData["offerInstitution"];
                $donorFolder = $this->box_api->createFolder($name, ROOT_FOLDER);
                if ($donorFolder['success']) {
                    $insert["donor_boxfolder_id"] = $donorFolder["data"]->id;
                    /* Create donor folder metadata */
                    $donorMetadata = (object) array(
                        "offerInstitution" => $inputData['offerInstitution'],
                        "offerInstitutionPref" => $this->Prefmst->getPrefNameById($inputData['offerInstitutionPref']),
                        "donorFullName" => $inputData['firstName'] . $inputData['secondName'],
                        "age" => $inputData['age'],
                        "sex" => SEX[$inputData['sex']],
                        "deathReasonMstId" => $this->Causedeathmst->getCauseDeathNameById($inputData["deathReasonMstId"]),
                        "message" => $inputData['message'],
                    );
                    $this->box_api->addMetadata($donorFolder["data"]->id, $donorMetadata)["success"] || redirect("errors/metadata_can_not_create");

                    /* Create cordinator to headquarter folder */
                    $coFolder = $this->box_api->createFolder(CO_FOLDER, $donorFolder['data']->id);
                    /* Create headquarter to cordinator folder */
                    $headToCoFolder = $this->box_api->createFolder(HEAD_CO_FOLDER, $donorFolder['data']->id);
                    /* Create headquarter to transplant folder */
                    $headToTpFolder = $this->box_api->createFolder(HEAD_TP_FOLDER, $donorFolder['data']->id);
                    if ($coFolder["success"] && $headToCoFolder["success"] && $headToTpFolder["success"]) {
                        $insert["offer_institution_boxfolder_id"] = $coFolder["data"]->id;
                        $insert["jot_boxfolder_id"] = $headToCoFolder["data"]->id;
                        $insert["institution_boxfolder_id"] = $headToTpFolder["data"]->id;
                        if (!$this->Donorbasetbl->insertDonorData($insert)) {
                            redirect('donor/edit/error');
                        }
                    } else {
                        $this->box_api->deleteFolder($donorFolder["data"]->id);
                        redirect('errors/folder_can_not_create');
                    }
                } else {
                    redirect('folder_can_not_create');
                }
            }
            $this->session->keep_flashdata('id');
            $this->session->keep_flashdata('edit_data');
            $this->session->set_flashdata('disp_did', $dId);
            redirect('edit/end');
        } else {
            redirect('edit');
        }
    }
    private function updateFolderName($boxFolderId, $newFolderName)
    {
        $this->box_api->updateFolderName($boxFolderId, $newFolderName)["success"] || redirect("errors/folder_can_not_update");
    }

    private function updateMetadata($donor, $inputData)
    {
        $donorMetadata = array();
        if ($donor->offer_institution_name != $inputData['offerInstitution']) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/offerInstitution",
                "value" => $inputData['offerInstitution'],
            ));
        }

        if ($donor->pref_mst_id != $inputData['offerInstitutionPref']) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/offerInstitutionPref",
                "value" => $this->Prefmst->getPrefNameById($inputData['offerInstitutionPref']),
            ));
        }

        if ($donor->sei . $donor->mei != $inputData['firstName'] . $inputData["secondName"]) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/donorFullName",
                "value" => $inputData['firstName'] . $inputData['secondName'],
            ));
        }

        if ($donor->age != $inputData["age"]) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/age",
                "value" => $inputData['age'],
            ));
        }

        if ($donor->sex != $inputData["sex"]) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/sex",
                "value" => SEX[$inputData['sex']],
            ));
        }

        if ($donor->cause_death_mst_id != $inputData["deathReasonMstId"]) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/deathReasonMstId",
                "value" => $this->Causedeathmst->getCauseDeathNameById($inputData["deathReasonMstId"]),
            ));
        }

        if ($donor->comment != $inputData["message"]) {
            array_push($donorMetadata, (object) array(
                "op" => "replace",
                "path" => "/message",
                "value" => $inputData["message"],
            ));
        }
        /* Update metadata if need */
        if (count($donorMetadata) != 0) {
            $this->box_api->updateMetadata($donor->donor_boxfolder_id, $donorMetadata)["success"] || redirect("errors/metadata_can_not_update");
        }
    }

    public function error()
    {
        $this->data['d_id'] = $this->session->flashdata('disp_did');
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('edit/error');
        $this->load->view('footer');
    }

    public function end()
    {
        $this->session->flashdata("edit_data");
        if ($this->session->flashdata('id')) {
            $this->data['page_title'] = $this->config->item('page_donor_edit_comp');
        } else {
            $this->data['page_title'] = $this->config->item('page_donor_regist_comp');
        }

        $this->data['id'] = $this->session->flashdata('id');

        $data = $this->session->flashdata('edit_data');
        $this->data['d_id'] = $this->session->flashdata('disp_did');
        foreach ($data as $key => $val) {
            $this->data[$key] = $val;
        }

        $this->session->keep_flashdata('id');
        $this->session->keep_flashdata('edit_data');
        $this->session->keep_flashdata('disp_did');

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('edit/end');
        $this->load->view('footer');
    }
}
