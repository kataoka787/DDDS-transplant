<?php defined("BASEPATH") or exit("No direct script access allowed");

class CsvLoad extends CI_Controller
{
    private $blockMst = array();
    private $prefMst = array();
    private $institutionMst = array();
    private $mcdMst = array();
    private $results = array();

    public function __construct()
    {
        parent::__construct();
        $this->lang->load(array("db_lang", "form_validation_lang"));
        $this->data = array(
            "js" => array("bootstrap.min.js", "bootstrap.bundle.min.js"),
            "page_title" => "移植者情報CSV取込",
            "css" => array(
                array("css" => "tp_style.css"),
                array("css" => "bootstrap.min.css"),
                array("css" => "font-awesome.min.css"),
            ),
        );
        foreach ($this->Blockmst->getBlockmst() as $row) {
            $this->blockMst[$row->block_name] = $row->id;
        }
        foreach ($this->Prefmst->getPrefMst() as $row) {
            $this->prefMst[$row->pref_name] = $row->id;
        }
        foreach ($this->Institutionmst->getInstitutionMst() as $row) {
            $this->institutionMst[$row->institution_kubun][$row->institution_name] = $row->SISETU_CD;
        }
        foreach ($this->Mcd->getMcd() as $row) {
            $this->mcdMst[$row->CODE_TYPE][$row->VALUE] = $row->CODE;
        }
    }

    public function index()
    {
        $this->load->vars($this->data);
        $this->load->view("header");
        $this->load->view("csv_load");
        $this->load->view("footer");
    }

    public function load()
    {
        $this->input->method() == "get" && redirect("csvLoad");

        if (!file_exists($_FILES["upfile"]["tmp_name"]) || !is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
            $this->data["errorReadFile"] = true;
            $this->load->vars($this->data);
            $this->load->view("header");
            $this->load->view("csv_load");
            $this->load->view("footer");
        } else {
            $path = $_FILES["upfile"]["name"];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ("csv" !== $ext) {
                $this->data["errorReadFile"] = true;
                $this->load->vars($this->data);
                $this->load->view("header");
                $this->load->view("csv_load");
                $this->load->view("footer");
            } else {
                include APPPATH . "/config/csv_constants.php";
                include APPPATH . "/config/csv_convert_code.php";

                /* Read csv file */
                $csv = $_FILES["upfile"]["tmp_name"];
                $handle = fopen($csv, "r");

                /* Skip first row */
                fgetcsv($handle, ",");
                /* Get organ */
                $organName = fgetcsv($handle, ",")[1] ?? null;
                $organ = $this->Mcd->getCodeByCodeTypeValue(CODE_TYPE["ORGAN"], $organName);
                if (empty($organ)) {
                    $this->data["errorFormat"] = true;
                    $this->load->vars($this->data);
                    $this->load->view("header");
                    $this->load->view("csv_load");
                    $this->load->view("footer");
                    return;
                }
                $zokiCode = $organ->CODE;

                $groupNames = array();
                $csvIndex = array();
                /* Skip file header rows */
                $isFileHeader = true;
                while ($isFileHeader === true && ($csvData = fgetcsv($handle, ",")) !== false) {
                    if ($csvData[0] === "基本情報" && $csvData["1"] === "基本情報") {
                        $groupNames = $csvData;
                    } elseif ($csvData[0] === "登録者ID" & $csvData[1] === "居住地ブロック") {
                        foreach ($csvData as $index => $value) {
                            $csvIndex[$groupNames[$index]][$value] = $index + 1;
                        }
                        $isFileHeader = false;
                    }
                }

                $csvStructure = array();
                foreach (CSV_STRUCTURE as $row) {
                    $column = $csvIndex[$row["groupNameCSV"]][$row["itemNameCSV"]] ?? null;
                    empty($column) || array_push($csvStructure, array(
                        "column" => $column,
                        "tableName" => $row["tableName"],
                        "columnName" => $row["columnName"],
                        "groupNameCSV" => $row["groupNameCSV"],
                        "itemNameCSV" => $row["itemNameCSV"],
                    ));
                }

                /* Read file body (data) */
                while (($csvData = fgetcsv($handle, ",")) !== false) {
                    /* Re-index data (array will start from 1 instead of 0) */
                    array_unshift($csvData, null);
                    unset($csvData[0]);

                    $recipientId = $csvData[1];
                    $isoyokuCNT = $csvData[33];

                    /* Convert data */
                    $convertedData = $this->onConvert($zokiCode, $csvData, $recipientId, $csvStructure);

                    /* If conversion error */
                    if (empty($convertedData)) {
                        continue;
                    }

                    /* Insert data */
                    $this->insert($recipientId, $zokiCode, $isoyokuCNT, $convertedData);
                }
                fclose($handle);

                $this->data["results"] = $this->results;
                $this->data["errorFormat"] = empty($this->results);
                $this->load->vars($this->data);
                $this->load->view("header");
                $this->load->view("csv_load");
                $this->load->view("footer");

            }
        }
    }

    /**
     * Read each column and convert
     *
     * @param string $zokiCode
     * @param array $dataMCD
     * @param array $csvData
     * @param array $dataPrefMST
     * @param array $result
     * @param string $recipientId
     * @return void
     */
    private function onConvert($zokiCode, $csvData, $recipientId, $csvStructure)
    {
        /* Convert 同時移植 */
        $this->onDoijiIshokuConvert($zokiCode, $csvData);

        $convertedData = array();
        $recordIndex = array(
            T_DONOR => 0,
            T_ISHOKUGO_KEIKA => 0,
            T_LIVING => 0,
            T_KENSA => 0,
            T_REJECTION => 0,
            T_GAPPEI => 0,
        );

        if ($zokiCode == ORGAN_LIVER) {
            /* 肝臓原疾患_1が空の場合に設定する */
            if ($csvData[707] == "") {
                $csvData[707] = $csvData[171];
                $csvData[708] = $csvData[172];
            }
        }

        foreach ($csvStructure as $item) {
            $tableName = $item["tableName"];
            $columnName = $item["columnName"];
            $groupNameCSV = $item["groupNameCSV"];
            $itemNameCSV = $item["itemNameCSV"];

            if (!$this->onMcdConvert($item, $csvData, $recipientId, CSV_CONVERT_CODE[$groupNameCSV][$itemNameCSV][$zokiCode] ?? null)) {
                return false;
            }

            if ($tableName != T_ISHOKUGO_KEIKA && $tableName != T_DONOR && isset($convertedData[$tableName][$recordIndex[$tableName]][$columnName])) {
                $recordIndex[$tableName]++;
            }
            switch ($tableName) {
                case T_KENSA:
                    isset($convertedData[$tableName][$recordIndex[$tableName]]["KENSA_NAME"]) || $convertedData[$tableName][$recordIndex[$tableName]]["KENSA_NAME"] = explode("_", $item["itemNameCSV"])[0];
                    break;
                case T_REJECTION:
                    isset($convertedData[$tableName][$recordIndex[$tableName]]["REJECTION_TYPE"]) || $convertedData[$tableName][$recordIndex[$tableName]]["REJECTION_TYPE"] = CSV_REJECTION_TYPE[$item["groupNameCSV"]];
                    break;
                case T_ISHOKUGO_KEIKA:
                case T_DONOR:
                    if (!empty(TABLE_STRUCTURE[$tableName][$columnName]["isRequired"]) && empty($csvData[$item["column"]])) {
                        $this->setResults($recipientId, "error", str_replace("{field}", $item["groupNameCSV"] . "、" . $item["itemNameCSV"], lang("required")));
                        return false;
                    }
                    break;
            }
            switch (TABLE_STRUCTURE[$tableName][$columnName]["type"] ?? null) {
                case "date":
                    $csvData[$item["column"]] = datetimeToString($csvData[$item["column"]], DATE_TIME_DEFAULT_WITHOUT_DELEMETER);
                    break;
                case "datetime":
                    $csvData[$item["column"]] = datetimeToString($csvData[$item["column"]], DATE_TIME_LONG_WITHOUT_DELEMETER);
                    break;
            }
            $convertedData[$tableName][$recordIndex[$tableName]][$columnName] = $csvData[$item["column"]];
        }

        /* ドナー発生地 DONOR_TODOFUKEN */
        $donorPrefColumn = CSV_COLUMN_PREF_MST[$zokiCode];
        $csvData[$donorPrefColumn] = $this->prefMst[$csvData[$donorPrefColumn]] ?? null;
        if (empty($csvData[$donorPrefColumn])) {
            $this->setResults($recipientId, "error", lang("mcd_not_existed_value") . "ドナー、ドナー発生地");
            return false;
        }

        /* IJYUUCHI_BLOCK */
        $convertedData[T_ISHOKUGO_KEIKA][0]["IJYUUCHI_BLOCK"] = $this->blockMst[$convertedData[T_ISHOKUGO_KEIKA][0]["IJYUUCHI_BLOCK"]] ?? null;
        if (empty($convertedData[T_ISHOKUGO_KEIKA][0]["IJYUUCHI_BLOCK"])) {
            $this->setResults($recipientId, "error", lang("mcd_not_existed_value") . "基本情報、居住地ブロック");
            return false;
        }

        /* IJYUUCHI_KEN */
        $convertedData[T_ISHOKUGO_KEIKA][0]["IJYUUCHI_KEN"] = $this->prefMst[$convertedData[T_ISHOKUGO_KEIKA][0]["IJYUUCHI_KEN"]] ?? null;
        if (empty($convertedData[T_ISHOKUGO_KEIKA][0]["IJYUUCHI_KEN"])) {
            $this->setResults($recipientId, "error", lang("mcd_not_existed_value") . "基本情報、居住地県");
            return false;
        }

        /* Check if ISYOKU_ISYOKUSISETU_CD is valid */
        if (!in_array($convertedData[T_ISHOKUGO_KEIKA][0]["ISYOKU_ISYOKUSISETU_CD"], $this->institutionMst[INSTITUTION_KUBUN_TRANSPLANT])) {
            $this->setResults($recipientId, "error", lang("mcd_not_existed_value") . "小腸、移植施設コード");
            return false;
        }

        /* Set ISHOKUGO_KEIKAJYOUHOU_SISETU_CD and ISHOKUGO_KEIKAJYOUHOU_SISETU_KBN */
        if (empty($convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_CD"])) {
            $convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_CD"] = $convertedData[T_ISHOKUGO_KEIKA][0]["ISYOKU_ISYOKUSISETU_CD"];
            $convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_KBN"] = INSTITUTION_KUBUN_TRANSPLANT;
        } else {
            $convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_CD"] = $this->institutionMst[INSTITUTION_KUBUN_TRANSFER][$convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_CD"]] ?? null;
            /* Check if ISHOKUGO_KEIKAJYOUHOU_SISETU_CD is valid */
            if (empty($convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_CD"])) {
                $this->setResults($recipientId, "error", lang("mcd_not_existed_value") . "基本情報、フォローアップ施設");
                return false;
            } else {
                $convertedData[T_ISHOKUGO_KEIKA][0]["ISHOKUGO_KEIKAJYOUHOU_SISETU_KBN"] = INSTITUTION_KUBUN_TRANSFER;
            }
        }

        return $convertedData;
    }

    /**
     * Convert value to code (use Mcd table)
     *
     * @param array $item
     * @param array $data
     * @param string $recipientId
     * @return void
     */
    private function onMcdConvert($item, &$data, $recipientId, $convertCode = null)
    {
        if (empty($convertCode)) {
            return true;
        }

        $mcdValue = $data[$item["column"]];
        if ($mcdValue === "") {
            $data[$item["column"]] = "";
            return true;
        }
        $mcdCode = $this->mcdMst[$convertCode][$mcdValue] ?? null;
        if (isset($mcdCode)) {
            $data[$item["column"]] = $mcdCode;
            return true;
        }
        /* Mcd value not existed */
        $this->setResults($recipientId, "error", lang("mcd_not_existed_value") . $item["groupNameCSV"] . "、" . $item["itemNameCSV"]);
        return false;
    }

    /**
     * Convert DOUJI_ISHOKU
     *
     * @param string $zokiCode
     * @param array $data
     * @return void
     */
    private function onDoijiIshokuConvert($zokiCode, &$data)
    {
        $doijiIshoku = "";
        $organPosition = array(
            ORGAN_HEART => 5,
            ORGAN_LUNG => 6,
            ORGAN_LIVER => 7,
            ORGAN_KIDNEY => 8,
            ORGAN_PANCREAS => 9,
            ORGAN_SMALL_INTENSTINE => 10,
        );
        unset($organPosition[$zokiCode]);
        foreach ($organPosition as $index => $value) {
            if ($data[$value] != "") {
                $doijiIshoku .= ORGAN[$index] . "、";
            }
        }
        $data[5] = trim($doijiIshoku, "、");
    }

    /**
     * Insert data to database
     *
     * @param string $recipientId
     * @param string $zokiCode
     * @param string $isyokuCnt
     * @param array $data
     * @return void
     */
    private function insert($recipientId, $zokiCode, $isyokuCnt, $data)
    {
        $defaultData = array(
            "DSPNO" => "",
            "KENSA_UNIT" => "",
            "RECIPIENT_ID" => $recipientId,
            "ZOKI_CODE" => $zokiCode,
            "ISYOKU_CNT" => $isyokuCnt,
            "INS_USER_ID" => $this->session->userdata("account")->accountId,
            "INS_PROGRAM_ID" => PROGRAM_ID[6],
            "UPD_USER_ID" => $this->session->userdata("account")->accountId,
            "UPD_PROGRAM_ID" => PROGRAM_ID[6],
            "DEL_FLG" => IN_USE_FLG,
        );
        $this->db->trans_begin();
        $isDataValid = true;
        foreach (CSV_TABLE_MODEL as $table => $model) {
            foreach ($data[$table] as $record) {
                switch ($table) {
                    case T_GAPPEI:
                        if (empty($record["GAPPEI"]) || empty($record["NYUIN_DATE"])) {
                            continue 2;
                        }
                        break;
                    case T_KENSA:
                        if (empty($record["KENSA_NAME"])) {
                            continue 2;
                        }
                        break;
                    case T_LIVING:
                        if (empty($record["INPUT_DATE"])) {
                            continue 2;
                        }
                        break;
                    case T_REJECTION:
                        if (empty($record["REJECTION_TYPE"]) || empty($record["SINDAN_DATE"])) {
                            continue 2;
                        }
                        break;

                }
                if (!$this->isDataDuplicated($recipientId, $zokiCode, $isyokuCnt, $table, $record)) {
                    $this->$model->insert(array_merge($record, $defaultData));
                    continue;
                }
                $isDataValid = false;
                break 2;
            }
        }

        if ($isDataValid && $this->db->trans_status()) {
            $this->db->trans_commit();
            $this->setResults($recipientId, "success", lang("db_success_csv_import"));
        } else {
            if ($isDataValid && $this->db->trans_status() === false) {
                $this->setResults($recipientId, "error", lang("db_error_csv_import"));
            }
            $this->db->trans_rollback();
        }

    }

    /**
     * Check if data is duplicated
     *
     * @param string $recipientId
     * @param string $zokiCode
     * @param string $isyokuCnt
     * @param string $table
     * @param array $data
     * @return boolean
     */
    private function isDataDuplicated($recipientId, $zokiCode, $isyokuCnt, $table, $data = null)
    {
        switch ($table) {
            case T_DONOR:
                if (!empty($this->Tdonor->getOneByPrimaryKeys($data["DONOR_ID"]))) {
                    $this->setResults($recipientId, "error", lang("db_error_csv_import"));
                    return true;
                }
                break;
            case T_ISHOKUGO_KEIKA:
                if (!empty($this->Tishokugokeika->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, IN_USE_FLG))) {
                    $this->setResults($recipientId, "warning", lang("recipient_id_existed"));
                    return true;
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                if (!empty($this->Tishokugokeika->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, DELETED_FLG))) {
                    $this->Tgappei->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
                    $this->Tliving->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
                    $this->Trejection->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
                    $this->Tkensa->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
                    $this->Tishokugokeika->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt);
                }
                break;
            case T_GAPPEI:
                if (!empty($this->Tgappei->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["GAPPEI"], $data["NYUIN_DATE"], IN_USE_FLG))) {
                    $this->setResults($recipientId, "error", lang("db_error_csv_import"));
                    return true;
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                $this->Tgappei->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["GAPPEI"], $data["NYUIN_DATE"], DELETED_FLG);
                break;
            case T_LIVING:
                if (!empty($this->Tliving->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["INPUT_DATE"], IN_USE_FLG))) {
                    $this->setResults($recipientId, "error", lang("db_error_csv_import"));
                    return true;
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                $this->Tliving->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["INPUT_DATE"], DELETED_FLG);
                break;
            case T_REJECTION:
                if (!empty($this->Trejection->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["REJECTION_TYPE"], $data["SINDAN_DATE"], IN_USE_FLG))) {
                    $this->setResults($recipientId, "error", lang("db_error_csv_import"));
                    return true;
                }
                /* Hard delete if soft deleted record (DEL_FLAG = 1) existed */
                $this->Trejection->deleteByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["REJECTION_TYPE"], $data["SINDAN_DATE"], DELETED_FLG);
                break;
            case T_KENSA:
                if (!empty($this->Tkensa->getOneByPrimaryKeys($recipientId, $zokiCode, $isyokuCnt, $data["KENSA_NAME"]))) {
                    $this->setResults($recipientId, "error", lang("db_error_csv_import"));
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Set result
     *
     * @param string $recipientId
     * @param string $status
     * @param string $message
     * @return void
     */
    private function setResults($recipientId, $status, $message)
    {
        array_push($this->results, array(
            "recipientId" => $recipientId,
            "status" => $status,
            "message" => $message,
        ));
    }
}
