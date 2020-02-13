<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Searchresults_model extends My_Model
{

    private $INIT_MSG = 'Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

    public function get_search_bytime($brand, $d_bgn = '', $d_end = '')
    {
        /* Empty D-END */
        if ($d_end == '') {
            $this->db->select('max(search_time) as max_time');
            $this->db->from('sb_search_results');
            if ($brand!=='ALL') {
                $this->db->where('brand', $brand);
            }
            $res = $this->db->get()->row_array();
            if (isset($res['max_time'])) {
                $max_time = $res['max_time'];
                $max_time = strtotime($max_time);
            } else {
                $max_time = time();
            }
            $d_end = strtotime(date('Y-m-d', $max_time) . ' 23:59:59');
        }
        /* Calculate nearlest SUNDAY */
        $cur_weekday = date('w', $d_end);
        switch ($cur_weekday) {
            case 0:
                $period = 0 * 24 * 60 * 60;
                break;
            case 1:
                $period = 6 * 24 * 60 * 60;
                break;
            case 2:
                $period = 5 * 24 * 60 * 60;
                break;
            case 3:
                $period = 4 * 24 * 60 * 60;
                break;
            case 4:
                $period = 3 * 24 * 60 * 60;
                break;
            case 5:
                $period = 2 * 24 * 60 * 60;
                break;
            case 6:
                $period = 1 * 24 * 60 * 60;
                break;
            default:
                $period = 0;
                break;
        }
        $d_end = $d_end + $period;

        if ($d_bgn == '') {
            $this->db->select('min(search_time) as min_time');
            $this->db->from('sb_search_results');
            $res = $this->db->get()->row_array();
            if (isset($res['min_time'])) {
                $min_time = $res['min_time'];
                $min_time = strtotime($min_time);
            } else {
                $min_time = time();
            }
            $d_bgn = strtotime(date('Y-m-d', $min_time) . ' 00:00:00');
        }
        /* Calculate nearlest monday */
        $cur_weekday = date('w', $d_bgn);
        switch ($cur_weekday) {
            case 0:
                $period = 6 * 24 * 60 * 60;
                break;
            case 1:
                $period = 0;
                break;
            case 2:
                $period = 1 * 24 * 60 * 60;
                break;
            case 3:
                $period = 2 * 24 * 60 * 60;
                break;
            case 4:
                $period = 3 * 24 * 60 * 60;
                break;
            case 5:
                $period = 4 * 24 * 60 * 60;
                break;
            case 6:
                $period = 5 * 24 * 60 * 60;
                break;
            default:
                $period = 0;
                break;
        }


        $d_bgn = $d_bgn - $period;
        $this->db->select('distinct date_format(search_time,\'%v_%Y\') as search_date',FALSE);
        $this->db->from('sb_search_results');
        $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        $this->db->where('unix_timestamp(search_time) <= ', $d_end);

        $res_ar = $this->db->get()->result_array();

        $out_array = array();
        $search_array = array();
        $start_date = $d_bgn;

        foreach ($res_ar as $row) {
            $week_bgn = $start_date;
            $week_end = strtotime(date("Y-m-d", $week_bgn) . " +6 days");
            $month_bgn = date('m', $week_bgn);
            $month_end = date('m', $week_end);
            if ($month_bgn != $month_end) {
                $index_date = date('M d', $week_bgn) . '-' . date('M d, Y', $week_end);
            } else {
                $index_date = date('M d', $week_bgn) . '-' . date('d, Y', $week_end);
            }

            $out_array[] = array('date' => $index_date, 'mon_good' => 0, 'mon_bad' => 0, 'tue_good' => 0, 'tue_bad' => 0, 'wed_good' => 0, 'wed_bad' => 0, 'thu_good' => 0, 'thu_bad' => 0, 'fri_good' => 0, 'fri_bad' => 0, 'sat_good' => 0, 'sat_bad' => 0, 'sun_good' => 0, 'sun_bad' => 0);
            $search_array[] = $row['search_date'];
            $start_date = strtotime(date("Y-m-d", $start_date) . " +7 days");
        }

        $this->db->select('date_format(search_time,\'%Y-%m-%d\') as search_date,search_result,count(*) as cnt');
        $this->db->from('sb_search_results');
        $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        $this->db->where('unix_timestamp(search_time) <= ', $d_end);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('search_date,search_result');
        $this->db->order_by('search_time');
        $res_ar=$this->db->get()->result_array();

        foreach ($res_ar as $row) {
            /* Search week number */
            $dat = strtotime($row['search_date']);
            $seach_week = date('W_Y', $dat);


            $index = array_search($seach_week, $search_array);
            if ($row['search_result'] == 0) {
                $prefix = 'bad';
            } else {
                $prefix = 'good';
            }
            switch (date('w', $dat)) {
                case 0:
                    /* Sunday */
                    $out_array[$index]['sun_' . $prefix] = $row['cnt'];
                    break;
                case 1:
                    $out_array[$index]['mon_' . $prefix] = $row['cnt'];
                    break;
                case 2:
                    $out_array[$index]['tue_' . $prefix] = $row['cnt'];
                    break;
                case 3:
                    $out_array[$index]['wed_' . $prefix] = $row['cnt'];
                    break;
                case 4:
                    $out_array[$index]['thu_' . $prefix] = $row['cnt'];
                    break;
                case 5:
                    $out_array[$index]['fri_' . $prefix] = $row['cnt'];
                    break;
                case 6:
                    $out_array[$index]['sat_' . $prefix] = $row['cnt'];
                    break;
                default:
                    break;
            }
        }
        return $out_array;
    }

    public function get_search_bykeywords($brand, $d_bgn, $d_end, $show_result) {
        /* Empty D-END */
        if ($d_end=='') {
            $this->db->select('max(search_time) as max_time');
            $this->db->from('sb_search_results');
            if ($brand!=='ALL') {
                $this->db->where('brand', $brand);
            }
            $res=$this->db->get()->row_array();
            if (isset($res['max_time'])) {
                $max_time=$res['max_time'];
                $max_time=strtotime($max_time);
            } else {
                $max_time=time();
            }
            $d_end=strtotime(date('Y-m-d',$max_time).' 23:59:59');
        } else {
            $d_end=strtotime(date('Y-m-d',$d_end).' 23:59:59');
        }

        if ($d_bgn=='') {
            $this->db->select('min(search_time) as min_time');
            $this->db->from('sb_search_results');
            if ($brand!=='ALL') {
                $this->db->where('brand', $brand);
            }
            $res=$this->db->get()->row_array();
            if (isset($res['min_time'])) {
                $min_time=$res['min_time'];
                $min_time=strtotime($min_time);
            } else {
                $min_time=time();
            }
            $d_bgn=strtotime(date('Y-m-d',$min_time).' 00:00:00');
        } else {
            $d_bgn=strtotime(date('Y-m-d',$d_bgn).' 00:00:00');
        }
        $this->db->select('search_text, search_result, count(*) as cnt');
        $this->db->from('sb_search_results');
        $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        $this->db->where('unix_timestamp(search_time) <= ', $d_end);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        if ($show_result==0) {
            $this->db->where('search_result',0);
        } elseif ($show_result==1) {
            $this->db->where('search_result',1);
        }
        $this->db->group_by('search_text, search_result');
        $this->db->order_by('cnt desc');
        $res_ar=$this->db->get()->result_array();
        return $res_ar;
    }
}