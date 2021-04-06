<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * My Loader Class
 *
 * Override Loader Class
 */
class MY_Loader extends CI_Loader
{
    public $template_path = "";

    public function __construct()
    {
        parent::__construct();
        $this->template_path = key($this->_ci_view_paths);
    }

    /**
     * Auto detect view folder by branch
     *
     * @param string $view
     * @param array $vars
     * @param boolean $return
     * @return function $this->_ci_load
     */
    public function view($view, $vars = array(), $return = false)
    {
        if (in_array(explode("/", $view)[0], config_item("common_view"))) {
            $view = "common/$view";
            if (config_item("branch") === APP_CORDINATOR && file_exists($this->template_path . $view . '_mobile.php')) {
                $view .= "_mobile";
            }
        } else {
            switch (config_item("branch")) {
                case APP_HEAD:
                    $view = "headquarter/$view";
                    break;
                case APP_CORDINATOR:
                    $view = "cordinator/$view";
                    if (file_exists($this->template_path . $view . '_mobile.php')) {
                        $view .= '_mobile';
                    }
                    break;
                case APP_TRANSPLANT:
                    $view = "transplant/$view";
                    break;
            }
        }
        return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_prepare_view_vars($vars), '_ci_return' => $return));
    }
}
