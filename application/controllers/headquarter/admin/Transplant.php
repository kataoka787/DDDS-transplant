<?php defined('BASEPATH') or exit('No direct script access allowed');

class Transplant extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array();
    }

    public function index()
    {
        if (!$this->input->post()) {
            $_POST["pref_id"] = "";
            $_POST["institution"] = "";
            $_POST['organs'] = array();
        }

        $offset = $this->uri->segment(3, 0);
        $this->form_validation->run('admin/transplant/search');
        $config['base_url'] = base_url() . "admin/transplant";
        /* XSS clean */
        $organs = $this->input->post("organs", true);
        $prefId = $this->input->post("pref_id", true);
        $institution = $this->input->post("institution", true);

        $config['total_rows'] = $this->Institutionmst->getTransplantInstitutionSearchListCount($organs, $prefId, $institution, $this->session->userdata("affiliation_mst_id"));
        $config['per_page'] = config_item('transplant_search_list_count');
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
            $this->data['next']['link'] = $offset + config_item('transplant_search_list_count');
        }

        if ($prev) {
            $this->data['prev']['flg'] = true;
            $link = "";
            if ($offset - config_item('transplant_search_list_count')) {
                $link = $offset - config_item('transplant_search_list_count');
            }
            $this->data['prev']['link'] = $link;
        }

        $this->data['institutionList'] = $this->Institutionmst->getTransplantInstitutionSearchList($organs, $prefId, $institution, $this->session->userdata("affiliation_mst_id"), $offset, config_item('transplant_search_list_count'));

        $this->data['prefList'] = $this->Prefmst->getPrefMst();

        $organs = $this->Internalorgansmst->getInternalOrgansMst();
        $this->data['organs'] = $organs;
        $this->data['page_title'] = config_item('page_transplant_list_search');

        $this->load->vars($this->data);

        $this->load->view('header');
        $this->load->view('admin/transplant/searchlist');
        $this->load->view('footer');
    }

    public function newedit()
    {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('input_data');
        $this->session->unset_userdata('disp_data');
        redirect('admin/transplant/edit');
    }

    public function edit()
    {
        $this->data['prefList'] = $this->Prefmst->getPrefMst();
        $this->data['organsMst'] = $this->Internalorgansmst->getInternalOrgansMst();
        /* XSS clean */
        $id = $this->input->post("id", true);
        if ($id !== null) {
            $data = $this->Institutionmst->getTransplantInstitutionDataById($id);
            if (!$data) {
                redirect('admin/transplant');
            }
            $data = $this->Institutionmst->getTransplantInstitutionDataById($id);
            $this->data['id'] = $data['id'];
            $_POST['id'] = $data['id'];
            $_POST['pref_id'] = $data['pref_mst_id'];
            $_POST['organs'] = $data['organ_id'];
            $_POST['institution'] = $data['institution_name'];
            $_POST['institution_kubun'] = $data['institution_kubun'];
            $_POST['institution_code'] = $data['institution_code'];
        } else {
            if ($this->session->userdata('input_data')) {
                $input_data = $this->session->userdata('input_data');
                $_POST['id'] = $this->session->userdata('id');
                $_POST['pref_id'] = $input_data['pref_id'];
                $_POST['organs'] = $input_data['organs'];
                $_POST['institution'] = $input_data['institution'];
                $_POST['institution_kubun'] = $input_data['institution_kubun'];
                $_POST['institution_code'] = $input_data['institution_code'];
                $this->data['id'] = $this->session->userdata('id');
                $this->data['pref_id'] = $input_data['pref_id'];
                $this->data['organs'] = $input_data['organs'];
                $this->data['institution'] = $input_data['institution'];
                $this->data['institution_kubun'] = $input_data['institution_kubun'];
                $this->data['institution_code'] = $input_data['institution_code'];
            } else {
                $this->data['id'] = "";
            }
        }

        $this->session->set_userdata('id', $this->data['id']);
        if ($this->session->userdata('id')) {
            $this->data['page_title'] = config_item('page_transplant_edit');
        } else {
            $this->data['page_title'] = config_item('page_transplant_regist');
        }

        $this->form_validation->run('admin/transplant/edit');
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view('admin/transplant/edit');
        $this->load->view('footer');
    }

    public function conf()
    {
        $this->data['id'] = $this->session->userdata('id');

        if ($this->form_validation->run('admin/transplant/edit')) {
            /* XSS clean */
            $input_data = $this->input->post(null, true);
            $this->data['pref'] = $this->Prefmst->getPrefMstById($input_data['pref_id'])->pref_name;
            $this->data['organs'] = $this->Internalorgansmst->getInternalOrgansMstByIds($input_data["organs"]);
            $this->data['institution'] = $input_data["institution"];
            $this->data['institution_kubun'] = $input_data['institution_kubun'];
            $this->data['institution_code'] = $input_data['institution_code'];

            $this->session->set_userdata('disp_data', $this->data);
            $this->session->set_flashdata('conf', true);
            $this->session->set_userdata('input_data', $input_data);
            if ($this->session->userdata('id')) {
                $this->data['page_title'] = config_item('page_transplant_edit_conf');
            } else {
                $this->data['page_title'] = config_item('page_transplant_regist_conf');
            }

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/transplant/conf');
            $this->load->view('footer');
        } else {
            $this->data['prefList'] = $this->Prefmst->getPrefMst();
            $this->data['organsMst'] = $this->Internalorgansmst->getInternalOrgansMst();
            if ($this->session->userdata('id')) {
                $this->data['page_title'] = config_item('page_transplant_edit');
            } else {
                $this->data['page_title'] = config_item('page_transplant_regist');
            }

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/transplant/edit');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        if ($this->session->flashdata('conf')) {
            $input_data = $this->session->userdata('input_data');
            if (!$this->session->userdata('id')) {
                /* Create institution */
                $insert = array(
                    "institution_code" => $input_data['institution_code'],
                    "pref_mst_id" => $input_data['pref_id'],
                    "institution_kubun" => $input_data['institution_kubun'],
                    "institution_name" => $input_data['institution'],
                );
                $institution_id = $this->Institutionmst->insertTransplantInstitutionMstData($insert);

                foreach ($input_data['organs'] as $organId) {
                    $insert = array(
                        "internal_organs_mst_id" => $organId,
                        "institution_mst_id" => $institution_id,
                    );
                    $this->Institutionrelationorganstbl->insertInstitutionRelationOrgansTblData($insert);
                }
                redirect('admin/transplant/end');
            } else {
                /* Update institution */
                $update = array(
                    "institution_code" => $input_data['institution_code'],
                    "pref_mst_id" => $input_data['pref_id'],
                    "institution_kubun" => $input_data['institution_kubun'],
                    "institution_name" => $input_data['institution'],
                    "updated_at" => date('Y-m-d H:i:s'),
                );
                $this->Institutionmst->updateTransplantInstitutionMstData($update, $this->session->userdata('id'));
                $this->Institutionrelationorganstbl->deleteInstitutionRelationOrgansTblByInstitutionId($this->session->userdata('id'));

                foreach ($input_data['organs'] as $val) {
                    $insert = array(
                        "internal_organs_mst_id" => $val,
                        "institution_mst_id" => $this->session->userdata('id'),
                    );
                    $this->Institutionrelationorganstbl->insertInstitutionRelationOrgansTblData($insert);
                }
            }
            redirect('admin/transplant/end');
        } else {
            redirect('admin/transplant/edit');
        }
    }

    public function end()
    {
        $this->session->unset_userdata('id');
        if ($this->session->userdata('disp_data')) {
            $disp_data = $this->session->userdata('disp_data');
            if ($disp_data['id']) {
                $this->data['page_title'] = config_item('page_transplant_edit_comp');
            } else {
                $this->data['page_title'] = config_item('page_transplant_regist_comp');
            }

            $this->data['id'] = $disp_data['id'];
            $this->data['pref'] = $disp_data['pref'];
            $this->data['organs'] = $disp_data['organs'];
            $this->data['institution'] = $disp_data['institution'];
            $this->data['institution_kubun'] = $disp_data['institution_kubun'];
            $this->data['institution_code'] = $disp_data['institution_code'];

            $this->load->vars($this->data);
            $this->load->view('header');
            $this->load->view('admin/transplant/end');
            $this->load->view('footer');
        } else {
            redirect('admin/transplant');
        }
    }

    public function csv()
    {
        $institutionList = $this->Institutionmst->getTransplantInstitutionSearchList(null, null, "", $this->session->userdata("affiliation_mst_id"));

        $csv = "都道府県名,施設名,施設コード,施設区分,心臓,肺,肝臓,腎臓,膵臓,小腸\n";

        $delimiter = ",";
        foreach ($institutionList as $institution) {
            $csv .= $institution->pref_name . $delimiter;
            $csv .= $institution->institution_name . $delimiter;
            $csv .= $institution->SISETU_CD . $delimiter;
            $csv .= INSTITUTION_KUBUN[$institution->institution_kubun] . $delimiter;

            for ($i = 1; $i <= 6; $i++) {
                $str = "organ" . $i;
                if ($institution->$str) {
                    $csv .= "○";
                } else {
                    $csv .= "×";
                }
                if ($i != 6) {
                    $csv .= $delimiter;
                }
            }
            $csv .= "\n";
        }

        $csv = mb_convert_encoding($csv, "SJIS", "UTF-8");
        $fname = $this->config->item('download_transplant_prefix') . date("Ymd") . '.csv';
        force_download($fname, $csv);
    }
}
