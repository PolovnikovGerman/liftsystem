<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Calendars_model extends MY_Model
{

    private $error_message='Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

    public function get_calendars_list($brand) {
        $this->db->select('*');
        $this->db->from('calendars');
        if ($brand!=='ALL') {

        }
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_calendars() {
        $this->db->select('*');
        $this->db->from('calendars');
        $this->db->where('calendar_status',1);
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function count_calendars($brand) {
        $this->db->select('count(calendar_id) as cnt');
        $this->db->from('calendars');
        $this->db->where('calendar_status',1);
        if ($brand!=='ALL') {
            //
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

//    function get_calendar($calend_id) {
//        if ($calend_id==0) {
//            $calend=array(
//                'calendar_id'=>0,
//                'calendar_name'=>'',
//                'calendar_status'=>0,
//                'websystem_id'=>'',
//                'mon_work'=>1,
//                'tue_work'=>1,
//                'wed_work'=>1,
//                'thu_work'=>1,
//                'fri_work'=>1,
//                'sat_work'=>0,
//                'sun_work'=>0,
//            );
//        } else {
//            $this->db->select('*');
//            $this->db->from('calendars');
//            $this->db->where('calendar_id',$calend_id);
//            $calend=$this->db->get()->row_array();
//        }
//        return $calend;
//    }

    public function get_calendar_lines($calend_id) {
        $this->db->select('*');
        $this->db->from('calendar_lines');
        $this->db->where('calendar_id',$calend_id);
        $this->db->order_by('line_date','desc');
        $res=$this->db->get()->result_array();
        return $res;
    }

//    function savecalend($options) {
//        /* Check unique name */
//        $out=array('res'=>'','msg'=>'');
//        $this->db->select('count(calendar_id) as cnt');
//        $this->db->from('calendars');
//        $this->db->where('calendar_name',$options['calendar_name']);
//        $this->db->where('calendar_id !=',$options['calendar_id']);
//        $res=$this->db->get()->row_array();
//        if (trim($options['calendar_name'])=='') {
//            $out['res']=Calendars_model::ERR_FLAG;
//            $out['msg']='Non unique business calendar name';
//        } elseif ($res['cnt']!=0) {
//            $out['res']=Calendars_model::ERR_FLAG;
//            $out['msg']='Non unique business calendar name';
//        } elseif (!isset($options['men_work']) && !isset($options['tue_work']) && !isset($options['wed_work']) && !isset($options['thu_work']) && !isset($options['fri_work']) && !isset($options['sat_work']) && !isset($options['sun_work'])) {
//            $out['res']=Calendars_model::ERR_FLAG;
//            $out['msg']='Please, select business days';
//        } else {
//            $this->db->set('calendar_name',$options['calendar_name']);
//            if (isset($options['mon_work'])) {
//                $this->db->set('mon_work',1);
//            } else {
//                $this->db->set('mon_work',0);
//            }
//            if (isset($options['tue_work'])) {
//                $this->db->set('tue_work',1);
//            } else {
//                $this->db->set('tue_work',0);
//            }
//            if (isset($options['wed_work'])) {
//                $this->db->set('wed_work',1);
//            } else {
//                $this->db->set('wed_work',0);
//            }
//            if (isset($options['thu_work'])) {
//                $this->db->set('thu_work',1);
//            } else {
//                $this->db->set('thu_work',0);
//            }
//            if (isset($options['fri_work'])) {
//                $this->db->set('fri_work',1);
//            } else {
//                $this->db->set('fri_work',0);
//            }
//            if (isset($options['sat_work'])) {
//                $this->db->set('sat_work',1);
//            } else {
//                $this->db->set('sat_work',0);
//            }
//            if (isset($options['sun_work'])) {
//                $this->db->set('sun_work',1);
//            } else {
//                $this->db->set('sun_work',0);
//            }
//            /* Temporary */
//            $this->db->set('calendar_status',$options['calendar_status']);
//            if ($options['calendar_id']==0) {
//                $this->db->insert('calendars');
//                $options['calendar_id']=$this->db->insert_id();
//            } else {
//                $this->db->where('calendar_id',$options['calendar_id']);
//                $this->db->update('calendars');
//            }
//            if ($options['calendar_id']==0) {
//                $out['res']=Calendars_model::ERR_FLAG;
//                $out['msg']='Calendar not saved. Please, try later';
//            } else {
//                $out['res']=Calendars_model::SUCCESS_RESULT;
//            }
//        }
//        if ($out['res']!=Calendars_model::ERR_FLAG) {
//            /* save lines */
//            $this->db->where('calendar_id',$options['calendar_id']);
//            $this->db->delete('calendar_lines');
//            if ($options['deldat']!='') {
//                $dat=explode('|', $options['deldat']);
//                /* Delete old Lines */
//                foreach ($dat as $row) {
//                    $this->db->set('calendar_id',$options['calendar_id']);
//                    $this->db->set('line_date',$row);
//                    $this->db->insert('calendar_lines');
//                }
//            }
//        }
//        return $out;
//    }
//
//    function del_calendar($calendar_id) {
//        $this->db->where('calendar_id',$calendar_id);
//        $this->db->delete('calendars');
//        return $this->db->affected_rows();
//    }

    public function get_calendar_holidays($calendar, $startdate=0, $enddate=0) {
        $this->db->select('line_date');
        $this->db->from('calendar_lines');
        $this->db->where('calendar_id',$calendar);
        if ($startdate!=0) {
            $this->db->where('line_date >= ', $startdate);
        }
        if ($enddate!=0) {
            $this->db->where('line_date <= ', $enddate);
        }
        $res=$this->db->get()->result_array();

        $holidays=array();
        foreach ($res as $row) {
            $date=strtotime(date('Y-m-d',$row['line_date']));
            array_push($holidays,$date);
        }
        return $holidays;
    }

    public function get_calendar_edit($calend_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if ($calend_id==0) {
            $out['result']=$this->success_result;
            $out['data']=array(
                'calendar_id'=>0,
                'calendar_name'=>'',
                'calendar_status'=>0,
                'websystem_id'=>'',
                'mon_work'=>1,
                'tue_work'=>1,
                'wed_work'=>1,
                'thu_work'=>1,
                'fri_work'=>1,
                'sat_work'=>0,
                'sun_work'=>0,
            );

        } else {
            $this->db->select('*');
            $this->db->from('calendars');
            $this->db->where('calendar_id',$calend_id);
            $calend=$this->db->get()->row_array();
            if (!isset($calend['calendar_id'])) {
                $out['msg']='Calendar Not Found';
                return $out;
            }
            $out['data']=$calend;
            $out['result']=$this->success_result;
        }
        // Calc Business Days, Elapsed, Remaining
        $now=strtotime(date('Y-m-d'));
        $yearbgn=strtotime(date('Y').'-01-01');
        $nxtyear=strtotime(date("Y-m-d", $yearbgn) . " +1year")-1;
        if ($out['data']['calendar_id']==0) {
            $total_bankdays=BankDays($yearbgn, $nxtyear, -1);
            $elaps_bankdays=BankDays($yearbgn, $now, -1);
            $reman_bankdays=($total_bankdays-$elaps_bankdays);
        } else {
            $total_bankdays=BankDays($yearbgn, $nxtyear, $calend_id);
            $elaps_bankdays=BankDays($yearbgn, $now, $calend_id);
            $reman_bankdays=($total_bankdays-$elaps_bankdays);
        }
        $out['total_days']=$total_bankdays;
        $out['elaps_days']=$elaps_bankdays;
        $out['remin_days']=$reman_bankdays;
        return $out;
    }

    public function businessdate($date) {
        $calendar=$this->config->item('bank_calendar');
        $holidays=$this->get_calendar_holidays($calendar);
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

    public function add_holiday($session_data, $holiday, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=> 'Error duaring add a holiday'];
        $calend_lines = $session_data['calend_lines'];
        $minidx = count($calend_lines)+1;
        $newline = [];
        $newline[] = [
            'calendar_line_id' => $minidx*(-1),
            'line_date' => $holiday,
        ];
        $newdata = array_merge($newline, $calend_lines);
        $session_data['calend_lines']=$newdata;
        $out['result']=$this->success_result;
        $out['calend_lines']=$newdata;
        usersession($session_id, $session_data);
        return $out;
    }

    public function delete_calendline($session_data, $line, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=> 'Holiday date not found'];
        $calend_lines = $session_data['calend_lines'];
        $deleted = $session_data['deleted'];
        $found = 0;
        $newdata = [];
        foreach ($calend_lines as $row) {
            if ($row['calendar_line_id']==$line) {
                $found=1;
                if ($line>0) {
                    $deleted[]=$line;
                }
            } else {
                $newdata[]=$row;
            }
        }
        if ($found==1) {
            $session_data['calend_lines']=$newdata;
            $session_data['deleted']=$deleted;
            $out['result']=$this->success_result;
            $out['calend_lines']=$newdata;
            usersession($session_id, $session_data);
        }
        return $out;
    }

    public function save_calendar($session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=> 'Holiday date not found'];
        $calend = $session_data['calend'];
        $calend_lines = $session_data['calend_lines'];
        $deleted = $session_data['deleted'];
        $this->db->set('calendar_status', $calend['calendar_status']);
        $this->db->set('calendar_name', $calend['calendar_name']);
        $this->db->set('mon_work', $calend['mon_work']);
        $this->db->set('tue_work', $calend['tue_work']);
        $this->db->set('wed_work', $calend['wed_work']);
        $this->db->set('thu_work', $calend['thu_work']);
        $this->db->set('fri_work', $calend['fri_work']);
        $this->db->set('sat_work', $calend['sat_work']);
        $this->db->set('sun_work', $calend['sun_work']);
        if ($calend['calendar_id']>0) {
            $this->db->where('calendar_id', $calend['calendar_id']);
            $this->db->update('calendars');
            $calend_id = $calend['calendar_id'];
        } else {
            $this->db->insert('calendars');
            $calend_id = $this->db->insert_id();
        }
        foreach ($calend_lines as $row) {
            if ($row['calendar_line_id']<0) {
                $this->db->set('line_date', $row['line_date']);
                $this->db->set('calendar_id', $calend_id);
                $this->db->insert('calendar_lines');
            }
        }
        foreach ($deleted as $row) {
            $this->db->where('calendar_line_id', $row);
            $this->db->delete('calendar_lines');
        }
        $out['result']=$this->success_result;
        usersession($session_id, null);
        return $out;
    }

    public function parse_rushcalend($item_id) {
        $start=date("Y-m-d");// current date
        /* Check Current Day - may be it is weekend or holiday */
        $start_time=strtotime($start);
        if ($this->chk_business_day($start_time, $item_id)==0) {
            $start_time=$this->get_business_date($start_time, 1, $item_id);
            $start=date('Y-m-d',$start_time);
        }

        $proof_date=$start_time;

        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id',FALSE);
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id");
        $this->db->where('i.item_id',$item_id);
        $leads = $this->db->get()->row_array();

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
        if (!isset($leads['calendar_id'])) {
            $this->db->select('min(calendar_id) as calendar_id');
            $this->db->from("calendars");
            $dat=$this->db->get()->row_array();
            $leads['calendar_id']=$dat['calendar_id'];
        }

        $min=($leads['item_lead_a']==0 ? 1 : $leads['item_lead_a']); $ship_array=array();

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
        $cicle_min=$leads['item_lead_a']+120;

        $last_date=strtotime(date("Y-m-d", $proof_date) . " +".$cicle_min." days");

        // Select limitation of data
        $this->db->select('line_date',FALSE);
        $this->db->from("calendar_lines");
        $this->db->where('calendar_id',$leads['calendar_id']);
        $this->db->where("line_date between '".$proof_date."' and '".$last_date."'");
        $cal=$this->db->get()->result_array();

        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['line_date']);
        }

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
                    if ($i>=$row['min'] && $i<$row['max']) {
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
                    // Wed-Apr 27 (+$100 - 1 Day Rush)
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

    public function parse_rushblankcalend($item_id) {
        $start=date("Y-m-d");// current date
        /* Check Current Day - may be it is weekend or holiday */
        $start_time=strtotime($start);
        if ($this->chk_business_day($start_time, $item_id)==0) {
            while (1==1) {
                $start_time=$this->get_business_date($start_time, 1, $item_id);
                if ($this->chk_business_day($start_time, $item_id)==1) {
                    break;
                }
            }
            $start=date('Y-m-d',$start_time);
        }

        $proof_date=$start_time;

        $this->db->select('item_id, item_lead_a, coalesce(item_lead_b,0) as item_lead_b, coalesce(item_lead_c,0) as item_lead_c, c.calendar_id as calendar_id',FALSE);
        $this->db->from("sb_items i");
        $this->db->join("sb_vendor_items vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id");
        $this->db->where('i.item_id',$item_id);
        $leads = $this->db->get()->row_array();

        $min=1;
        $ship_array[]=array(
            'min'=>$min,
            'max'=>1000,
            'price'=>0,
            'rush_term'=>'Standard',
        );
        $cicle_min=$leads['item_lead_a']+120;

        $last_date=strtotime(date("Y-m-d", $proof_date) . " +".$cicle_min." days");

        /* Select limitation of data */
        $this->db->select('line_date',FALSE);
        $this->db->from("calendar_lines");
        $this->db->where('calendar_id',$leads['calendar_id']);
        $this->db->where("line_date between '".$proof_date."' and '".$last_date."'");
        $cal=$this->db->get()->result_array();

        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['line_date']);
        }

        $i=0;$cnt=1;
        $rush=array();$current_rush=0;
        while ($i <= $cicle_min) {
            if ($cnt==1) {
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
                    if ($i>=$row['min'] && $i<$row['max']) {
                        $prefix=$row['price'];
                        $rushterm=$row['rush_term'];
                        break;
                    }
                }
                if ($prefix>=0) {
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

    public function chk_business_day($start_time, $item_id) {
        /* Extract Date from Start Time */
        $start_date=strtotime(date('Y-m-d',$start_time));
        /* Get Weekdate */
        $dayweek=date('w',$start_date);
        /* Get weekends */
        $this->db->select('item_id, c.calendar_id as calendar_id, c.mon_work, c.tue_work, c.wed_work, c.thu_work, c.fri_work, c.sat_work, c.sun_work',FALSE);
        $this->db->from('sb_items i');
        $this->db->join('sb_vendor_items vi','vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id");
        $this->db->where('i.item_id',$item_id);
        $cal = $this->db->get()->row_array();
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

    public function get_business_date($startdate,$diffday,$item_id) {
        // Get a dates of resting during year
        // Select calendar for check bussiness day
        $this->db->select('item_id, c.calendar_id as calendar_id',FALSE);
        $this->db->from('sb_items i');
        $this->db->join('sb_vendor_items vi','vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->join("calendars c","c.calendar_id=v.calendar_id");
        $this->db->where('i.item_id',$item_id);
        $cal = $this->db->get()->row_array();
        $calendar_id=($cal['calendar_id']==NULL ? '0' : $cal['calendar_id']);

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

    public function get_delivery_date($item_id, $blank=0) {
        if ($item_id < 0) {
            // Custom item
            $lead_times = [];
            $delivdate = $this->get_business_date(time(), 1 , $item_id);
            $lead_times[] = [
                'id' => $delivdate.'-0',
                'name' => 'Standard 1 day',
                'current' => 1,
            ];
        } else {
            if ($blank==1) {
                $rushdat = $this->parse_rushblankcalend($item_id);
            } else {
                $rushdat = $this->parse_rushcalend($item_id);
            }
            $caleendlines = $rushdat['rush'];
            $start = $caleendlines[0];
            $lead_times = [];
            $lead_times[] = [
                'id' => $start['id'],
                'name' => $start['rushterm'],
                'current' => $start['current'],
                'price' => $start['price'],
                'date' => $start['date'],
            ];
            $currentterm = $start['rushterm'];
            foreach ($caleendlines as $caleendline) {
                if ($caleendline['rushterm']!==$currentterm) {
                    $lead_times[] = [
                        'id' => $caleendline['id'],
                        'name' => $caleendline['rushterm'],
                        'current' => $caleendline['current'],
                        'price' => $caleendline['price'],
                        'date' => $caleendline['date'],
                    ];
                    $currentterm = $caleendline['rushterm'];
                }
            }
        }
        return $lead_times;
    }

}