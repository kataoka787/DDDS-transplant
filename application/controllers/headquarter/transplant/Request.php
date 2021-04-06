<?php defined('BASEPATH') or exit('No direct script access allowed');

class Request extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = $this->session->userdata('data');
        $this->data['page_title'] = config_item('page_transplant_donor_data');
        if (!$this->session->userdata('data')) {
            redirect('donor/searchlist');
        }
        $this->load->library('box_api');
    }

    public function index()
    {
        $pref = "";
        $institution = "";
        $doctor = "";

        $fileList = $this->Upfiletbl->getUpfileTransplantUploadByDid($this->data['d_id'], $this->session->userdata('affiliation_mst_id'));
        $list = array();
        foreach ($fileList as $file) {
            $list[$file->folderId]['folder_name'] = $file->folder_name;
            $fileName = explode(".", $file->file_name)[0];
            $fileExt = explode(".", $file->file_name)[1];
            if ($file->file_name_prefix) {
                $fileName .= "(" . $file->file_name_prefix . ")";
            }
            $list[$file->folderId]['file'][$file->id] = "$fileName.$fileExt";
        }

        $organs = $this->Institutionmst->getInstitutionAvailableOrgan();

        if ($this->session->flashdata('conf')) {
            $input_data = $this->session->userdata('input_data');
            $pref = $this->Institutionmst->getPrefByOrgansIdRequest($input_data['organs']);
            $institution = $this->Institutionmst->getTransplantInstitutionByPrefIdOrgansIdRequest($input_data['pref'], $input_data['organs']);
            $doctor = $this->Doctortbl->getCanAcceptRequestDoctor($input_data['pref'], $input_data['organs'], $input_data['institution']);

            $_POST['pref'] = $input_data['pref'];
            $_POST['organs'] = $input_data['organs'];
            $_POST['institution'] = $input_data['institution'];
            $_POST['user'] = $input_data['user'];

            $files = array();
            foreach ($input_data['files'] as $key => $val) {
                $files[] = $val;
            }
            $_POST['files'] = $files;
        } else {
            $this->session->unset_userdata('input_data');
        }

        $this->data['pref'] = $pref;
        $this->data['list'] = $list;
        $this->data['organs'] = $organs;
        $this->data['institution'] = $institution;
        $this->data['user'] = $doctor;

        $this->form_validation->run('transplant/request');

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('transplant/request');
        $this->load->view('footer');
    }

    public function conf()
    {
        if ($this->input->post('files')) {
            $files = array();
            foreach ($this->input->post('files') as $key => $val) {
                $files[] = $val;
            }
            $_POST['files'] = $files;
        }

        $fileList = $this->Upfiletbl->getUpfileTransplantUploadByDid($this->data['d_id'], $this->session->userdata('affiliation_mst_id'));
        $list = array();
        foreach ($fileList as $file) {
            $list[$file->folderId]['folder_name'] = $file->folder_name;
            $fileName = explode(".", $file->file_name)[0];
            $fileExt = explode(".", $file->file_name)[1];
            if ($file->file_name_prefix) {
                $fileName .= "(" . $file->file_name_prefix . ")";
            }
            $list[$file->folderId]['file'][$file->id] = "$fileName.$fileExt";
        }

        if ($this->form_validation->run('transplant/request') == true) {
            $input_data = $this->input->post();
            $this->data['organ'] = $this->Internalorgansmst->getInternalOrgansMstById($input_data["organs"]);
            $this->data['pref'] = $this->Prefmst->getPrefMstById($input_data["pref"]);
            $this->data['institution'] = $this->Institutionmst->getTransplantInstitutionMstById($input_data["institution"]);
            $this->data['user'] = $this->Accounttbl->getAccountByIds($input_data["user"]);

            $list = array();
            foreach ($fileList as $file) {
                if (in_array($file->id, $input_data["files"])) {
                    $list[$file->folderId]['folder_name'] = $file->folder_name;
                    $fileName = explode(".", $file->file_name)[0];
                    $fileExt = explode(".", $file->file_name)[1];
                    if ($file->file_name_prefix) {
                        $fileName .= "(" . $file->file_name_prefix . ")";
                    }
                    $list[$file->folderId]['file'][$file->id] = "$fileName.$fileExt";
                }
            }
            $this->data['list'] = $list;
            $this->session->set_userdata('input_data', $input_data);
            /* Set institution, organ name*/
            $this->session->set_userdata('institutionName', $this->data["institution"]->institution_name);
            $this->session->set_userdata('organName', $this->data["organ"]->organ_name);

            $this->session->set_flashdata('conf', true);
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('transplant/request_conf');
            $this->load->view('footer');
        } else {
            $organs = $this->Institutionmst->getInstitutionAvailableOrgan();
            $this->data['organs'] = $organs;
            $this->data['institution'] = "";
            $this->data['pref'] = "";
            $this->data['user'] = "";

            if ($this->input->post('organs')) {
                $pref = $this->Institutionmst->getPrefByOrgansIdRequest($this->input->post('organs'), $this->session->userdata('affiliation_mst_id'));
                $this->data['pref'] = $pref;

                if ($this->input->post('pref')) {
                    $institution = $this->Institutionmst->getTransplantInstitutionByPrefIdOrgansIdRequest($this->input->post('pref'), $this->input->post('organs'));
                    $this->data['institution'] = $institution;

                    if ($this->input->post('institution')) {
                        $doctor = $this->Doctortbl->getCanAcceptRequestDoctor($this->input->post('pref'), $this->input->post('organs'), $this->input->post('institution'));
                        $this->data['user'] = $doctor;
                    }
                }
            }
            $this->data['list'] = $list;
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('transplant/request');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        $this->data['institution'] = "";
        $this->data['user'] = "";
        $this->data['pref'] = "";
        if ($this->session->flashdata('conf')) {
            $input_data = $this->session->userdata('input_data');

            /* Box process */
            $data = $this->session->userdata('data');
            $dId = $data["d_id"];

            /* Get requested institution folder info */
            $tpFolder = $this->Donorinstitutionorganstbl->getRequestFolder($dId, $input_data['institution'], $input_data['organs']);

            /* If requested transplant folder already existed */
            if ($tpFolder) {
                $this->copyFile($input_data, $tpFolder->donorinfo_boxfolder_id, $tpFolder->id);
                $this->setCollaborationAndSendMail($dId, $input_data, $tpFolder->parent_boxfolder_id, $tpFolder->id);
            } else { /* Create requested transplant folder */
                /* Get donor info */
                $donor = $this->Donorbasetbl->getDonorBaseTblByDid($dId);
                /* Create transplant folder */
                $institutionName = $this->session->userdata('institutionName');
                $organName = $this->session->userdata('organName');
                $folderName = $institutionName . "_" . $organName . "_" . $dId;
                $tpFolder = $this->box_api->createFolder($folderName, $donor->donor_boxfolder_id);
                /* Create requested transplant main folder */
                if ($tpFolder["success"]) {
                    $tpFolderId = $tpFolder["data"]->id;
                    /* Create headquarter to transplant sub folder */
                    $requestFolder = $this->box_api->createFolder(REQUEST_FOLDER, $tpFolderId);
                    /* Create transplant to headquarter sub folder */
                    $tpHeadFolder = $this->box_api->createFolder(TP_HEAD_FOLDER, $tpFolderId);
                    if ($requestFolder["success"] && $tpHeadFolder["success"]) {
                        $donorInstitutionOrganId = $this->Donorinstitutionorganstbl->insert(array(
                            "d_id" => $dId,
                            "institution_mst_id" => $input_data["institution"],
                            "internal_organs_mst_id" => $input_data["organs"],
                            "parent_boxfolder_id" => $tpFolder["data"]->id,
                            "donorinfo_boxfolder_id" => $requestFolder["data"]->id,
                            "jot_offer_boxfolder_id" => $tpHeadFolder["data"]->id,
                        ));
                        $this->copyFile($input_data, $requestFolder["data"]->id, $donorInstitutionOrganId);
                        $this->setCollaborationAndSendMail($dId, $input_data, $tpFolderId, $donorInstitutionOrganId);
                    } else {
                        $this->box_api->deleteFolder($tpFolderId);
                        redirect("errors/folder_can_not_create");
                    }
                } else {
                    redirect("errors/folder_can_not_create");
                }

            }
            redirect('/transplant/receipt');
        }
        redirect('/transplant/request');
    }

    /**
     * Copy file and insert info to database
     *
     * @param array $inputData
     * @param string $requestFolderId
     * @return void
     */
    public function copyFile($inputData, $requestFolderId, $donorInstitutionOrganId)
    {
        foreach ($inputData['files'] as $fileId) {
            $upFile = $this->Upfiletbl->getUpFileTblById($fileId);
            /* Check if file already requested (copied) */
            if ($this->Fileaccessinstitutiontbl->getFileInfo($upFile->id, $donorInstitutionOrganId)) {
                continue;
            } else {
                $copyFile = $this->box_api->copyFile($upFile->boxfile_id, $requestFolderId);
                /* Insert copied file info to DB */
                if ($copyFile["success"]) {
                    /* Add share link to copied file */
                    $shareLink = $this->box_api->addShareLink($copyFile["data"]->id);
                    if ($shareLink["success"]) {
                        /* Insert request data to DB */
                        $insert = array(
                            'upfile_tbl_id' => $fileId,
                            "donor_institution_organs_tbl_id" => $donorInstitutionOrganId,
                            'up_account_tbl_id' => $this->session->userdata('account')->accountId,
                            "boxfile_id" => $copyFile["data"]->id,
                        );
                        $this->Fileaccessinstitutiontbl->insertFileAccessTransplant($insert);
                    } else {
                        $this->box_api->deleteFile($copyFile["data"]->id);
                        redirect("errors/shared_link_can_not_create");
                    }
                } else {
                    redirect("errors/file_can_not_copy");
                }
            }
        }
    }

    public function setCollaborationAndSendMail($dId, $inputData, $folderId, $donorInstitutionOrganId)
    {
        /* Get users info */
        $users = $this->Accounttbl->getAccountByIds($inputData['user']);
        if ($users) {
            foreach ($users as $user) {
                $accId = $user->id;
                /* Skip if user was collaboration already */
                if ($this->Doctoraccountcollaborationtbl->getCollaborationDoctor($accId, $folderId)) {
                    continue;
                }
                $userEmail = $user->mail;
                /* Get donor */
                $donor = $this->Donorbasetbl->getDonorBaseTblByDid($dId);
                /* Set collaboration */
                $item = array(
                    "type" => "folder",
                    "id" => $folderId,
                );
                $accessibleBy = array(
                    "type" => "user",
                    "login" => $userEmail,
                );
                $role = "viewer uploader";
                $expiresAt = date("Y-m-d\TH:i:s", strtotime($donor->created_at . config_item("request_expired"))) . TIME_ZONE;
                /* Create collaboration */
                $collaboration = $this->box_api->createCollaboration($item, $accessibleBy, $role);
                $collaboration["success"] || redirect("errors/collaboration_can_not_create");
                $collaborationId = $collaboration["data"]->id;
                /* Set collaboration expired date */
                $collaboration = $this->box_api->updateCollaboration($collaborationId, $role, $expiresAt);
                if ($collaboration["success"]) {
                    /* Insert added collaboration info to database */
                    $data = array(
                        "donor_institution_organs_tbl_id" => $donorInstitutionOrganId,
                        "account_tbl_id" => $accId,
                        "collaboration_id" => $collaborationId,
                    );
                    $this->Doctoraccountcollaborationtbl->insert($data);
                    /* Send notify email */
                    $mailSettings = config_item("mail");
                    $requestMailSettings = $mailSettings["request"];
                    $this->Mailsend->setFromName($mailSettings["from_name"]);
                    $this->Mailsend->setFrom($mailSettings["from_address"]);
                    $this->Mailsend->setBody($requestMailSettings["template"]);
                    $this->Mailsend->setTo($userEmail);
                    $this->Mailsend->setSubject($requestMailSettings["subject"]);
                    $this->Mailsend->str_replace("DID", $dId, false);
                    $this->Mailsend->str_replace("DID", $dId, true);
                    //$this->Mailsend->str_replace("URL", config_item('tp_base_url') . APP_TRANSPLANT, true);
                    $this->Mailsend->str_replace("URL", config_item('tp_base_url'), true);
                    $this->Mailsend->send();
                } else {
                    /* Remove collaboration if can not set expired date */
                    $this->box_api->deleteCollaboration($collaborationId);
                    /* Redirect to error screen */
                    redirect("errors/collaboration_can_not_update");
                }
            }
        }
    }
}
