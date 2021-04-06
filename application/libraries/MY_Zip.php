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
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Zip Compression Class
 *
 * This class is based on a library I found at Zend:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Rick Ellis
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Encryption
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/libraries/zip.html
 */
class MY_Zip extends CI_Zip
{
    /**
     * Download
     *
     * @param    string    $filename    the file name
     * @return    void
     */
    public function download($filename = 'backup.zip')
    {
        if (!preg_match('|.+?\.zip$|', $filename)) {
            $filename .= '.zip';
        }

        get_instance()->load->helper('download');
        $get_zip = $this->get_zip();
        $zip_content = &$get_zip;

        if ($zip_content === false) {
            force_download($filename);
        } else {
            force_download($filename, $zip_content);
        }
    }

    // --------------------------------------------------------------------
}
