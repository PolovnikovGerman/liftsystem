<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Shipping_model extends MY_Model
{
    private $error_message='Unknown error. Try later';
    private $empty_htmlcontent='&nbsp;';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_countries_list($options=array()) {
        $this->db->select('*');
        $this->db->from('ts_countries');
        if (isset($options['orderby'])) {
            if (isset($options['direct'])) {
                $this->db->order_by($options['orderby'], $options['direct']);
            } else {
                $this->db->order_by($options['orderby']);
            }
        }
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        $res=$this->db->get()->result_array();
        return $res;
    }

    // Get List of Country States
    public function get_country_states($country_id) {
        $this->db->select('*');
        $this->db->from('ts_states');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('state_code');
        $res=$this->db->get()->result_array();
        return $res;
    }

    // Get Country Full Data
    public function get_country($country_id) {
        $this->db->select('*');
        $this->db->from('ts_countries');
        $this->db->where('country_id', $country_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    // Get State full data
    public function get_state($state_id) {
        $this->db->select('*');
        $this->db->from('ts_states');
        $this->db->where('state_id', $state_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    // Get Rush list
    public function get_rushlist($item_id, $startdate=0) {
        if ($startdate==0) {
//            $resttimeto = mktime(18,00,0);
//            $time1 = time();
//
//            if ($time1 <$resttimeto) {
//                $start=date("Y-m-d");// current date
//            } else {
//                $start_time=$this->_get_business_date(time(), 1, $item_id);
//                $start=date('Y-m-d',$start_time);
//            }
//            /* Check Current Day - may be it is weekend or holiday */
            $start=date("Y-m-d");// current date
            $start_time=strtotime($start);
        } else {
            $start_time=$startdate;
        }
        $start_time=$startdate;
        if ($this->_chk_business_day($start_time, $item_id)==0) {
            while(true) {
                $start_time=$this->_get_business_date($start_time, 1, $item_id);
                if ($this->_chk_business_day($start_time, $item_id)==1) {
                    break;
                }
            }
            $start=date('Y-m-d',$start_time);
        }

        $proof_date=$start_time;

        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id',FALSE);
        $this->db->from("{$this->item_table} i");
        $this->db->join("{$this->venditem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id",'left');
        $this->db->where('i.item_id',$item_id);
        $leads = $this->db->get()->row_array();
        if (!isset($leads['calendar_id'])) {
            $leads['calendar_id']=$this->_get_default_calend();
        }
        /* Rebuild as array */
        if (!isset($leads['item_lead_a'])) {
            $leads['item_lead_a']=0;
        }
        if (!isset($leads['item_lead_b'])) {
            $leads['item_lead_b']=0;
        }
        if (!isset($leads['item_lead_c'])) {
            $leads['item_lead_c']=0;
        }

        $min=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']); $ship_array=array();
        $leads['item_lead_a']=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']);

        if ($leads['item_lead_b']>0) {
            $min=$leads['item_lead_b'];
            $ship_array[]=array(
                'min'=>$min,
                'max'=>$leads['item_lead_a'],
                'price'=>$this->_get_config_value('rush_3days'),
                'rush_term'=>$leads['item_lead_b'].' Day Rush',
            );
        }

        if ($leads['item_lead_c']>0) {
            $min=$leads['item_lead_c'];
            $ship_array[]=array(
                'min'=>$min,
                'max'=>($leads['item_lead_b']==0 ? $leads['item_lead_a'] : $leads['item_lead_b']),
                'price'=>$this->_get_config_value('rush_next_day'),
                'rush_term'=>$leads['item_lead_c'].' Day Rush',
            );
        }

        $ship_array[]=array(
            'min'=>$min,
            'max'=>1000,
            'price'=>0,
            'rush_term'=>'Standard',
        );
        if ($item_id==$this->config->item('custom_id')) {
            $cicle_min=$leads['item_lead_a']+140;
        } else {
            $cicle_min=$leads['item_lead_a']+120;
        }

        $last_date=strtotime(date("Y-m-d", $proof_date) . " +".$cicle_min." days");


        /* Select limitation of data */
        $this->db->select('line_date',FALSE);
        $this->db->from('calendar_lines');
        $this->db->where('calendar_id',$leads['calendar_id']);
        $this->db->where("line_date between '".$proof_date."' and '".$last_date."'");
        $cal=$this->db->get()->result_array();

        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['line_date']);
        }

        // $i=0;$cnt=1; // changed for add current day
        $i=-1;$cnt=0;
        $rush=array();$current_rush=0;
        while ($i <= $cicle_min) {
            $current=0;
            $dat=strtotime(date("Y-m-d", $proof_date) . " +".$cnt." days");
            $day=$dat;
            if (date('w',$dat)!=0 && date('w',$dat)!=6 && !in_array($day, $calend)) {
                $prefix=-1;
                $i++;
                foreach ($ship_array as $row) {
                    $cmp = ($i==0 ? 1 : $i);
                    if ($cmp>=$row['min'] && $i<$row['max']) {
                        $prefix=$row['price'];
                        $rushterm=$row['rush_term'];
                        break;
                    }
                }
                if ($prefix>=0) {
                    if ($i==$leads['item_lead_a']) {
                        $current=1;
                        $current_rush=$prefix;
                    }
                    $rush[]=array(
                        'id'=>$dat.'-'.$prefix,
                        'list'=>date('D M d',$dat).' ('.($prefix==0 ? '' : '$'.$prefix.' - ').''.$rushterm.')',
                        // 'list'=>$rushterm.'-'.($prefix==0 ? '' : '$'.$prefix.' - ').date('M d (D)',$dat),
                        'current'=>$current,
                        'rushterm'=>$rushterm,
                        'date'=>$dat,
                        'price'=>$prefix
                    );
                }
            }
            $cnt++;
        }
        return array('rush'=>$rush,'current_rush'=>$current_rush);
    }

    // Build Rush List for Blank order
    public function get_rushlist_blank($item_id, $startdate=0) {
        if ($startdate==0) {
//            $resttimeto = mktime(18,00,0);
//            $time1 = time();
//
//            if ($time1 <$resttimeto) {
//                $start=date("Y-m-d");// current date
//            } else {
//                $start_time=$this->_get_business_date(time(), 1, $item_id);
//                $start=date('Y-m-d',$start_time);
//            }
            /* Check Current Day - may be it is weekend or holiday */
            $start=date("Y-m-d");// current date
            $start_time=strtotime($start);
        } else {
            $start_time=$startdate;
        }

        if ($this->_chk_business_day($start_time, $item_id)==0) {
            while(true) {
                $start_time=$this->_get_business_date($start_time, 1, $item_id);
                if ($this->_chk_business_day($start_time, $item_id)==1) {
                    break;
                }
            }
            $start=date('Y-m-d',$start_time);
        }

        $proof_date=$start_time;

        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id',FALSE);
        $this->db->from("{$this->item_table} i");
        $this->db->join("{$this->venditem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id",'left');
        $this->db->where('i.item_id',$item_id);
        $leads = $this->db->get()->row_array();
        if (!isset($leads['calendar_id'])) {
            $leads['calendar_id']=$this->_get_default_calend();
        }
        // Rebuild as array
        if (!isset($leads['item_lead_a'])) {
            $leads['item_lead_a']=0;
        }
        /*
        if (!isset($leads['item_lead_b'])) {
            $leads['item_lead_b']=0;
        }
        if (!isset($leads['item_lead_c'])) {
            $leads['item_lead_c']=0;
        }
        */
        $min=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']); $ship_array=array();
        $leads['item_lead_a']=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']);
        /*
        if ($leads['item_lead_b']>0) {
            $min=$leads['item_lead_b'];
            $ship_array[]=array(
                'min'=>$min,
                'max'=>$leads['item_lead_a'],
                'price'=>$this->_get_config_value('rush_3days'),
                'rush_term'=>$leads['item_lead_b'].' Day Rush',
            );
        }

        if ($leads['item_lead_c']>0) {
            $min=$leads['item_lead_c'];
            $ship_array[]=array(
                'min'=>$min,
                'max'=>($leads['item_lead_b']==0 ? $leads['item_lead_a'] : $leads['item_lead_b']),
                'price'=>$this->_get_config_value('rush_next_day'),
                'rush_term'=>$leads['item_lead_c'].' Day Rush',
            );
        }
        */
        $ship_array[]=array(
            'min'=>1,
            'max'=>1000,
            'price'=>0,
            'rush_term'=>'Standard',
        );
        $cicle_min=$leads['item_lead_a']+120;

        $last_date=strtotime(date("Y-m-d", $proof_date) . " +".$cicle_min." days");


        /* Select limitation of data */
        $this->db->select('line_date',FALSE);
        $this->db->from('calendar_lines');
        $this->db->where('calendar_id',$leads['calendar_id']);
        $this->db->where("line_date between '".$proof_date."' and '".$last_date."'");
        $cal=$this->db->get()->result_array();

        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['line_date']);
        }

        $i=-1;$cnt=0;
        $rush=array();$current_rush=0;
        while ($i <= $cicle_min) {
            if ($i<0) {
                $current=1;
            } else {
                $current=0;
            }
            $dat=strtotime(date("Y-m-d", $proof_date) . " +".$cnt." days");
            $day=$dat;
            if (date('w',$dat)!=0 && date('w',$dat)!=6 && !in_array($day, $calend)) {
                $prefix=-1;
                $i++;
                foreach ($ship_array as $row) {
                    $cmp = ($i==0 ? 1 : $i);
                    if ($cmp>=$row['min'] && $i<$row['max']) {
                        $prefix=$row['price'];
                        $rushterm=$row['rush_term'];
                        break;
                    }
                }
                if ($prefix>=0) {
                    /*
                    if ($i==$leads['item_lead_a']) {
                        $current=1;
                        $current_rush=$prefix;
                    }
                     *
                     */
                    $rush[]=array(
                        'id'=>$dat.'-'.$prefix,
                        'list'=>date('D M d',$dat).' ('.($prefix==0 ? '' : '$'.$prefix.' - ').''.$rushterm.')',
                        // 'list'=>$rushterm.'-'.($prefix==0 ? '' : '$'.$prefix.' - ').date('M d (D)',$dat),
                        'current'=>$current,
                        'rushterm'=>$rushterm,
                        'date'=>$dat,
                        'price'=>$prefix
                    );
                }
            }
            $cnt++;
        }
        return array('rush'=>$rush,'current_rush'=>$current_rush);
    }

    private function _get_business_date($startdate,$diffday,$item_id) {

        $this->db->select('item_id, c.calendar_id as calendar_id',FALSE);
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi","vi.vendor_item_id=i.vendor_item_id");
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id", 'left');
        $this->db->where('i.item_id',$item_id);
        $cal = $this->db->get()->row_array();
        $calendar_id=(!isset($cal['calendar_id']) ? $this->_get_default_calend() : $cal['calendar_id']);

        $start=date("Y-m-d",$startdate);
        $last_date=strtotime(date("Y-m-d", strtotime($start)) . " +365 days");
        $this->db->select('line_date as date',FALSE);
        $this->db->from("calendar_lines");
        $this->db->where('calendar_id',$calendar_id);
        $this->db->where("line_date between '".strtotime($start)."' and '".$last_date."'");
        $cal=$this->db->get()->result_array();
        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['date']);
        }

        $dat=strtotime(date("Y-m-d", strtotime($start)));

        $i=1;$cnt=1;
        while ($i <= $diffday) {
            $dat=strtotime(date("Y-m-d", strtotime($start)) . " +".$cnt." days");
            $day=$dat;
            if (date('w',$dat)!=0 && date('w',$dat)!=6 && !in_array($day, $calend)) {
                $i++;
            }
            $cnt++;
        }
        return $dat;
    }

    private function _chk_business_day($start_time, $item_id) {
        /* Extract Date from Start Time */
        $start_date=strtotime(date('Y-m-d',$start_time));
        /* Get Weekdate */
        $dayweek=date('w',$start_date);
        /* Get weekends */
        $this->db->select('item_id, c.calendar_id as calendar_id, c.mon_work, c.tue_work, c.wed_work, c.thu_work, c.fri_work, c.sat_work, c.sun_work',FALSE);
        $this->db->from("{$this->item_table} i");
        $this->db->join("{$this->venditem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id",'left');
        $this->db->where('i.item_id',$item_id);
        $cal = $this->db->get()->row_array();
        if (!isset($cal['calendar_id'])) {
            $cal['calendar_id']=$this->_get_default_calend();
            $this->db->select('mon_work, tue_work, wed_work, thu_work, fri_work, sat_work, sun_work',FALSE);
            $this->db->from("calendars");
            $this->db->where('calendar_id',$cal['calendar_id']);
            $wd = $this->db->get()->row_array();
            $cal['mon_work']=$wd['mon_work'];
            $cal['tue_work']=$wd['tue_work'];
            $cal['wed_work']=$wd['wed_work'];
            $cal['thu_work']=$wd['thu_work'];
            $cal['fri_work']=$wd['fri_work'];
            $cal['sat_work']=$wd['sat_work'];
            $cal['sun_work']=$wd['sun_work'];
        }
        switch ($dayweek) {
            case 0:
                $workday=$cal['sun_work'];
                break;
            case 1:
                $workday=$cal['mon_work'];
                break;
            case 2:
                $workday=$cal['tue_work'];
                break;
            case 3:
                $workday=$cal['wed_work'];
                break;
            case 4:
                $workday=$cal['thu_work'];
                break;
            case 5:
                $workday=$cal['fri_work'];
                break;
            case 6:
                $workday=$cal['sat_work'];
                break;
        }
        if ($workday==1) {
            /* Get Holiday */
            $this->db->select('count(calendar_line_id) cnt');
            $this->db->from('calendar_lines');
            $this->db->where('calendar_id',$cal['calendar_id']);
            $this->db->where('line_date',$start_date);
            $res=$this->db->get()->row_array();
            if ($res['cnt']==0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return $workday;
        }
    }

    private function _get_config_value($config_name) {
        $this->db->select('*');
        $this->db->from('sb_configs');
        $this->db->where('config_name',$config_name);
        $res=$this->db->get()->row_array();
        $out_val=FALSE;
        switch ($res['config_type']) {
            case 'INT':
                $out_val=intval($res['config_value']);
                break;
            case 'FLOAT':
                $out_val=floatval($res['config_value']);
                break;
            case 'DATE':
                $out_val=strtotime($res['config_value']);
                break;
            default:
                $out_val=$res['config_value'];
                break;
        }
        return $out_val;
    }

    public function count_shiprates($items, $shipaddr, $deliv_date, $default_ship_method='') {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $outrate=array();
        $ratekey=array();
        if (isset($shipaddr['item_qty'])) {
            $order_qty=0;
            foreach ($items as $row) {
                $order_qty+=$row['item_qty'];
            }
            if ($order_qty==0) {
                $kf=1;
            } else {
                $kf=($shipaddr['item_qty']/$order_qty);
            }
        } else {
            $kf=1;
        }
        $this->load->config('shipping');
        foreach ($items as $row) {
            $cntdat=$this->get_country($shipaddr['country_id']);
            $carton_qty=((isset($row['cartoon_qty']) && intval($row['cartoon_qty'])>0) ? $row['cartoon_qty'] : $this->config->item('default_inpack'));
            $cartoon_depth=((isset($row['cartoon_depth']) && intval($row['cartoon_depth'])>0) ? $row['cartoon_depth'] : $this->config->item('default_pack_depth'));
            $cartoon_width=((isset($row['cartoon_width']) && intval($row['cartoon_width'])>0) ? $row['cartoon_width'] : $this->config->item('default_pack_width'));
            $cartoon_heigh=((isset($row['cartoon_heigh']) && intval($row['cartoon_heigh'])>0) ? $row['cartoon_heigh'] : $this->config->item('default_pack_heigth'));
            $itemweight=((isset($row['item_weigth']) && floatval($row['item_weigth'])>0) ? $row['item_weigth'] : 0.010);
            $options=array(
                'zip'=>$shipaddr['zip'],
                'numinpack'=>$carton_qty,
                'itemqty'=>ceil($row['item_qty']*$kf),
                'startdeliv'=>$deliv_date,
                'vendor_zip'=>$row['vendor_zipcode'],
                'item_length'=>$cartoon_depth,
                'item_width'=>$cartoon_width,
                'item_height'=>$cartoon_heigh,
                'ship'=>'',
                'weight' =>$itemweight,
                'cnt_code'=>$cntdat['country_iso_code_2'],
            );

            $out=calculate_shipcost($options);

            if (!$out['result']) {
                $res['msg']=$out['error'].' - '.$out['error_code'];
                return $res;
            }
            $ship=$out['ship'];
            $codearray= array_keys($ship);

            if ($default_ship_method=='') {
                if (isset($ship['GND'])) {
                    $ship['deliv']=$ship['GND']['DeliveryDate'];
                    $ship['GND']['current']=1;
                } elseif (isset($ship['UPSStandard'])) {
                    $ship['deliv']=$ship['UPSStandard']['DeliveryDate'];
                    $ship['UPSStandard']['current']=1;
                } elseif (isset ($ship['UPSExpedited'])) {
                    $ship['deliv']=$ship['UPSExpedited']['DeliveryDate'];
                    $ship['UPSExpedited']['current']=1;
                } elseif (isset($ship['UPSSaver'])) {
                    $ship['deliv']=$ship['UPSSaver']['DeliveryDate'];
                    $ship['UPSSaver']['current']=1;
                }
            } else {
                $shiddeliv=0;
                foreach ($codearray as $coderow) {
                    if ($ship[$coderow]['ServiceName']==$default_ship_method) {
                        $ship[$coderow]['current']=1;
                        $shiddeliv=$ship[$coderow]['DeliveryDate'];
                    }
                }
                if ($shiddeliv!==0) {
                    $ship['deliv']=$shiddeliv;
                } else {
                    if (isset($ship['GND'])) {
                        $ship['deliv']=$ship['GND']['DeliveryDate'];
                        $ship['GND']['current']=1;
                    } elseif (isset($ship['UPSStandard'])) {
                        $ship['deliv']=$ship['UPSStandard']['DeliveryDate'];
                        $ship['UPSStandard']['current']=1;
                    } elseif (isset ($ship['UPSExpedited'])) {
                        $ship['deliv']=$ship['UPSExpedited']['DeliveryDate'];
                        $ship['UPSExpedited']['current']=1;
                    } elseif (isset($ship['UPSSaver'])) {
                        $ship['deliv']=$ship['UPSSaver']['DeliveryDate'];
                        $ship['UPSSaver']['current']=1;
                    }
                }
            }

            $itemdat=array(
                'charge_perorder'=>(isset($row['charge_perorder']) ? $row['charge_perorder'] : 0),
                'charge_pereach'=>(isset($row['charge_pereach']) ? $row['charge_pereach'] : 0),
            );
            /* Recalc Rates */
            $shiplast = recalc_rates($ship,$itemdat,$row['item_qty'],$cntdat['country_iso_code_2'], $shipaddr['country_id']);

            foreach ($shiplast as $key=>$row) {
                if ($key!='deliv') {
                    if (!in_array($key , $ratekey)) {
                        array_push($ratekey, $key);
                        $outrate[]=array(
                            'ServiceName'=>$row['ServiceName'],
                            'Rate'=>0,
                            'DeliveryDate'=>0,
                            'current'=>$row['current'],
                        );
                        $srchkey=count($outrate)-1;
                    } else {
                        $srchkey=array_search($key, $ratekey);
                    }
                    if ($outrate[$srchkey]['DeliveryDate']<$row['DeliveryDate']) {
                        $outrate[$srchkey]['DeliveryDate']=$row['DeliveryDate'];
                    }
                    $outrate[$srchkey]['Rate']+=$row['Rate'];
                }
            }
        }
        $res['result']=$this->success_result;
        $res['ships']=$outrate;
        return $res;
    }


//    public function get_ship_methods($country_id,$option='all') {
//
//        $this->db->select('m.shipping_method_id, m.shipping_method_name, m.ups_code, m.default_rate, m.minimal_rate,
//            m.shipping_method_available, zm.method_dimens,zm.method_percent');
//        $this->db->from('sb_countries cnt');
//        $this->db->join('sb_shipzone_methods zm','cnt.zone_id=zm.shipzone_id');
//        $this->db->join('sb_shipping_methods m','m.shipping_method_id=zm.method_id');
//        $this->db->where('cnt.country_id',$country_id);
//        if ($option=='not_null') {
//            $this->db->where('m.default_rate != ',0);
//            $this->db->where('m.minimal_rate != ',0);
//        }
//        $this->db->order_by('m.sort');
//        $result = $this->db->get()->result_array();
//        return $result;
//    }
//
//    public function get_stateups($statecode, $cntcode) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        $this->db->select('s.state_id, s.state_name, c.country_id, c.country_name');
//        $this->db->from('ts_states s');
//        $this->db->join('ts_countries c','c.country_id=s.country_id');
//        $this->db->where('c.country_iso_code_2', $cntcode);
//        $this->db->where('s.state_code', $statecode);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['state_id'])) {
//            $out['msg']='State Not Found';
//        } else {
//            $out['result']=$this->success_result;
//            $out['state_id']=$res['state_id'];
//            $out['state_name']=$res['state_name'];
//            $out['country_id']=$res['country_id'];
//            $out['country_name']=$res['country_name'];
//        }
//        return $out;
//    }

    private function _get_default_calend() {
        $this->db->select('calendar_id');
        $this->db->from('calendars');
        $this->db->order_by('calendar_id');
        $res=$this->db->get()->row_array();
        return $res['calendar_id'];
    }

//    public function track_codes() {
//        $datechk=new DateTime('NOW');
//        $datechk->modify('-12 hours');
//        $datelimit=new DateTime();
//        $datelimit->setDate('2015', '12', '31');
//        $this->db->select('o.order_num, o.order_id, s.order_shipaddr_id,  p.deliver_service, p.track_code, p.order_shippack_id');
//        $this->db->from('ts_orders o');
//        $this->db->join('ts_order_shipaddres s','s.order_id=o.order_id');
//        $this->db->join('ts_order_shippacks p','p.order_shipaddr_id=s.order_shipaddr_id');
//        $this->db->where('p.delivered',0);
//        $this->db->where('p.deliver_service','UPS');
//        $this->db->where("p.track_code != ''");
//        $this->db->where("(p.track_date = 0 or p.track_date < {$datechk->format('U')} )");
//        $this->db->where('o.order_date > ', $datelimit->format('U'));
//        $this->db->order_by('o.order_num','desc');
//        $this->db->limit(10);
//        $res=$this->db->get()->result_array();
//        // Prepare Track lib
//        $this->load->library('United_parcel_service');
//        $upsserv=new United_parcel_service();
//        foreach ($res as $row) {
//            $tracking=$upsserv->trackpackage(strtoupper($row['track_code']));
//            if ($tracking['result']==FALSE) {
//                echo 'Track Code '.$row['track_code'].' Error '.$tracking['msg'].PHP_EOL;
//            } else {
//                $tracklog=$tracking['tracklog'];
//                $deliver=1;
//                $delivtime=0;
//                echo 'Check Order '.$row['order_num'].PHP_EOL;
//                foreach ($tracklog as $trrow) {
//                    if ($trrow['status']!='DELIVERED') {
//                        $deliver=0;
//                        break;
//                    } else {
//                        if ($trrow['date']>$delivtime) {
//                            $delivtime=$trrow['date'];
//                        }
//                    }
//                }
//                if ($deliver==0) {
//                    $this->db->set('track_date', time());
//                    $this->db->where('order_shippack_id', $row['order_shippack_id']);
//                    $this->db->update('ts_order_shippacks');
//                } else {
//                    // All packages delivered
//                    $this->db->set('track_date', time());
//                    $this->db->set('delivered', $delivtime);
//                    $this->db->set('delivery_address', $tracklog[0]['address']);
//                    $this->db->where('order_shippack_id', $row['order_shippack_id']);
//                    $this->db->update('ts_order_shippacks');
//                    // Log
//                    $this->db->where('order_shippack_id', $row['order_shippack_id']);
//                    $this->db->delete('ts_shippack_tracklogs');
//                    foreach ($tracklog as $lrow) {
//                        $this->db->set('package_num', $lrow['package_num']);
//                        $this->db->set('status', $lrow['status']);
//                        $this->db->set('date', $lrow['date']);
//                        $this->db->set('address', $lrow['address']);
//                        $this->db->set('order_shippack_id', $row['order_shippack_id']);
//                        $this->db->insert('ts_shippack_tracklogs');
//                    }
//                    // Update Order
//                    $this->db->select('count(p.order_shippack_id) as cnt');
//                    $this->db->from('ts_orders o');
//                    $this->db->join('ts_order_shipaddres s','s.order_id=o.order_id');
//                    $this->db->join('ts_order_shippacks p','p.order_shipaddr_id=s.order_shipaddr_id');
//                    $this->db->where('o.order_id', $row['order_id']);
//                    $cntall=$this->db->get()->row_array();
//
//                    $this->db->select('count(p.order_shippack_id) as cnt, max(p.delivered) as delivdate');
//                    $this->db->from('ts_orders o');
//                    $this->db->join('ts_order_shipaddres s','s.order_id=o.order_id');
//                    $this->db->join('ts_order_shippacks p','p.order_shipaddr_id=s.order_shipaddr_id');
//                    $this->db->where('o.order_id', $row['order_id']);
//                    $cntarv=$this->db->get()->row_array();
//
//                    if ($cntall['cnt']==$cntarv['cnt']) {
//                        $this->db->set('deliverydate', $cntarv['delivdate']);
//                        $this->db->where('order_id', $row['order_id']);
//                        $this->db->update('ts_orders');
//                        echo 'Order '.$row['order_num'].' Updated'.PHP_EOL;
//                    }
//                }
//            }
//        }
//    }

}