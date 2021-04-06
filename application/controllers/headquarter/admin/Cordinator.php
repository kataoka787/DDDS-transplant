<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cordinator extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array();
    }

    public function index()
    {
        if (!$this->input->post()) {
            $_POST["sei_kana"] = "";
            $_POST["mei_kana"] = "";
            $_POST["mail"] = "";
        }

        $offset = $this->uri->segment(3, 0);
        $this->data['page_title'] = config_item('page_co_list_search');
        $this->form_validation->run('admin/cordinator/search');

        $config['base_url'] = base_url() . "admin/cordinator";
        $config['total_rows'] = $this->Cordinatortbl->getCordinatorSearchListCount($this->input->post('sei_kana'), $this->input->post('mei_kana'), $this->input->post('mail'));
        $config['per_page'] = config_item('cordinator_search_list_count');
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
            $this->data['next']['link'] = $offset + config_item('cordinator_search_list_count');
        }

        if ($prev) {
            $this->data['prev']['flg'] = true;
            $link = "";
            if ($offset - config_item('cordinator_search_list_count')) {
                $link = $offset - config_item('cordinator_search_list_count');
            }
            $this->data['prev']['link'] = $link;
        }

        $list = $this->Cordinatortbl->getCordinatorSearchList($this->input->post('sei_kana'), $this->input->post('mei_kana'), $this->input->post('mail'), $offset, config_item('cordinator_search_list_count'));
        $this->data['list'] = $list;

        $this->load->vars($this->data);

        $this->load->view('header');
        $this->load->view('admin/cordinator/searchlist');
        $this->load->view('footer');
    }

    public function confirm()
    {
        if ($this->input->post('id')) {
            /* Get account id */
            $data = $this->Cordinatortbl->getCordinatorById($this->input->post('id'));
            if (!$data) {
                redirect('admin/cordinator');
            }

            $this->data = array(
                "accountId" => $data->id,
                "name" => $data->sei . " " . $data->mei,
                "kana" => $data->sei_kana . " " . $data->mei_kana,
                "mail" => $data->mail,
                "admin_flg" => $data->admin_flg,
                "workName" => $data->work_name,
                "page_title" => config_item('page_co_delete_confirm'),
            );
            $this->session->set_userdata('id', $data->id);
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/cordinator/confirm');
            $this->load->view('footer');
        } else {
            redirect('admin/cordinator');
        }
    }

    public function newedit()
    {
        $this->session->unset_userdata("account_id");
        $this->session->unset_userdata('input_data');
        $this->session->unset_userdata('disp_data');
        redirect('admin/cordinator/edit');
    }

    public function edit()
    {
        /* Cordinator account id */
        $accId = $this->input->post("account_id");
        if ($accId !== null) { /* Edit cordinator */
            $data = $this->Cordinatortbl->getCordinatorById($accId);
            if (!$data) {
                redirect('admin/cordinator');
            }
            $_POST['account_id'] = $accId;
            $_POST['sei'] = $data->sei;
            $_POST['mei'] = $data->mei;
            $_POST['sei_kana'] = $data->sei_kana;
            $_POST['mei_kana'] = $data->mei_kana;
            $_POST['mail'] = $data->mail;
            $_POST["works"] = explode(",", $data->work_id);
            $_POST['admin_flg'] = $data->admin_flg;
            $this->data['accountId'] = $accId;
            $this->data['admin_flg'] = $_POST['admin_flg'];
        } else {
            /* Back from conf */
            if ($this->session->userdata('input_data')) {
                $input_data = $this->session->userdata('input_data');
                $_POST['id'] = $this->session->userdata('id');
                $_POST['sei'] = $input_data['sei'];
                $_POST['mei'] = $input_data['mei'];
                $_POST['sei_kana'] = $input_data['sei_kana'];
                $_POST['mei_kana'] = $input_data['mei_kana'];
                $_POST['mail'] = $input_data['mail'];
                $_POST['password'] = $input_data['password'];
                $_POST['admin_flg'] = $input_data['admin_flg'];
                $_POST["works"] = $input_data["works"];
                $this->data['accountId'] = $this->session->userdata('accountId');
                $this->data['admin_flg'] = $input_data['admin_flg'];
            } else { /* Create new cordinator */
                $this->data['sei'] = "";
                $this->data['mei'] = "";
                $this->data['sei_kana'] = "";
                $this->data['mei_kana'] = "";
                $this->data['mail'] = "";
                $this->data['passowrd'] = "";
                $this->data['admin_flg'] = "0";
                $this->data["accountId"] = "";
            }
        }
        $this->session->set_userdata('accountId', $this->data['accountId']);

        if ($this->session->userdata('accountId')) {
            $this->data['page_title'] = config_item('page_co_edit');
            $this->form_validation->run('admin/cordinator/edit');
        } else {
            $this->data['page_title'] = config_item('page_co_regist');
            $this->form_validation->run('admin/cordinator/new');
        }

        $this->data["works"] = $this->Workmst->getWorkMst();
        $this->load->vars($this->data);

        $this->load->view('header');
        $this->load->view('admin/cordinator/edit');
        $this->load->view('footer');
    }

    public function conf()
    {
        $workMst = $this->Workmst->getWorkMst();
        $this->data['accountId'] = $this->session->userdata('accountId');
        $this->data['admin_flg'] = $this->input->post('admin_flg');
        /* Run validation depend on cordinator editing or creating */
        $rule = "admin/cordinator/new";
        $accId = $this->session->userdata("accountId");
        if ($accId != null) {
            $_POST["account_id"] = $accId;
            $rule = "admin/cordinator/edit";
        }
        if ($this->form_validation->run($rule)) {
            $this->data['sei'] = $this->input->post('sei');
            $this->data['mei'] = $this->input->post('mei');
            $this->data['sei_kana'] = $this->input->post('sei_kana');
            $this->data['mei_kana'] = $this->input->post('mei_kana');
            $this->data['mail'] = $this->input->post('mail');
            $this->data['password'] = $this->input->post('password');
            $this->data['admin_flg'] = $this->input->post('admin_flg');
            $selectedWork = array();
            foreach ($workMst as $work) {
                if (in_array($work->id, $this->input->post("works"))) {
                    array_push($selectedWork, $work->work_name);
                }
            }
            $this->data["works"] = implode(" | ", $selectedWork);

            $this->session->set_userdata('disp_data', $this->data);
            $this->session->set_userdata('input_data', $this->input->post());
            $this->session->set_flashdata('conf', true);

            if ($this->session->userdata('accountId')) {
                $this->data['page_title'] = config_item('page_co_edit_conf');
            } else {
                $this->data['page_title'] = config_item('page_co_regist_conf');
            }

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/cordinator/conf');
            $this->load->view('footer');
        } else {
            $this->data["works"] = $workMst;
            if ($this->session->userdata('accountId')) {
                $this->data['page_title'] = config_item('page_co_edit');
            } else {
                $this->data['page_title'] = config_item('page_co_regist');
            }

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/cordinator/edit');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        if ($this->session->flashdata('conf')) {
            $input_data = $this->session->userdata('input_data');
            $accId = $this->session->userdata('accountId');
            if ($accId == null) { /* Create new cordinator */
                /* accountTbl */
                $insert = array(
                    "sei" => $input_data['sei'],
                    "mei" => $input_data['mei'],
                    "sei_kana" => $input_data['sei_kana'],
                    "mei_kana" => $input_data['mei_kana'],
                    "password" => $input_data['password'],
                    "mail" => $input_data['mail'],
                    "account_type_mst_id" => ACC_TYPE_CO,
                    "status_mst_id" => ACC_STT_CONF,
                    "admin_flg" => $input_data['admin_flg'],
                    "delete_flg" => IN_USE_FLG,
                    "password_datetime" => date('Y-m-d'),
                );
                $insertedAccId = $this->Accounttbl->insertAccountTblData($insert);

                /* accountRelationWorkTbl */
                $works = $input_data["works"];
                foreach ($works as $workId) {
                    $insert = array(
                        "account_tbl_id" => $insertedAccId,
                        "work_mst_id" => $workId,
                    );
                    $this->Accountrelationworktbl->insert($insert);
                }

                /* cordinatorTbl */
                $insert = array(
                    "account_tbl_id" => $insertedAccId,
                    "cordinator_type_mst_id" => NW_CO,
                );
                $insertedCoId = $this->Cordinatortbl->insertCordinatorData($insert);

                /* affiliationCordinatorTbl */
                $insert = array(
                    "cordinator_tbl_id" => $insertedCoId,
                    "affiliation_mst_id" => AFF_NW,
                );
                $this->Affiliationcordinatortbl->insertAffiliationCordinatorTblData($insert);

                /* Send mail */
                $this->sendMail($input_data['mail'], "register");
                /* Redirect to complete screen */
                redirect('admin/cordinator/end');
            } else { /* Edit existed cordinator */

                $data = $this->Cordinatortbl->getCordinatorById($accId);
                $before = array(
                    "sei" => $data->sei,
                    "mei" => $data->mei,
                    "sei_kana" => $data->sei_kana,
                    "mei_kana" => $data->mei_kana,
                    "mail" => $data->mail,
                    "password" => $data->password,
                    "admin_flg" => $data->admin_flg,
                    "works" => explode(",", $data->work_id),
                );

                $after = array(
                    "sei" => $input_data['sei'],
                    "mei" => $input_data['mei'],
                    "sei_kana" => $input_data['sei_kana'],
                    "mei_kana" => $input_data['mei_kana'],
                    "mail" => $input_data['mail'],
                    "password" => $input_data['password'],
                    "admin_flg" => $input_data['admin_flg'],
                    "works" => $input_data["works"],
                );

                $password_flg = false;
                $change = "";
                if ($after['sei'] . $after['mei'] != $before['sei'] . $before['mei']) {
                    $change .= "[氏名]\n" . $before['sei'] . $before['mei'] . " => " . $after['sei'] . $after['mei'] . "\n";
                }

                if ($after['sei_kana'] . $after['mei_kana'] != $before['sei_kana'] . $before['mei_kana']) {
                    $change .= "[フリガナ]\n" . $before['sei_kana'] . $before['mei_kana'] . " => " . $after['sei_kana'] . $after['mei_kana'] . "\n";
                }

                if ($after['mail'] != $before['mail']) {
                    $change .= "[メールアドレス]\n" . $before['mail'] . " => " . $after['mail'] . "\n";
                }

                if (!empty($after['password']) && !password_verify($after['password'], $before['password'])) {
                    $password_flg = true;
                    $change .= "[パスワード変更]\n";
                }

                if (array_key_exists('admin_flg', $before) && array_key_exists('admin_flg', $after)) {
                    if ($after['admin_flg'] != $before['admin_flg']) {
                        if ($after['admin_flg'] == '1') {
                            $befor_admin_flg = "一般";
                            $after_admin_flg = "管理";
                        } else {
                            $befor_admin_flg = "管理";
                            $after_admin_flg = "一般";
                        }
                        $change .= "[権限]\n" . $befor_admin_flg . " => " . $after_admin_flg . "\n";
                    }
                }

                /* Update accountTbl */
                $update = array(
                    "sei" => $input_data['sei'],
                    "mei" => $input_data['mei'],
                    "sei_kana" => $input_data['sei_kana'],
                    "mei_kana" => $input_data['mei_kana'],
                    "password" => $input_data['password'],
                    "mail" => $input_data['mail'],
                    "admin_flg" => $input_data['admin_flg'],
                    "updated_at" => date('Y-m-d H:i:s'),
                );
                if ($password_flg) {
                    $update['password_datetime'] = date('Y-m-d');
                }
                $this->Accounttbl->updateAccountTblData($update, $this->session->userdata('id'));

                /* Update cordinatorTbl */
                $update = array(
                    "cordinator_type_mst_id" => NW_CO,
                    "updated_at" => date('Y-m-d H:i:s'),
                );
                $this->Cordinatortbl->updateCordinatorData($update, $this->session->userdata('id'));

                /* Update accountRelationWorkTbl */
                $this->Accountrelationworktbl->delete($this->session->userdata('id'));
                foreach ($after["works"] as $workId) {
                    $insert = array(
                        "account_tbl_id" => $this->session->userdata('id'),
                        "work_mst_id" => $workId,
                    );
                    $this->Accountrelationworktbl->insert($insert);
                }

                if ($change) {
                    $insert = array(
                        "contents" => $change,
                        "account_tbl_id" => $this->session->userdata('id'),
                        "account_type_mst_id" => ACC_TYPE_CO,
                        "affiliation_mst_id" => AFF_NW,
                    );
                    $this->Accountchangehistorytbl->insertAccountChangeHistoryTblData($insert);
                }
                $this->sendMail($input_data['mail'], "edit");
            }
            redirect('admin/cordinator/end');
        } else {
            redirect('admin/cordinator/edit');
        }
    }

    private function sendMail($mail, $mode)
    {
        $mailSettings = config_item("mail");
        $coMailSettings = $mailSettings["cordinator"];
        $this->Mailsend->setFromName($mailSettings["from_name"]);
        $this->Mailsend->setFrom($mailSettings["from_address"]);
        $this->Mailsend->setBody($coMailSettings["template"][$mode]);
        $this->Mailsend->setTo($mail);
        $this->Mailsend->setSubject($coMailSettings["subject"][$mode]);
        $this->Mailsend->str_replace("SIGNATURE", $coMailSettings["signature"]);
        $this->Mailsend->str_replace("URL2", config_item('url_guide'));
        $this->Mailsend->str_replace("URL3", config_item('url_faq'));
        $this->Mailsend->send();
    }

    public function end()
    {
        if ($this->session->userdata('disp_data')) {
            $disp_data = $this->session->userdata('disp_data');

            if ($disp_data['accountId']) {
                $this->data['page_title'] = config_item('page_co_edit_comp');
            } else {
                $this->data['page_title'] = config_item('page_co_regist_comp');
            }
            $this->data['accountId'] = $disp_data['accountId'];
            $this->data['admin_flg'] = $disp_data['admin_flg'];
            $this->data['sei'] = $disp_data['sei'];
            $this->data['mei'] = $disp_data['mei'];
            $this->data['sei_kana'] = $disp_data['sei_kana'];
            $this->data['mei_kana'] = $disp_data['mei_kana'];
            $this->data['mail'] = $disp_data['mail'];
            $this->data['password'] = $disp_data['password'];
            $this->session->unset_userdata('accountId');
            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/cordinator/end');
            $this->load->view('footer');
        } else {
            redirect('admin/cordinator');
        }
    }

    public function delete()
    {
        $accId = $this->input->post("account_id");
        if ($this->Accounttbl->getAccountById($accId)) {
            $update = array(
                'delete_flg' => DELETED_FLG,
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $this->Accounttbl->updateAccountTblData($update, $accId);

            $contents = "アカウント削除";
            $insert = array(
                'contents' => $contents,
                'account_tbl_id' => $accId,
                'account_type_mst_id' => ACC_TYPE_CO,
            );
            $this->Accountchangehistorytbl->insertAccountChangeHistoryTblData($insert);
            redirect('admin/cordinator/delete_end');
        }
        redirect('admin/cordinator');
    }

    public function delete_end()
    {
        $this->load->view('header');
        $this->load->view('admin/cordinator/delete_end');
        $this->load->view('footer');
    }
}
