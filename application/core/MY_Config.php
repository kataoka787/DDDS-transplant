<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Config Class
 *
 *
 * @package      CodeIgniter
 * @subpackage   Libraries
 * @author       xpressionEngine Dev Team
 * @category     Loader
 * @link         http://codeigniter.com/user_guide/libraries/loader.html
 */

class MY_Config extends CI_Config
{
    public function __construct()
    {
        parent::__construct();

        if (isset($_SERVER['SERVER_NAME'])) {
            switch ($_SERVER['SERVER_NAME']) {
                case HQ_DOMAIN:
                    $branch = APP_HEAD;
                    $baseUrl = config_item("hq_base_url");
                    $cookieDomain = HQ_DOMAIN;
                    $this->set_item("sub_branch", explode('/', $_SERVER['REQUEST_URI'])[1]);
                    break;
                case CO_DOMAIN:
                    $branch = APP_CORDINATOR;
                    $baseUrl = config_item("co_base_url");
                    $cookieDomain = CO_DOMAIN;
                    break;
                case TP_DOMAIN:
                    $branch = APP_TRANSPLANT;
                    $baseUrl = config_item("tp_base_url");
                    $cookieDomain = TP_DOMAIN;
                    break;
            }
            /* Set config */
            $this->set_item('base_url', $baseUrl);
            $this->set_item('branch', $branch);
            $this->set_item('cookie_domain', $cookieDomain);
        } elseif (php_sapi_name() === "cli") { /* Check if cli request? */
            /* Set config */
            $this->set_item('base_url', config_item("hq_base_url"));
            $this->set_item('branch', APP_HEAD);
        }
    }
}
