<?php
/**
 * CodeIgniter
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license    https://opensource.org/licenses/MIT    MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter Form Helpers
 *
 * @package        CodeIgniter
 * @subpackage    Helpers
 * @category    Helpers
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Override form_input function to remove space after form_input
 */
if (!function_exists('form_input')) {
    /**
     * Text Input Field
     *
     * @param    mixed
     * @param    string
     * @param    mixed
     * @return    string
     */
    function form_input($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'text',
            'name' => is_array($data) ? '' : $data,
            'value' => $value,
        );

        // return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
        return '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " />";
    }
}

// ------------------------------------------------------------------------

/**
 * Override form_checkbox function to remove space after form_checkbox
 */
if (!function_exists('form_checkbox')) {
    /**
     * Checkbox Field
     *
     * @param    mixed
     * @param    string
     * @param    bool
     * @param    mixed
     * @return    string
     */
    function form_checkbox($data = '', $value = '', $checked = false, $extra = '')
    {
        $defaults = array('type' => 'checkbox', 'name' => (!is_array($data) ? $data : ''), 'value' => $value);

        if (is_array($data) && array_key_exists('checked', $data)) {
            $checked = $data['checked'];

            if ($checked == false) {
                unset($data['checked']);
            } else {
                $data['checked'] = 'checked';
            }
        }

        if ($checked == true) {
            $defaults['checked'] = 'checked';
        } else {
            unset($defaults['checked']);
        }

        // return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
        return '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " />";
    }
}

if (!function_exists("addEmptySelect")) {
    /**
     * Auto add empty option to select list
     *
     * @param array $list
     * @return array $emptySelectAddedArray
     */
    function addEmptySelect($list = array())
    {
        return array("" => "") + $list;
    }
}

// ------------------------------------------------------------------------
