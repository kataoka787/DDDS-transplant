<?php
class Formvalidation extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check if pref id is valid and not empty
     *
     * @param string $prefId
     * @return boolean
     */
    public function pref_id($prefId)
    {
        if (empty($prefId)) {
            $this->form_validation->set_message('pref_id', lang('select'));
            return false;
        } elseif (empty($this->Prefmst->getPrefMstById($prefId))) {
            $this->form_validation->set_message("pref_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if sex id is valid and not empty
     *
     * @param string $sexId
     * @return boolean
     */
    public function sex_id($sexId)
    {
        if (empty($sexId)) {
            $this->form_validation->set_message('sex_id', lang('select'));
            return false;
        } elseif (empty(SEX[$sexId])) {
            $this->form_validation->set_message("sex_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if cause death reason id is valid and not empty
     *
     * @param string $deathId
     * @return boolean
     */
    public function death_reason_id($deathId)
    {
        if (empty($deathId)) {
            $this->form_validation->set_message('death_reason_id', lang('select'));
            return false;
        } elseif (empty($this->Causedeathmst->getCauseDeathNameById($deathId))) {
            $this->form_validation->set_message("death_reason_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if folder id is valid and not empty
     *
     * @param string $folderId
     * @return boolean
     */
    public function folder_id($folderId)
    {
        if (empty($folderId)) {
            $this->form_validation->set_message('folder_id', lang('select'));
            return false;
        } elseif (empty($this->Foldercategorymanagementtbl->getFolderDataCheckById($folderId))) {
            $this->form_validation->set_message('folder_id', lang('valid_value'));
            return false;
        }
        return true;
    }

    /**
     * Check if category id is valid and not empty
     *
     * @param string $categoryId
     * @return boolean
     */
    public function category_id($categoryId)
    {
        if (empty($categoryId)) {
            $this->form_validation->set_message('category_id', lang('select'));
            return false;
        } elseif (empty($this->Foldercategorymanagementtbl->getCategoryDataCheckById($categoryId))) {
            $this->form_validation->set_message('category_id', lang('valid_value'));
            return false;
        }
        return true;
    }

    /**
     * Check if organ id is valid and not empty
     *
     * @param string $organId
     * @return boolean
     */
    public function organ_id($organId)
    {
        if (empty($organId)) {
            $this->form_validation->set_message("organ_id", lang("select"));
            return false;
        } elseif (empty($this->Internalorgansmst->getInternalOrgansMstById($organId))) {
            $this->form_validation->set_message("organ_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if work id is valid and not empty
     *
     * @param string $workId
     * @return boolean
     */
    public function work_id($workId)
    {
        if (!isset($workId)) {
            $this->form_validation->set_message("work_id", lang("select"));
            return false;
        } elseif (empty($this->Workmst->getWorkMstById($workId))) {
            $this->form_validation->set_message("work_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if doctor (transplant user) registed organ id is valid and not empty
     *
     * @param string $organId
     * @return boolean
     */
    public function doctor_organ_id($organId)
    {
        if (empty($organId)) {
            $this->form_validation->set_message('doctor_organ_id', lang('select'));
            return false;
        } else {
            $organ = $this->Internalorgansmst->getInternalOrgansMstById($organId);
            if (empty($organ)) {
                $this->form_validation->set_message('doctor_organ_id', lang('valid_value'));
                return false;
            } else {
                $institutionId = $this->input->post('institution');
                if (!empty($institutionId) && empty($this->Institutionmst->getTransplantByIdOrgansId($institutionId, $organId))) {
                    $this->form_validation->set_message('doctor_organ_id', str_replace("{field}", $organ->organ_name, lang('not_organs')));
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check if transplant user (doctor) work id is valid and not empty
     *
     * @param string $workId
     * @return boolean
     */
    public function doctor_work_id($workId)
    {
        if (empty($workId)) {
            $this->form_validation->set_message('doctor_work_id', lang('select'));
            return false;
        } else {
            $work = $this->Workmst->getWorkMstById($workId);
            if (empty($work)) {
                $this->form_validation->set_message("doctor_work_id", lang("valid_value"));
                return false;
            }
            $institutionKubun = $this->input->post("institution_kubun");
            if ($institutionKubun != INSTITUTION_KUBUN_TRANSPLANT && $workId == WORK_DDDS) {
                $this->form_validation->set_message("doctor_work_id", str_replace("{field}", $work->work_name, lang("work_permission")));
                return false;
            }
        }
        return true;
    }

    /**
     * Check if permission id (admin flag) is valid and not empty
     *
     * @param string $permissionId
     * @return boolean
     */
    public function permission($permissionId)
    {
        if (!isset($permissionId)) {
            $this->form_validation->set_message("permission", lang("select"));
            return false;
        } elseif (empty(CO_TYPE[$permissionId])) {
            $this->form_validation->set_message("permission", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check if kubun id is valid and not empty
     *
     * @param string $kubunId
     * @return boolean
     */
    public function kubun_id($kubunId)
    {
        if (empty($kubunId)) {
            $this->form_validation->set_message('kubun_id', lang('select'));
            return false;
        } elseif (empty(INSTITUTION_KUBUN[$kubunId])) {
            $this->form_validation->set_message('kubun_id', lang('valid_value'));
            return false;
        }
        return true;
    }

    /**
     * Check if institution id is valid and not empty
     *
     * @param string $institutionId
     * @return boolean
     */
    public function institution_id($institutionId)
    {
        if (empty($institutionId)) {
            $this->form_validation->set_message("institution_id", lang("select"));
            return false;
        } elseif (empty($this->Institutionmst->getTransplantInstitutionMstById($institutionId))) {
            $this->form_validation->set_message("institution_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check transplant user (doctor) account id is valid and not empty
     *
     * @param string $doctorAccId
     * @return boolean
     */
    public function doctor_account_id($doctorAccId)
    {
        if (empty($doctorAccId)) {
            $this->form_validation->set_message("doctor_account_id", lang("select"));
            return false;
        } elseif (empty($this->Doctortbl->getDoctorByAccountId($doctorAccId))) {
            $this->form_validation->set_message("doctor_account_id", lang("valid_value"));
            return false;
        }
        return true;
    }

    /**
     * Check request file id is valid and not empty
     *
     * @param string $fileId
     * @return boolean
     */
    public function request_file_id($fileId)
    {
        if (empty($fileId)) {
            $this->form_validation->set_message("request_file_id", lang("select"));
            return false;
        } elseif (empty($this->Upfiletbl->getUpfileTransplantUploadByDidCheck($this->session->userdata('d_id'), AFF_NW, $fileId))) {
            $this->form_validation->set_message("request_file_id", lang("valid_value"));
            return false;
        }
        return true;
    }
}
