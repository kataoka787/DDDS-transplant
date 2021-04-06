<?php defined('BASEPATH') or exit('No direct script access allowed');

class CsvReport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function downloadCsvbasic()
    {
        $data = $this->input->post("data");
        $data == null && redirect(base_url());
        $data = json_decode($data);
        try {
            $fp = fopen("php://memory", 'w');
            $header = array('登録者ID', 'カナ氏名漢字氏名', '入力対象経過期間', '報告期限日', '入力状況生活状況', '入力状況検査項目', '生年月日', '年齢', '性別', '移植臓器', '同時移植', '移植実施日', '移植回数', '移植施設', '移植後経過情報管理施設', '臓器転帰', '患者転帰');
            if ($this->session->userdata('account')->account_type_mst_id == ACC_TYPE_CO) {
                array_push($header, 'ドナーID');
            }
            fputcsv($fp, $header);
            foreach ($data as $row) {
                $list = array(
                    str_pad($row->RECIPIENT_ID, 7, 0, STR_PAD_LEFT),
                    $row->KANA_NAME . $row->KANJI_NAME,
                    $row->elapsedPeriod,
                    datetimeToString($row->REPORT_DEADLINE),
                    $row->living_conditions,
                    $row->inspection_item,
                    datetimeToString($row->BIRTHDAY),
                    empty($row->BIRTHDAY) ? '' : date_diff(date_create($row->BIRTHDAY), date_create('now'))->y,
                    $row->sex,
                    $row->organ,
                    $row->DOUJI_ISHOKU,
                    datetimeToString($row->ISYOKU_DATE),
                    $row->ISYOKU_CNT,
                    $row->transplant_name,
                    $row->transfer_destination_name,
                    $row->organ_outcome,
                    $row->patient_outcome,
                );
                if ($this->session->userdata('account')->account_type_mst_id == ACC_TYPE_CO) {
                    $donnorId = isset($row->DONOR_ID) ? str_pad($row->DONOR_ID, 7, 0, STR_PAD_LEFT) : '';
                    array_push($list, $donnorId);
                }
                fputcsv($fp, $list);
            }
            fseek($fp, 0);
            force_download('移植後経過情報一覧.csv', stream_get_contents($fp));
        } catch (Exception $e) {
            redirect("errors/csv_can_not_download");
            exit(1);
        }
    }

    public function downloadCsvAll()
    {
        try {
            $tIshokugoKeikaColumnName = $this->Tishokugokeika->getColumnName();
            $tDonorColumnName = $this->Tdonor->getColumnName();
            $tRejectionColumnName = $this->Trejection->getColumnName();
            $tGappeiColumnName = $this->Tgappei->getColumnName();
            $tKensaColumnName = $this->Tkensa->getColumnName();
            $tLivingColumnName = $this->Tliving->getColumnName();
            $csvDetailOrgan = $this->input->post("csvDetailOrgan");
            $csvDetailSimultaneousTransplantation = $this->input->post("csvDetailSimultaneousTransplantation");

            $csvDetailOrgan = json_decode($csvDetailOrgan);
            $csvDetailSimultaneousTransplantation = json_decode($csvDetailSimultaneousTransplantation);
            if ($csvDetailOrgan == null && $csvDetailSimultaneousTransplantation == null) {
                redirect(base_url());
            }
            if (!empty($csvDetailOrgan)) {
                $data = $this->Tishokugokeika->getTIshokugoKeikaDownloadCSVZoki($csvDetailOrgan[0]);
                $name = $csvDetailOrgan;
                switch ($name[0]) {
                    case 1:
                        $name = '心臓';
                        break;
                    case 2:
                        $name = '肺';
                        break;
                    case 3:
                        $name = '肝臓';
                        break;
                    case 4:
                        $name = '腎臓';
                        break;
                    case 5:
                        $name = '膵臓';
                        break;
                    case 6:
                        $name = '小腸';
                        break;
                }
            }
            $fp = fopen("php://memory", 'w');

            if (!empty($data)) {
                // Tishokugokeika
                $this->setHeader($tIshokugoKeikaColumnName, $fp);
                foreach ($data as $row) {
                    fputcsv($fp, (array) $row);
                }
                // Tdonor
                $this->setHeader($tDonorColumnName, $fp);
                $donnorExport = array();
                foreach ($data as $row) {
                    $dataDonnor[] = $this->Tdonor->getAllByPrimaryKeys($row->DONOR_ID);
                }
                foreach ($dataDonnor as $key) {
                    foreach ($key as $row) {
                        if (!in_array($row, $donnorExport)) {
                            array_push($donnorExport, $row);
                        }
                    }
                }
                $this->setData($donnorExport, $fp);
                // Trejection
                $this->setHeader($tRejectionColumnName, $fp);
                foreach ($data as $row) {
                    $dataInDatabase = $this->Trejection->getTRejectionDownloadCSV($row->RECIPIENT_ID, $row->ZOKI_CODE, $row->ISYOKU_CNT);
                    $this->setData($dataInDatabase, $fp);
                }
                // Tgappei
                $this->setHeader($tGappeiColumnName, $fp);
                foreach ($data as $row) {
                    $dataInDatabase = $this->Tgappei->getTGappeiDownloadCSV($row->RECIPIENT_ID, $row->ZOKI_CODE, $row->ISYOKU_CNT);
                    $this->setData($dataInDatabase, $fp);
                }
                // Tkensa
                $this->setHeader($tKensaColumnName, $fp);
                foreach ($data as $row) {
                    $dataInDatabase = $this->Tkensa->getTKensaDownloadCSV($row->RECIPIENT_ID, $row->ZOKI_CODE, $row->ISYOKU_CNT);
                    $this->setData($dataInDatabase, $fp);
                }
                // Tliving
                $this->setHeader($tLivingColumnName, $fp);
                foreach ($data as $row) {
                    $dataInDatabase = $this->Tliving->getTLivingDownloadCSV($row->RECIPIENT_ID, $row->ZOKI_CODE, $row->ISYOKU_CNT);
                    $this->setData($dataInDatabase, $fp);
                }
            } else {
                $this->setHeader($tIshokugoKeikaColumnName, $fp);
                $this->setHeader($tDonorColumnName, $fp);
                $this->setHeader($tRejectionColumnName, $fp);
                $this->setHeader($tGappeiColumnName, $fp);
                $this->setHeader($tKensaColumnName, $fp);
                $this->setHeader($tLivingColumnName, $fp);
            }
            fseek($fp, 0);
            force_download($name . '移植後経過情報.csv', stream_get_contents($fp));

        } catch (Exception $e) {
            redirect("errors/csv_can_not_download");
            exit(1);
        }
    }

    /**
     * get header for fp
     *
     * @param array $tableName
     * @return void
     */
    private function setHeader($tableName, &$fp)
    {
        $header = array();
        // header
        foreach ($tableName as $columnName) {
            array_push($header, $columnName);
        }
        fputcsv($fp, $header);
    }

    /**
     * set data for fp
     *
     * @param array $data
     * @param array $fp
     * @return void
     */
    private function setData($data, &$fp)
    {
        if (!empty($data)) {
            foreach ($data as $record) {
                fputcsv($fp, (array) $record);
            }
        }
    }
}
