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
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['BT','SB']);
                }
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

            $out_array[] = array('date' => $index_date,
                'mon_good' => 0, 'mon_bad' => 0, 'mon_total' => 0,
                'tue_good' => 0, 'tue_bad' => 0, 'tue_total' => 0,
                'wed_good' => 0, 'wed_bad' => 0, 'wed_total' => 0,
                'thu_good' => 0, 'thu_bad' => 0, 'thu_total' => 0,
                'fri_good' => 0, 'fri_bad' => 0, 'fri_total' => 0,
                'sat_good' => 0, 'sat_bad' => 0, 'sat_total' => 0,
                'sun_good' => 0, 'sun_bad' => 0, 'sun_total' => 0,);
            $search_array[] = $row['search_date'];
            $start_date = strtotime(date("Y-m-d", $start_date) . " +7 days");
        }

        $this->db->select('date_format(search_time,\'%Y-%m-%d\') as search_date,search_result,count(*) as cnt');
        $this->db->from('sb_search_results');
        $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        $this->db->where('unix_timestamp(search_time) <= ', $d_end);
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $this->db->group_by('search_date,search_result');
        $this->db->order_by('search_date');
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
                    $out_array[$index]['sun_total'] += $row['cnt'];
                    break;
                case 1:
                    $out_array[$index]['mon_' . $prefix] = $row['cnt'];
                    $out_array[$index]['mon_total'] += $row['cnt'];
                    break;
                case 2:
                    $out_array[$index]['tue_' . $prefix] = $row['cnt'];
                    $out_array[$index]['tue_total'] += $row['cnt'];
                    break;
                case 3:
                    $out_array[$index]['wed_' . $prefix] = $row['cnt'];
                    $out_array[$index]['wed_total'] += $row['cnt'];
                    break;
                case 4:
                    $out_array[$index]['thu_' . $prefix] = $row['cnt'];
                    $out_array[$index]['thu_total'] += $row['cnt'];
                    break;
                case 5:
                    $out_array[$index]['fri_' . $prefix] = $row['cnt'];
                    $out_array[$index]['fri_total'] += $row['cnt'];
                    break;
                case 6:
                    $out_array[$index]['sat_' . $prefix] = $row['cnt'];
                    $out_array[$index]['sat_total'] += $row['cnt'];
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
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['BT','SB']);
                }
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
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['BT','SB']);
                }
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
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
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

    public function get_search_byipaddress($brand, $d_bgn,$d_end) {
        if ($d_end=='') {
            $this->db->select('max(search_time) as max_time');
            $this->db->from('sb_search_results');
            if ($brand!=='ALL') {
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['BT','SB']);
                }
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
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['BT','SB']);
                }
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
        $this->db->select('s.search_ip, g.city_name, g.region_code, g.country_name, g.country_code,  count(*) as cnt');
        $this->db->from('sb_search_results s');
        $this->db->join('sb_geoips g', 'g.user_ip=s.search_ip','left');
        $this->db->where('unix_timestamp(s.search_time) >= ', $d_bgn);
        $this->db->where('unix_timestamp(s.search_time) <= ', $d_end);
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('s.brand', $brand);
            } else {
                $this->db->where_in('s.brand', ['BT','SB']);
            }
        }
        $this->db->group_by('s.search_ip, g.city_name, g.region_code, g.country_name, g.country_code');
        $this->db->order_by('cnt','desc');
        $results=$this->db->get()->result_array();
        $out = [];
        $numpp = 1;
        foreach ($results as $result) {
            if ($result['country_code']=='US') {
                $usrgeo = $result['city_name'].', '.$result['region_code'];
//            } elseif ($result['country_code']=='CA') {
//                $usrgeo = $result['city_name'].', '.$result['region_code'].', '.$result['country_code'];
            } else {
                $usrgeo = $result['city_name'].' '.$result['country_name'];
            }
            $out[] = [
                'rank' => $numpp,
                'search_ip' => $result['search_ip'],
                'search_user' => $usrgeo,
                'cnt' => $result['cnt'],
            ];
            $numpp++;
        }
        return $out;
    }

    public function get_searchdates($brand)
    {
        $this->db->select('max(search_time) as max_time, min(search_time) as min_time');
        $this->db->from('sb_search_results');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        $minyear = date('Y', strtotime($res['min_time']));
        $maxyear = date('Y', strtotime($res['max_time']));
        $maxdate = strtotime($res['max_time']);
        $mindate = strtotime($res['min_time']);
        // Transform
        $maxdate = strtotime(date('Y-m', $maxdate).'-01');
        $final = strtotime("-1 month", $mindate);
        $mindate = strtotime(date('Y-m',$final).'-01');
        $months = [];
        while ($maxdate > $mindate) {
            $months[] = ['key' => date('Y-m', $maxdate), 'val' => date('F Y', $maxdate)];
            $maxdate = strtotime("-1 month", $maxdate);
            if ($maxdate<=$mindate) {
                break;
            }
        }
        return [
            'minyear' => $minyear,
            'maxyear' => $maxyear,
            'months' => $months,
        ];
    }

    public function get_count_searches($display_option, $d_bgn, $d_end, $brand)
    {
        $out=[];
        // Calc words
        $this->db->select('search_text, search_result, count(search_result_id)')->from('sb_search_results');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand',['SB', 'BT']);
            }
        }
        if (!empty($d_bgn)) {
            $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        }
        if (!empty($d_end)) {
            $this->db->where('unix_timestamp(search_time) <= ', $d_end);
        }
        if ($display_option==1) {
            $this->db->where('search_result',1);
        } elseif ($display_option==2) {
            $this->db->where('search_result',0);
        }
        $this->db->group_by('search_text, search_result');
        $kres = $this->db->get()->result_array();
        $out['keyword'] = count($kres);
        $this->db->select('search_ip, count(search_result_id)')->from('sb_search_results');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand',['SB', 'BT']);
            }
        }
        if (!empty($d_bgn)) {
            $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        }
        if (!empty($d_end)) {
            $this->db->where('unix_timestamp(search_time) <= ', $d_end);
        }
        if ($display_option==1) {
            $this->db->where('search_result',1);
        } elseif ($display_option==2) {
            $this->db->where('search_result',0);
        }
        $this->db->group_by('search_ip');
        $ires = $this->db->get()->result_array();
        $out['ipaddr'] = count($ires);
        return $out;
    }

    public function get_keywords_data($display_option, $d_bgn, $d_end, $brand, $limit, $offset)
    {
        $this->db->select('search_text,  search_result, count(search_result_id) as cnt')->from('sb_search_results');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand',['SB', 'BT']);
            }
        }
        if (!empty($d_bgn)) {
            $this->db->where('unix_timestamp(search_time) >= ', $d_bgn);
        }
        if (!empty($d_end)) {
            $this->db->where('unix_timestamp(search_time) <= ', $d_end);
        }
        if ($display_option==1) {
            $this->db->where('search_result',1);
        } elseif ($display_option==2) {
            $this->db->where('search_result',0);
        }
        $this->db->group_by('search_text,  search_result');
        $this->db->order_by('cnt', 'desc');
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        $results = $this->db->get()->result_array();
        $out = [];
        $start = $offset+1;
        foreach ($results as $result) {
            $out[] = [
                'rank' => $start,
                'keyword' => $result['search_text'],
                'result' => $result['search_result'],
                'searches' => $result['cnt'],
            ];
            $start++;
        }
        return $out;
    }

    public function get_ipaddress_data($display_option, $d_bgn, $d_end, $brand, $limit, $offset)
    {
        $this->db->select('s.search_ip, g.city_name, g.region_code, g.country_name, g.country_code, count(s.search_result_id) as cnt');
        $this->db->from('sb_search_results s');
        $this->db->join('sb_geoips g', 'g.user_ip=s.search_ip','left');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('s.brand', $brand);
            } else {
                $this->db->where_in('s.brand',['SB', 'BT']);
            }
        }
        if (!empty($d_bgn)) {
            $this->db->where('unix_timestamp(s.search_time) >= ', $d_bgn);
        }
        if (!empty($d_end)) {
            $this->db->where('unix_timestamp(s.search_time) <= ', $d_end);
        }
        if ($display_option==1) {
            $this->db->where('s.search_result',1);
        } elseif ($display_option==2) {
            $this->db->where('s.search_result',0);
        }
        $this->db->group_by('s.search_ip, g.city_name, g.region_code, g.country_name, g.country_code');
        $this->db->order_by('cnt', 'desc');
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        $results = $this->db->get()->result_array();
        $start = $offset+1;
        $out=[];
        foreach ($results as $result) {
            if ($result['country_code']=='US') {
                $location=$result['city_name'].', '.$result['region_code'];
            } else {
                $location=$result['city_name'].' '.$result['country_name'];
            }
            $out[] = [
                'rank' => $start,
                'ipaddres' => $result['search_ip'],
                'searches' => $result['cnt'],
                'location' => $location,
            ];
            $start++;
        }
        return $out;
    }

    private function _searchdates($brand)
    {
        $this->db->select('max(search_time) as max_time, min(search_time) as min_time')->from('sb_search_results');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        return [
            'd_bgn' => strtotime(date('Y-m-d', $res['min_time']).' 00:00:00'),
            'd_end' => strtotime(date('Y-m-d', $res['max_time']).' 23:59:59'),
        ];
    }
}