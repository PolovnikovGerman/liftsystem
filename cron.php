<?php

define('CRONJOB', 1);

$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];


$default_server_name = 'bluetrack.net';
$default_server_addr = '127.0.0.1';



if ($argc > 1 && isset($argv[1])) {
    $_SERVER['PATH_INFO']   = $argv[1];
    $_SERVER['REQUEST_URI'] = $argv[1];

    if(isset($argv[2])){
        $_SERVER['SERVER_NAME'] = $argv[2];
    }else{
        $_SERVER['SERVER_NAME'] = $default_server_name;
    }

    if(isset($argv[3])){
        $_SERVER['SERVER_ADDR'] = $argv[3];
    }else{
        $_SERVER['SERVER_ADDR'] = $default_server_addr;
    }


}
//$_SERVER['SERVER_NAME'] = 'tdbeta.com';
//$_SERVER['SERVER_ADDR'] = '212.150.184.62';

set_time_limit(0);
require_once('index.php');

?>