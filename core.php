<?php
define("ROOT_PATH", __DIR__);
define("ROOT_URL", "http://kit.kvantorium33.ru/");

spl_autoload_register(function ($class_name) {
    $path = ROOT_PATH."/classes/".$class_name.".php";
    if(file_exists($path))
        require_once $path;
    else {
        $items = explode('\\', strtolower($class_name));
        if(sizeof($items) == 2) {
            $path = ROOT_PATH."/modules/".$items[0]."/".$items[1].".php";
            if(file_exists($path))
                require_once $path;
        }
    }
});

$servername = "localhost";
$username   = "kit";
$password   = "1q2w3e4r";
$dbname     = "kit";

Utils::$db = new MysqliDb($servername, $username, $password, $dbname);
