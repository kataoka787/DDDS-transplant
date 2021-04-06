<?php defined('BASEPATH') or exit('No direct script access allowed');

class Output extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "branch" => config_item('branch'),
        );
        $this->load->library('box_api');
    }

    public function index()
    {
        $key = $this->input->get('key');
        $this->session->keep_flashdata('file_conf');
        $this->session->keep_flashdata('insert_id');
        $file = $this->Tmpupfiletbl->getTmpUpFileByIdAccountId($key, $this->session->userdata('account')->accountId);
        if ($file) {
            $fileExt = explode(".", $file->file_name)[1];
            if ($fileExt) {
                $fileExt = strtolower($fileExt);
            }
            if (in_array($fileExt, config_item("image_ext"))) {
                $this->output->set_content_type($fileExt)->set_output($file->file);
            } elseif (array_key_exists($fileExt, config_item("ext_icon"))) {
                $this->output->set_content_type("jpeg")->set_output(file_get_contents(config_item("img_path") . config_item("ext_icon")[$fileExt]));
            } else {
                $this->output->set_content_type("jpeg")->set_output(file_get_contents(config_item("img_path") . config_item("ext_icon")["undefined"]));
            }
        }
    }

    public function download()
    {
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        $dbType = $type == "preview" ? 2 : $type;
        if ($id !== null) {
            $file = null;
            $downloadedFile = null;
            if ($this->data['branch'] === APP_HEAD) {
                $file = $this->Upfiletbl->getUpFileByUpFileTblIdAccountId($id, $dbType);
            } else if ($this->data['branch'] === APP_CORDINATOR) {
                $file = $this->Upfiletbl->getUpFileByUpFileTblIdCordinatorId($id, $dbType);
            }
            $downloadedFile = $this->box_api->downloadFile($file->boxfile_id);

            if ($downloadedFile["success"]) {
                /* Update file access log */
                if ($type == '2') {
                    $accId = $this->session->userdata('account')->accountId;
                    if (!$this->Filedownloadlogtbl->getFiledownloadLogByUpfileTblIdAccountTblId($accId, $id)) {
                        $insert = array(
                            "account_tbl_id" => $accId,
                            "upfile_tbl_id" => $id,
                        );
                        $this->Filedownloadlogtbl->insertFiledownloadLog($insert);
                    }
                }
                /* Do download */
                $fileName = explode(".", $file->file_name)[0];
                $fileExt = explode(".", $file->file_name)[1];
                $fileName = $file->file_name_prefix != 0 ? $fileName . "(" . $file->file_name_prefix . ")" : $fileName;
                force_download("$fileName.$fileExt", $downloadedFile["data"]["content"]);
            }
            redirect("errors/file_not_found");
        }
    }

    public function tp_download()
    {
        $id = $this->input->get("id");
        $fileName = $this->input->get("file_name");
        if ($id !== null) {
            $downloadedFile = $this->box_api->downloadFile($id);
            if ($downloadedFile["success"]) {
                /* Do download */
                force_download($fileName, $downloadedFile["data"]["content"]);
            }
            redirect("errors/file_not_found");
        }
    }
}
