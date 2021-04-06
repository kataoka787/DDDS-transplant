<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * User Agent Class for Japanese
 *
 * @package
 * @subpackage Libraries
 * @category Validation
 * @author Copyright (c) 2011, AIDREAM.
 * @link
 */
class MY_User_agent extends CI_User_agent
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function is_smartPhone()
    {
        $branch = config_item("branch");
        if ($branch === APP_CORDINATOR) {
            if (strtolower(parent::agent_string()) == 'smartphone.ddd') {
                return true;
            } else {
                return true;
            }
        } else if ($branch === APP_TRANSPLANT || $branch === APP_USER_DATA) {
            if (preg_match('/android/', strtolower(parent::agent_string())) || preg_match('/iphone/', strtolower(parent::agent_string()))) {
                return true;
            } else {
                return false;
            }
        }
    }

}

// END MY User Agent Class

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */
