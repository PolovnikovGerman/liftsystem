<?php
define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);

class Npigw
{
    private $_CI;

    function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->config->load('npigw_config');
        $this->login['security_key'] = $this->_CI->item('gw_apikey');

    }
}