<?php
class Init extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        php_sapi_name() === "cli" || redirect(base_url());
        $this->load->library("box_api");
    }

    public function index()
    {
        /* Create donor and transplant user (doctor) root folder */
        $result = $this->box_api->createFolder("事例", "0");
        if ($result["success"]) {
            echo "事例フォルダ作成完了\n";
        } else {
            echo "事例フォルダ作成不可\n";
        }
        $result = $this->box_api->createFolder("ユーザー", "0");
        if ($result["success"]) {
            echo "ユーザーフォルダ作成完了\n";
        } else {
            echo "ユーザーフォルダ作成不可\n";
        }
        /* Create donor metadata template */
        $result = $this->box_api->createMetadataTemplate();
        if ($result["success"]) {
            echo "メタデータ作成完了\n";
        } else {
            echo "メタデータ作成不可\n";
        }
    }
}
