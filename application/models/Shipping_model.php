<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Shipping_model extends MY_Model
{
    private $error_message='Unknown error. Try later';
    private $empty_htmlcontent='&nbsp;';
    private $box_empty_weight = 25;

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
            $start=date("Y-m-d");// current date
            $start_time=strtotime($start);
        } else {
            $start_time=$startdate;
        }
        $start_time=$startdate;
        $proof_date = $start_time;
        if ($this->_chk_business_day($start_time, $item_id)==0) {
            $proof_date=$this->_get_business_date($start_time, 1, $item_id);
        }

        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id',FALSE);
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
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
        $i=0;$cnt=1;
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
        // Add 1 business day
        $start_time=$this->_get_business_date($start_time, 1, $item_id);

        $proof_date=$start_time;

        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id',FALSE);
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
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
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
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

    public function count_shiprates($items, $shipaddr, $deliv_date, $brand, $default_ship_method='') {
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
            // $itemweight=((isset($row['item_weigth']) && floatval($row['item_weigth'])>0) ? $row['item_weigth'] : 0.010);
            $itemweight = (ifset($row, 'item_weigth', 0)>0 ? $row['item_weigth'] : $this->box_empty_weight / $carton_qty);
            $options=array(
                'zip'=>$shipaddr['zip'],
                'numinpack'=>$carton_qty,
                'itemqty'=>ceil($row['item_qty']*$kf),
                'startdeliv'=>$deliv_date,
                'vendor_zip'=>$row['vendor_zipcode'],
                'item_length'=>$cartoon_depth,
                'item_width'=>$cartoon_width,
                'item_height'=>$cartoon_heigh,
                'ship'=> array(),
                'weight' =>$itemweight,
                'cnt_code'=>$cntdat['country_iso_code_2'],
                'brand' => $brand,
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
            $shiplast = recalc_rates($ship,$itemdat,$row['item_qty'],$brand, $cntdat['country_iso_code_2'], $shipaddr['country_id']);

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


    public function get_ship_methods($country_id, $brand, $option='all') {

        $this->db->select('m.shipping_method_id, m.shipping_method_name, m.ups_code, m.default_rate, m.minimal_rate,
            m.shipping_method_available, zm.method_dimens,zm.method_percent');
        $this->db->from('sb_countries cnt');
        $this->db->join('sb_shipzone_methods zm','cnt.zone_id=zm.shipzone_id');
        $this->db->join('sb_shipping_methods m','m.shipping_method_id=zm.method_id');
        $this->db->where('zm.brand', $brand);
        $this->db->where('cnt.country_id',$country_id);
        if ($option=='not_null') {
            $this->db->where('m.default_rate != ',0);
            $this->db->where('m.minimal_rate != ',0);
        }
        $this->db->order_by('m.sort');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_stateups($statecode, $cntcode) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->db->select('s.state_id, s.state_name, c.country_id, c.country_name');
        $this->db->from('ts_states s');
        $this->db->join('ts_countries c','c.country_id=s.country_id');
        $this->db->where('c.country_iso_code_2', $cntcode);
        $this->db->where('s.state_code', $statecode);
        $res=$this->db->get()->row_array();
        if (!isset($res['state_id'])) {
            $out['msg']='State Not Found';
        } else {
            $out['result']=$this->success_result;
            $out['state_id']=$res['state_id'];
            $out['state_name']=$res['state_name'];
            $out['country_id']=$res['country_id'];
            $out['country_name']=$res['country_name'];
        }
        return $out;
    }

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

    public function shipcalk_stardate($brand) {
        $this->db->select('min(calcdate) min');
        $this->db->from('sb_shipcalc_log');
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        if (!isset($res['min'])) {
            return time();
        } else {
            return $res['min'];
        }
    }

    public function get_shipzones($options=array()) {
        $this->db->select('*');
        $this->db->from('sb_shipping_zones');
        if (isset($options['zone_name'])) {
            $this->db->where('zone_name',$options['zone_name']);
        }
        $this->db->order_by('zone_id');
        $result = $this->db->get()->result_array();
        return $result;
    }

    /* Ship Zone - shipping methods */
    public function get_shipzone_details($zone_id, $brand) {
        $this->db->select('zm.zonemethod_id as id, shm.shipping_method_name as name, zm.method_percent, zm.method_dimens');
        $this->db->from('sb_shipzone_methods zm');
        $this->db->join('sb_shipping_methods shm','shm.shipping_method_id=zm.method_id');
        $this->db->where('zm.shipzone_id',$zone_id);
        $this->db->where('zm.brand', $brand);
        $this->db->order_by('shm.sort');
        $results=$this->db->get()->result_array();
        return $results;
    }

    public function shipcalc_report($month, $year, $brand) {
        $datstart=  strtotime($year.'-'.$month.'-01');
        $datend = strtotime(date("Y-m-d", $datstart) . " +1 month");
        /* First day of week */
        $datcalendbgn = strtotime('monday this week', $datstart);
        $datcalendend = strtotime('sunday this week', $datend);

        $this->db->select('scl.logdate, count(scl.shipcalclog_id) as cnt');
        $this->db->from('sb_shipcalc_log scl');
        $this->db->where('scl.calcdate >=',$datcalendbgn);
        $this->db->where('scl.calcdate <', $datcalendend);
        $this->db->group_by('scl.logdate');
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->result_array();
        $days=array();
        $start=$datcalendbgn;
        for ($i=1; $i<=60; $i++) {
            $search=date('Y-m-d', $start);
            $clicks=0;
            foreach ($res as $row) {
                if ($row['logdate']==$search) {
                    $clicks=$row['cnt'];
                    break;
                }
            }
            $link='';
            if ($clicks!=0) {
                $link='data-content="/settings/schipcalend_details?d='.$start.'&brand='.$brand.'"';
            }
            $days[]=array(
                'id'=>$start,
                'title'=>date('M j', $start),
                'count'=>($clicks==0 ? '' : $clicks),
                'type'=>(date('N',$start)<6 ? '' : 'weekend'),
                'href'=>$link,
                'active'=>($clicks==0 ? '' : 'active')
            );
            $start=strtotime(date("Y-m-d", $start) . " +1 day");
            if ($start>$datcalendend) {
                break;
            }
        }
        return $days;
    }

    public function get_zones($options=array()) {
        $this->db->select('*');
        $this->db->from('sb_shipping_zones');
        if (isset($options['zone_name'])) {
            $this->db->where('zone_name',$options['zone_name']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            //
        }
        $this->db->order_by('zone_id');
        $result = $this->db->get()->result_array();
        if (count($result)==1) {
            $ret_array=$result[0];
        } elseif (count($result)==0) {
            $ret_array=array();
        } else {
            $ret_array=$result;
        }
        return $ret_array;
    }
    // List of available methods
    public function get_shipmethodlist() {
        $this->db->select('*');
        $this->db->from('sb_shipzone_methods');
        $result=$this->db->get()->result_array();
        return $result;
    }
    // Save data
    public function save_zones_methods_dat($data) {
        foreach ($data as $row) {
            $this->db->set('method_percent',$row['method_percent']);
            $this->db->set('method_dimens',$row['method_dimens']);
            $this->db->where('zonemethod_id',$row['zonemethod_id']);
            $this->db->update('sb_shipzone_methods');
        }
        return TRUE;
    }
    // Get calc details
    public function get_shipcalcdayreport($start, $end, $brand) {
        $this->db->select('scl.calcdate,i.item_number, i.item_name, scl.item_qty, c.country_name');
        $this->db->select('c.country_iso_code_2 as country_code, scl.zip');
        $this->db->select('coalesce(g.city_name,\'\') as usr_city, coalesce(g.country_name,\'\') as usr_country, scl.user_ip',FALSE);
        $this->db->select('coalesce(g.region_code,\'\') as usr_region',FALSE);
        $this->db->from('sb_shipcalc_log scl');
        $this->db->join('sb_items i','i.item_id=scl.item_id');
        $this->db->join('sb_countries c','c.country_id=scl.country_id');
        $this->db->join('sb_geoips g','g.user_ip=scl.user_ip','left');
        $this->db->where('scl.calcdate >=', $start);
        $this->db->where('scl.calcdate <=', $end);
        if ($brand!=='ALL') {
            $this->db->where('scl.brand', $brand);
        }
        $this->db->order_by('scl.calcdate');

        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            $location=(empty($row['usr_country']) ? 'N\A' : $row['usr_country']);
            if (!empty($row['usr_region'])) {
                $location.=', '.$row['usr_region'].',';
            }
            if (!empty($row['usr_city'])) {
                $location.=' '.$row['usr_city'];
            }
            $out[]=array(
                'time'=>date('H:i:s', $row['calcdate']),
                'item'=>$row['item_number'].' '.$row['item_name'],
                'qty'=>$row['item_qty'],
                'country'=>$row['country_name'].'('.$row['country_code'].')',
                'zip'=>$row['zip'],
                'user_ip'=>$row['user_ip'],
                'location'=>$location,
            );
        }
        return $out;
    }

    /* get firsrt letters of Country Names */
    public function get_country_search_templates() {
        $this->db->select('distinct(substr(country_name,1,1)) as template',FALSE);
        $this->db->from('sb_countries');
        $this->db->order_by('template');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_countries_data($filtr,$sort, $direc) {
        $this->db->select('*');
        $this->db->from('sb_countries');
        if (isset($filtr['search_template'])) {
            $this->db->where("substr( country_name, 1, 1 ) = '{$filtr['search_template']}'");
        }
        $this->db->order_by('sort,country_name');
        $result=$this->db->get()->result_array();
        return $result;
    }

    public function update_countries($country_id,$options) {
        $fl_upd=0;
        $this->db->where('country_id',$country_id);
        if (isset($options['shipallow'])) {
            $this->db->set('shipallow',$options['shipallow']);
            $fl_upd=1;
        }
        if ($fl_upd) {
            $this->db->update('sb_countries');
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function update_geoip($ipdata, $user_ip) {
        $usrdata=$this->ipdata_exist($user_ip);
        if (!$usrdata['result']) {
            // Get Code of region
            $this->db->set('user_ip',$ipdata['ip']);
            $this->db->set('country_code',(isset($ipdata['country_code']) ? $ipdata['country_code'] : NULL));
            $this->db->set('country_name',(isset($ipdata['country_name']) ? $ipdata['country_name'] : NULL));
            $this->db->set('city_name',(isset($ipdata['city_name'])  ? $ipdata['city_name'] : NULL ));
            if (isset($ipdata['region_name'])) {
                $this->db->set('region_name',$ipdata['region_name']);
                $this->db->set('region_code',$this->get_statecode_byname($ipdata['region_name']));
            }
            $this->db->set('latitude',(isset($ipdata['latitude']) ? $ipdata['latitude'] : NULL));
            $this->db->set('longitude',(isset($ipdata['longitude']) ? $ipdata['longitude'] : NULL));
            if (isset($ipdata['zipcode'])) {
                $this->db->set('zipcode',($ipdata['zipcode']=='-' ? NULL : $ipdata['zipcode']));
            }
            $this->db->insert('sb_geoips');
        }
    }

    public function ipdata_exist($userip) {
        $this->db->select('gi.*, cntr.country_id');
        $this->db->from('sb_geoips gi');
        $this->db->join('sb_countries cntr','cntr.country_iso_code_2=gi.country_code','left');
        $this->db->where('gi.user_ip',$userip);
        $res=$this->db->get()->row_array();
        if (!isset($res['user_ip'])) {
            $out=array('result'=>FALSE);
        } else {
            $out=$res;
            $out['result']=TRUE;
        }
        return $out;
    }

    public function get_statecode_byname($state_name) {
        $this->db->select('*');
        $this->db->from('sb_states');
        $this->db->where('state_name',$state_name);
        $res=$this->db->get()->row_array();
        if (isset($res['state_code'])) {
            return $res['state_code'];
        } else {
            return '';
        }
    }


    public function get_geolocation($ip) {
        // $>load->model('shippzones_model','mship');
        $api_key = $this->config->item('geo_apikey');
        $d = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=$api_key&ip=$ip&format=json");

        //Use backup server if cannot make a connection
        if (!$d) {
            return false; // Failed to open connection
        } else {
            $result = json_decode($d);
            $country_id='';
            if ($result->countryCode) {
                $cntr=$this->get_country_bycode2($result->countryCode);
                if (isset($cntr['country_id']) && $cntr['country_id']) {
                    $country_id=$cntr['country_id'];
                }
            }
            $out_array = array(
                'ip' => $ip,
                'country_code' => $result->countryCode,
                'country_name' => $result->countryName,
                'city_name' => $result->cityName,
                'region_name' => $result->regionName,
                'latitude' => $result->latitude,
                'longitude' => $result->longitude,
                'country_id' => $country_id,
                'zipcode' => $result->zipCode,
            );
            return $out_array;
        }
    }

    public function get_country_bycode2($country_code) {
        $this->db->select('*');
        $this->db->from('ts_countries');
        $this->db->where('country_iso_code_2',$country_code);
        $result=$this->db->get()->row_array();
        return $result;
    }

    public function calc_proofdate($item_id, $newval) {
        // Select data about itttem
        $proofdate = $newval;
        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id');
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id",'left');
        $this->db->where('i.item_id',$item_id);
        $leads = $this->db->get()->row_array();
        if (!isset($leads['calendar_id'])) {
            $leads['calendar_id']=$this->shipping_model->_get_default_calend();
        }
        if (!isset($leads['item_lead_a'])) {
            $leads['item_lead_a']=0;
        }
        if (!isset($leads['item_lead_b'])) {
            $leads['item_lead_b']=0;
        }
        if (!isset($leads['item_lead_c'])) {
            $leads['item_lead_c']=0;
        }
        $min = ($leads['item_lead_a']==0 ? ($leads['item_lead_b']==0 ? $leads['item_lead_c'] : $leads['item_lead_b']) : $leads['item_lead_a']);
        if ($min!=0) {
            $i=0;$cnt=0;
            while ($i <= 140) {
                $dat=strtotime(date("Y-m-d", $newval) . " -".$i." days");
                $day=$dat;
                if ($this->_chk_business_day($day, $item_id)) {
                    $cnt++;
                }
                if ($cnt > $min) {
                    $proofdate = $day;
                    break;
                }
                $i++;
            }
        }
        return $proofdate;
    }

    public function get_country_shipmethods($country_code, $brand) {
        $this->db->select('z.zone_id, ssm.shipping_method_name, ssm.ups_rate_code, zm.method_percent, zm.method_dimens, c.country_id');
        $this->db->from('sb_shipping_zones z');
        $this->db->join('sb_shipzone_methods zm','zm.shipzone_id=z.zone_id');
        $this->db->join('sb_shipping_methods ssm','zm.method_id = ssm.shipping_method_id');
        $this->db->join('sb_countries c','c.zone_id=z.zone_id');
        $this->db->where('c.country_iso_code_2',$country_code);
        $this->db->where('brand', $brand);
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function get_itemshipbox($item_id, $editmode) {
        $this->db->select('*');
        $this->db->from('sb_item_shipping');
        $this->db->where('item_id', $item_id);
        $this->db->order_by('box_qty');
        $res = $this->db->get()->result_array();
        if ($editmode==1) {
            if (count($res) < 3) {
                $numbox = count($res)+1;
                for ($i=$numbox; $i<4; $i++) {
                    $res[] = [
                        'item_shipping_id' => (-1)*$i,
                        'box_qty' => '',
                        'box_width' => '',
                        'box_length' => '',
                        'box_height' => '',
                    ];
                }
            }
        }
        return $res;
    }

}