<?php
include __DIR__.'/core.php';

session_start();

$view = null;

if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] != true) {
    $_SESSION['before_login'] = $_REQUEST;
    $view = 'core.login';
} else {
    if(isset($_SESSION['before_login'])) {
        foreach($_SESSION['before_login'] as $k => $v)
            $_REQUEST[$k] = $v;
        unset($_SESSION['before_login']);
    } else if($view == 'core.login') {
        $view = 'core.index';
    }
}

if(empty($view))
    $view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'core.index';

Facade::Run($view, null, ROOT_PATH, ROOT_URL);
