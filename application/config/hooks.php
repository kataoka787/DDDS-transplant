<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|    https://codeigniter.com/user_guide/general/hooks.html
|
 */

switch (config_item("branch")) {
    case APP_HEAD:
        $class = 'Nw_MyClasses';
        break;
    case APP_CORDINATOR:
        $class = 'Co_MyClasses';
        break;
    case APP_TRANSPLANT:
        $class = 'Tp_MyClasses';
        break;
}

$hook['post_controller_constructor'][] = array(
    'class'    => $class,
    'function' => 'writeAccessLog',
    'filename' => $class . '.php',
    'filepath' => 'hooks',
);

$hook['post_controller_constructor'][] = array(
    'class'    => $class,
    'function' => 'loginCheck',
    'filename' => $class . '.php',
    'filepath' => 'hooks',
);

$hook['post_controller_constructor'][] = array(
    'class'    => $class,
    'function' => 'enable_profiler',
    'filename' => $class . '.php',
    'filepath' => 'hooks',
);

$hook['post_controller'][] = array(
    'class' => 'Log_Query',
    'function' => 'run',
    'filename' => 'Log_query.php',
    'filepath' => 'hooks'
);