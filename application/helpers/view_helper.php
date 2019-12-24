<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('MoneyOutput')) {
    function MoneyOutput($total, $decimal = 2, $thousdelim = ',')
    {
        if ($total < 0) {
            $output = '-$';
        } else {
            $output = '$';
        }
        $output .= number_format(abs($total), $decimal, '.', $thousdelim);
        return $output;
    }
}

if (!function_exists('QTYOutput')) {
    function QTYOutput($qty, $decimal = 0, $thousdelim = ',', $show_positiv = 0)
    {
        if ($qty < 0) {
            $output = '-';
        } else {
            $output = '';
            if ($show_positiv == 1) {
                $output = '+';
            }
        }
        if (abs($qty) >= 10000) {
            $output .= number_format(abs($qty), $decimal, '.', $thousdelim);
        } else {
            if ($decimal > 0 && fmod(abs($qty), 1) == 0) {
                $decimal = 0;
            }
            $output .= number_format(abs($qty), $decimal, '.', '');

        }
        return $output;
    }
}

if (!function_exists('get_timerestrict')) {
    function get_timerestrict($restict_id) {
        $dat=date('Y-m-d');
        switch ($restict_id) {
            case 1:
                $datstr=$dat." 09:00:00";
                $datbgn=strtotime($datstr);
                $datstr=$dat." 17:00:00";
                $datend=strtotime($datstr);
                break;
            case 2:
                $datstr=$dat." 09:00:00";
                $datbgn=strtotime($datstr);
                $datstr=$dat." 19:00:00";
                $datend=strtotime($datstr);
                break;
            case 3:
                $datstr=$dat." 07:00:00";
                $datbgn=strtotime($datstr);
                $datstr=$dat." 19:00:00";
                $datend=strtotime($datstr);
                break;
            case 4:
                $datstr=$dat." 08:00:00";
                $datbgn=strtotime($datstr);
                $datstr=$dat." 20:00:00";
                $datend=strtotime($datstr);
                break;
            default :
                $datstr=$dat." 00:00:00";
                $datbgn=strtotime($datstr);
                $datstr=$dat." 23:59:59";
                $datend=strtotime($datstr);
                break;
        }
        return array('begin'=>$datbgn, 'end'=>$datend);
    }
}

if (!function_exists('getsitejsversion')) {
    function getsitejsversion() {
        $ci = &get_instance();
        return $ci->config->item('js_version');
    }
}

if (!function_exists('getsitecssversion')) {
    function getsitecssversion() {
        $ci = &get_instance();
        return $ci->config->item('css_version');
    }
}

if (!function_exists('firephplog')) {
    function firephplog($Object, $Label = null) {
        $ci = & get_instance();
        $logip=$ci->config->item('logip');
        $userip=$ci->input->ip_address();
        if (in_array($userip, $logip)) {
            if (!empty($Label)) {
                $ci->firephp->log($Object, $Label);
            } else {
                $ci->firephp->log($Object);
            }
        }
    }
}

if (!function_exists('createPath')) {
    function createPath($path) {
        $chkpath = BASEPATH.'..'.$path;
        if (is_dir($chkpath)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = createPath($prev_path);
        return ($return && is_writable(BASEPATH.'..'.$prev_path)) ? mkdir($chkpath, 0755, true) : false;
    }
}

if (!function_exists('ifset')) {
    function ifset($var, $key, $default = '') {
        if (is_array($var)) {return isset($var[$key]) ? $var[$key] : $default;}
        else if (is_object($var)) {return isset($var->$key) ? $var->$key : $default;}
        return $default;
    }
}

if (!function_exists('viewhtmlempty')) {
    function viewhtmlempty($var) {
        if (empty($var)) {
            $var='&nbsp;';
        }
        return $var;
    }
}

if (!function_exists('profitClass')) {
    function profitClass($profit) {
        if ($profit===NULL) {
            $profitClass = 'projectprofit';
        } else {
            if ($profit<=0) {
                $profitClass='blackprofit';
            } elseif ($profit<10) {
                $profitClass='darkredprofit';
            } elseif ($profit<20) {
                $profitClass='redprofit';
            } elseif ($profit<30) {
                $profitClass='orangeprofit';
            } elseif ($profit<40) {
                $profitClass='whiteprofit';
            } elseif ($profit<50) {
                $profitClass='greenprofit';
            } else {
                $profitClass='grassgreenprofit';
            }
        }
        return $profitClass;
    }
}

if (!function_exists('usersession')) {
    function usersession() {
        @session_start();
        $args = func_get_args();
        if (count($args) > 0) {
            $key = $args[0];
            if (count($args) > 1) {
                $value = $args[1];
                if ($value === NULL) {
                    unset($_SESSION[$key]);
                } else {
                    $_SESSION[$key] = $value;
                }
            }
            return ifset($_SESSION, $key, NULL);
        } else {
            return FALSE;
        }
    }
}

if (!function_exists('uniq_link')) {
    function uniq_link($length=10,$type='any') {
        if ($type=='any') {
            $possible_letters = "abcdefghijklmnopqrstuzwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        } elseif ($type=='digits') {
            $possible_letters = "0123456789";
        } elseif ($type=='chars') {
            $possible_letters = "abcdefghijklmnopqrstuzwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }

        $letter_len = strlen($possible_letters)-1;
        $string = "";
        for($i = 0; $i < $length; $i++)
            $string .= $possible_letters[rand(0, $letter_len)];

        return $string;
    }

}

?>