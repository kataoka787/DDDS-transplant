<?php defined('BASEPATH') or exit('No direct script access allowed');

class DonorDataDownload extends CI_Controller
{

    public $zipPath = "";
    public $downPath = "";
    public $upPath = "";
    public $affId = "";

    public function __construct()
    {
        parent::__construct();

        $this->zipPath = config_item("zip_download_file_tmp_path");
        $this->upPath = config_item("admin_download_upload_path");
        $this->downPath = config_item("admin_download_download_path");
        $this->affId = $this->session->userdata('affiliation_mst_id');
    }

    public function index()
    {
        if (!$this->session->userdata('d_id')) {
            redirect('donor/searchlist');
        } else {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            $d_id = $this->session->userdata('d_id');

            //ドナー情報CSVファイル作成
            $this->donorCsv();

            //コーディネーター情報
            $this->createCordinatorData($d_id);

            //移植医情報
            $this->createTransplantData($d_id);

            //ZIPファイルダウンロード
            $this->zip->download(config_item('download_donor_prefix') . date('YmdHis') . '.zip');
        }
    }

    public function createTransplantData($dId)
    {
        $delimiter = ",";
        /* Headquarter to tranplant (nw_tp_upload) */
        $tpUpPath = config_item('admin_download_transplant_path') . "/" . $this->upPath . "/";
        $tpUpFile = $this->Upfiletbl->getUpfileTransplantUploadByDid($dId, $this->affId);
        $csv = "ファイル名,更新日時\n";
        foreach ($tpUpFile as $file) {
            $fileName = explode(".", $file->file_name)[0];
            $fileExt = explode(".", $file->file_name)[1];
            $fileName = $file->file_name_prefix != 0 ? $fileName . "(" . $file->file_name_prefix . ")" : $fileName;
            $downloadedFile = $this->box_api->downloadFile($file->boxfile_id);
            if ($downloadedFile["success"]) {
                $this->zip->add_data($this->zipPath . $tpUpPath . mb_convert_encoding($fileName, "sjis-win", "UTF-8") . '.' . $fileExt, $downloadedFile["data"]["content"]);
            } else {
                redirect("errors/file_can_not_download");
            }
            $csv .= $fileName . $delimiter;
            $csv .= $file->created_at . "\n";
        }
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");
        $this->zip->add_data($this->zipPath . $tpUpPath . config_item('admin_download_upload_csv'), $csv);

        /* Transplant to headquarter (nw_tp_download) */
        $tpDownPath = config_item('admin_download_transplant_path') . "/" . $this->downPath . "/";
        $requestFolders = $this->Donorinstitutionorganstbl->getRequestFolders($dId, null, null);
        $csv = "ファイル名,更新日付,担当者\n";
        if ($requestFolders) {
            foreach ($requestFolders as $folder) {
                $folderItems = $this->box_api->getFolderItems($folder->jot_offer_boxfolder_id, "id,extension,created_at,name,uploader_display_name");
                if ($folderItems["success"]) {
                    foreach ($folderItems["data"]->entries as $item) {
                        $fileName = explode(".", $item->name, -1);
                        $fileName = implode(".", $fileName);
                        $downloadedFile = $this->box_api->downloadFile($item->id);
                        if ($downloadedFile["success"]) {
                            $this->zip->add_data($this->zipPath . $tpDownPath . mb_convert_encoding($fileName, "sjis-win", "UTF-8") . '.' . $item->extension, $downloadedFile["data"]["content"]);
                        }
                        $csv .= $fileName . $delimiter;
                        $csv .= $item->created_at . $delimiter;
                        $csv .= $item->uploader_display_name . "\n";
                    }
                } else {
                    redirect("errors/folder_can_not_get_item");
                }
            }
        }
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");
        $this->zip->add_data($this->zipPath . $tpDownPath . config_item('admin_download_download_csv'), $csv);

        /* 受取確認 (nw_tp_receipt) */
        $tpRePath = config_item('admin_download_transplant_path') . "/" . config_item('admin_download_receipt_path') . "/";
        $csv = "ファイル名,施設名,臓器,ダウンロードカウント,プレビューカウント,更新日付\n";
        if ($requestFolders) {
            foreach ($requestFolders as $requestFolder) {
                $headTpFolder = $this->box_api->getFolderItems($requestFolder->donorinfo_boxfolder_id, "id,extension,name,modified_at,shared_link");
                if ($headTpFolder["success"]) {
                    foreach ($headTpFolder["data"]->entries as $item) {
                        $fileName = explode(".", $item->name, -1);
                        $fileName = implode(".", $fileName);
                        $institution = $this->Institutionmst->getTransplantInstitutionMstById($requestFolder->institution_mst_id)->institution_name;
                        $organ = $this->Internalorgansmst->getInternalOrgansMstById($requestFolder->internal_organs_mst_id)->organ_name;
                        $csv .= $fileName . $delimiter;
                        $csv .= $institution . $delimiter;
                        $csv .= $organ . $delimiter;
                        $csv .= $item->shared_link->download_count . $delimiter;
                        $csv .= $item->shared_link->preview_count . $delimiter;
                        $csv .= $item->modified_at . "\n";
                    }
                } else {
                    redirect("errors/folder_can_not_get_item");
                }
            }
        }
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");
        $this->zip->add_data($this->config->item('zip_download_file_tmp_path') . $tpRePath . $this->config->item('admin_download_receipt_csv'), $csv);
    }

    public function createCordinatorData($d_id)
    {
        $delimiter = ",";
        /* Headquarter to cordinator (nw_co_upload) */
        $coUpPath = config_item('admin_download_cordinator_path') . "/" . $this->upPath . "/";
        $coUpFile = $this->Upfiletbl->getUpfileCoUploadByDid($d_id, $this->session->userdata("affiliation_mst_id"));
        $csv = "ファイル名,更新日時\n";
        foreach ($coUpFile as $file) {
            $fileName = explode(".", $file->file_name)[0];
            $fileExt = explode(".", $file->file_name)[1];
            $fileName = $file->file_name_prefix ? $fileName . "(" . $file->file_name_prefix . ")" : $fileName;
            $downloadedFile = $this->box_api->downloadFile($file->boxfile_id);
            if ($downloadedFile["success"]) {
                $this->zip->add_data($this->zipPath . $coUpPath . mb_convert_encoding($fileName, "sjis-win", "UTF-8") . '.' . $fileExt, $downloadedFile["data"]["content"]);
            } else {
                redirect("errors/file_can_not_download");
            }
            $csv .= $fileName . $delimiter;
            $csv .= $file->created_at . "\n";
        }
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");
        $this->zip->add_data($this->zipPath . $coUpPath . config_item('admin_download_upload_csv'), $csv);

        /* Cordinator to headquarter (nw_co_download) */
        $coDownPath = config_item('admin_download_cordinator_path') . "/" . config_item('admin_download_download_path') . "/";
        $coDownFile = $this->Upfiletbl->getUpfileCoDownloadByDid($d_id, $this->session->userdata("affiliation_mst_id"));
        $csv = "ファイル名,更新日付,担当者\n";

        foreach ($coDownFile as $file) {
            $fileName = explode(".", $file->file_name)[0];
            $fileExt = explode(".", $file->file_name)[1];
            $fileName = $file->file_name_prefix ? $fileName . "(" . $file->file_name_prefix . ")" : $fileName;
            $downloadedFile = $this->box_api->downloadFile($file->boxfile_id);
            if ($downloadedFile["success"]) {
                $this->zip->add_data($this->zipPath . $coDownPath . mb_convert_encoding($fileName, "sjis-win", "UTF-8") . '.' . $fileExt, $downloadedFile["data"]["content"]);
            } else {
                redirect("errors/file_can_not_download");
            }
            $csv .= $fileName . $delimiter;
            $csv .= $file->created_at . $delimiter;
            $csv .= $file->sei . " " . $file->mei . "\n";
        }
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");
        $this->zip->add_data(config_item('zip_download_file_tmp_path') . $coDownPath . config_item('admin_download_download_csv'), $csv);

        /* 受取確認 (nw_co_receipt) */
        $coRePath = config_item('admin_download_cordinator_path') . "/" . config_item('admin_download_receipt_path') . "/";
        $fileList = $this->Filedownloadlogtbl->getUpfileCoReceiptByDid($d_id, $this->affId);

        $csv = "ファイル名,受取確認,更新日付\n";
        foreach ($fileList as $file) {
            $fileName = explode(".", $file["file_name"])[0];
            $fileName = $file["file_name_prefix"] ? $fileName . "(" . $file["file_name_prefix"] . ")" : $fileName;
            $csv .= $fileName . $delimiter;
            $csv .= '"';
            $csv .= str_replace(",", "", $file["user"]);
            $csv .= '"' . $delimiter;
            $csv .= $file["updated_at"] . "\n";
        }
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");
        $this->zip->add_data($this->zipPath . $coRePath . config_item('admin_download_receipt_csv'), $csv);
    }

    public function donorCsv()
    {
        $data = $this->session->userdata('data');
        $csv = "事例ID,名前,提供施設,提供施設都道府県,年齢,性別,脳死/心停止,コメント\n";
        $delimiter = ",";
        $csv .= $data['d_id'] . $delimiter;
        $csv .= $data['donorNeme'] . $delimiter;
        $csv .= $data['offerInstitution'] . $delimiter;
        $csv .= $data['offerInstitutionPref'] . $delimiter;
        $csv .= $data['age'] . "歳" . $delimiter;
        $sex = ($data['sex'] == '1') ? "男性" : "女性";
        $csv .= $sex . $delimiter;
        $csv .= $data['deathReason'] . $delimiter;
        $csv .= '"' . $data['message'] . '"';
        $csv = mb_convert_encoding($csv, "sjis-win", "UTF-8");

        $this->zip->add_data($this->zipPath . config_item('admin_download_donor_csv'), $csv);
    }
}
