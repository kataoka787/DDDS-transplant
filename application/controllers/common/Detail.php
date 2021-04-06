<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Detail extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            'page_title' => '移植後経過情報詳細',
            'js' => array('bootstrap.min.js', 'bootstrap.bundle.min.js'),
            'css' => array(
                array('css' => 'tp_style.css'),
                array('css' => 'bootstrap.min.css'),
                array('css' => 'font-awesome.min.css'),
            ),
            'userType' => $this->session->userdata('account')->account_type_mst_id,
        );
    }

    public function index()
    {
        $postData = $this->input->post();
        /* Check if request is valid */
        if (empty($postData['recipientId']) || empty($postData['zokiCode']) || empty($postData['isyokuCnt'])) {
            redirect('/search');
        }

        /* Get recipient info */
        $info = $this->Tishokugokeika->getTIshokugoKeikaInfo($postData['recipientId'], $postData['zokiCode'], $postData['isyokuCnt']);
        $recipientId = $info->RECIPIENT_ID;
        $zokiCode = $info->ZOKI_CODE;
        $isyokuCnt = $info->ISYOKU_CNT;

        /* 免疫抑制剤（導入）and 免疫抑制剤（維持）*/
        foreach (config_item("immunosuppressant_drugs") as $type => $drugs) {
            $immunosuppressant[$type] = array();
            foreach ($drugs as $drug => $tableColumnName) {
                $immunosuppressant[$type][$drug] = $info->$tableColumnName;
            }
        }

        /* 移植後経過情報管理施設 */
        $followUpInstitution = array();
        foreach ($this->Institutionmst->getInstitutionMstByKubun(INSTITUTION_KUBUN_TRANSFER) as $institution) {
            $followUpInstitution[$institution->SISETU_CD] = $institution->institution_name;
        }

        /* 患者の転帰 */
        $patientOutcome = $this->Mcd->getByCodeType(CODE_TYPE['PATIENT_OUTCOME']);
        array_splice($patientOutcome, 1, 0, array($patientOutcome[3]));
        unset($patientOutcome[4]);

        /* 死因 */
        $causeOfDeath['major'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CAUSE_OF_DEATH_MAJOR']));
        $causeOfDeath['subclass'] = empty($info->SIIN_H) ? array() : addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CAUSE_OF_DEATH_SUBCLASS'], $info->SIIN_H));

        /* 拒絶反応 select options */
        if ($info->ZOKI_CODE == ORGAN_HEART) {
            /* (心臓) */
            $therapeuticEffect = $this->Mcd->getByCodeType(CODE_TYPE['THERAPEUTIC_EFFECT_HEART']);
        } else {
            /* (肺, 肝臓, 腎臓, 膵臓, 小腸 ) */
            $therapeuticEffect = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['THERAPEUTIC_EFFECT_OTHER']));
        }

        if ($info->ZOKI_CODE == ORGAN_LUNG) {
            /* 急性拒絶反応 */
            $rejection['data'][REJECTION_ACUTE] = $this->Trejection->getAllByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, REJECTION_ACUTE);
            $grade['a'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['GRADE_A']));
            $grade['b'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['GRADE_B']));
            $grade['c'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['GRADE_C']));
            $grade['d'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['GRADE_D']));
            /* 慢性拒絶反応 */
            $rejection['data'][REJECTION_CHRONIC] = $this->Trejection->getAllByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, REJECTION_CHRONIC);
            $stageAb['stage'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CHRONIC_REJECTION_STAGE']));
            $stageAb['ab'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CHRONIC_REJECTION_AB']));
        } else {
            /* 拒絶反応 */
            $rejection['data'] = $this->Trejection->getAllByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, REJECTION_COMMON);
        }

        /* 透析, インスリン治療 select options */
        $dialysisWithdrawal = $this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['DIALYSIS_WITHDRAWAL']);

        /* 入力対象経過期間 */
        $cycle['list'] = $this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CYCLE']);

        /* 検査項目 */
        /* Settings */
        $inspection['items'] = INSPECTIONS_ROWS_DATA[$zokiCode];
        $inspection['tableSetting']['maxColumn'] = $this->Tkensa->getMaxCycle($recipientId, $zokiCode, $isyokuCnt);
        $inspection['tableSetting']['numbersOfTable'] = ceil($inspection['tableSetting']['maxColumn'] / config_item('inspection_max_table_column'));
        /* Set data */
        $inspection['data'] = $this->Tkensa->getAllByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
        foreach ($inspection['data'] as $item) {
            $items[$item->KENSA_NAME] = $item;
            $kensaUpdateTime[$item->KENSA_NAME] = $item->UPD_DATE;
        }
        $inspection['data'] = $items ?? array();
        $inspection["cycle"] = array();
        foreach ($cycle['list'] as $code => $value) {
            array_push($inspection['cycle'], array('code' => $code, 'value' => $value));
        }

        /* 入院を要する合併症 */
        $complications['data'] = $this->Tgappei->getAllByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
        $complications['type'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['COMPLICATIONS'][$zokiCode]));

        /* 生活状況 */
        $livingConditions['data'] = $this->Tliving->getAllByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
        $livingConditions['reportForm'] = addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['LIVING_CONDITIONS_REPORT_FORM']));

        /* ブロック (移植施設検索 institution search modal) */
        $block = array('' => '選択してください');
        foreach ($this->Blockmst->getBlockmst() as $value) {
            $block[$value->id] = $value->block_name;
        }

        $diff = date_diff(date_create($info->ISYOKU_DATE), date_create('now')->sub(new DateInterval('P1D')));
        $months = $diff->y * 12 + $diff->m;
        $currentCycle = 'M1';
        if ($months >= 3 && $months < 6) {
            $currentCycle = 'M3';
        } else if ($months >= 6 && $months < 12) {
            $currentCycle = 'M6';
        } else if ($months >= 12) {
            $diffYear = min(config_item('max_cycle_year'), $diff->y);
            $currentCycle = str_pad($diffYear, 2, '0', STR_PAD_LEFT);
        }

        /* Inject variables to view */
        $this->data = array_merge($this->data, array(
            'info' => $info,
            'postTransplant' => $followUpInstitution,
            'organOutcome' => $this->Mcd->getByCodeType(CODE_TYPE['ORGAN_OUTCOME']),
            'causeOfAbolition' => addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['CAUSE_OF_ABOLITION'])),
            'patientOutcome' => $patientOutcome,
            'patientOutcomeDetails' => $this->Mcd->getByCodeType(CODE_TYPE['PATIENT_OUTCOME_DETAILS']),
            'causeOfDeath' => $causeOfDeath,
            'immunosuppressant' => $immunosuppressant,
            'rejection' => $rejection,
            'treatmentMethod' => addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['TREATMENT_METHOD'])),
            'therapeuticEffect' => $therapeuticEffect,
            'grade' => $grade ?? null,
            'stageAb' => $stageAb ?? null,
            'dialysisWithdrawal' => $dialysisWithdrawal,
            'insulinTreatment' => $dialysisWithdrawal,
            'causesOfDialysisFailure' => $this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['DIALYSIS_CAUSES_OF_DIALYSIS_FAILURE']),
            'inspection' => $inspection,
            'complications' => $complications,
            'rehabilitationStatus' => addEmptySelect($this->Mcd->getCodeValueArrayByCodeType(CODE_TYPE['REHABILITATION'][$zokiCode])),
            'livingConditions' => $livingConditions,
            'cycle' => $cycle,
            'block' => $block,
            'currentCycle' => $currentCycle,
            'diffYear' => $diff->y,
            'updateToken' => $this->getUpdateToken($recipientId, $zokiCode, $isyokuCnt),
        ));
        $this->load->vars($this->data);

        $detailInsert = array(
            'RECIPIENT_ID' => $recipientId,
            'ZOKI_CODE' => $zokiCode,
            'ISYOKU_CNT' => $isyokuCnt,
            'ZOKI_TENKI' => $info->ZOKI_TENKI,
            'RECIPIENT_TENKI' => $info->RECIPIENT_TENKI,
        );

        $this->session->set_userdata('detailInsert', $detailInsert);
        $this->load->view('header');
        $this->load->view('detail/script');
        $this->load->view('detail/basic');
        $this->load->view('detail/rejection');
        $this->load->view('detail/kensa');
        $this->load->view('detail/living');
        $this->load->view('transplantSearch');
        $this->load->view('footer');
    }

    /**
     * Download file 移植後経過報告書送付先施設変更届.docx
     *
     * @return void
     */
    public function downloadApplicationForm()
    {
        force_download(APPPATH . "download/移植後経過報告書送付先施設変更届.docx", null);
    }

    /**
     * Save data
     *
     * @return void
     */
    public function save()
    {
        $postData = $this->input->post();
        $validationErrors = array();
        $storedData = $this->session->userdata('detailInsert');
        $recipientId = $storedData['RECIPIENT_ID'];
        $zokiCode = $storedData['ZOKI_CODE'];
        $isyokuCnt = $storedData['ISYOKU_CNT'];

        /* Check if data is outdated */
        if ($postData['updateToken'] != $this->getUpdateToken($recipientId, $zokiCode, $isyokuCnt)) {
            array_push($validationErrors, lang('outdated_data'));
        }
        /* Check if recipient and organ status is conflict */
        if (isset($postData['patientOutcome']) && $postData['patientOutcome'] == PATIENT_OUTCOME_CODE['DEATH']) {
            if (isset($postData['organOutcome']) && $postData['organOutcome'] == ORGAN_OUTCOME_ENGRAFTMENT_CODE) {
                array_push($validationErrors, lang('recipient_organ_status_conflict'));
            } else {
                $allRecipientWithDifferentOrganStatus = $this->Tishokugokeika->getAllRecipientWithDifferentOrganStatus($recipientId, $zokiCode, PATIENT_OUTCOME_CODE['DEATH']);
                if (!empty($allRecipientWithDifferentOrganStatus)) {
                    $organs = array();
                    foreach ($allRecipientWithDifferentOrganStatus as $recipient) {
                        array_push($organs, ORGAN[$recipient->ZOKI_CODE]);
                    }
                    $this->onSetErrorMessage($validationErrors, implode("、", $organs) . lang('recipient_status_conflict'));
                }
            }
        }

        /* Living condition cycle must be unique */
        if (isset($postData['livingConditions'])) {
            $this->checkUniqueCycle($postData['livingConditions']) || $this->onSetErrorMessage($validationErrors, lang("cycle_duplicated"));
        }

        /* Can not change recipient, organ status in specific condition */
        if ($this->session->userdata("account")->account_type_mst_id == ACC_TYPE_TP) {
            /* 生存 */
            $storedData["RECIPIENT_TENKI"] != 1 && $postData['patientOutcome'] = $storedData["RECIPIENT_TENKI"];
            /* 生着 */
            $storedData["ZOKI_TENKI"] != 1 && $postData['organOutcome'] = $storedData["ZOKI_TENKI"];
        }

        /* Update database if no validation error */
        if (empty($validationErrors)) {
            $this->db->trans_begin();
            foreach (config_item("should_update_tables") as $tableName => $dataIndex) {
                $shouldConvertData = empty($dataIndex) ? $postData : ($postData[$dataIndex] ?? null);
                $shouldConvertData && $this->onValidateAndUpdateData($recipientId, $zokiCode, $isyokuCnt, $tableName, $this->onConvert($tableName, $shouldConvertData), $validationErrors);
            }
            /* Update database if no error, rollback if any */
            $this->db->trans_status() && empty($validationErrors) ? $this->db->trans_commit() : $this->db->trans_rollback();
        }

        /* Return status to client */
        echo json_encode(array(
            "status" => empty($validationErrors) ? 200 : 400,
            "message" => $validationErrors,
        ));
        return;
    }

    /**
     * Convert posted data to corresponding columns
     *
     * @param string $tableName
     * @param array $data
     * @return array $convertedData
     */
    public function onConvert($tableName, $data)
    {
        $storedData = $this->session->userdata("detailInsert");
        $recipientId = $storedData["RECIPIENT_ID"];
        $zokiCode = $storedData["ZOKI_CODE"];
        $isyokuCnt = $storedData["ISYOKU_CNT"];

        $defaultData = array(
            "RECIPIENT_ID" => $recipientId,
            "ZOKI_CODE" => $zokiCode,
            "ISYOKU_CNT" => $isyokuCnt,
            "INS_USER_ID" => $this->session->userdata("account")->accountId,
            "INS_PROGRAM_ID" => PROGRAM_ID[13],
            "UPD_USER_ID" => $this->session->userdata("account")->accountId,
            "UPD_PROGRAM_ID" => PROGRAM_ID[13],
        );
        $convertedData = array();
        $inputNameConvertDefinitions = config_item("$tableName" . "_input_name_convert_def");
        if ($tableName == "Trejection" && $zokiCode == ORGAN_LUNG) {
            $inputNameConvertDefinitions += config_item("Trejection_lung_input_name_convert_def");
            /* Flatten array */
            $tmpData = $data;
            $data = array();
            foreach ($tmpData as $row) {
                foreach ($row as $child) {
                    array_push($data, $child);
                }
            }
        }
        if ($tableName != "Tishokugokeika") {
            foreach ($data as $row) {
                $rowData = array();
                foreach ($inputNameConvertDefinitions as $column => $inputName) {
                    if (is_array($inputName)) {
                        if (array_key_exists($inputName[1], $row)) {
                            $rowData[$column] = call_user_func(array($this, $inputName[0]), $row[$inputName[1]]);
                        }
                        continue;
                    }
                    array_key_exists($inputName, $row) && $rowData[$column] = $row[$inputName];
                }
                array_push($convertedData, array_merge($rowData, $defaultData));
            }
        } else {
            foreach ($inputNameConvertDefinitions as $column => $inputName) {
                if (is_array($inputName)) {
                    if (array_key_exists($inputName[1], $data)) {
                        $convertFunction = $inputName[0];
                        $convertedData[$column] = $this->$convertFunction($data[$inputName[1]]);
                    }
                    continue;
                }
                array_key_exists($inputName, $data) && $convertedData[$column] = $data[$inputName];
            }
            $convertedData["SIIN_H"] = $data["causeOfDeath"]["major"] ?? null;
            $convertedData["SIIN_L"] = $data["causeOfDeath"]["subclass"] ?? null;
            empty($convertedData["ISHOKUGO_KEIKAJYOUHOU_SISETU_CD"]) || $convertedData["ISHOKUGO_KEIKAJYOUHOU_SISETU_KBN"] = INSTITUTION_KUBUN_TRANSFER;
            $this->onConvertImmunosuppressantDrugs($data, $convertedData);
            $convertedData = array(array_merge($convertedData, $defaultData));
        }
        /* Push deleted row to first */
        if ($tableName != "Tishokugokeika" && $tableName != "Tkensa") {
            usort($convertedData, function ($beforeRow, $afterRow) {
                if (empty($beforeRow["SHOULD_DELETE"]) == empty($afterRow["SHOULD_DELETE"])) {
                    return 0;
                }
                return empty($beforeRow["SHOULD_DELETE"]) ? 1 : -1;
            });
        }

        return $convertedData;
    }

    /**
     * Convert immunosuppressant drugs input to corresponding columns
     *
     * @param array $data
     * @param array $shouldConvertData
     * @return void
     */
    private function onConvertImmunosuppressantDrugs($data, &$shouldConvertData)
    {
        $drugsList = config_item("immunosuppressant_drugs");
        /* 免疫抑制剤 （導入）*/
        $introductionDrugsList = $data["immunosuppressant"]["introduction"] ?? array();
        foreach ($drugsList["introduction"] as $key => $value) {
            $shouldConvertData[$value] = in_array($key, $introductionDrugsList) ? 1 : null;
        }
        $shouldConvertData["DONYU_ETC"] = $data["immunosuppressantOtherIntro"] ?? null;

        /* 免疫抑制剤 （維持）*/
        $maintenanceDrugsList = $data["immunosuppressant"]["maintenance"] ?? array();
        foreach ($drugsList["maintenance"] as $key => $value) {
            $shouldConvertData[$value] = in_array($key, $maintenanceDrugsList) ? 1 : null;
        }
        $shouldConvertData["IJI_ETC"] = $data["immunosuppressantOtherMaintenance"] ?? null;
    }

    /**
     * Validate and update data
     *
     * @param string $recipientId
     * @param string $zokiCode
     * @param string $isyokuCnt
     * @param string $table
     * @param array $data
     * @param array $validationErrors
     * @return void
     */
    private function onValidateAndUpdateData($recipientId, $zokiCode, $isyokuCnt, $tableName, $data, &$validationErrors)
    {
        foreach ($data as $row) {
            $this->form_validation->reset_validation();
            $this->form_validation->set_data($row);
            $isDataValid = $this->form_validation->run("detail/$tableName");
            if ($isDataValid) {
                empty($row["SHOULD_DELETE"]) || $this->onDelete($recipientId, $zokiCode, $isyokuCnt, $tableName, $row);
                if ($this->shouldUpdate($recipientId, $zokiCode, $isyokuCnt, $tableName, $row)) {
                    if ($this->isPrimaryKeysChanged($tableName, $row)) {
                        [$isDataValid, $errorMessage] = $this->checkPrimarykeysDuplicated($recipientId, $zokiCode, $isyokuCnt, $tableName, $row);
                    }
                    if ($isDataValid) {
                        $this->$tableName->update($row);
                        continue;
                    }
                } else {
                    [$isDataValid, $errorMessage] = $this->checkPrimarykeysDuplicated($recipientId, $zokiCode, $isyokuCnt, $tableName, $row);
                    if ($isDataValid) {
                        $this->$tableName->insert($row);
                        continue;
                    }
                }

            }
            /* Set error message */
            $this->onSetErrorMessage($validationErrors, $errorMessage ?? validation_errors(), $tableName, $row["ORDINAL"] ?? $row["KENSA_NAME"] ?? null);
        }
    }

    /**
     * Delete data
     *
     * @param string $recipientId
     * @param string $zokiCode
     * @param string $isyokuCnt
     * @param string $table
     * @param array $data
     * @return void
     */
    private function onDelete($recipientId, $zokiCode, $isyokuCnt, $table, $data)
    {
        switch ($table) {
            case "Tgappei":
                $this->Tgappei->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["GAPPEI"], $data["NYUIN_DATE"]);
                break;
            case "Tliving":
                $this->Tliving->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["INPUT_DATE"]);
                break;
            case "Trejection":
                $this->Trejection->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, null, $data["SINDAN_DATE"]);
                break;
        }
    }

    /**
     * Check if data should be updated or not
     *
     * @param string $recipientId
     * @param string $zokiCode
     * @param string $isyokuCnt
     * @param string $tableName
     * @param array $data
     * @return boolean
     */
    private function shouldUpdate($recipientId, $zokiCode, $isyokuCnt, $tableName, $data)
    {
        switch ($tableName) {
            case "Tkensa":
                return !empty($this->Tkensa->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["KENSA_NAME"]));
            case "Tishokugokeika":
                return true;
            default:
                return boolval($data["SHOULD_UPDATE"]);
        }
    }

    /**
     * Check if pirmary keys of data is changed or not
     *
     * @param string  $table
     * @param array $data
     * @return boolean
     */
    private function isPrimaryKeysChanged($table, $data)
    {
        switch ($table) {
            case 'Tgappei':
                return $data["GAPPEI"] != $data["ORIGINAL_GAPPEI"] || $data["NYUIN_DATE"] != $data["ORIGINAL_NYUIN_DATE"];
            case 'Tliving':
                return $data["INPUT_DATE"] != $data["ORIGINAL_INPUT_DATE"];
            case 'Trejection':
                return $data["SINDAN_DATE"] != $data["ORIGINAL_SINDAN_DATE"];
        }
        return false;
    }

    /**
     * Check if cycle in posted living conditions is duplicated or not
     *
     * @param array $data
     * @param array $validationErrors
     * @return void
     */
    private function checkUniqueCycle($data)
    {
        $cycles = array();
        foreach ($data as $row) {
            if (empty($row['cycle']) || !empty($row['isDeleted'])) {
                continue;
            }
            if (in_array($row["cycle"], $cycles)) {
                return false;
            }
            array_push($cycles, $row['cycle']);
        }
        return true;
    }

    /**
     * Check if primary keys is duplicated or not
     *
     * @param string $table
     * @param array $data
     * @return array $checkedResultWithErrorMessage
     */
    private function checkPrimarykeysDuplicated($recipientId, $zokiCode, $isyokuCnt, $table, $data)
    {
        switch ($table) {
            case 'Tgappei':
                if (!empty($this->Tgappei->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["GAPPEI"], $data["NYUIN_DATE"], IN_USE_FLG))) {
                    return array(false, "<p>" . str_replace("{field}", "合併症と入院日", lang("duplicated_primary_keys")) . "</p>");
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                $this->Tgappei->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["GAPPEI"], $data["NYUIN_DATE"], DELETED_FLG);
                break;
            case 'Tliving':
                if (!empty($this->Tliving->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["INPUT_DATE"], IN_USE_FLG))) {
                    return array(false, "<p>" . str_replace("{field}", "記録日", lang("duplicated_primary_keys")) . "</p>");
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                $this->Tliving->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["INPUT_DATE"], DELETED_FLG);
                break;
            case 'Trejection':
                if (!empty($this->Trejection->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["REJECTION_TYPE"], $data["SINDAN_DATE"], IN_USE_FLG))) {
                    return array(false, "<p>" . str_replace("{field}", "診断日", lang("duplicated_primary_keys")) . "</p>");
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                $this->Trejection->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["REJECTION_TYPE"], $data["SINDAN_DATE"], DELETED_FLG);
                break;
        }
        return array(true, null);
    }

    /**
     * Set error message(s)
     *
     * @param array $validationErrors
     * @param string $errorMessages
     * @param string $tableName
     * @param int $rowIndex
     * @return void
     */
    private function onSetErrorMessage(&$validationErrors, $errorMessages = null, $tableName = null, $rowIndex = null)
    {
        if (empty($tableName) || $tableName == "Tishokugokeika") {
            return array_push($validationErrors, $errorMessages);
        }
        if ($tableName == "Trejection") {
            if ($this->session->userdata('detailInsert')["ZOKI_CODE"] == ORGAN_LUNG) {
                $rowAndTableIndex = explode("_", $rowIndex);
                $rowIndex = $rowAndTableIndex[1];
                $japaneseTableName = config_item("japanese_table_name")[$tableName][$rowAndTableIndex[0]];
            } else {
                $japaneseTableName = config_item("japanese_table_name")[$tableName][0];
            }
        } else {
            $japaneseTableName = config_item("japanese_table_name")[$tableName];
        }

        return array_push($validationErrors, str_replace("<p>", "<p>$japaneseTableName" . "の$rowIndex" . "行の", $errorMessages));
    }

    /**
     * Get update data token (for validate outdated data)
     *
     * @param string $recipientId
     * @param string $zokiCode
     * @param string $isyokuCnt
     * @return void
     */
    private function getUpdateToken($recipientId, $zokiCode, $isyokuCnt)
    {
        $token = "";
        foreach (array_keys(config_item("should_update_tables")) as $table) {
            foreach ($this->$table->getUpdateToken($recipientId, $zokiCode, $isyokuCnt) as $item) {
                $token .= $item->UPD_DATE;
            };
        }
        return md5($token);
    }

    /**
     * Convert date string to YYYYmmdd
     *
     * @param string $date
     * @return string $formatedString if susscess
     * @return null if fail
     */
    private function formatDate($date)
    {
        return empty($date) ? null : date(DATE_TIME_DEFAULT_WITHOUT_DELEMETER, strtotime($date));
    }

}
