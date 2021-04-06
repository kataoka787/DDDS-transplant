<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * My Router Class
 *
 * Override Router Class
 */
class MY_Router extends CI_Router
{
    /**
     * Set auth to default controller
     *
     * @return void
     */
    protected function _set_default_controller()
    {
        $this->set_directory("common");
        $this->set_class("auth");
        $this->set_method("index");

        // Assign routed segments, index starting from 1
        $this->uri->rsegments = array(
            1 => "auth",
            2 => "index",
        );
    }

    /**
     * Set folder and change segment depend on branch
     *
     * @param array $segments
     * @return array $segments
     */
    protected function _validate_request($segments)
    {
        switch (config_item('branch')) {
            case APP_HEAD:
                /* Check if headquarter sub-system */
                if (in_array($segments[0], config_item("headquarter_sub_system"))) {
                    $this->set_directory("headquarter/$segments[0]/");
                    array_shift($segments);
                } else {
                    $this->set_directory("headquarter");
                }
                break;
            case APP_CORDINATOR:
                $this->set_directory("cordinator");
                break;
            case APP_TRANSPLANT:
                $this->set_directory("transplant");
                break;
        }
        empty($segments) || in_array(lcfirst($segments[0]), config_item("common_controller")) && $this->set_directory("common");
        return $segments;
    }
}
