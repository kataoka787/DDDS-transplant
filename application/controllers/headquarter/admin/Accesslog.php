<?php defined('BASEPATH') or exit('No direct script access allowed');

class Accesslog extends CI_Controller
{

    public $from_year;
    public $from_month;
    public $from_day;
    public $to_year;
    public $to_month;
    public $to_day;
    public $kbn;
    public $d_id;
    public $sei_kana;
    public $mei_kana;

    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            'page_title' => config_item('page_access_log_search_list'),
        );
    }

    public function index()
    {
        if (!$this->input->post()) {
            $_POST["kbn"] = "";
            $_POST["d_id"] = "";
            $_POST["sei_kana"] = "";
            $_POST["mei_kana"] = "";
        }
        $error = array();

        if ($this->input->post("hidden_btn")) {
            $this->from_year = $this->input->post("from_year");
            $this->from_month = $this->input->post("from_month");
            $this->from_day = $this->input->post("from_day");
            $this->to_year = $this->input->post("to_year");
            $this->to_month = $this->input->post("to_month");
            $this->to_day = $this->input->post("to_day");
            $this->kbn = $this->input->post("kbn");
            $this->d_id = $this->input->post("d_id");
            $this->sei_kana = $this->input->post("sei_kana");
            $this->mei_kana = $this->input->post("mei_kana");
        } else {
            $_POST['kbn'] = $this->session->flashdata('kbn');
            $_POST['d_id'] = $this->session->flashdata('d_id');
            $_POST['sei_kana'] = $this->session->flashdata('sei_kana');
            $_POST['mei_kana'] = $this->session->flashdata('mei_kana');
            $_POST['from_month'] = $this->session->flashdata('from_month');
            $_POST['from_day'] = $this->session->flashdata('from_day');
            $_POST['to_year'] = $this->session->flashdata('to_year');
            $_POST['to_month'] = $this->session->flashdata('to_month');
            $_POST['to_day'] = $this->session->flashdata('to_day');
            $this->from_year = $this->session->flashdata('from_year');
            $this->from_month = $this->session->flashdata('from_month');
            $this->from_day = $this->session->flashdata('from_day');
            $this->to_year = $this->session->flashdata('to_year');
            $this->to_month = $this->session->flashdata('to_month');
            $this->to_day = $this->session->flashdata('to_day');
            $this->kbn = $this->session->flashdata('kbn');
            $this->d_id = $this->session->flashdata('d_id');
            $this->sei_kana = $this->session->flashdata('sei_kana');
            $this->mei_kana = $this->session->flashdata('mei_kana');
        }

        $this->form_validation->run('admin/accesslog/search');
        $this->session->set_flashdata('kbn', $this->kbn);
        $this->session->set_flashdata('d_id', $this->d_id);
        $this->session->set_flashdata('sei_kana', $this->sei_kana);
        $this->session->set_flashdata('mei_kana', $this->mei_kana);
        $this->session->set_flashdata('from_year', $this->from_year);
        $this->session->set_flashdata('from_month', $this->from_month);
        $this->session->set_flashdata('from_day', $this->from_day);
        $this->session->set_flashdata('to_year', $this->to_year);
        $this->session->set_flashdata('to_month', $this->to_month);
        $this->session->set_flashdata('to_day', $this->to_day);

        if ($this->from_year or $this->from_month or $this->from_day) {
            if (is_numeric($this->from_year) && is_numeric($this->from_month) && is_numeric($this->from_day)) {
                if ($this->from_year && $this->from_month && $this->from_day) {
                    if (!checkdate($this->from_month, $this->from_day, $this->from_year)) {
                        $error[] = "FROM";
                    }
                }
            } else {
                $error[] = "FROM";
            }
        }

        if ($this->to_year or $this->to_month or $this->to_day) {
            if (is_numeric($this->to_year) && is_numeric($this->to_month) && is_numeric($this->to_day)) {
                if ($this->to_year && $this->to_month && $this->to_day) {
                    if (!checkdate($this->to_month, $this->to_day, $this->to_year)) {
                        $error[] = "TO";
                    }
                }
            } else {
                $error[] = "TO";
            }
        }

        $year = array();
        for ($i = (date('Y') - config_item('accesslog_search_year_ago')); $i <= date('Y'); $i++) {
            $year[$i] = $i;
        }

        $month = array();
        for ($i = 1; $i <= 12; $i++) {
            $month[$i] = $i;
        }

        $kbn = array();
        $kbn[0] = "全員";
        $account_type = $this->Accounttypemst->getAccountType();
        foreach ($account_type as $val) {
            $kbn[$val->id] = $val->account_type;
        }
        $from_day = array();
        if ($this->from_year && $this->from_month) {
            if (is_numeric($this->from_year) && is_numeric($this->from_month)) {
                $last_day = date("t", mktime(0, 0, 0, $this->from_month, 1, $this->from_year));
                for ($i = 1; $i <= $last_day; $i++) {
                    $from_day[$i] = $i;
                }
            }
        }

        $to_day = array();
        if ($this->to_year && $this->to_month) {
            if (is_numeric($this->to_year) && is_numeric($this->to_month)) {
                $last_day = date("t", mktime(0, 0, 0, $this->to_year, 1, $this->to_month));
                for ($i = 1; $i <= $last_day; $i++) {
                    $to_day[$i] = $i;
                }
            }
        }

        $this->data['list'] = array();
        $this->data['next']['flg'] = false;
        $this->data['prev']['flg'] = false;

        if (!$error or !validation_errors()) {
            $this->makeList();
        }

        $this->data['error'] = $error;
        $this->data['kbn'] = $kbn;
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['from_day'] = $from_day;
        $this->data['to_day'] = $to_day;

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('admin/accesslog/searchlist');
        $this->load->view('footer');
    }

    public function csv()
    {
        $error = array();
        $this->form_validation->run('admin/accesslog/search');
        if ($this->input->post('from_year') or $this->input->post('from_month') or $this->input->post('from_day')) {
            if (is_numeric($this->input->post('from_year')) && is_numeric($this->input->post('from_month')) && is_numeric($this->input->post('from_day'))) {
                if ($this->input->post('from_year') && $this->input->post('from_month') && $this->input->post('from_day')) {
                    if (!checkdate($this->input->post('from_month'), $this->input->post('from_day'), $this->input->post('from_year'))) {
                        $error[] = "FROM";
                    }
                }
            } else {
                $error[] = "FROM";
            }
        }

        if ($this->input->post('to_year') or $this->input->post('to_month') or $this->input->post('to_day')) {
            if (is_numeric($this->input->post('to_year')) && is_numeric($this->input->post('to_month')) && is_numeric($this->input->post('to_day'))) {
                if ($this->input->post('to_year') && $this->input->post('to_month') && $this->input->post('to_day')) {
                    if (!checkdate($this->input->post('to_month'), $this->input->post('to_day'), $this->input->post('to_year'))) {
                        $error[] = "TO";
                    }
                }
            } else {
                $error[] = "TO";
            }
        }
        if (!$error or !validation_errors()) {

            $from = "";
            $to = "";

            if ($this->input->post('from_year') && $this->input->post('from_month') && $this->input->post('from_day')) {
                $from = date('Y-m-d', mktime(0, 0, 0, $this->input->post('from_month'), $this->input->post('from_day'), $this->input->post('from_year')));
            }

            if ($this->input->post('to_year') && $this->input->post('to_month') && $this->input->post('to_day')) {
                $to = date('Y-m-d', mktime(0, 0, 0, $this->input->post('to_month'), $this->input->post('to_day'), $this->input->post('to_year')));
            }

            $list = $this->Accesslogtbl->getAccessLogSearchList($this->input->post('kbn'), $this->input->post('sei_kana'), $this->input->post('mei_kana'), $this->input->post('d_id'), $from, $to);
            $csv = "日時,ユーザー区分,ユーザ名,URL,アクセス元,パラメータ\n";
            $delimiter = ",";
            foreach ($list as $key => $val) {
                $csv .= $val->created_at . $delimiter;
                $csv .= $val->account_type . $delimiter;
                $csv .= $val->sei . " " . $val->mei . $delimiter;
                $csv .= $val->url . $delimiter;
                $csv .= '"' . $val->ip_address . "\n" . $val->user_agent . '"' . $delimiter;
                $csv .= '"' . "[GET]\n" . $val->get_param . "\n[POST]\n" . $val->post_param . '"';
                $csv .= "\n";
            }

            $csv = mb_convert_encoding($csv, "SJIS", "UTF-8");
            $fname = config_item('download_accesslog_prefix') . date("Ymd") . '.csv';
            force_download($fname, $csv);
        } else {
            redirect('admin/accesslog');
        }
    }

    public function makeList()
    {
        $offset = $this->uri->segment(3, 0);

        $from = "";
        $to = "";

        if ($this->from_year && $this->from_month && $this->from_day) {
            $from = date('Y-m-d', mktime(0, 0, 0, $this->from_month, $this->from_day, $this->from_year));
        }

        if ($this->to_year && $this->to_month && $this->to_day) {
            $to = date('Y-m-d', mktime(0, 0, 0, $this->to_month, $this->to_day, $this->to_year));
        }

        $config['base_url'] = base_url() . "admin/accesslog";
        $config['total_rows'] = $this->Accesslogtbl->getAccessLogSearchListCount($this->kbn, $this->sei_kana, $this->mei_kana, $this->d_id, $from, $to);
        $config['per_page'] = config_item('accesslog_search_list_count');
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
            $this->data['next']['link'] = $offset + config_item('accesslog_search_list_count');
        }

        if ($prev) {
            $this->data['prev']['flg'] = true;
            $link = "";
            if ($offset - config_item('accesslog_search_list_count')) {
                $link = $offset - config_item('accesslog_search_list_count');
            }
            $this->data['prev']['link'] = $link;
        }
        $this->data['list'] = $this->Accesslogtbl->getAccessLogSearchList($this->kbn, $this->sei_kana, $this->mei_kana, $this->d_id, $from, $to, $offset, config_item('accesslog_search_list_count'));
    }
}
