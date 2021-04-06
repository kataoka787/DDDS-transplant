<?php defined('BASEPATH') or exit('No direct script access allowed');

class Doctor extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "page_title" => config_item('page_transplant_user_list_search'),
        );
        $this->load->library('box_api');
        $this->load->library('encryption');
    }

    public function index()
    {
        /* Unset all session */
        $this->session->unset_userdata('disp_data');
        $this->session->unset_userdata('input_data');
        $this->session->unset_userdata('isEdit');
        $this->session->unset_userdata('isHadPassword');
        /* Get account info */
        $account = $this->session->userdata("account");
        if (!$this->input->post()) {
            $_POST["sei_kana"] = "";
            $_POST["mei_kana"] = "";
            $_POST["organs"] = "";
        }

        $this->data['organs'] = $this->Internalorgansmst->getInternalOrgansMst();
        $this->form_validation->run('transplant/doctor/search');
        $this->makeList($account);
        $this->data['prefName'] = $account->pref_name;
        $this->data['institutionName'] = $account->institution_name;
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('doctor/searchlist');
        $this->load->view('footer');
    }

    public function confirm()
    {
        /* Unset all session */
        $this->session->unset_userdata('disp_data');
        $this->session->unset_userdata('input_data');
        $this->session->unset_userdata('isEdit');
        $this->session->unset_userdata('isHadPassword');
        /* Get doctor account id */
        $id = $this->input->post("id");
        if ($id !== null) {
            $data = $this->Doctortbl->getTransplantDoctorTblData($id);
            $data || redirect('doctor');
            $this->session->set_userdata('conf_id', $data->id);
            $this->data['user_data'] = $data;
            $this->data['page_title'] = config_item('page_transplant_user_delete_confirm');
            $this->data["isPasswordInputable"] = $data->admin_flg == IS_ADMIN || in_array(WORK_FOLLOW_UP, explode(",", $data->work_id));
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('doctor/confirm');
            $this->load->view('footer');
        } else {
            redirect('doctor');
        }
    }

    public function edit()
    {
        $account = $this->session->userdata("account");
        $input_data = $this->session->userdata('input_data');
        if (!$input_data) {
            $accId = $this->input->post('account_id');
            if ($accId !== null) { /* Edit transplant user */
                $this->session->set_userdata("isEdit", true);
                $data = $this->Doctortbl->getTransplantDoctorTblData($accId);
                /* Current doctor account no longer belongs to this institution */
                if ($data->institution_mst_id != $account->institution_mst_id) {
                    redirect("doctor");
                }
                $_POST["institution"] = $data->institution_mst_id;
                $_POST["doctor_type_id"] = $data->admin_flg;
                $_POST["sei"] = $data->sei;
                $_POST["mei"] = $data->mei;
                $_POST["sei_kana"] = $data->sei_kana;
                $_POST["mei_kana"] = $data->mei_kana;
                $_POST["mail"] = $data->mail;
                $_POST["organs"] = explode(",", $data->organ_id);
                $_POST["works"] = explode(",", $data->work_id);
                $_POST["institution_kubun"] = $data->institution_kubun;
                $_POST["account_id"] = $data->id;
                $this->data["organs"] = $this->Institutionmst->getInstitutionOrgans($data->institution_mst_id);
                $this->session->set_userdata("isHadPassword", !empty($data->password));
            } else { /* Create new transplant user */
                $this->session->set_userdata("isEdit", false);
                $this->data['organs'] = $this->Institutionmst->getInstitutionOrgans($account->institution_mst_id);
                $_POST["institution_kubun"] = $account->institution_kubun;
            }
        } else {
            /* Back from conf */
            /* Current doctor account no longer belongs to this institution */
            $data = $this->Doctortbl->getTransplantDoctorTblData($input_data["account_id"]);
            if ($data->institution_mst_id != $account->institution_mst_id) {
                redirect("doctor");
            }
            $_POST["doctor_type_id"] = $input_data["doctor_type_id"];
            $_POST["organs"] = $input_data["organs"];
            $_POST["works"] = $input_data["works"];
            $_POST["sei"] = $input_data["sei"];
            $_POST["mei"] = $input_data["mei"];
            $_POST["sei_kana"] = $input_data["sei_kana"];
            $_POST["mei_kana"] = $input_data["mei_kana"];
            $_POST["mail"] = $input_data["mail"];
            if ($this->session->userdata("isEdit")) {
                $_POST["password"] = $input_data["password"];
                $_POST["account_id"] = $input_data["account_id"];
            }
            $this->data['organs'] = $this->Institutionmst->getInstitutionOrgans($input_data["institution"]);
            $_POST["institution_kubun"] = $input_data["institution_kubun"];
        }
        /* Run form validation */
        $this->form_validation->run("transplant/doctor/edit");
        /* Set view variables's value */
        $this->data["prefName"] = $account->pref_name;
        $this->data["institutionName"] = $account->institution_name;
        $this->data["institutionKubun"] = INSTITUTION_KUBUN[$account->institution_kubun];
        $this->data["works"] = $this->Workmst->getWorkMst();
        if ($this->session->userdata("isEdit")) {
            $this->data['page_title'] = config_item('page_transplant_user_edit');
            $isHadPassword = $this->session->userdata("isHadPassword");
            $isAdmin = $this->input->post("doctor_type_id");
            $works = is_array($this->input->post("works")) ? $this->input->post("works") : array();
            $this->data["isPasswordInputable"] = $isHadPassword && ($isAdmin == IS_ADMIN || in_array(WORK_FOLLOW_UP, $works));
        } else {
            $this->data['page_title'] = config_item('page_transplant_user_regist');
            $this->data["isPasswordInputable"] = false;
        }

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('doctor/edit');
        $this->load->view('footer');
    }

    public function conf()
    {
        $inputData = $this->input->post();
        $account = $this->session->userdata("account");
        $this->data["prefName"] = $account->pref_name;
        $this->data["institutionName"] = $account->institution_name;
        $this->data["institutionKubun"] = INSTITUTION_KUBUN[$account->institution_kubun];
        /* Current doctor account no longer belongs to this institution */
        if (!empty($inputData["account_id"])) {
            $data = $this->Doctortbl->getTransplantDoctorTblData($inputData["account_id"]);
            if ($data->institution_mst_id != $account->institution_mst_id) {
                redirect("doctor");
            }
        }
        $_POST["institution"] = $account->institution_mst_id;
        $isHadPassword = $this->session->userdata("isHadPassword");
        if ($this->form_validation->run("transplant/doctor/edit") === true) {
            $this->session->set_flashdata('conf', true);
            if ($this->session->userdata("isEdit")) {
                $this->data['page_title'] = config_item('page_transplant_user_edit_conf');
                $this->data["isPasswordInputable"] = $isHadPassword && ($inputData["doctor_type_id"] == IS_ADMIN || in_array(WORK_FOLLOW_UP, $inputData["works"]));
            } else {
                $this->data['page_title'] = config_item('page_transplant_user_regist_conf');
                $this->data["isPasswordInputable"] = false;
            }
            $this->data['name'] = $inputData["sei"] . " " . $inputData["mei"];
            $this->data['institution_kubun'] = $inputData["institution_kubun"];
            $this->data['prefName'] = $account->pref_name;
            $this->data['kana'] = $inputData["sei_kana"] . " " . $inputData["mei_kana"];
            $this->data['mail'] = $this->input->post('mail');
            $this->data['institution'] = $this->Institutionmst->getTransplantInstitutionMstById($this->input->post('institution'))->institution_name;
            $this->data["organs"] = array_reduce($this->Internalorgansmst->getInternalOrgansMst(), function ($organs, $organ) use ($inputData) {
                return in_array($organ->id, $inputData["organs"]) ? $organs .= $organ->organ_name . "　" : $organs;
            });
            $this->data['doctor_type_name'] = DOCTOR_TYPE[$inputData["doctor_type_id"]];
            $this->data["works"] = array_reduce($this->Workmst->getWorkMst(), function ($works, $work) use ($inputData) {
                return in_array($work->id, $inputData["works"]) ? $works .= $work->work_name . "　" : $works;
            });
            $this->session->set_userdata('input_data', $inputData);
            $this->session->set_userdata('disp_data', $this->data);
            /* Render view */
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('doctor/conf');
            $this->load->view('footer');
        } else {
            $this->data['organs'] = $this->data["organs"] = $this->Institutionmst->getInstitutionOrgans($account->institution_mst_id);
            $this->data["works"] = $this->Workmst->getWorkMst();
            if ($this->input->post('pref_id')) {
                $this->data['institution'] = $this->Institutionmst->getTransplantInstitutionMstByPrefId($this->input->post('pref_id'));
            }
            if ($this->session->userdata("isEdit")) {
                $this->data['page_title'] = config_item('page_transplant_user_edit');
                $isAdmin = $this->input->post("doctor_type_id");
                $works = isset($inputData["works"]) ? $inputData["works"] : array();
                $this->data["isPasswordInputable"] = $isHadPassword && ($isAdmin == IS_ADMIN || in_array(WORK_FOLLOW_UP, $works));
            } else {
                $this->data['page_title'] = config_item('page_transplant_user_regist');
                $this->data["isPasswordInputable"] = false;
            }
            /* Render view */
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('doctor/edit');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        if ($this->session->flashdata('conf')) {
            if ($this->session->userdata('isEdit')) {
                $this->updateTransplantUser();
            } else {
                $this->createTransplantUser();
            }
        }
        redirect('doctor/edit');
    }

    private function createTransplantUser()
    {
        $data = $this->session->userdata('input_data');
        $displayData = $this->session->userdata('disp_data');
        /* Create box folder if ddds transplant user */
        if (in_array(WORK_DDDS, $data["works"])) {
            $organFolder = "";
            foreach (explode("　", $displayData['organs']) as $organName) {
                $organFolder .= mb_substr($organName, 0, 1);
            }
            /* Create transplant user box folder */
            $folderName = $this->session->userdata("account")->institution_name . "_" . $organFolder . "_" . $data["sei"] . $data["mei"];
            $tpUserFolder = $this->createTpUserFolder($folderName, $data["mail"]);
            $data["boxfolder_id"] = $tpUserFolder["boxfolder_id"];
            $data["box_collaboration_id"] = $tpUserFolder["box_collaboration_id"];
        } else {
            $data["boxfolder_id"] = null;
            $data["box_collaboration_id"] = null;
        }
        /* Send register mail if transplant user is admin or belong to transfer institution */
        if ($data["doctor_type_id"] == IS_ADMIN || in_array(WORK_FOLLOW_UP, $data["works"])) {
            /* Create temporary password */
            $data["password"] = createRandomPassword(config_item("random_password_pool"), config_item("random_password_length"));
            $data["password_datetime"] = createExpiredPasswordDate(config_item("password_expired"));
        } else {
            /* ddds transplant user does not have password */
            $data["password"] = null;
            $data["password_datetime"] = null;
        }
        /* Insert account info */
        $this->insertAccountInfo($data);
        /* Send register complete email */
        $this->sendRegisterCompletedEmail($data["mail"], $data);
        redirect("doctor/end");
    }

    private function createTpUserFolder($folderName, $tpUserMail)
    {
        /* Create transplant user box folder */
        $tpUserFolder = $this->box_api->createFolder($folderName, TP_USER_ROOT_FOLDER);
        if ($tpUserFolder["success"]) {
            /* Set collaboration */
            $item = array(
                "type" => "folder",
                "id" => $tpUserFolder["data"]->id,
            );
            $accessibleBy = array(
                "type" => "user",
                "login" => $tpUserMail,
            );
            $role = "viewer uploader";
            $collaboration = $this->box_api->createCollaboration($item, $accessibleBy, $role);
            if ($collaboration["success"]) {
                return array(
                    "boxfolder_id" => $tpUserFolder["data"]->id,
                    "box_collaboration_id" => $collaboration["data"]->id,
                );
            } else {
                /* Delete created folder if set collaboration fail */
                $this->box_api->deleteFolder($tpUserFolder["data"]->id);
                redirect("errors/collaboration_can_not_create");
            }
        } else {
            redirect("errors/folder_can_not_create");
        }
    }

    private function insertAccountInfo($data)
    {
        /* Insert account info */
        $insert = array(
            "sei" => $data["sei"],
            "mei" => $data["mei"],
            "sei_kana" => $data["sei_kana"],
            "mei_kana" => $data["mei_kana"],
            "password" => $data["password"],
            "password_datetime" => $data["password_datetime"],
            "mail" => $data["mail"],
            "account_type_mst_id" => ACC_TYPE_TP,
            "status_mst_id" => ACC_STT_CONF,
            "admin_flg" => $data["doctor_type_id"],
            "delete_flg" => IN_USE_FLG,
        );
        $insertedAccId = $this->Accounttbl->insertAccountTblData($insert);

        /* Insert doctorRelationOrgansTbl */
        foreach ($data['works'] as $workId) {
            $insert = array(
                "account_tbl_id" => $insertedAccId,
                "work_mst_id" => $workId,
            );
            $this->Accountrelationworktbl->insert($insert);
        }

        /* Insert doctorTbl */
        $insert = array(
            "account_tbl_id" => $insertedAccId,
            "institution_mst_id" => $data["institution"],
            "boxfolder_id" => $data["boxfolder_id"],
            "box_collaboration_id" => $data["box_collaboration_id"],
        );
        $insertedDoctorId = $this->Doctortbl->insertTransplantDoctorTblData($insert);

        /* Insert doctorRelationOrgansTbl */
        foreach ($data['organs'] as $organId) {
            $this->Doctorrelationorganstbl->insertDoctorRelationOrgansTblData(array(
                'doctor_tbl_id' => $insertedDoctorId,
                'internal_organs_mst_id' => $organId,
            ));
        }
    }

    private function sendRegisterCompletedEmail($mailAddress, $data)
    {
        $mailSettings = config_item("mail");
        $tpMailSettings = $mailSettings["transplant_user"];
        $this->Mailsend->setFromName($mailSettings["from_name"]);
        $this->Mailsend->setFrom($mailSettings["from_address"]);
        $this->Mailsend->setTo($mailAddress);
        $this->Mailsend->setSubject($tpMailSettings["subject"]["register"]);
        $this->Mailsend->setBody($tpMailSettings["template"]);
        $this->Mailsend->strReplace("HEADER", $tpMailSettings["header"]["register"]);
        if (isset($data["password"])) {
            $this->Mailsend->strReplace("PASSWORD_NOTIFICATION", $tpMailSettings["password_notification"]["register"]);
            $this->Mailsend->strReplace("PASSWORD", $data["password"]);
            $this->Mailsend->strReplace("TRANSPLANT_BASE_URL", config_item("tp_base_url"));
        } else {
            $this->Mailsend->strReplace("PASSWORD_NOTIFICATION", "");
            $this->Mailsend->strReplace("PASSWORD", "");
        }
        if (isset($data["boxfolder_id"])) {
            $this->Mailsend->strReplace("BOX_NOTIFICATION", $tpMailSettings["box_notification"]);
        } else {
            $this->Mailsend->strReplace("BOX_NOTIFICATION", "");
        }
        $this->Mailsend->strReplace("SIGNATURE", $mailSettings["signature"]);
        $this->Mailsend->strReplace("URL1", config_item('url_user_edit'));
        $this->Mailsend->strReplace("URL2", config_item('url_guide'));
        $this->Mailsend->strReplace("URL3", config_item('url_faq'));
        $this->Mailsend->send();
    }

    private function updateTransplantUser()
    {
        $input_data = $this->session->userdata('input_data');
        $data = $this->Doctortbl->getTransplantDoctorTblData($this->session->userdata('conf_id'));
        /* If editing user not belong to this institution */
        if ($this->session->userdata("account")->institution_mst_id != $data->institution_mst_id) {
            redirect("doctor");
        }
        $before = array(
            "mail" => $data->mail,
            "sei" => $data->sei,
            "mei" => $data->mei,
            "sei_kana" => $data->sei_kana,
            "mei_kana" => $data->mei_kana,
            "password" => $data->password,
            "status_mst_id" => $data->status_mst_id,
            "doctor_type_id" => $data->admin_flg,
            "doctor_type_name" => DOCTOR_TYPE[$data->admin_flg],
            "boxfolder_id" => $data->boxfolder_id,
            "organs" => explode(",", $data->organ_id),
            "works" => explode(",", $data->work_id),
        );

        $after = array(
            "sei" => $input_data['sei'],
            "mei" => $input_data['mei'],
            "sei_kana" => $input_data['sei_kana'],
            "mei_kana" => $input_data['mei_kana'],
            "password" => $input_data['password'],
            "doctor_type_id" => $input_data['doctor_type_id'],
            "doctor_type_name" => DOCTOR_TYPE[$input_data["doctor_type_id"]],
            "boxfolder_id" => $data->boxfolder_id,
            "organs" => $input_data['organs'],
            "works" => $input_data['works'],
        );

        $this->updateTpUserFolder($before, $after);
        $this->updateAccountInfo($before, $after);
        $this->updateAccountHistory($before, $after);
        $isSendMail = (empty($before["password"]) && !empty($after["password"])) || (isset($after["boxfolder_id"]) && $before["boxfolder_id"] != $after["boxfolder_id"]);
        $isSendMail && $this->sendUpdateCompletedEmail($before, $after);
        /* Unset unused session */
        $this->session->unset_userdata('conf_id');
        redirect('doctor/end');
    }

    private function updateTpUserFolder($before, &$after)
    {
        /* Update transplant user's box folder name */
        if (isset($before["boxfolder_id"])) {
            $isNameChanged = $before["sei"] . $before["mei"] != $after["sei"] . $after["mei"];
            $isOrganChanged = $before["organs"] != $after["organs"];
            if ($isNameChanged || $isOrganChanged) {
                $organs = "";
                foreach ($after["organs"] as $organId) {
                    $organs .= mb_substr($this->Internalorgansmst->getInternalOrgansMstById($organId)->organ_name, 0, 1);
                }
                $newFolderName = $this->session->userdata("account")->institution_name . "_" . $organs . "_" . $after["sei"] . $after['mei'];
                $this->box_api->updateFolderName($before["boxfolder_id"], $newFolderName)["success"] || redirect("errors/folder_can_not_update");
            }
        }

        if ($before['works'] != $after['works']) {
            /* ddds transplant user -> transfer transplant user */
            if (isset($before["boxfolder_id"]) && !in_array(WORK_DDDS, $after["works"])) {
                $this->box_api->deleteFolder($before["boxfolder_id"])["success"] || redirect("errors/folder_can_not_delete");
                $after["boxfolder_id"] = null;
                $after["box_collaboration_id"] = null;
            }
            /* transfer transplant user -> ddds transplant user */
            if (empty($before["boxfolder_id"]) && in_array(WORK_DDDS, $after["works"])) {
                $organs = "";
                foreach ($after["organs"] as $organId) {
                    $organs .= mb_substr($this->Internalorgansmst->getInternalOrgansMstById($organId)->organ_name, 0, 1);
                }
                /* Create transplant user box folder */
                $folderName = $this->session->userdata("account")->institution_name . "_" . $organs . "_" . $after["sei"] . $after["mei"];
                $tpUserFolder = $this->createTpUserFolder($folderName, $before["mail"]);
                $after["boxfolder_id"] = $tpUserFolder["boxfolder_id"];
                $after["box_collaboration_id"] = $tpUserFolder["box_collaboration_id"];
            }
        }
    }

    private function updateAccountInfo($before, &$after)
    {
        /* Update account tbl */
        $update = array(
            "sei" => $after['sei'],
            "mei" => $after['mei'],
            "sei_kana" => $after['sei_kana'],
            "mei_kana" => $after['mei_kana'],
            "admin_flg" => $after['doctor_type_id'],
            "updated_at" => date('Y-m-d H:i:s'),
        );
        if (empty($before["password"])) {
            if (in_array(WORK_FOLLOW_UP, $after["works"]) || $after["doctor_type_id"] == IS_ADMIN) {
                $after["password"] = createRandomPassword(config_item("random_password_pool"), config_item("random_password_length"));
                $update["password"] = $after["password"];
                $update["password_datetime"] = createExpiredPasswordDate(config_item("password_expired"));
            }
        } else if (!empty($after['password'])) {
            $update["password"] = $after['password'];
            $update['password_datetime'] = date('Y-m-d');
        }
        $isDeletePassword = count($after["works"]) == 1 && in_array(WORK_DDDS, $after["works"]) && $after["doctor_type_id"] != IS_ADMIN;
        $this->Accounttbl->updateAccountTblData($update, $this->session->userdata('conf_id'), $isDeletePassword);

        /* Update doctorRelationOrgansTbl */
        if ($before['organs'] != $after['organs']) {
            $doctor = $this->Doctortbl->getTransplantDoctorTblDataByAccountId($this->session->userdata('conf_id'));
            $this->Doctorrelationorganstbl->deleteDoctorRelationOrgansTblData($doctor->id);
            foreach ($after['organs'] as $organId) {
                $insert = array(
                    "doctor_tbl_id" => $doctor->id,
                    "internal_organs_mst_id" => $organId,
                );
                $this->Doctorrelationorganstbl->insertDoctorRelationOrgansTblData($insert);
            }
        }

        /* Update accountRelationWorkTbl */
        if ($before['works'] != $after['works']) {
            $this->Accountrelationworktbl->delete($this->session->userdata('conf_id'));
            foreach ($after['works'] as $workId) {
                $insert = array(
                    "account_tbl_id" => $this->session->userdata('conf_id'),
                    "work_mst_id" => $workId,
                );
                $this->Accountrelationworktbl->insert($insert);
            }
        }
        /* Update doctorTbl (box folder id) */
        if ($before["boxfolder_id"] != $after["boxfolder_id"]) {
            $update = array(
                "boxfolder_id" => $after["boxfolder_id"],
                "box_collaboration_id" => $after["boxfolder_id"],
            );
            $this->Doctortbl->updateTransplantDoctorTblData($update, $this->session->userdata('conf_id'));
        }
    }

    private function updateAccountHistory($before, $after)
    {
        $change = "";
        /* Organs change */
        if ($before['organs'] != $after['organs']) {
            $organsMst = $this->Internalorgansmst->getInternalOrgansMst();
            $organs = array();
            foreach ($organsMst as $val) {
                $organs[$val->id] = $val->organ_name;
            }
            $before_organs = "";
            $after_organs = "";
            foreach ($before['organs'] as $val) {
                $before_organs .= $organs[$val] . " ";
            }
            foreach ($after['organs'] as $val) {
                $after_organs .= $organs[$val] . " ";
            }
            $change .= "[臓器]\n" . $before_organs . "=> " . $after_organs . "\n";
        }
        /* Name change */
        if ($before['sei'] . $before['mei'] != $after['sei'] . $after['mei']) {
            $change .= "[氏名]\n" . $before['sei'] . $before['mei'] . " => " . $after['sei'] . $after['mei'] . "\n";
        }
        /* Name (kana) change */
        if ($before['sei_kana'] . $before['mei_kana'] != $after['sei_kana'] . $after['mei_kana']) {
            $change .= "[フリガナ]\n" . $before['sei_kana'] . $before['mei_kana'] . " => " . $after['sei_kana'] . $after['mei_kana'] . "\n";
        }
        /* Password change */
        if ($before['password'] != $after['password']) {
            $change .= "[パスワード変更]\n";
        }
        /* Permission change (admin flag) */
        if ($before["doctor_type_id"] != $after["doctor_type_id"]) {
            $change .= "[利用者権限]\n" . $before["doctor_type_name"] . " => " . $after["doctor_type_name"] . "\n";
        }
        /* Work change (ddds/transfer) */
        if ($before['works'] != $after['works']) {
            $workMst = $this->Workmst->getWorkMst();
            $works = array();
            foreach ($workMst as $work) {
                $works[$work->id] = $work->work_name;
            }
            $beforeWorks = "";
            $afterWorks = "";
            foreach ($before['works'] as $workId) {
                $beforeWorks .= $works[$workId] . " ";
            }
            foreach ($after['works'] as $workId) {
                $afterWorks .= $works[$workId] . " ";
            }
            $change .= "[業務権限]\n" . $beforeWorks . " => " . $afterWorks . "\n";
        }
        if ($change != "") {
            /* Update account history (accountChangeHistoryTbl) */
            $insert = array(
                "contents" => $change,
                "account_tbl_id" => $this->session->userdata('conf_id'),
                "account_type_mst_id" => ACC_TYPE_TP,
            );
            $this->Accountchangehistorytbl->insertAccountChangeHistoryTblData($insert);
        }
    }

    private function sendUpdateCompletedEmail($before, $after)
    {
        $mailSettings = config_item("mail");
        $tpMailSettings = $mailSettings["transplant_user"];
        $this->Mailsend->setFromName($mailSettings["from_name"]);
        $this->Mailsend->setFrom($mailSettings["from_address"]);
        $this->Mailsend->setTo($before["mail"]);
        $this->Mailsend->setSubject($tpMailSettings["subject"]["edit"]);
        $this->Mailsend->setBody($tpMailSettings["template"]);
        $this->Mailsend->strReplace("HEADER", $tpMailSettings["header"]["edit"]);
        if (empty($before["password"]) && !empty($after["password"])) {
            $this->Mailsend->strReplace("PASSWORD_NOTIFICATION", $tpMailSettings["password_notification"]["edit"]);
            $this->Mailsend->strReplace("TRANSPLANT_BASE_URL", config_item("tp_base_url"));
            $this->Mailsend->strReplace("PASSWORD", $after["password"]);
        } else {
            $this->Mailsend->strReplace("PASSWORD_NOTIFICATION", "");
            $this->Mailsend->strReplace("PASSWORD", "");
        }
        if (isset($after["boxfolder_id"]) && $before["boxfolder_id"] != $after["boxfolder_id"]) {
            $this->Mailsend->strReplace("BOX_NOTIFICATION", $tpMailSettings["box_notification"]);
        } else {
            $this->Mailsend->strReplace("BOX_NOTIFICATION", "");
        }
        $this->Mailsend->str_replace("SIGNATURE", $mailSettings["signature"]);
        $this->Mailsend->str_replace("URL1", config_item('url_user_edit'));
        $this->Mailsend->str_replace("URL2", config_item('url_guide'));
        $this->Mailsend->str_replace("URL3", config_item('url_faq'));
        $this->Mailsend->send();
    }

    public function end()
    {
        if ($this->session->userdata('disp_data')) {
            $this->data = $this->session->userdata('disp_data');
            $this->data["workIds"] = $this->session->userdata("input_data")["works"];
            $this->data["isEdit"] = $this->session->userdata("isEdit");
            if ($this->data["isEdit"]) {
                $this->data['page_title'] = config_item('page_transplant_user_edit_comp');
            } else {
                $this->data['page_title'] = config_item('page_transplant_user_regist_comp');
            }
            /* Unset all session */
            $this->session->unset_userdata('disp_data');
            $this->session->unset_userdata('input_data');
            $this->session->unset_userdata('isEdit');
            $this->session->unset_userdata('isHadPassword');
            /* Render view */
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('doctor/end');
            $this->load->view('footer');
        } else {
            redirect('doctor');
        }
    }

    public function delete()
    {
        $accId = $this->input->post('account_id');
        $account = $this->session->userdata("account");
        if ($accId !== null) {
            $doctor = $this->Doctortbl->getTransplantDoctorTblData($accId);
            /* Current doctor account no longer belongs to this institution */
            if ($doctor->institution_mst_id != $account->institution_mst_id) {
                redirect("doctor");
            }
            if ($doctor) {
                if ($doctor->boxfolder_id != null) {
                    $this->box_api->deleteFolder($doctor->boxfolder_id)["success"] || redirect("errors/folder_can_not_delete");
                    /* Update transplant user info */
                    $update = array(
                        "boxfolder_id" => null,
                        "box_collaboration_id" => null,
                    );
                    $this->Doctortbl->updateTransplantDoctorTblData($update, $accId);
                }
                /* Update account info */
                $update = array(
                    'delete_flg' => DELETED_FLG,
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->Accounttbl->updateAccountTblData($update, $accId);
                /* Update account change history */
                $contents = "アカウント削除";
                $insert = array(
                    "contents" => $contents,
                    "account_tbl_id" => $accId,
                    "account_type_mst_id" => ACC_TYPE_TP,
                );
                $this->Accountchangehistorytbl->insertAccountChangeHistoryTblData($insert);
                redirect('doctor/delete_end');
            }
        }
        redirect('doctor');
    }

    public function delete_end()
    {
        $this->load->view('header');
        $this->load->view('doctor/delete_end');
        $this->load->view('footer');
    }

    public function makeList($account)
    {
        $seiKana = $this->input->post("sei_kana");
        $meiKana = $this->input->post("mei_kana");

        if ($this->input->post('organs')) {
            $organs = $this->input->post('organs');
        } else {
            $organs = array();
        }
        $offset = is_numeric($this->uri->segment(2, 0)) ? $this->uri->segment(2, 0) : 0;

        $config['base_url'] = base_url() . "doctor";
        $config['total_rows'] = $this->Doctortbl->getTransplantDoctorTblSearchCount($account->pref_mst_id, $account->institution_mst_id, $organs, $seiKana, $meiKana, ACC_STT_CONF, $this->session->userdata("affiliation_mst_id"));
        $config['per_page'] = config_item('transplant_user_search_list_count');
        $config['next_link'] = "NEXT";
        $config['prev_link'] = "PREV";
        $config['query_string_segment'] = true;
        $config['display_pages'] = false;

        $this->pagination->initialize($config);
        $links = $this->pagination->create_links();

        preg_match('/NEXT/', $links, $next);
        preg_match('/PREV/', $links, $prev);

        $this->data['next']['flg'] = false;
        $this->data['prev']['flg'] = false;

        if ($next) {
            $this->data['next']['flg'] = true;
            $this->data['next']['link'] = $offset + config_item('transplant_user_search_list_count');
        }

        if ($prev) {
            $this->data['prev']['flg'] = true;
            $link = "";
            if ($offset - config_item('transplant_user_search_list_count')) {
                $link = $offset - config_item('transplant_user_search_list_count');
            }
            $this->data['prev']['link'] = $link;
        }
        $this->data['list'] = $this->Doctortbl->getTransplantDoctorTblSearch($account->pref_mst_id, $account->institution_mst_id, $organs, $seiKana, $meiKana, ACC_STT_CONF, $this->session->userdata("affiliation_mst_id"), $offset, config_item('transplant_user_search_list_count'));
    }
}
