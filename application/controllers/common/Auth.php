<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Auth Class
 * @link /cordinator/auth
 */
class Auth extends CI_Controller
{
    public $branch = null;
    public $redirectPage = "";

    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            "page_title" => config_item('page_login'),
            "id" => "",
            "d_id" => "",
            "pw" => "",
            "error" => false,
        );
        $this->branch = config_item("branch");
        switch ($this->branch) {
            case APP_HEAD:
                $this->data['view'] = 'login';
                $this->redirectPage = 'menu';
                break;
            case APP_CORDINATOR:
                $this->data['view'] = 'login';
                $this->redirectPage = 'donorlist';
                break;
            case APP_TRANSPLANT:
                $this->data['view'] = 'login';
                $this->redirectPage = 'menu';
                break;
        }
    }

    /**
     * Auth index page
     *
     * @return redirect if user already logged in
     * @return login if user has not logged in
     */
    public function index()
    {
        /* Check session (is_login) status */
        $isLogin = $this->session->userdata('is_login');
        $accType = $this->session->userdata('account_type_mst_id');
        /* Check branch and account type */
        switch ($this->branch) {
            case APP_CORDINATOR:
                $isLogin = $isLogin && $accType == ACC_TYPE_CO;
                break;
            case APP_TRANSPLANT:
                $isLogin = $isLogin && $accType == ACC_TYPE_TP;
                break;
        }

        if ($isLogin) {
            redirect($this->redirectPage);
        }

        $this->session->unset_userdata('is_login');
        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view($this->data['view']);
        $this->load->view('footer');
    }

    /**
     * Auth login page
     *
     * @return redirect if login success
     * @return login if login fail
     */
    public function login()
    {
        // Email
        $id = $this->input->post('id');
        // Password
        $pw = $this->input->post('pw');
        // Handle validate and login
        if ($this->form_validation->run('login')) {
            $account = $this->Accounttbl->getAccountByIdPw($id, $pw);
            if ($this->branch === APP_TRANSPLANT && $account === null) {
                $this->data['error'] = true;
            }
            if ($account) {
                if (strtotime(config_item('password_expired'), strtotime($account->password_datetime)) < strtotime(date('Y-m-d'))) {
                    $this->session->set_flashdata('account', $account);
                    redirect('password');
                } else {
                    session_regenerate_id(true);
                    $this->session->set_userdata('is_login', true);
                    $this->session->set_userdata('account_type_mst_id', $account->account_type_mst_id);
                    $this->session->set_userdata('affiliation_mst_id', $account->affiliation_mst_id);
                    $this->session->set_userdata('account', $account);
                    $this->session->set_userdata('admin_flg', $account->admin_flg);

                    if (($this->branch === APP_HEAD && $account->affiliation_mst_id) || $this->branch === APP_CORDINATOR) {
                        $this->session->set_userdata('cordinatorId', $account->cordinatorId);
                        $this->session->set_userdata('cordinator_type_mst_id', $account->cordinator_type_mst_id);
                    } else if ($this->branch === APP_TRANSPLANT) {
                        $this->session->set_userdata('accountId', $account->accountId);
                        $doctor = $this->Doctortbl->getTransplantDoctorOrgansByDoctorId($account->accountId);
                        foreach ($doctor as $val) {
                            $institution_mst_id = $val->institution_mst_id;
                            $internal_organs_mst_ids[] = $val->internal_organs_mst_id;
                        }
                        $this->session->set_userdata('institution_mst_id', $institution_mst_id);
                        $this->session->set_userdata('internal_organs_mst_ids', $internal_organs_mst_ids);
                    }
                    redirect($this->redirectPage);
                }
            }
            $this->data["error_message"] = lang("login");
        }

        $this->load->vars($this->data);
        $this->load->view('header');
        $this->load->view($this->data['view']);
        $this->load->view('footer');
    }

    /**
     * Auth logout page
     *
     * @return redirect to base url
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}
