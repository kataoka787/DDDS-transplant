<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Co_MyClasses
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
        $no_check = $ci_config['co_no_login_check'];
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
    }

    public function writeAccessLog()
    {

        $CI = &get_instance();
        $session = $CI->session->userdata;

        // 2020/10/14 Start convert 2x->3x
        // $ip_address = $session['ip_address'];
        // $user_agent = $session['user_agent'];
        $ip_address = $CI->input->ip_address();
        $user_agent = $CI->agent->agent_string();
        // 2020/10/14 End convert 2x->3x

        $url = $_SERVER['REQUEST_URI'];
        $post = "";
        $get = "";

        $account_type_mst_id = 0;
        $affiliation_mst_id = 0;
        $account_tbl_id = 0;
        $d_id = "";

        foreach ($_POST as $key => $val) {
            if (!is_array($val)) {
                $post .= $key . "=" . $val . "\n";
            } else {
                $flg = false;
                foreach ($val as $key2 => $val2) {
                    if (!$flg) {
                        $post .= $key . "=" . $val2;
                        $flg = true;
                    } else {
                        $post .= "," . $val2;
                    }
                }
                $post .= "\n";
            }
        }
        foreach ($_GET as $key => $val) {
            $get .= $key . "=" . $val . "\n";
        }

        if (array_key_exists('account', $session)) {
            if (array_key_exists('accountId', $session["account"])) {
                $account_tbl_id = $CI->session->userdata('account')->accountId;
        }

            if (array_key_exists('affiliation_mst_id', $session["account"])) {
                $affiliation_mst_id = $CI->session->userdata('account')->affiliation_mst_id;
        }

            if (array_key_exists('account_type_mst_id', $session["account"])) {
                $account_type_mst_id = $CI->session->userdata('account')->account_type_mst_id;
            }
        }

        if (array_key_exists('d_id', $session)) {
            $d_id = $session['d_id'];
        }

        $insert["account_tbl_id"] = $account_tbl_id;
        $insert["affiliation_mst_id"] = $affiliation_mst_id;
        $insert["account_type_mst_id"] = $account_type_mst_id;
        $insert["d_id"] = $d_id;
        $insert["url"] = $url;
        $insert["ip_address"] = $ip_address;
        $insert["user_agent"] = $user_agent;
        $insert["get_param"] = $get;
        $insert["post_param"] = $post;

        $CI->Accesslogtbl->insertAccessLogTbl($insert);
    }

} //endofclass

/* End of file MyClasses.php */
/* Location: ./application/hooks/MyClasses.php */
