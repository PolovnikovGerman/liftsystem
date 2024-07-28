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
    public function get_rushlist($item_id, $startdate=0, $defstart = '') {
        if ($startdate==0) {
            $start=date("Y-m-d");// current date
            $start_time=strtotime($start);
        } else {
            $start_time=$startdate;
        }
        // $start_time=$startdate;
        $proof_date = $start_time;
        if ($this->_chk_business_day($start_time, $item_id)==0) {
            $proof_date=$this->_get_business_date($start_time, 1, $item_id);
        }
        if ($item_id < 0 ) {
            $leads = [];
            if ($item_id==$this->config->item('custom_id')) {
                $leads['item_lead_a'] = $this->config->item('custom_proof_time');
            } elseif ($item_id==$this->config->item('other_id')) {
                $leads['item_lead_a'] = $this->config->item('other_proof_time');
            } else {
                $leads['item_lead_a']=0;
            }
            $leads['item_lead_b']=0;
            $leads['item_lead_c']=0;
            $leads['calendar_id']=$this->_get_default_calend();
        } else {
            $this->db->select('item_id, brand')->from('sb_items')->where('item_id', $item_id);
            $itmdat = $this->db->get()->row_array();
            if ($itmdat['brand']=='SR') {
                $this->db->select('i.item_id, i.item_lead_a, coalesce(i.item_lead_b,0) as item_lead_b, coalesce(i.item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id');
                $this->db->select('i.brand, p.item_sale_rush1, p.item_sale_rush2');
                $this->db->from("sb_items i");
                $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
                $this->db->join("calendars c","c.calendar_id=v.calendar_id",'left');
                $this->db->join('sb_item_prices p','p.item_price_itemid=i.item_id');
                $this->db->where('i.item_id',$item_id);
                $leads = $this->db->get()->row_array();
            } else {
                $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id, i.brand');
                $this->db->from("sb_items i");
                $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
                $this->db->join("calendars c","c.calendar_id=v.calendar_id",'left');
                $this->db->where('i.item_id',$item_id);
                $leads = $this->db->get()->row_array();
            }
            if (!isset($leads['calendar_id'])) {
                $leads['calendar_id']=$this->_get_default_calend();
            }
            /* Rebuild as array */
            if (!isset($leads['item_lead_a'])) {
                if ($item_id==$this->config->item('custom_id')) {
                    $leads['item_lead_a'] = $this->config->item('custom_proof_time');
                } elseif ($item_id==$this->config->item('other_id')) {
                    $leads['item_lead_a'] = $this->config->item('other_proof_time');
                } else {
                    $leads['item_lead_a']=0;
                }
            }
            if (!isset($leads['item_lead_b'])) {
                $leads['item_lead_b']=0;
            }
            if (!isset($leads['item_lead_c'])) {
                $leads['item_lead_c']=0;
            }

        }

        if ($item_id==$this->config->item('custom_id') || $item_id==$this->config->item('other_id')) {
            $min = 1;
        } else {
            $min=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']); $ship_array=array();
        }

        $leads['item_lead_a']=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']);

        if ($leads['item_lead_b']>0) {
            $min=$leads['item_lead_b'];
            if (ifset($leads,'brand','SB')=='SR') {
                $ship_array[]=array(
                    'min'=>$min,
                    'max'=>$leads['item_lead_a'],
                    'price'=>$leads['item_sale_rush1'],
                    'rush_term'=>$leads['item_lead_b'].' Day Rush',
                );
            } else {
                $ship_array[]=array(
                    'min'=>$min,
                    'max'=>$leads['item_lead_a'],
                    'price'=>$this->_get_config_value('rush_3days'),
                    'rush_term'=>$leads['item_lead_b'].' Day Rush',
                );
            }
        }

        if ($leads['item_lead_c']>0) {
            $min=$leads['item_lead_c'];
            if (ifset($leads,'brand','SB')=='SR') {
                $ship_array[]=array(
                    'min'=>$min,
                    'max'=>($leads['item_lead_b']==0 ? $leads['item_lead_a'] : $leads['item_lead_b']),
                    'price'=>$leads['item_sale_rush2'],
                    'rush_term'=>$leads['item_lead_c'].' Day Rush',
                );
            } else {
                $ship_array[]=array(
                    'min'=>$min,
                    'max'=>($leads['item_lead_b']==0 ? $leads['item_lead_a'] : $leads['item_lead_b']),
                    'price'=>$this->_get_config_value('rush_next_day'),
                    'rush_term'=>$leads['item_lead_c'].' Day Rush',
                );
            }
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
                    if ($current==0) {
                        if (empty($defstart)) {
                            if ($i==$leads['item_lead_a']) {
                                $current = 1;
                                $current_rush = $prefix;
                            }
                        } else {
                            if ($rushterm==$defstart) {
                                $current = 1;
                                $current_rush = $prefix;
                            }
                        }
                    }
                    // if ($i==$leads['item_lead_a']) {
                    //    $current=1;
                    //    $current_rush=$prefix;
                    // }
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
        if ($current==0) {
            $rush[0]['current'] = 1;
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

    public function count_shiprates_new($items, $shipaddr, $deliv_date, $brand, $default_ship_method='') {
        $res=['result'=>$this->error_result, 'msg'=>$this->error_message];
        $this->load->model('items_model');
        $this->load->model('vendors_model');
        $outrate=[];
        $ratekey=[];
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
        $shiper = $this->config->item('ups_shiper');
        foreach ($items as $item) {
            if ($item['item_id'] > 0) {
                // Get Item Shipboxes
                $shipboxes = $this->items_model->get_item_shipboxes($item['item_id']);
                // Vendor
                $vendordat = $this->vendors_model->get_item_vendor($item['vendor_item_id']);
                $shipFrom = [
                    "Name" => $vendordat['vendor_name'],
                    "Address" => [
                        "City" => $vendordat['item_shipcity'],
                        "StateProvinceCode" => $vendordat['item_shipstate'],
                        "PostalCode" => $vendordat['vendor_item_zipcode'],
                        "CountryCode" => $vendordat['item_shipcountry_code']
                    ],
                ];
            } else {
                $shipboxes=[];
                $shipboxes[] = [
                    'box_qty' => $this->config->item('default_inpack'),
                    'box_width' => $this->config->item('default_pack_width'),
                    'box_height' => $this->config->item('default_pack_heigth'),
                    'box_length' => $this->config->item('default_pack_depth'),
                ];
                $shipFrom = [
                    "Name" => "INTERNAL",
                    "Address" => $shiper['Address'],
                ];
            }
            $ship_state = ltrim(str_replace($shipaddr['zip'], '', $shipaddr['out_zip']));
            $shipTo = [
                "Name" => !empty($shipaddr['ship_company']) ? $shipaddr['ship_company'] : "Test Company",
                "Address" => [
                    "City" => $shipaddr['city'],
                    "StateProvinceCode" => $ship_state,
                    "PostalCode" => $shipaddr['zip'],
                    "CountryCode" => $shipaddr['out_country']
                ]
            ];
            $cnt_code = $shipaddr['out_country'];
            $package_price = $item['item_subtotal'];
            $itemqty = ceil($item['item_qty']*$kf);
            $itemweigth = ifset($item, 'item_weigth',0)==0 ? 0.010 : $item['item_weigth'];
            $datpackages = $this->prepare_ship_packages($itemqty, $shipboxes, $itemweigth);
            $shipoptions = [
                'itemqty' => $itemqty,
                'weight' => $itemweigth,
                'packages' => $datpackages['packages'],
                'numpackages' => $datpackages['numpackages'],
                'startdeliv'=> $deliv_date,
                'shipTo' => $shipTo,
                'shipFrom' => $shipFrom,
                'target_country' => $cnt_code,
                'brand' => $brand,
                'package_price' => $package_price,
            ];
            $shipres = $this->calculate_shipcost($shipoptions);
            if ($shipres['result']==$this->error_result) {
                $res['msg']=$shipres['msg'];
                return $res;
            }
            $ship=$shipres['ship'];
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
                'charge_perorder'=>(isset($item['charge_perorder']) ? $item['charge_perorder'] : 0),
                'charge_pereach'=>(isset($item['charge_pereach']) ? $item['charge_pereach'] : 0),
            );
            /* Recalc Rates */
            $shiplast = recalc_rates($ship,$itemdat,$item['item_qty'],$brand, $cnt_code, $shipaddr['country_id']);

            foreach ($shiplast as $key=>$row) {
                if ($key!='deliv') {
                    if (!in_array($key , $ratekey)) {
                        array_push($ratekey, $key);
                        $outrate[]=array(
                            'ServiceName'=>$row['ServiceName'],
                            'Rate'=>0,
                            'DeliveryDate'=>0,
                            'current'=>$row['current'],
                            'arrive' => $row['arrive'],
                            'tntdays' => $row['tntdays'],
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

    public function count_shiprates($items, $shipaddr, $deliv_date, $brand, $default_ship_method='') {
        if ($brand=='SR') {
            return $this->count_shiprates_new($items, $shipaddr, $deliv_date, $brand, $default_ship_method);
        } else {
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
    }


    public function get_ship_methods($country_id, $brand, $option='all') {

        $this->db->select('m.shipping_method_id, m.shipping_method_name, m.ups_code, m.default_rate, m.minimal_rate,
            m.shipping_method_available, zm.method_dimens,zm.method_percent,m.ups_sort'); //
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
            // $leads['item_lead_a']=0;
            if ($item_id==$this->config->item('custom_id')) {
                $leads['item_lead_a'] = $this->config->item('custom_proof_time');
            } elseif ($item_id==$this->config->item('other_id')) {
                $leads['item_lead_a'] = $this->config->item('other_proof_time');
            } else {
                $leads['item_lead_a']=0;
            }
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

    public function get_zip_address($country_id, $zipcode) {
        $out=['result' => $this->error_result, 'msg' => 'Address not found'];
        $cntdat = $this->get_country($country_id);
        if ($cntdat['country_iso_code_2']=='CA') {
            $seachzip = substr($zipcode,0, 3);
        } else {
            $seachzip = $zipcode;
        }
        $this->db->select('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code as state, t.state_id, count(c.geoip_city_id) as cntcity');
        $this->db->from('ts_geoipdata gdata');
        $this->db->join('ts_geoip_city c','c.geoname_id=gdata.geoname_id');
        $this->db->join('ts_countries cntr','cntr.country_iso_code_2=c.country_iso_code');
        $this->db->join('ts_states t','t.state_code=c.subdivision_1_iso_code','left');
        $this->db->where('gdata.postal_code', $seachzip);
        $this->db->where('cntr.country_id', $country_id);
        $this->db->group_by('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code, t.state_id');
        $this->db->order_by('cntcity','desc');
        $validdata = $this->db->get()->result_array();
        if (count($validdata) > 0) {
            $out['result'] = $this->success_result;
            $out['city'] = $validdata[0]['city_name'];
            $out['state'] = $validdata[0]['state'];
        }
        return $out;
    }

    public function count_quoteshiprates($items, $quote, $deliv_date, $brand, $default_ship_method='') {
        if ($brand=='SR') {
            return $this->count_quoteshiprates_new($items, $quote, $deliv_date, $brand, $default_ship_method='');
        }
        $res=['result'=>$this->error_result,  'msg'=>$this->error_message];
        $outrate = [];
        $ratekey = [];
        $kf=1;
        $this->load->config('shipping');
        $cntdat=$this->get_country($quote['shipping_country']);
        foreach ($items as $item) {
            if (ifset($item,'item_qty',0) > 0) {
                $carton_qty = intval(ifset($item, 'cartoon_qty', 0)) > 0 ? intval($item['cartoon_qty']) : $this->config->item('default_inpack');
                $cartoon_depth = intval(ifset($item, 'cartoon_depth', 0)) > 0 ? intval($item['cartoon_depth']) : $this->config->item('default_pack_depth');
                $cartoon_width = intval(ifset($item, 'cartoon_width' , 0)) > 0 ? intval($item['cartoon_width']) : $this->config->item('default_pack_width');
                $cartoon_heigh = intval(ifset($item, 'cartoon_heigh',0)) > 0 ? intval($item['cartoon_heigh']) : $this->config->item('default_pack_heigth');
                $itemweight = ifset($item, 'item_weigth', 0) > 0 ? $item['item_weigth'] : $this->box_empty_weight / $carton_qty;
                $options=array(
                    'zip' => $quote['shipping_zip'],
                    'numinpack'=>$carton_qty,
                    'itemqty'=>ceil($item['item_qty']*$kf),
                    'startdeliv' => $deliv_date,
                    'vendor_zip' => empty($item['vendor_zipcode']) ? $this->config->item('zip') : $item['vendor_zipcode'],
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
                    'charge_perorder'=> intval(ifset($item, 'charge_perorder',0)),
                    'charge_pereach'=> intval(ifset($item, 'charge_pereach',0)),
                );
                /* Recalc Rates */
                $shiplast = recalc_rates($ship,$itemdat, $item['item_qty'],$brand, $cntdat['country_iso_code_2'], $quote['shipping_country']);

                foreach ($shiplast as $key=>$row) {
                    if ($key!='deliv') {
                        if (!in_array($key , $ratekey)) {
                            array_push($ratekey, $key);
                            $outrate[]=array(
                                'ServiceName'=>$row['ServiceName'],
                                'Rate'=>0,
                                'DeliveryDate'=>0,
                                'current'=>$row['current'],
                                'code' => $key,
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
        }
        $res['result']=$this->success_result;
        $res['ships']=$outrate;
        return $res;
    }

    public function count_quoteshiprates_new($items, $quote, $deliv_date, $brand, $default_ship_method='')
    {
        $res=['result'=>$this->error_result, 'msg'=>$this->error_message];
        $outrate = [];
        $ratekey = [];
        $this->load->model('items_model');
        $this->load->model('vendors_model');
        // $kf=1;
        $this->load->config('shipping');
        $cntdat=$this->get_country($quote['shipping_country']);
        $this->load->config('shipping');
        $shiper = $this->config->item('ups_shiper');
        foreach ($items as $item) {
            $flagitem = 0;
            $itemqty = ifset($item, 'item_qty', 0);
            $qtykf = 1;
            if ($item['item_id'] > 0) {
                $itemres = $this->items_model->get_item($item['item_id']);
                if ($itemres['result']==$this->success_result) {
                    $itemdat =  $itemres['data'];
                    $flagitem = 1;
                    $item['item_weigth'] = $itemdat['item_weigth'];
                    // Get Item Shipboxes
                    $shipboxes = $this->items_model->get_item_shipboxes($item['item_id']);
                    // Vendor
                    // QTY KF
                    $qtykf = 1;
                    $maxqty = $shipboxes[0]['box_qty'] * 50;
                    if ($itemqty > $maxqty) {
                        $qtykf = $maxqty / $itemqty;
                        $itemqty = round($itemqty*$qtykf,0);
                    }
                    $vendordat = $this->vendors_model->get_item_vendor($itemdat['vendor_item_id']);
                    $shipFrom = [
                        "Name" => $vendordat['vendor_name'],
                        "Address" => [
                            "City" => $vendordat['item_shipcity'],
                            "StateProvinceCode" => $vendordat['item_shipstate'],
                            "PostalCode" => $vendordat['vendor_item_zipcode'],
                            "CountryCode" => $vendordat['item_shipcountry_code']
                        ],
                    ];
                }
            }
            if ($flagitem==0){
                $shipboxes=[];
                $shipboxes[] = [
                    'box_qty' => $this->config->item('default_inpack'),
                    'box_width' => $this->config->item('default_pack_width'),
                    'box_height' => $this->config->item('default_pack_heigth'),
                    'box_length' => $this->config->item('default_pack_depth'),
                ];
                $shipFrom = [
                    "Name" => "INTERNAL",
                    "Address" => $shiper['Address'],
                ];
            }

            $shipTo = [
                "Name" => !empty($quote['shipping_company']) ? $quote['shipping_company'] : "Test Company",
                "Address" => [
                    "City" => $quote['shipping_city'],
                    "StateProvinceCode" => $quote['shipping_state'],
                    "PostalCode" => $quote['shipping_zip'],
                    "CountryCode" => $cntdat['country_iso_code_2'],
                ]
            ];
            $package_price = $quote['items_subtotal'];
            // $itemqty = ceil($item['item_qty']*$kf);
            $itemweigth = ifset($item, 'item_weigth',0)==0 ? 0.010 : $item['item_weigth'];
            $datpackages = $this->prepare_ship_packages($itemqty, $shipboxes, $itemweigth);
            $shipoptions = [
                'itemqty' => $itemqty,
                'weight' => $itemweigth,
                'packages' => $datpackages['packages'],
                'numpackages' => $datpackages['numpackages'],
                'startdeliv'=> $deliv_date,
                'shipTo' => $shipTo,
                'shipFrom' => $shipFrom,
                'target_country' => $cntdat['country_iso_code_2'],
                'brand' => $brand,
                'package_price' => $package_price,
                'qtykf' => $qtykf,
            ];
            $shipres = $this->calculate_shipcost($shipoptions);
            if ($shipres['result']==$this->error_result) {
                $res['msg']=$shipres['msg'].' - '.$shipres['error_code'];
                return $res;
            }
            $ship=$shipres['ship'];
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
                'charge_perorder'=> intval(ifset($item, 'charge_perorder',0)),
                'charge_pereach'=> intval(ifset($item, 'charge_pereach',0)),
            );
            /* Recalc Rates */
            $shiplast = recalc_rates($ship,$itemdat, $item['item_qty'],$brand, $cntdat['country_iso_code_2'], $quote['shipping_country']);

            foreach ($shiplast as $key=>$row) {
                if ($key!='deliv') {
                    if (!in_array($key , $ratekey)) {
                        array_push($ratekey, $key);
                        $outrate[]=array(
                            'ServiceName'=>$row['ServiceName'],
                            'Rate'=>0,
                            'DeliveryDate'=>0,
                            'current'=>$row['current'],
                            'code' => $key,
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

    public function get_statebycode($state_code, $country_id=0) {
        $out=['result' => $this->error_result, 'msg' => 'State Not Found'];
        $this->db->select('*');
        $this->db->from('sb_states');
        $this->db->where('state_code', $state_code);
        if (!empty($country_id)) {
            $this->db->where('country_id', $country_id);
        }
        $state = $this->db->get()->row_array();
        if (ifset($state, 'state_id', 0) > 0) {
            $out['result'] = $this->success_result;
            $out['data'] = $state;
        }
        return $out;
    }

    public function get_itemshipbox($item_id, $editmode) {
        $this->db->select('*');
        $this->db->from('sb_item_shipping');
        $this->db->where('item_id', $item_id);
        $this->db->order_by('box_qty');
        $res = $this->db->get()->result_array();
        if ($editmode==1) {
            if (count($res) < 4) {
                $numbox = count($res)+1;
                for ($i=$numbox; $i<5; $i++) {
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

    public function prepare_ship_packages($shipqty, $shipboxes, $itemweight ) {
        $maxshipbox = count($shipboxes)-1;
        $packages = [];
        $numpackages = 0;
        if ($maxshipbox==0) {
            $ceilpart = floor($shipqty/$shipboxes[0]['box_qty']);
            $boxweight = $itemweight * $shipboxes[0]['box_qty'];
            for ($i=0; $i < $ceilpart; $i++) {
                $packages[] = [
                    "PackagingType" => [
                        "Code" => "02",
                        "Description" => "Packaging"
                    ],
                    "Dimensions" => [
                        "UnitOfMeasurement" => [
                            "Code" => "IN",
                            "Description" => "Inches"
                        ],
                        "Length" => "{$shipboxes[0]['box_length']}",
                        "Width" => "{$shipboxes[0]['box_width']}",
                        "Height" => "{$shipboxes[0]['box_height']}"
                    ],
                    "PackageWeight" => [
                        "UnitOfMeasurement" => [
                            "Code" => "LBS",
                            "Description" => "Pounds"
                        ],
                        "Weight" => "{$boxweight}"
                    ],
                ];
                $numpackages++;
            }
            $restqty = $shipqty - $ceilpart * $shipboxes[0]['box_qty'];
            if ($restqty > 0) {
                //
                $packages[] = [
                    "PackagingType" => [
                        "Code" => "02",
                        "Description" => "Packaging"
                    ],
                    "Dimensions" => [
                        "UnitOfMeasurement" => [
                            "Code" => "IN",
                            "Description" => "Inches"
                        ],
                        "Length" => "{$shipboxes[0]['box_length']}",
                        "Width" => "{$shipboxes[0]['box_width']}",
                        "Height" => "{$shipboxes[0]['box_height']}"
                    ],
                    "PackageWeight" => [
                        "UnitOfMeasurement" => [
                            "Code" => "LBS",
                            "Description" => "Pounds"
                        ],
                        "Weight" => "{$boxweight}"
                    ],
                ];
                $numpackages++;
            }
        } else {
            $restqty = $shipqty;
            if ($restqty > $shipboxes[$maxshipbox]['box_qty']) {
                $ceilpart = floor($shipqty / $shipboxes[$maxshipbox]['box_qty']);
                if ($ceilpart > 0) {
                    $boxweight = $itemweight * $shipboxes[$maxshipbox]['box_qty'];
                    for ($i=0; $i < $ceilpart; $i++) {
                        $packages[] = [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Packaging"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "IN",
                                    "Description" => "Inches"
                                ],
                                "Length" => "{$shipboxes[$maxshipbox]['box_length']}",
                                "Width" => "{$shipboxes[$maxshipbox]['box_width']}",
                                "Height" => "{$shipboxes[$maxshipbox]['box_height']}"
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "LBS",
                                    "Description" => "Pounds"
                                ],
                                "Weight" => "{$boxweight}"
                            ],
                        ];
                        $numpackages++;
                    }
                    $restqty = $restqty - ($ceilpart * $shipboxes[$maxshipbox]['box_qty']);
                }
            }

            while ($restqty > 0) {
                foreach ($shipboxes as $shipbox) {
                    if ($shipbox['box_qty']>=$restqty) {
                        $boxweight = $itemweight * $restqty;
                        $packages[] = [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Packaging"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "IN",
                                    "Description" => "Inches"
                                ],
                                "Length" => "{$shipbox['box_length']}",
                                "Width" => "{$shipbox['box_width']}",
                                "Height" => "{$shipbox['box_height']}"
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "LBS",
                                    "Description" => "Pounds"
                                ],
                                "Weight" => "{$boxweight}"
                            ],
                        ];
                        $numpackages++;
                        $restqty = $restqty - $shipbox['box_qty'];
                    }
                    if ($restqty <= 0) {
                        break;
                    }
                }
            }
        }
        return [
            'packages' => $packages,
            'numpackages' => $numpackages,
        ];
    }

    public function calculate_shipcost($options) {
        $out=['result' => $this->error_result, 'msg' => 'Error During Calc Ship rates', 'error_code'=>'Auth'];
        $this->load->config('shipping');
        $this->load->model('calendars_model');
        $itemweight = ifset($options, 'weight', '0')==0 ? 0.010 : $options['weight'];
        $qty = ifset($options, 'itemqty', 250);
        $startdeliv = ifset($options, 'startdeliv', time());
        $cnt_code = (isset($options['target_country']) ? $options['target_country'] : 'US');
        $package_price = ifset($options, 'package_price', 100);
        $qtykf = ifset($options,'qtykf',1);
        $shipTo = $options['shipTo'];
        $shipFrom = $options['shipFrom'];
        // Calculate REST of full cartoon
        $tntpacks = ifset($options, 'numpackages', 1);
        $earlier = new DateTime(date('Y-m-d'));
        $later = new DateTime(date('Y-m-d', $startdeliv));
        $daydiff = $later->diff($earlier)->format("%r%a");

        $token = usersession('upstoken');
        $tokenres = $this->_UpsAuthToken($token);
        $out['msg'] = $tokenres['msg'];
        if ($tokenres['result']==$this->success_result) {
            $token = $tokenres['token'];
            // Get Times in transit
            $oldstart = 0;
            if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                $oldstart = $startdeliv;
                $startdeliv = strtotime(date('Y-m-d'));
            }
            $tntweigth = $itemweight * $qty;
            $ratescalc = 0;
            $this->load->library('UPS_service');
            $upsservice = new UPS_service();

            $transitres = $upsservice->timeInTransit($token, $shipFrom['Address'], $shipTo['Address'], $tntweigth, $tntpacks, $package_price,  date('Y-m-d', $startdeliv), '10:00:00');
            $out['msg'] = $transitres['msg'];
            $out['error_code'] = 'TNT';
            if ($transitres['error']==2) {
                if (isset($transitres['cities'])) {
                    $candidates = $transitres['cities'];
                    foreach ($candidates as $candidate) {
                        if ($candidate['postalCode']==$shipTo['Address']['PostalCode']) {
                            $shipTo['Address']['StateProvinceCode'] = $candidate['stateProvince'];
                            $shipTo['Address']['City'] = $candidate['city'];
                            break;
                        }
                    }
                    $transitres = $upsservice->timeInTransit($token, $shipFrom['Address'], $shipTo['Address'], $tntweigth, $tntpacks, $package_price,  date('Y-m-d', $startdeliv), '10:00:00');
                    $out['msg'] = $transitres['msg'];
                    if ($transitres['error']==0) {
                        $out['cityname'] = $candidate['city'];
                    }
                }
            }
            if ($transitres['error']==0) {
                // All ok
                $times = $transitres['services'];
                // Calc rates
                $out['error_code']='Rates';
                $packDimens = $options['packages'];
                $rateres = $upsservice->getRates($token, $shipTo, $shipFrom, $tntpacks,  $packDimens, $tntweigth);
                if ($rateres['error'] > 0) {
                    $out['msg'] = $rateres['msg'];
                } else {
                    $out['result'] = $this->success_result;
                    $rates = $rateres['rates'];
                    // Make merged array
                    $ship=[];
                    $code = '';
                    $codes = [];
                    $calendar_id=$this->config->item('bank_calendar');
                    $this->load->model('calendars_model');
                    if ($cnt_code=='US') {
                        foreach ($rates as $rate) {
                            $transit = 0;
                            if ($rate['service_code']=='03') {
                                // Ground
                                foreach ($times as $time) {
                                    if ($time['code']=='GND') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'GND');
                                    $code .= "GND|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $delivdate = $this->calendars_model->businessdate($delivdate);
                                    $ship['GND'] = [
                                        'ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='02') {
                                // Two days
                                foreach ($times as $time) {
                                    if ($time['code']=='2DA') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'DA2');
                                    $code .= "2DA|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['DA2'] = [
                                        'ServiceCode' => 'DA2', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => '2nd Day Air', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='14') {
                                // UPS Next Day Air Early
                                foreach ($times as $time) {
                                    if ($time['code']=='1DM') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'DA1');
                                    $code .= "1DA|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['DA1'] = array(
                                        'ServiceCode' => '1DM',
                                        'ServiceName' => 'Next Day AM',
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    );
                                }
                            } elseif ($rate['service_code']=='13') {
                                // UPS Next Day Air Saver
                                foreach ($times as $time) {
                                    if ($time['code']=='1DP') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'DP1');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        // Make changes in deliv date
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['DP1'] = array(
                                        'ServiceCode' => '1DP',
                                        'ServiceName' => 'Next Day PM',
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    );
                                    $code .= "1DP|";
                                }
                            } elseif ($rate['service_code']=='12') {
                                foreach ($times as $time) {
                                    if ($time['code']=='3DS') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, '3DS');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        // Make changes in deliv date
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['3DS'] = array(
                                        'ServiceCode' => '3DS',
                                        'ServiceName' => 'UPS 3 Day Select',
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    );
                                    $code .= "3DS|";
                                }
                            }
                        }
                    } elseif ($cnt_code=='CA') {
                        foreach ($rates as $rate) {
                            $transit = 0;
                            if ($rate['service_code']=='11') {
                                // Ground
                                foreach ($times as $time) {
                                    if ($time['code']=='03') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'GND');
                                    $code .= "GND|";
//                                    if ($time['deliverytime'] > '16:00:00') {
//                                        $newdate  = $this->calendars_model->get_business_date(strtotime($time['deliverydate']),1);
//                                        $time['deliverydate'] = date('Y-m-d', $newdate);
//                                        $time['deliverytime'] = '16:00:00';
//                                    }
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $delivdate = $this->calendars_model->businessdate($delivdate);
                                    $ship['GND'] = [
                                        'ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='08') {
                                // UPS Expedited
                                foreach ($times as $time) {
                                    if ($time['code']=='05') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpedited');
                                    $code .= "08|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpedited'] = [
                                        'ServiceCode' => '08', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Expedited', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='65') {
                                // Saver
                                foreach ($times as $time) {
                                    if ($time['code']=='28') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSSaver');
                                    $code .= "65|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSSaver'] = [
                                        'ServiceCode' => '65', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Saver', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='07') {
                                // UPSWorExpress
                                foreach ($times as $time) {
                                    if ($time['code']=='29') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpress');
                                    $code .= "07|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpress'] = [
                                        'ServiceCode' => '07', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Express', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            }
                        }
                    } else {
                        foreach ($rates as $rate) {
                            if ($rate['service_code']=='07') {
                                // UPSWorExpress
                                foreach ($times as $time) {
                                    if ($time['code']=='01') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($time['code']=='29') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpress');
                                    $code .= "07|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpress'] = [
                                        'ServiceCode' => '07', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Express', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='08') {
                                // UPS Expedited
                                foreach ($times as $time) {
                                    if ($time['code']=='05') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpedited');
                                    $code .= "08|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpedited'] = [
                                        'ServiceCode' => '08', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Expedited', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='11') {
                                // Ground
                                foreach ($times as $time) {
                                    if ($time['code']=='04') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($time['code']=='11') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($time['code']=='82') {
                                        $transit = 1;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'GND');
                                    $code .= "GND|";
//                                    if ($time['deliverytime'] > '16:00:00') {
//                                        $newdate  = $this->calendars_model->get_business_date(strtotime($time['deliverydate']),1);
//                                        $time['deliverydate'] = date('Y-m-d', $newdate);
//                                        $time['deliverytime'] = '16:00:00';
//                                    }
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $delivdate = $this->calendars_model->businessdate($delivdate);
                                    $ship['GND'] = [
                                        'ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            } elseif ($rate['service_code']=='54') {
                                foreach ($times as $time) {
                                    if ($time['code']=='54') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($transit==1) {
                                        array_push($codes, 'ExpressPlus');
                                        $code .= "54|";
                                        $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                        if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                            $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                        }
                                        $ship['ExpressPlus'] = [
                                            'ServiceCode' => '54', // 'ServiceName' =>$row['ServiceName'],
                                            'ServiceName' => 'Express Plus', // 'Rate' =>$row['Rate'],
                                            'Rate' => round($rate['rate']/$qtykf, 2),
                                            'DeliveryDate' => $delivdate,
                                            'current' => 0,
                                            'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                            'tntdays' => $time['bisnessdays'],
                                        ];
                                    }

                                }
                            } elseif ($rate['service_code']=='65') {
                                // Saver
                                foreach ($times as $time) {
                                    if ($time['code']=='28') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSSaver');
                                    $code .= "65|";
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > $this->config->item('delivery_daydiff')) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSSaver'] = [
                                        'ServiceCode' => '65', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Saver', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate']/$qtykf, 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                        'arrive' => $time['deliverydate'].' '.$time['deliverytime'],
                                        'tntdays' => $time['bisnessdays'],
                                    ];
                                }
                            }
                        }
                    }
                    $out['ship'] = $ship;
                    $out['code'] = $code;
                }
            }
        }
        return $out;
    }

    public function recalc_arrive_date($delivdate, $tnt_days, $calendar_id = 0) {
        if ($calendar_id==0) {
            $calendar_id=$this->config->item('bank_calendar');
        }
        $start=date("Y-m-d",$delivdate);
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
        $i=1;$cnt=1;
        while ($i <= $tnt_days) {
            $dat=strtotime(date("Y-m-d", strtotime($start)) . " +".$cnt." days");
            $day=$dat;
            if (date('w',$dat)!=0 && date('w',$dat)!=6 && !in_array($day, $calend)) {
                $i++;
            }
            $cnt++;
        }
        $newdeliv=$dat;
        return $newdeliv;
    }


    private function _UpsAuthToken($token) {
        $out = ['result' => $this->error_result, 'msg' => 'Error during Token generation'];
        $this->load->library('UPS_service');
        $upsservice = new UPS_service();
        $sessionId = session_id();
        if ($token) {
            $tokenresult = $upsservice->refreshToken($token);
            if ($tokenresult['error']==1) {
                $out['msg'] = 'Authorize Error. Code '.$tokenresult['code'];
            } else {
                if (!isset($tokenresult['errors'])) {
                    $out['result'] = $this->success_result;
                    $out['token'] = $token;
                    $out['session'] = $sessionId;
                    usersession('upstoken', $token);
                }
            }
        }
        if ($out['result']==$this->error_result) {
            $tokenresult = $upsservice->generateToken($sessionId);
            if ($tokenresult['error']==1) {
                $out['msg'] = $tokenresult['msg'];
            } else {
                if (isset($tokenresult['errors'])) {
                    $errors = $tokenresult['errors'][0];
                    $out['msg'] = 'Error Code '.$errors['code'].' - '.$errors['message'];
                } else {
                    $out['result'] = $this->success_result;
                    $out['token'] = $tokenresult['access_token'];
                    $out['session'] = $sessionId;
                    usersession('upstoken', $tokenresult['access_token']);
                }
            }
        }
        return $out;
    }

    public function prepare_shipaddress($address) {
        $shipaddress = '';
        if (!empty($address['ship_contact'])) {$shipaddress.=$address['ship_contact'].PHP_EOL;}
        if (!empty($address['ship_company'])) {$shipaddress.=$address['ship_company'].PHP_EOL;}
        if (!empty($address['ship_address1'])) {$shipaddress.=$address['ship_address1'].PHP_EOL;}
        if (!empty($address['ship_address2'])) {$shipaddress.=$address['ship_address2'].PHP_EOL;}
        $adrline = 0;
        if (!empty($address['city'])) {$shipaddress.=$address['city'].', '; $adrline = 1;}
        if (!empty($address['state_id'])) {
            $statres = $this->get_state($address['state_id']);
            if (ifset($statres,'state_code','')!=='') {
                $shipaddress.=$statres['state_code'].' ';$adrline = 1;
            }
        }
        if (!empty($address['zip'])) {$shipaddress.=$address['zip'];$adrline = 1;}
        if ($adrline ==1) {
            $shipaddress.=PHP_EOL;
        }
        return $shipaddress;
    }

    public function prepare_billaddress($address) {
        $billaddress = '';
        if (!empty($address['customer_name'])) {$billaddress.=$address['customer_name'].PHP_EOL;}
        if (!empty($address['company'])) {$billaddress.=$address['company'].PHP_EOL;}
        if (!empty($address['address_1'])) {$billaddress.=$address['address_1'].PHP_EOL;}
        if (!empty($address['address_2'])) {$billaddress.=$address['address_2'].PHP_EOL;}
        $adrline = 0;
        if (!empty($address['city'])) {$billaddress.=$address['city'].', '; $adrline = 1;}
        if (!empty($address['state_id'])) {
            $statres = $this->get_state($address['state_id']);
            if (ifset($statres,'state_code','')!=='') {
                $billaddress.=$statres['state_code'].' ';$adrline = 1;
            }
        }
        if (!empty($address['zip'])) {$billaddress.=$address['zip'];$adrline = 1;}
        if ($adrline ==1) {
            $billaddress.=PHP_EOL;
        }
        return $billaddress;
    }

}