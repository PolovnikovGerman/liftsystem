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
        $filenamedetails = extract_filename($filename);
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

if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phoneNumber)
    {
        $plusdig = 0;
        if (substr($phoneNumber, 0, 1) == '+') {
            $plusdig = 1;
            $phoneNumber = substr($phoneNumber, 1);
        }
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6);
        $phoneNumber = ($plusdig == 1 ? '+' : '') . $areaCode . '-' . $nextThree . '-' . $lastFour;
        return $phoneNumber;
    }
}

if (!function_exists('getVMDDueDate')) {
    function getVMDDueDate($batch_date, $paymethod) {
        if ($paymethod=='PAYPAL') {
            $duedate=strtotime(date('Y-m-d',$batch_date). " +2 day");
        } else {
            $duedate=strtotime(date('Y-m-d',$batch_date). " +1 day");
        }
        return $duedate;
    }
}

if (!function_exists('getAmexDueDate')) {
    function getAmexDueDate($batch_date,$paymeth) {
        if ($paymeth=='PAYPAL') {
            $duedate=strtotime(date('Y-m-d',$batch_date). " +2 day");
        } else {
            $weekday=date('N',$batch_date);
            $weeknum=date('W',$batch_date);
            $year=date('Y',$batch_date);
            if ($weekday==1) {
                /* Monday */
                $duedate=strtotime($year . 'W' . $weeknum . '5 00:00:00');
            } elseif ($weekday>1 && $weekday<5) {
                /* Tuesday, Wednesday, Thuesday  */
                $batch_date=strtotime(date('Y-m-d',$batch_date). " +7 day");
                $weeknum=date('W',$batch_date);
                $year=date('Y',$batch_date);
                $duedate=strtotime($year . 'W' . $weeknum . '1 00:00:00');
            } else {
                /* Friday, Saturday, Sunday  */
                $batch_date=strtotime(date('Y-m-d',$batch_date). " +7 day");
                $weeknum=date('W',$batch_date);
                $year=date('Y',$batch_date);
                $duedate=strtotime($year . 'W' . $weeknum . '2 00:00:00');
            }
        }
        return $duedate;
    }
}

if (!function_exists('businessdate')) {
    function businessdate($date) {
        $ci=&get_instance();
        $ci->load->model('calendars_model');
        $calendar=$ci->config->item('bank_calendar');
        $holidays=$ci->calendars_model->get_calendar_holidays($calendar);
        for ($i=1; $i<=15;$i++) {
            if (in_array($date, $holidays)) {
                $date=strtotime(date('Y-m-d',$date)."+1day");
            } elseif (date('N',$date)==6) {
                $date=strtotime(date('Y-m-d',$date)."+1day");
            } elseif (date('N',$date)==7) {
                $date=strtotime(date('Y-m-d',$date)."+1day");
            } else {
                break;
            }
        }
        return $date;
    }
}

if (!function_exists('ValidEmail')) {
    function ValidEmail($mail) {
        if (empty($mail)) {
            return FALSE;
        } else {
            return valid_email_address($mail);
        }
    }
}


if (!function_exists('BankDays')) {
    function BankDays($datbgn, $datend, $calendar_id=0) {
        $bank_days = 0;
        $ci=&get_instance();
        if ($calendar_id==0) {
            $def_calendar=$ci->config->item('bank_calendar');
        } else {
            $def_calendar=$calendar_id;
        }

        $ci->load->model('calendars_model');
        $holidays_src=$ci->calendars_model->get_calendar_holidays($def_calendar, $datbgn, $datend);
        $holidays=array();
        foreach ($holidays_src as $row) {
            array_push($holidays, date('Y-m-d',$row));
        }

        $weekends=array(0,6);
        $days = ceil(($datend - $datbgn) / 3600 / 24);
        for ($i = 0; $i <= $days; $i++) {
            $curr = strtotime('+' . $i . ' days', $datbgn);
            if (!in_array(date('Y-m-d',$curr), $holidays) && (!in_array(date('w', $curr), $weekends))) {
                $bank_days++;
            }
        }
        return $bank_days;
    }
}
if (!function_exists('short_number')) {
    function short_number($value, $precesion=1) {
        $base=1000;
        $returnValue = number_format(round($value,0),0);
        if ($value > $base) {
            if ($value<1000000) {
                $returnValue=number_format(round($value/1000,$precesion),$precesion).'K';
            } else {
                $returnValue=number_format(round($value/1000000,$precesion),$precesion).'M';
            }
        }
        return $returnValue;
    }
}
if (!function_exists('getNameFromNumber')) {
    function getNameFromNumber($num) {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }
}
if (!function_exists('dates_diff')) {
    function dates_diff($date_min, $date_max, $type='D') {
        // if (PHP_VERSION_ID)
        // echo date_diff(new DateTime(), new DateTime('1986-01-04 00:00:01'))->days;
        $diff=$date_max-$date_min;
        if ($type=='D') {
            return max(round($diff/(24*60*60)),1);
        } else {
            // Weeks
            return ceil(max(round($diff/(24*60*60)),1)/7);
        }
    }
}
if (!function_exists('getDayOfWeek')) {
    function getDayOfWeek($_week_number, $_year = null,$weekday=1) {
        $year = $_year ? $_year : date('Y');
        $week_number = sprintf('%02d', $_week_number);
        $day_week = strtotime($year . 'W' . $week_number . $weekday.' 00:00:00');
        return $day_week;
    }
}
if (!function_exists('getDatesByMonth')) {
    function getDatesByMonth($_month_number,$_year=null) {
        $year = $_year ? $_year : date('Y');
        $month_number = sprintf('%02d', $_month_number);
        $date_base = strtotime($year . '-' . $month_number . '-01 00:00:00');
        $date_limit=strtotime(date("Y-m-d", ($date_base)) . " +1 month");
        $date_limit=$date_limit-1;
        return array('start_month'=>$date_base, 'end_month'=>$date_limit);
    }
}

if ( ! function_exists('show_403'))
{
    function show_403()
    {
        $_error =& load_class('Exceptions', 'core');
        $heading = $message = 'Forbidden';
        $template = 'error_403';
        $status_code = 403;
        echo $_error->show_error($heading, $message, $template, $status_code);
        exit;
    }
}

?>