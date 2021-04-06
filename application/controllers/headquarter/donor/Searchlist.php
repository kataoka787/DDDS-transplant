<?php defined('BASEPATH') or exit('No direct script access allowed');

class Searchlist extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data = array();
        $this->session->unset_userdata('d_id');
    }

    public function index()
    {
        $offset = $this->uri->segment(3, 0);
        $this->data['page_title'] = config_item('page_donor_list_search');
        $this->form_validation->run('donor/search');
        $query = array(
            "dId" => $this->input->post("d_id"),
            "blockId" => $this->input->post("block_id"),
            "offerInstitution" => $this->input->post("offer_institution"),
            "sex" => $this->input->post("sex"),
            "age" => $this->input->post("age"),
        );

        $config['base_url'] = base_url() . "donor/searchlist";
        $config['total_rows'] = $this->Donorbasetbl->getSearchListCount($query);
        $config['per_page'] = config_item('donor_search_list_count');
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
            $this->data['next']['link'] = $offset + config_item('donor_search_list_count');
        }

        if ($prev) {
            $this->data['prev']['flg'] = true;
            $link = "";
            if ($offset - config_item('donor_search_list_count')) {
                $link = $offset - config_item('donor_search_list_count');
            }
            $this->data['prev']['link'] = $link;
        }
        $list = $this->Donorbasetbl->getSearchList($query, $offset, config_item('donor_search_list_count'));
        $this->data['list'] = $list;

        $this->data['block_mst'] = $this->Blockmst->getBlockmstByAffiliation($this->session->userdata('affiliation_mst_id'));
        $this->load->vars($this->data);

        $this->load->view('header');
        $this->load->view('donor/searchlist');
        $this->load->view('footer');
    }
}
