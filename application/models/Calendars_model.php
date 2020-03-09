<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Calendars_model extends MY_Model
{

    private $error_message='Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

//    function get_calendars_list($websys) {
//        $this->db->select('*');
//        $this->db->from('calendars');
//        $res=$this->db->get()->result_array();
//        $calend=array();
//        foreach ($res as $row) {
//            foreach ($websys as $wrow) {
//                if ($row['websystem_id']==$wrow['websystem_id']) {
//                    $row['ws_'.$wrow['websystem_id']]='Available';
//                } else {
//                    $row['ws_'.$wrow['websystem_id']]='';
//                }
//            }
//            $calend[]=$row;
//        }
//        return $calend;
//    }
//
//    function get_calendars() {
//        $this->db->select('*');
//        $this->db->from('calendars');
//        $this->db->where('calendar_status',1);
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
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
//
//    function get_calendar_lines($calend_id) {
//        $this->db->select('*');
//        $this->db->from('calendar_lines');
//        $this->db->where('calendar_id',$calend_id);
//        $this->db->order_by('line_date');
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
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

//    public function get_calendar_edit($calend_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if ($calend_id==0) {
//            $out['result']=$this->success_result;
//            $out['data']=array(
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
//
//        } else {
//            $this->db->select('*');
//            $this->db->from('calendars');
//            $this->db->where('calendar_id',$calend_id);
//            $calend=$this->db->get()->row_array();
//            if (!isset($calend['calendar_id'])) {
//                $out['msg']='Calendar Not Found';
//                return $out;
//            }
//            $out['data']=$calend;
//            $out['result']=$this->success_result;
//        }
//        // Calc Business Days, Elapsed, Remaining
//        $now=strtotime(date('Y-m-d'));
//        $yearbgn=strtotime(date('Y').'-01-01');
//        $nxtyear=strtotime(date("Y-m-d", $yearbgn) . " +1year")-1;
//        if ($out['data']['calendar_id']==0) {
//            $total_bankdays=BankDays($yearbgn, $nxtyear, -1);
//            $elaps_bankdays=BankDays($yearbgn, $now, -1);
//            $reman_bankdays=($total_bankdays-$elaps_bankdays);
//        } else {
//            $total_bankdays=BankDays($yearbgn, $nxtyear, $calend_id);
//            $elaps_bankdays=BankDays($yearbgn, $now, $calend_id);
//            $reman_bankdays=($total_bankdays-$elaps_bankdays);
//        }
//        $out['total_days']=$total_bankdays;
//        $out['elaps_days']=$elaps_bankdays;
//        $out['remin_days']=$reman_bankdays;
//
//        return $out;
//
//    }

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

}