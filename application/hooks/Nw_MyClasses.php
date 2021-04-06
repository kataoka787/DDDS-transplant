<?php defined('BASEPATH') or exit('No direct script access allowed');

class Nw_MyClasses
{
    /**
     * includes the directory application\my_classes\ in your includes directory
     */
    public function enable_profiler()
    {
        // 開発時に自動的にプロファイラを有効にする
        if (ENVIRONMENT == 'development') {
            $CI = &get_instance();
            $ci_config = $CI->config->config;
            $no_profiler = $ci_config['no_profiler'];
            $class = $CI->router->class;
            $flg = true;
            foreach ($no_profiler as $key => $val) {
                if ($class == $val) {
                    $flg = false;
                    break;
                }
            }

            if ($flg) {
                $CI->output->enable_profiler(true);
            }
        }
    }

    public function loginCheck()
    {
        $CI = &get_instance();
        $class = $CI->router->class;
        $method = $CI->router->method;
        $ci_config = $CI->config->config;
        $no_check = $ci_config['no_login_check'];
        $check_flg = true;

        foreach ($no_check as $key => $val) {
            if (!is_array($val)) {
                if ($class == $val) {
                    $check_flg = false;
                    break;
                }
            } else {
                foreach ($val as $key2 => $val2) {
                    if ($class == $key2) {
                        if (in_array($method, $val2)) {
                            $check_flg = false;
                            break;
                        }
                    }
                }
            }
        }
        if ($check_flg) {
            if (!$CI->session->userdata('is_login')) {
                redirect(base_url());
            }
        }

        if ($CI->uri->segment(1) == 'admin') {
            if (!$CI->session->userdata('admin_flg')) {
                redirect('menu');
            }
        }

        if (in_array($class, $ci_config['check_user_work'])) {
            if (!in_array(WORK_FOLLOW_UP, explode(",", $CI->session->userdata("account")->work_id))) {
                redirect('/menu');
            }
        }
    }

    public function writeAccessLog()
    {
        $CI = &get_instance();
        $class = $CI->router->class;
        if ($class != 'cron' && $class != 'init') {
            $flattenedArray = array();
            flattenArray($_POST, $flattenedArray);
            $post = "";
            foreach ($flattenedArray as $key => $value) {
                if (is_array($value)) {
                    $post .= "$key=" . implode(",", $value);
                } else {
                    $post .= "$key=$value";
                }
                $post .= "\n";
            }

            $get = "";
            foreach ($_GET as $key => $val) {
                $get .= "$key=$val\n";
            }

            $account = $CI->session->userdata("account");
            $insert = array(
                "account_tbl_id" => $account->accountId ?? 0,
                "affiliation_mst_id" => $account->affiliation_mst_id ?? 0,
                "account_type_mst_id" => $account->account_type_mst_id ?? 0,
                "d_id" => empty($CI->session->userdata("d_id")) ? 0 : $CI->session->userdata("d_id"),
                "url" => $_SERVER['REQUEST_URI'],
                "ip_address" => $CI->input->ip_address(),
                "user_agent" => $CI->agent->agent_string(),
                "get_param" => $get,
                "post_param" => $post,
            );

            $CI->Accesslogtbl->insertAccessLogTbl($insert);
        }
    }

} //endofclass

/* End of file MyClasses.php */
/* Location: ./application/hooks/MyClasses.php */
