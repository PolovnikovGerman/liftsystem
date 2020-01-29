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

if (!function_exists('getDatesByWeek')) {
    function getDatesByWeek($_week_number, $_year = null)
    {
        $year = $_year ? $_year : date('Y');
        $week_number = sprintf('%02d', $_week_number);
        $date_base = strtotime($year . 'W' . $week_number . '1 00:00:00');
        $date_limit = strtotime($year . 'W' . $week_number . '7 23:59:59');
        return array('start_week' => $date_base, 'end_week' => $date_limit);
    }
}

if (!function_exists('get_json_param')) {
    function get_json_param($json_string, $param_name, $default=false )
    {
        $json_string = (array) json_decode($json_string);
        //  $this->quick_log($json_string[ $param_name ]);

        if( isset($json_string[ $param_name ])  && $json_string[ $param_name ])
            return $json_string[$param_name];
        else
            return $default;

    }
}

if (!function_exists('valid_email_address')) {
    function valid_email_address($email) {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/',
                    str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }
}

if (!function_exists('valid_url')) {
    function valid_url($url)
    {
        //return (!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
        return (!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
    }
}

if (!function_exists('getuploadid')) {
    function getuploadid() {
        return uniq_link(12,'digits');
    }
}

if (!function_exists('extract_filename')) {
    function extract_filename($filename){
        if(!$filename) return false;
        $result=array();
        $index = mb_strrpos($filename, '.');
        if($index){
            $result['ext']  = mb_substr($filename, $index+1);
            $result['name'] = mb_substr($filename, 0, $index);
            return $result;
        }
        return false;
    }
}

if (!function_exists('profit_bgclass')) {
    function profit_bgclass($profit_perc) {
        $profit_perc=round($profit_perc,0);
        $out_class='';
        if ($profit_perc<=0) {
            $out_class='black';
        } elseif($profit_perc>0 && $profit_perc<10) {
            $out_class='maroon';
        } elseif ($profit_perc>=10 && $profit_perc<20) {
            $out_class='red';
        } elseif ($profit_perc>=20 && $profit_perc<30) {
            $out_class='orange';
        } elseif ($profit_perc>=30 && $profit_perc<40) {
            $out_class='white';
        } elseif ($profit_perc>=40) {
            $out_class='green';
        }
        return $out_class;
    }
}

if (!function_exists('openfile')) {
    function openfile($url, $filename) {
        $filenamedetails = $this->extract_filename($filename);
        switch ($filenamedetails['ext']) {
            case 'jpg':
                header('Content-type: images/jpeg');
                break;
            case 'docx':
            case 'doc':
                header('Content-type: application/msword');
                break;
            case 'xls':
                header('Content-type: application/vnd.ms-excel');
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            default :
                header('Content-Type: application/octet-stream');
                break;
        }
        $url = base_url() . $url;
        // We'll be outputting a PDF
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        // The PDF source is in original.pdf
        readfile($url);
    }
}

if (!function_exists('orderProfitClass')) {
    function orderProfitClass($profit_perc) {
        $profit_class='';
        if (round($profit_perc,0)<=0) {
            $profit_class='black';
        } elseif ($profit_perc>0 && $profit_perc<10) {
            $profit_class='moroon';
        } elseif ($profit_perc>=10 && $profit_perc<20) {
            $profit_class='red';
        } elseif ($profit_perc>=20 && $profit_perc<30) {
            $profit_class='orange';
        } elseif ($profit_perc>=30 && $profit_perc<40) {
            $profit_class='white';
        } elseif ($profit_perc>=40) {
            $profit_class='green';
        }
        return $profit_class;
    }
}

if (!function_exists('leadProfdocOut')) {
    function leadProfdocOut($profdocs, $edit=0) {
        $ci=&get_instance();
        
        $proofview='<div class="proofs_content">';
        $numpor=0;
        $numdocs=count($profdocs);
        $numpp=0;
        $opendiv=1;        
        foreach ($profdocs as $row) {               
            $row['edit']=$edit;
            if ($row['approved']==0) {
                $proofview.=$ci->load->view('leadorderdetails/artwork_proofdocsrc_view', $row,TRUE);
            } else {
                $proofview.=$ci->load->view('leadorderdetails/artwork_proofdocapprov_view', $row,TRUE);
            }                        
            $numpor+=1;
            $numpp++;
            if ($numpor==5) {
                $numpor=0;
                $proofview.='</div>';
                $opendiv=0;
                if ($numpp<$numdocs) {
                    $proofview.='<div class="proofs_content">';
                    $opendiv=1;
                }
            }
        }
        if ($opendiv==1) {
            $proofview.='</div>';
        }
        return $proofview;
    }
}

if (!function_exists('creditcard_format')) {
    function creditcard_format($ccnum) {        
        $cardnum=str_replace('-', '', str_replace(' ','',$ccnum));
        $out='';
        for ($i=0; $i<1000; $i++) {
            $part=  substr($cardnum, 0, 4);
            if (strlen($part)==0) {
                $out=substr($out,0,-1);
                break;
            }
            $out.=$part.'-';
            $cardnum=substr($cardnum, 4);
        }
        return $out;
    }
}
?>