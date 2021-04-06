<?php defined('BASEPATH') or exit('No direct script access allowed');

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

/**
 * Image
 *
 * Generates an <img /> element
 *
 * @access    public
 * @param    mixed
 * @return    string
 */
if (!function_exists('img')) {
    function img($src = '')
    {
        if (!is_array($src)) {
            $src = array('src' => $src);
        }
        // If there is no alt attribute defined, set it to an empty string
        if (!isset($src['alt'])) {
            $src['alt'] = '';
        }
        $img = '<img';
        foreach ($src as $k => $v) {

            if ($k == 'src' and strpos($v, '://') === FALSE) {
                if (config_item('branch') === APP_HEAD) {
                    $img .= ' src="' . base_url($v) . '"';
                }elseif (config_item('branch') === APP_CORDINATOR) {
                    $img .= ' src="' . config_item("co_base_url") . $v . '"';
                }elseif (config_item('branch') === APP_TRANSPLANT) {
                    $img .= ' src="' . config_item("tp_base_url") . $v . '"';
                } else {
                    $img .= ' src="' . config_item("common_base_url") . $v . '"';
                }
            } else {
                $img .= " $k=\"$v\"";
            }
        }
        $img .= '/>';
        return $img;
    }
}

/* End of file html_helper.php */
/* Location: ./system/helpers/html_helper.php */
