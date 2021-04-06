<?php defined('BASEPATH') or exit('No direct script access allowed');

class Upload extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('data')) {
            redirect('data');
        }
        $this->data = $this->session->userdata('data');
    }

    public function index()
    {
        $folder = $this->Foldercategorymanagementtbl->getFolderData();
        $this->data['folder'] = $folder;
        $this->data['category'] = "";
        $this->data['photo_flg'] = "";

        $this->data['fileList'] = $this->Upfiletbl->getUpFileUploadByDid($this->data['d_id']);

        $this->data['page_title'] = $this->config->item('page_upload');
        $this->load->vars($this->data);

        $this->load->view('header');
        $this->load->view('upload/index');
        $this->load->view('footer');
    }

    public function conf()
    {
        $validate = true;
        $this->data['category'] = "";

        $data = $this->input->post();
        if ($this->form_validation->run('upload') != true) {
            $validate = false;
        } else {
            $config['upload_path'] = config_item('upload_file_tmp_path');
            $config['allowed_types'] = "*";
            $config['max_height'] = config_item('upload_file_max_height');
            $config['max_width'] = config_item('upload_file_max_width');
            $this->upload->initialize($config);
        }

        /* Do upload */
        if (!$validate || !$this->upload->do_upload('upfile')) {
            /* Return upload screen if any error */
            $this->data['folder'] = $this->Foldercategorymanagementtbl->getFolderData();
            $this->data['error'] = $this->upload->display_errors();

            if ($data['folder']) {
                $category = $this->Foldercategorymanagementtbl->getCategoryData($data['folder']);
                $this->data['category'] = $category;

                $category = $this->Foldermst->getFolderDataById($data['folder']);
                $photo_flg = false;
                if ($category) {
                    if ($category->folder_name == config_item('folder_name_photo')) {
                        $photo_flg = true;
                    }
                }
                $this->data['photo_flg'] = $photo_flg;
            }

            $this->data['fileList'] = $this->Upfiletbl->getUpFileUploadByDid($this->data['d_id']);
            $this->data['page_title'] = config_item('page_upload');
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('upload/index');
            $this->load->view('footer');
            return;
        } else {
            $upFile = $this->upload->data();
            $this->data["filePath"] = $upFile["full_path"];
            $this->data["fileExt"] = ltrim($upFile["file_ext"], ".");
            $this->data["isImage"] = ($this->data["fileExt"] == 'jpg' || $this->data["fileExt"] == 'jpeg') ? true : false;

            $fileName = $this->Filecategorymst->getCategoryDataById($data['category'])->category_name;
            $data['memo'] && $fileName .= $data['memo'];
            $fileName = preg_replace('/\s+/', '_', mb_convert_kana($fileName, 's'));
            $this->data["fileName"] = $fileName . "（" . $this->data["fileExt"] . "）";
            /* Insert to temporary upload file table */
            $insertData = array(
                "d_id" => $this->data['d_id'],
                "file_category_mst_id" => $data['category'],
                "file_name" => $fileName . $upFile["file_ext"],
                "file" => $upFile['file_name'],
                "account_tbl_id" => $this->session->userdata('account')->accountId,
            );
            $this->data["insertedId"] = $this->Tmpupfiletbl->insertUpfileData($insertData);
        }

        if ($validate) {
            $this->data['page_title'] = config_item('page_upload_conf');
            $this->session->set_userdata('fileConf', true);
            $this->session->set_userdata('filePath', $this->data["filePath"]);
            $this->session->set_userdata('insertId', $this->data["insertedId"]);
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('upload/conf');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        $filePath = $this->session->userdata("filePath");
        $insertedId = $this->session->userdata("insertId");

        if ($this->session->userdata("fileConf") && $insertedId) {
            $file = $this->Tmpupfiletbl->getTmpUpFileByIdAccountId($insertedId, $this->session->userdata('account')->accountId);
            $upFile = $this->Upfiletbl->getFileNamePrefixByDidFileName($file->d_id, $file->file_name);

            $prefix = 0;
            if ($upFile) {
                if (!is_null($upFile->file_name_prefix)) {
                    $prefix = $upFile->file_name_prefix + 1;
                }
            }
            /* Upload file to box */
            $fileName = explode(".", $file->file_name)[0];
            $fileExt = explode(".", $file->file_name)[1];
            $fileName = $prefix != 0 ? $fileName . "(" . $prefix . ")" : $fileName;

            /* Get donor info */
            $donor = $this->Donorbasetbl->getDonorBaseTblByDid($file->d_id);
            /* Upload file to cordinator -> headquarter folder */
            $uploadFile = $this->box_api->uploadFile("$fileName.$fileExt", $donor->offer_institution_boxfolder_id, $filePath);
            if ($uploadFile["success"]) {
                /* Insert file info to DB */
                $insertData = array(
                    "d_id" => $file->d_id,
                    "file_category_mst_id" => $file->file_category_mst_id,
                    "file_name" => $file->file_name,
                    "file_name_prefix" => $prefix,
                    "boxfile_id" => $uploadFile["data"]->entries[0]->id,
                    "account_tbl_id" => $file->account_tbl_id,
                );
                $this->Upfiletbl->insertUpfileData($insertData);
                $this->Tmpupfiletbl->deleteTmpUpFileById($insertedId);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            } else {
                redirect('errors/file_can_not_upload');
            }
            $this->session->unset_userdata("filePath");
            $this->session->unset_userdata("insertId");
            $this->session->unset_userdata("fileConf");
            redirect('upload/end');
        } else {
            redirect('upload');
        }
    }

    public function end()
    {
        $this->data['page_title'] = $this->config->item('page_upload_comp');
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('upload/end');
        $this->load->view('footer');
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if ($id !== null) {
            $boxFileId = $this->Upfiletbl->getUpFileTblById($id)->boxfile_id;
            $this->box_api->deleteFile($boxFileId)["success"] || redirect("errors/file_can_not_delete");
            $this->Filedownloadlogtbl->deleteByUpfileId($id);
            $this->Upfiletbl->deleteUpFileById($id);
        }
        redirect("upload");
    }

    public function folder_check($str)
    {
        $flg = true;
        if (!preg_match("/^[0-9]+$/", $str)) {
            $flg = false;
        } else {
            if (!$this->Foldercategorymanagementtbl->getFolderDataCheckById($str)) {
                $flg = false;
            }
        }
        if (!$flg) {
            $this->form_validation->set_message('folder_check', $this->lang->line('valid_value'));
            return false;
        }
        return true;
    }

    public function category_check($str)
    {
        $flg = true;
        if (!preg_match("/^[0-9]+$/", $str)) {
            $flg = false;
        } else {
            if (!$this->Foldercategorymanagementtbl->getCategoryDataCheckById($str)) {
                $flg = false;
            }
        }
        if (!$flg) {
            $this->form_validation->set_message('category_check', $this->lang->line('valid_value'));
            return false;
        }
        return true;
    }
}
