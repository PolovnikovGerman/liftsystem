<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Customform_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_count_forms($params) {
        $this->db->select('count(q.custom_quote_id) as cnt');
        $this->db->from('ts_custom_quotes q');
        if (ifset($params,'assign','')=='1') {
            $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
            $this->db->where('le.leademail_id is null');
        }
        if (ifset($params,'search','')!=='') {
            $this->db->like('concat(customer_name, customer_company,customer_email)', $params['search']);
        }
        if (ifset($params,'hideincl',0)==1) {
            $this->db->where('active', $params['hideincl']);
        }
        if (ifset($params,'brand','')!=='') {
            if ($params['brand']=='SR') {
                $this->db->where('brand', $params['brand']);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_customform_data($options) {
        $pagenum = ifset($options,'offset',0);
        $limit = ifset($options,'limit',0);
        $offset = $pagenum * $limit;
        $this->db->select('q.*,le.lead_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        if (ifset($options,'search','')!=='') {
            // Search by customer, company, email
            $this->db->like('concat(q.customer_name, q.customer_company,q.customer_email)', $options['search']);
        }
        if (ifset($options,'assign','')==1) {
            // Assign
            $this->db->where('le.leademail_id is null');
        }
        if (ifset($options,'brand','')!=='') {
            if ($options['brand']=='SR') {
                $this->db->where('q.brand', $options['brand']);
            } else {
                $this->db->where_in('q.brand', ['BT','SB']);
            }
        }
        if (ifset($options,'hideincl',0)==1) {
            $this->db->where('q.active', $options['hideincl']);
        }
        if ($limit !==0 ) {
            if ($offset !==0) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        if (ifset($options,'order_by','')!=='') {
            if (ifset($options,'direction','')!=='') {
                $this->db->order_by($options['order_by'], $options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }

        $dats = $this->db->get()->result_array();
        $out = [];
        // $numpp = $offset;
        $numpp = 0;
        $this->load->model('leads_model');
        foreach ($dats as $dat) {
            $numpp++;
            $dat['numorder'] = $numpp;
            $dat['numpp'] = $dat['quote_number'];
            if (!empty($dat['lead_id'])) {
                $ldat = $this->leads_model->get_lead($dat['lead_id']);
                $dat['lead_number']=ifset($ldat,'lead_number','');
            }
            if (empty($dat['ship_date'])) {
                $dat['event_date'] = '';
            } else {
                $dat['event_date'] = date('m/d/y', $dat['ship_date']);
            }
            $dat['weeknum'] = date('Y-W', strtotime($dat['date_add']));
            $out[] = $dat;
        }
        return $out;
    }

    public function get_customform_details($custom_quote_id) {
        $out=['result' => $this->error_result, 'msg' => 'Info doesn\'t found'];
        $this->db->select('q.*, c.country_name, c.country_iso_code_2, le.lead_id, le.leademail_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_countries c','c.country_id=q.ship_country','left');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        $this->db->where('q.custom_quote_id', $custom_quote_id);
        $data = $this->db->get()->row_array();
        if (ifset($data,'custom_quote_id',0)==$custom_quote_id) {
            $out['result'] = $this->success_result;
            if (empty($data['lead_id'])) {
                $data['lead_date'] = $data['lead_customer'] = $data['lead_mail'] = '';
            } else {
                $this->db->select('lead_date, lead_customer, lead_mail');
                $this->db->from('ts_leads');
                $this->db->where('lead_id', $data['lead_id']);
                $leaddat = $this->db->get()->row_array();
                $data['lead_date'] = $leaddat['lead_date'];
                $data['lead_customer'] = $leaddat['lead_customer'];
                $data['lead_mail'] = $leaddat['lead_mail'];
            }
            $out['data'] = $data;
            // Attachments
            $this->db->select('*');
            $this->db->from('ts_customquote_attachment');
            $this->db->where('custom_quote_id', $custom_quote_id);
            $attach = $this->db->get()->result_array();
            $out['attach'] = $attach;
        }
        return $out;
    }

    public function update_customforn($options) {
        $this->db->where('custom_quote_id', $options['form_id']);
        $this->db->set('active', ifset($options,'activity', 0));
        $this->db->update('ts_custom_quotes');
        return true;
    }

    public function get_customform_totals($brand)
    {
        $date_string = date('Y-m-d');
        $weekdat = explode('-',date("W-Y", strtotime($date_string)));
        $dats = getDatesByWeek($weekdat[0], $weekdat[1]);
        $monday = $dats['start_week'];
        $sunday = $dats['end_week'];
        $weeks = [];
        $numweeks = 52 * 3;
        for ($i=0; $i < $numweeks; $i++) {
            $startd = strtotime("-".$i." week", $monday);
            $finishd = strtotime("-".$i." week", $sunday);
            $curweek = [
                'week' => date('M j, y', $startd),
                'mon' => 0,
                'tue' => 0,
                'wed' => 0,
                'thu' => 0,
                'fri' => 0,
                'sat' => 0,
                'sun' => 0,
                'total' => 0
            ];
            // Get data
            $this->db->select('date_format(date_add, "%w") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
            if ($brand!=='ALL') {
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['SB','BT']);
                }
            }
            $this->db->where('unix_timestamp(date_add) >=', $startd);
            $this->db->where('unix_timestamp(date_add) <=', $finishd);
            $this->db->group_by('dayw');
            $quotes = $this->db->get()->result_array();
            foreach ($quotes as $quote) {
                if ($quote['dayw']==0) {
                    $curweek['sun']+=$quote['cnt'];
                } elseif ($quote['dayw']==1) {
                    $curweek['mon']+=$quote['cnt'];
                } elseif ($quote['dayw']==2) {
                    $curweek['tue']+=$quote['cnt'];
                } elseif ($quote['dayw']==3) {
                    $curweek['wed']+=$quote['cnt'];
                } elseif ($quote['dayw']==4) {
                    $curweek['thu']+=$quote['cnt'];
                } elseif ($quote['dayw']==5) {
                    $curweek['fri']+=$quote['cnt'];
                } elseif ($quote['dayw']==6) {
                    $curweek['sat']+=$quote['cnt'];
                }
                $curweek['total']+=$quote['cnt'];
            }
            $weeks[] = $curweek;
        }
        return $weeks;
    }

    public function get_customform_totalchart($brand)
    {
        $date_string = date('Y-m-d');
        $weekdat = explode('-',date("W-Y", strtotime($date_string)));
        $dats = getDatesByWeek($weekdat[0], $weekdat[1]);
        $monday = $dats['start_week'];
        $sunday = $dats['end_week'];
        $maxdat = $sunday;
//         $mindat = strtotime('-52 weeks', $monday);
        $mindat = strtotime('-156 weeks', $monday);
        $this->db->select('date_format(date_add, "%X-%V") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
        }
        $this->db->where('unix_timestamp(date_add) >=', $mindat);
        $this->db->where('unix_timestamp(date_add) <=', $maxdat);
        $this->db->group_by('dayw');
        $results = $this->db->get()->result_array();
        $data = [];
        $labels = [];
        foreach ($results as $result) {
            $days = explode('-', $result['dayw']);
            $dates = getDatesByWeek($days[1], $days[0]);
            $labels[] = date('M`y', $dates['start_week']);
            // $labels[] = $days[1].'/'.$days[0];
            // $labels[] = $result['dayw'];
            $data[] = $result['cnt'];
        }
        return ['labels'=>$labels,'data'=>$data];
    }

    public function get_customform_monthtotals($brand)
    {
        $months = [];
        $years = [];
        $curyear = intval(date('Y'));
        for ($i=3; $i >= 0; $i--) {
            $years[] = $curyear-$i;
        }
        for($j=1; $j<=12; $j++) {
            $monthname = date('F', strtotime('2012-'.str_pad($j,2,'0',STR_PAD_LEFT).'-01'));
            $monthrow = [
                'month_id' => str_pad($j,2,'0',STR_PAD_LEFT),
                'month' => $monthname,
            ];
            foreach ($years as $year) {
                $monthrow[str_pad($j,2,'0',STR_PAD_LEFT).'-'.$year] = 0;
            }
            $months[] = $monthrow;
        }
        $monthrow = [
            'month_id' => 0,
            'month' => 'Total',
        ];
        foreach ($years as $year) {
            $monthrow['0-'.$year] = 0;
        }
        $months[] = $monthrow;
        $startdate = strtotime($years[0].'-01-01');
        $this->db->select('date_format(date_add, "%m-%Y") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
        }
        $this->db->where('unix_timestamp(date_add) >= ', $startdate);
        $this->db->group_by('dayw');
        $results = $this->db->get()->result_array();
        foreach ($results as $result) {
            $dd = 1;
            $datkey = explode('-', $result['dayw']);
            $mdat = $datkey[0];
            $ydat = $datkey[1];
            $idx = 0;
            foreach ($months as $month) {
                if ($month['month_id']==$mdat) {
                    $months[$idx][$result['dayw']]+=$result['cnt'];
                    break;
                }
                $idx++;
            }
            $idx = 0;
            foreach ($months as $month) {
                if ($month['month_id']==0) {
                    $months[$idx]['0-'.$ydat]+=$result['cnt'];
                    break;
                }
                $idx++;
            }
        }
        return ['totals' => $months, 'years' => $years];
    }

    public function get_customform_monthchart($brand)
    {
        $curyear = intval(date('Y'));
        $yearstart = $curyear-3;
        $datestart = strtotime($yearstart.'-01-01');

        $this->db->select('date_format(date_add, "%Y-%m") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
        }
        $this->db->where('unix_timestamp(date_add) >=', $datestart);
        $this->db->group_by('dayw');
        $results = $this->db->get()->result_array();
        $data = [];
        $labels = [];
        foreach ($results as $result) {
            $days = explode('-', $result['dayw']);
            $date = strtotime($days[0].'-'.$days[1].'-01');
            $labels[] = date('M`y', $date);
            $data[] = $result['cnt'];
        }
        return ['labels'=>$labels,'data'=>$data];
    }

}