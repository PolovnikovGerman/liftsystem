<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of login_model
 *
 * @author german polovnikov
 */
class Email_model extends My_Model
{

    private $INIT_MSG = 'Unknown error. Try later';
    private $empty_html_content = '&nbsp;';
    function __construct()
    {
        parent::__construct();
    }

    public function get_email_templates()
    {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->order_by('email_template_id');
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function get_main_template()
    {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->order_by('email_template_id');
        $this->db->limit(1);
        $res = $this->db->get()->row_array();
        return $res;

    }

    public function get_email_template($template)
    {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->where('email_template_id', $template);
        $res = $this->db->get()->row_array();
        return $res;
    }

    public function get_emailtemplate_byname($template_name)
    {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->where('email_template_name', $template_name);
        $res = $this->db->get()->row_array();
        return $res;
    }

    public function save_template($email_template_id, $email_template_body, $email_template_subject, $email_template_address)
    {
        $out = array('result' => $this->error_result, 'msg' => $this->INIT_MSG);
        if (!$email_template_id) {
            $out['msg'] = 'Unknown Email Template';
        } elseif (empty($email_template_subject)) {
            $out['msg'] = 'Enter Email Subject';
        } elseif (empty($email_template_body)) {
            $out['msg'] = 'Enter Email Body';
            // }elseif (empty($email_template_address)) {
            //    $out['msg']='Enter Sender Email';
        } elseif (!empty($email_template_address) && !valid_email_address($email_template_address)) {
            $out['msg'] = 'Enter Correct Sender Email';
        } else {
            $this->db->set('email_template_body', $email_template_body);
            $this->db->set('email_template_subject', $email_template_subject);
            $this->db->set('email_template_address', $email_template_address);
            $this->db->where('email_template_id', $email_template_id);
            $this->db->update('ts_email_templates');
            $out['result'] = $this->success_result;
            $out['msg'] = '';
        }
        return $out;
    }

    public function logsendmail($options)
    {
        $fl_ins = 0;
        if (isset($options['from'])) {
            $fl_ins = 1;
            $mailfrom = '';
            if (is_array($options['from'])) {
                foreach ($options['from'] as $row) {
                    $mailfrom .= $row . ', ';
                }
                $mailfrom = substr($mailfrom, 0, -2);
            } else {
                $mailfrom = $options['from'];
            }
            $this->db->set('from', $mailfrom);
        }
        if (isset($options['to'])) {
            $fl_ins = 1;
            $mailto = '';
            if (is_array($options['to'])) {
                foreach ($options['to'] as $row) {
                    $mailto .= $row . ', ';
                }
                $mailto = substr($mailto, 0, -2);
            } else {
                $mailto = $options['to'];
            }
            $this->db->set('to', $mailto);
        }
        if (isset($options['cc'])) {
            $fl_ins = 1;
            $mailcc = '';
            if (is_array($options['cc'])) {
                foreach ($options['cc'] as $row) {
                    $mailcc .= $row . ', ';
                }
                $mailcc = substr($mailcc, 0, -2);
            } else {
                $mailcc = $options['cc'];
            }
            $this->db->set('cc', $mailcc);
        }
        if (isset($options['message'])) {
            $fl_ins = 1;
            $this->db->set('body', $options['message']);
        }
        if (isset($options['attachments'])) {
            $fl_ins = 1;
            $mailattach = '';
            if (is_array($options['attachments'])) {
                foreach ($options['attachments'] as $row) {
                    $mailattach .= $row . PHP_EOL;
                }
            } else {
                $mailattach = $options['attachments'];
            }
            $this->db->set('attachments', $mailattach);
        }
        if (isset($options['result'])) {
            $fl_ins = 1;
            $this->db->set('result', $options['result']);
        }
        if (isset($options['user_id'])) {
            $fl_ins = 1;
            $this->db->set('user_id', $options['user_id']);
        }
        if (isset($options['subject'])) {
            $fl_ins = 1;
            $this->db->set('subject', $options['subject']);
        }
        if ($fl_ins == 1) {
            $this->db->set('message_time', time());
            $this->db->insert('ts_sendmaillogs');
        }
        return TRUE;
    }

    public function get_emails_count($brand, $type, $status = -1)
    {
        $this->db->select("count(email_id) as cnt", FALSE);
        $this->db->from('ts_emails');
        $this->db->where("email_type", $type);
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand',$brand);
            } else {
                $this->db->where_in('brand',['BT','SB']);
            }
        }
        // $this->db->where('email_websys', $this->websys);
        if ($status != -1) {
            $this->db->where('email_status', $status);
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function count_messages($options)
    {
        $this->db->select('count(email_id) as cnt');
        $this->db->from('ts_emails');
        // $this->db->where('email_websys', $this->websys);

        if (isset($options['type'])) {
            $this->db->where("email_type", $options['type']);
        }
        if (isset($options['startdate'])) {
            $this->db->where('unix_timestamp(email_date) >=', $options['startdate']);
        }
        if (isset($options['enddate'])) {
            $this->db->where('unix_timestamp(email_date) <= ', $options['enddate']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
            if ($options=='SR') {
                $this->db->where('brand',$options['brand']);
            } else {
                $this->db->where_in('brand',['BT','SB']);
            }
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function export_signupdata($options)
    {
        $out = ['result' => 0, 'msg' => 'Data not found'];
        $this->db->select('email_date, email_sendermail');
        $this->db->from('ts_emails');
        // $this->db->where('email_websys', $this->websys);
        if (isset($options['type'])) {
            $this->db->where("email_type", $options['type']);
        }
        if (isset($options['startdate'])) {
            $this->db->where('unix_timestamp(email_date) >= ', $options['startdate']);
        }
        if (isset($options['enddate'])) {
            $this->db->where('unix_timestamp(email_date) <= ', $options['enddate']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
            if ($options['brand']=='SR') {
                $this->db->where('brand',$options['brand']);
            } else {
                $this->db->where_in('brand',['BT','SB']);
            }

        }
        $this->db->order_by('email_id');
        $res = $this->db->get()->result_array();
        if (count($res) > 0) {
            $this->load->model('exportexcell_model');
            $report_name = $this->exportexcell_model->export_signup($res);
            $out['result'] = 1;
            $out['url'] = $this->config->item('pathpreload') . $report_name;
        }
        return $out;
    }

    public function get_emails_count_by_type($brand)
    {
        $this->db->select('email_type, email_status, count(*) as cnt_rec', FALSE);
        $this->db->from('ts_emails');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand',$brand);
            } else {
                $this->db->where_in('brand',['BT','SB']);
            }
        }
        // $this->db->where('email_websys', $this->websys);
        $this->db->group_by('email_type, email_status');
        $res = $this->db->get()->result_array();
        $mailstat = array();
        $mailstat['Questions'] = array('new' => 0, 'all' => 0,);
        $mailstat['Custom_SB'] = array('new' => 0, 'all' => 0,);
        $mailstat['Leads'] = array('new' => 0, 'all' => 0,);
        $mailstat['Testimonials'] = array('new' => 0, 'all' => 0,);
        $mailstat['Signups'] = array('new' => 0, 'all' => 0,);
        $mailstat['Art_Submit'] = array('new' => 0, 'all' => 0,);
        foreach ($res as $row) {
            if ($row['email_status'] == 0) {
                $mailstat[$row['email_type']]['new'] += $row['cnt_rec'];
                $mailstat[$row['email_type']]['all'] += $row['cnt_rec'];
            } else {
                $mailstat[$row['email_type']]['all'] += $row['cnt_rec'];
            }
        }
        return $mailstat;
    }

    function get_emails($options, $order_by, $direct, $limit, $offset)
    {
        $this->db->select('*');
        $this->db->from('ts_emails');
        foreach ($options as $key => $value) {
            if ($key != 'startdate' && $key != 'enddate' && $key!= 'brand') {
                $this->db->where($key, $value);
            } elseif ($key == 'startdate') {
                $this->db->where('unix_timestamp(email_date) >= ', $value);
            } elseif ($key == 'enddate') {
                $this->db->where('unix_timestamp(email_date) <= ', $value);
            }
        }
        if ($options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('brand',$options['brand']);
            } else {
                $this->db->where_in('brand',['BT','SB']);
            }
        }
        $this->db->order_by($order_by, $direct);
        $this->db->limit($limit, $offset);
        $results = $this->db->get()->result_array();
        $out_array = array();
        foreach ($results as $row) {
            $row['email_qty'] = (intval($row['email_qty']) == 0 ? '' : number_format($row['email_qty'], 0, '.', ','));
            $row['email_total'] = (floatval($row['email_total']) == 0 ? '' : '$' . number_format($row['email_total'], 2, '.', ','));
            if ($row['email_ordernum'] == '') {
                $row['email_ordernum'] = str_repeat('-', 12);
            }
            $out_array[] = $row;
        }
        return $out_array;
    }

    function get_email_details($mail_id, $type_out = 'html')
    {
        $ci =& get_instance();
        $this->db->select("*,date_format(email_date,'%m/%d/%y') as email_date_show", FALSE);
        $this->db->from('ts_emails');
        $this->db->where('email_id', $mail_id);
        $res = $this->db->get()->row_array();
        if (!isset($res['email_id'])) {
            $out_arr = array('error' => 'Message not_found');
        } else {
            if ($res['email_rep'] == NULL) {
                $res['email_rep'] = '';
            }
            if ($type_out == 'html') {
                /* Add &nbsp instead empty val */
                foreach ($res as $key => $value) {
                    $out_arr[$key] = ($value == '' ? '&nbsp' : $value);
                }
            } else {
                $out_arr = $res;
            }
        }
        return $out_arr;
    }

    function get_email_attachments($mail_id)
    {
        $this->db->select('*');
        $this->db->from($this->emailattach_db);
        $this->db->where('email_attachment_emailid', $mail_id);
        $res = $this->db->get()->result_array();
        return $res;

    }

    function update_status($mail_id, $mail_rep)
    {
        $this->db->set('email_rep', strtoupper($mail_rep));
        $this->db->set('email_status', 1);
        $this->db->where('email_id', $mail_id);
        $this->db->update('ts_emails');
    }

    function email_insert($data)
    {
        $insallow = 1;
        if (isset($data['email_type']) && $data['email_type'] == 'Signups') {
            /* Check that such email is not present in DB */
            $this->db->select('count(*) as cnt', FALSE);
            $this->db->from('ts_emails');
            $this->db->where('email_websys', $this->websys);
            $this->db->where('email_type', 'Signups');
            $this->db->where('email_sendermail', $data['email_sendermail']);
            $res = $this->db->get()->row_array();
            if ($res['cnt'] != 0) {
                $insallow = 0;
            }
        }
        if ($insallow == 0) {
            $res = FALSE;
        } else {
            foreach ($data as $key => $value) {
                $this->db->set($key, $value);
            }
            $this->db->set('email_websys', $this->websys);
            $this->db->insert('ts_emails');
            $res = $this->db->insert_id();
            if ($res != 0) {
                /* Create Short notification */
                if ($data['email_type'] == Emails_model::EMAIL_PROOFREQUEST) {
                    // Insert - update additional Fields
                    $this->proof_request_extend($data, $res);
                }
            }
        }
        return $res;
    }

    /* Insert data to ARTWORK */
    private function artwork_update($data)
    {
        $db_table = $this->config->item('system_prefix') . '.ts_artworks';
        $this->db->set('mail_id', $data['mail_id']);
        $this->db->set('time_create', date('Y-m-d H:i:s'));
        $this->db->set('time_update', date('Y-m-d H:i:s'));
        $this->db->set('customer_instruct', $data['customer_instruct']);
        $this->db->set('customer', $data['customer']);
        $this->db->set('customer_phone', $data['customer_phone']);
        $this->db->set('customer_email', $data['customer_email']);
        $this->db->set('item_name', $data['item_name']);
        $this->db->set('item_number', $data['item_number']);
        $this->db->set('item_id', $data['item_id']);
        $this->db->set('item_color', $data['item_color']);
        $this->db->set('item_qty', $data['item_qty']);
        $this->db->set('artwork_note', $data['artwork_note']);
        $this->db->insert($db_table);
        $artw_id = $this->db->insert_id();
        return $artw_id;
    }

    /* Insert ARTWORK_ARTS */
    private function artdata_update($data)
    {
        $db_table = $this->config->item('system_prefix') . '.ts_artwork_arts';
        $this->db->set('artwork_id', $data['artwork_id']);
        $this->db->set('art_type', $data['art_type']);
        $this->db->set('art_ordnum', $data['art_ordnum']);
        $this->db->set('logo_src', $data['logo_src']);
        $this->db->set('redraw_time', $data['redraw_time']);
        $this->db->set('redrawvect', $data['redrawvect']);
        $this->db->set('customer_text', $data['customer_text']);
        $this->db->set('font', $data['font']);
        $this->db->set('art_numcolors', $data['art_numcolors']);
        $this->db->set('art_color1', $data['art_color1']);
        $this->db->set('art_color2', $data['art_color2']);
        $this->db->insert($db_table);
        $res = $this->db->insert_id();
        return $res;
    }

    /* Insert - update additional Fields  */
    function proof_request_extend($data, $email_id)
    {
        /* INS NEW proof num */
        $ci =& get_instance();
        $usrlogo = get_json_param($data['email_other_info'], 'userlogo');
        $usrtext = get_json_param($data['email_other_info'], 'usertext');
        $newproofnum = $this->new_proof_num();
        $this->db->set('proof_num', $newproofnum);
        $this->db->set('proof_updated', time());
        $this->db->where('email_id', $email_id);
        $this->db->update('ts_emails');

        /* Rename ART LOGO, rebuild Other Info */
        $new_otherinfo = array();
        if ($usrlogo) {
            //
            $short_place = $ci->config->item('artwork_logo_relative');
            $full_place = $ci->config->item('artwork_logo');
            $logofilename = str_replace($short_place, '', $usrlogo);

            $filedetails = extract_filename($logofilename);
            $newlogoname = 'pr' . $newproofnum . '-1.' . $filedetails['ext'];


            /* Copy Logo with new name */
            $res1 = copy($full_place . $logofilename, $full_place . $newlogoname);
            if ($res1) {
                $usrlogo = $short_place . $newlogoname;
                // unlink($full_place.$logofilename);
            }
            $new_otherinfo['usrlogo'] = $usrlogo;
        }
        /* Repack other Info */

        $color_1 = '';
        $color_2 = '';
        $font = '';
        if ($usrtext) {
            $new_otherinfo['usrtext'] = $usrtext;
        }
        $numcolors = get_json_param($data['email_other_info'], 'numcolors');
        if ($numcolors) {
            $new_otherinfo['numcolors'] = $numcolors;
        } else {
            $new_otherinfo['numcolors'] = '';
        }
        $user_color1 = get_json_param($data['email_other_info'], 'user_color1');
        if ($user_color1) {
            $new_otherinfo['user_color1'] = $user_color1;
            $color_1 = $user_color1;
        }
        $user_color2 = get_json_param($data['email_other_info'], 'user_color2');
        if ($user_color2) {
            $new_otherinfo['user_color2'] = $user_color2;
            $color_2 = $user_color2;
        }
        $user_font = get_json_param($data['email_other_info'], 'user_font');
        if ($user_font) {
            $new_otherinfo['user_font'] = $user_font;
            $font = $user_font;
        }
        $itemcolors = get_json_param($data['email_other_info'], 'itemcolors');
        if ($itemcolors) {
            $new_otherinfo['itemcolors'] = $itemcolors;
        }
        $item_id = get_json_param($data['email_other_info'], 'item_id');
        if ($item_id) {
            // $new_otherinfo['itemcolors']=$item_id;
            $data['item_id'] = $item_id;
        } else {
            $data['item_id'] = '';
        }

        /* Create Artwork */
        $art_note = 'Item Qty ' . $data['email_qty'] . PHP_EOL;
        $art_note .= 'Item Color ' . $data['email_special_requests'];
        $art = array('mail_id' => $email_id, 'customer_instruct' => $data['email_text'], 'customer' => $data['email_sender'], 'customer_phone' => $data['email_senderphone'], 'customer_email' => $data['email_sendermail'], 'item_name' => $data['email_item_name'], 'item_number' => $data['email_item_number'], 'item_id' => ($data['item_id'] == '' ? NULL : $data['item_id']), 'item_color' => $data['email_special_requests'], 'item_qty' => $data['email_qty'], 'artwork_note' => $art_note,);
        $artw_id = $this->artwork_update($art);

        if ($artw_id != 0) {
            // Add History
            $db_history = $this->config->item('system_prefix') . '.ts_artwork_history';
            $history_msg = 'Proof Request was created ' . date('m/d/Y H:i:s');
            $this->db->set('artwork_id', $artw_id);
            $this->db->set('created_time', time());
            $this->db->set('message', $history_msg);
            $this->db->insert($db_history);

            // All OK - insert artworks
            $logodat = 0;
            $textdat = 0;
            $num_pp = 1;
            if ($usrlogo) {
                $artdat = array('artwork_id' => $artw_id, 'art_type' => 'Logo', 'art_ordnum' => $num_pp, 'logo_src' => $usrlogo, 'redraw_time' => time(), 'redrawvect' => 1, 'customer_text' => '', 'font' => '', 'art_numcolors' => $numcolors, 'art_color1' => $color_1, 'art_color2' => $color_2,);
                $art_log = $this->artdata_update($artdat);
                if ($art_log) {
                    $num_pp++;
                    $logodat = 1;
                }
            }
            if ($usrtext) {
                $artdat = array('artwork_id' => $artw_id, 'art_type' => 'Text', 'art_ordnum' => $num_pp, 'logo_src' => NULL, 'redraw_time' => 0, 'redrawvect' => 0, 'customer_text' => $usrtext, 'font' => $font, 'art_numcolors' => $numcolors, 'art_color1' => $color_1, 'art_color2' => $color_2,);
                $art_txt = $this->artdata_update($artdat);
                if ($art_txt) {
                    $num_pp++;
                    $textdat = 1;
                }
            }
            if ($usrlogo || $usrtext) {
                $this->db->set('email_art', 1);
                $this->db->set('email_art_update', time());
            }
            if ($textdat == 1 && $logodat == 0) {
                $this->db->set('email_redrawn', 1);
                $this->db->set('email_redrawn_update', time());
                $this->db->set('email_vectorized', 1);
                $this->db->set('email_vectorized_update', time());
            } else {
                $this->db->set('email_redrawn', 1);
                $this->db->set('email_redrawn_update', time());
            }
            $this->db->where('email_id', $email_id);
            $this->db->update('ts_emails');
        }

        $this->db->set('email_other_info', json_encode($new_otherinfo));
        $this->db->where('email_id', $email_id);
        $this->db->update('ts_emails');
        return TRUE;
    }

    private function isProofExist($proof_num)
    {
        $this->db->select('email_id');
        $this->db->from('ts_emails');
        $this->db->where('proof_num', $proof_num);
        $res = $this->db->get()->row_array();
        if (isset($res['email_id'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function new_proof_num()
    {
        $this->db->select('max(proof_num) as proof');
        $this->db->from('ts_emails');
        $this->db->where('email_type', Emails_model::EMAIL_PROOFREQUEST);
        $res = $this->db->get()->row_array();
        if (!isset($res['proof']) || $res['proof'] == '') {
            $part1 = 0;
            $part2 = 0;
        } else {
            $mailarr = explode('-', $res['proof']);
            $part1 = intval($mailarr[1]);
            $part2 = intval($mailarr[0]);
        }
        $part1++;
        if ($part1 == 999) {
            $part2++;
            $part1 = 0;
        }
        $new_proof = str_pad($part2, 3, '0', STR_PAD_LEFT) . '-' . str_pad($part1, 3, '0', STR_PAD_LEFT);
        return $new_proof;
    }

    private function new_proof_num_old()
    {
        $max_attempts = 1000;
        $new_num = '';
        for ($i = 0; $i <= $max_attempts; $i++) {
            $part1 = uniq_link(3, 'digits');
            $part2 = uniq_link(3, 'digits');
            /* Check combinations */
            $num_proof = $part1 . '-' . $part2;
            if (!$this->isProofExist($num_proof)) {
                $new_num = $num_proof;
                break;
            }
            $num_proof = $part2 . '-' . $part1;
            if (!$this->isProofExist($num_proof)) {
                $new_num = $num_proof;
                break;
            }
        }
        return $new_num;
    }

    function artsubmit($options)
    {
        $ci =& get_instance();
        $this->db->select('notification_email');
        $this->db->from('sb_email_notifications');
        $this->db->where('notification_system', Emails_model::PROOF_REQUEST);
        $email_array = $this->db->get()->result_array();

        if (count($email_array) > 0) {
            $ci->load->library('email');
            $email_conf = array('protocol' => 'sendmail', 'charset' => 'utf-8', 'wordwrap' => TRUE, 'mailtype' => 'html',);
            $ci->email->initialize($email_conf);


            // $email_from=$options['email_sendermail'];
            $email_from = $this->config->item('proofrequest_notification');
            // $email_to=$this->config->item('mail_artdepart');
            $email_to = $email_array[0]['notification_email'];
            if (count($email_array) > 1) {
                $email_cc = array();
                for ($i = 1; $i < count($email_array); $i++) {
                    array_push($email_cc, $email_array[$i]['notification_email']);
                }
                $ci->email->cc($email_cc);
            }
            $location = '';
            if ($options['user_location']['country_code'] == 'US') {
                $location .= $options['user_location']['country'];
                $location .= '<br/>';
                if ($options['user_location']['city']) {
                    $location .= $options['user_location']['city'];
                }
                if ($options['user_location']['state']) {
                    $location .= ', ' . $options['user_location']['state'];
                }
                if ($options['user_location']['zipcode'] && $options['user_location']['zipcode'] != '-') {
                    $location .= ' ' . $options['user_location']['zipcode'];
                }
            } else {
                if ($options['user_location']['country']) {
                    $location .= $options['user_location']['country'] . '<br/>';
                }
                if ($options['user_location']['city']) {
                    $location .= $options['user_location']['city'];
                }
                if ($options['user_location']['region']) {
                    $location .= ', ' . $options['user_location']['region'];
                }
                if ($options['user_location']['zipcode'] && $options['user_location']['zipcode'] != '-') {
                    $location .= ' ' . $options['user_location']['zipcode'] . '<br/>';
                }
            }
            $options['userlocation'] = $location;
            $email_body = $ci->load->view('shop/arttwork_emailtemplate_view', $options, TRUE);
            $ci->email->from($email_from);
            $ci->email->to($email_to);

            $ci->email->subject('Artwork Submission');
            $ci->email->message($email_body);


            if (isset($options['attach_logo'])) {
                $ci->email->attach($options['attach_logo']);
            }

            $ci->email->send();
            $ci->email->clear(TRUE);

        }


    }

    function email_update($data)
    {
        $upd_flag = 0;
        if (count($data) < 2) {
            return FALSE;
        }
        foreach ($data as $key => $value) {
            if ($key == 'email_id') {
                $this->db->where($key, $value);
                $upd_flag = 1;
            } else {
                $this->db->set($key, $value);
            }
        }
        if ($upd_flag == 1) {
            $this->db->update('ts_emails');
            $res = TRUE;
        } else {
            $res = FALSE;
        }

        return $res;
    }

    function get_count_notifications($options=[])
    {
        $this->db->select('count(*) as cnt');
        $this->db->from('sb_email_notifications');
        if (isset($options['brand'])) {
            $this->db->where('brand', $options['brand']);
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    function get_notification_emails($order_by, $direct, $limit, $offset, $brand)
    {
        $this->db->select('*');
        $this->db->from('sb_email_notifications');
        $this->db->where('brand', $brand);
        $this->db->order_by($order_by, $direct);
        $this->db->limit($limit, $offset);
        $results = $this->db->get()->result_array();
        return $results;
    }

    function get_notification($notification_id)
    {
        $this->db->select('*');
        $this->db->from('sb_email_notifications');
        $this->db->where('notification_id', $notification_id);
        $res = $this->db->get()->row_array();
        return $res;
    }

    function delete_notification($notification_id)
    {
        $this->db->where('notification_id', $notification_id);
        $this->db->delete('sb_email_notifications');
        $res = $this->db->affected_rows();
        return $res;
    }

    function savenotification($notification_id, $notification_system, $notification_email, $brand)
    {
        $res = array('result' => $this->error_result, 'msg' => 'Unknow error');
        if ($notification_system == '') {
            $res['msg'] = 'Fill data about Notification system';
        } elseif ($notification_email == '') {
            $res['msg'] = 'Fill Email Address for Notification';
        } elseif (!valid_email_address($notification_email)) {
            $res['msg'] = 'Fill correct Email Address for Notification';
        } else {
            $this->db->select('count(*) as cnt');
            $this->db->from('sb_email_notifications');
            $this->db->where('notification_system', $notification_system);
            $this->db->where('notification_email', $notification_email);
            $this->db->where('brand', $brand);
            $this->db->where('notification_id != ', $notification_id);
            $cnt = $this->db->get()->row_array();
            if ($cnt['cnt'] != 0) {
                $res['msg'] = 'Non unique Email Addres for Notification';
            } else {
                $this->db->set('notification_system', $notification_system);
                $this->db->set('notification_email', $notification_email);
                if ($notification_id == 0) {
                    $this->db->set('brand', $brand);
                    $this->db->insert('sb_email_notifications');
                    $res['result'] = $this->success_result;
                } else {
                    $this->db->where('notification_id', $notification_id);
                    $this->db->update('sb_email_notifications');
                    $res['result'] = $this->success_result;
                }
            }
        }
        return $res;
    }

    public function get_surveydata()
    {
        $this->db->select('*');
        $this->db->from('sb_survey');
        $res = $this->db->get()->row_array();
        return $res;
    }

    public function save_surveycfg($options)
    {
        $out = array('result' => 0, 'msg' => 'Unknown Error, try later');
        if (!isset($options['survey_apiid']) || empty($options['survey_apiid'])) {
            $out['msg'] = 'Enter Survey ID';
            return $out;
        }
        $out['result'] = 1;
        $out['msg'] = '';
        $this->db->set('survey_apiid', $options['survey_apiid']);
        $this->db->set('survey_show', $options['survey_show']);
        $this->db->update('sb_survey');
        return $out;
    }

    function neworder_customer_notification($mail_body, $confirm, $email_to)
    {
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;
        $this->email->initialize($config);


        $email_from = $this->config->item('customer_notification_sender');

        $email_body = $mail_body;
        $this->email->from($email_from);
        $this->email->to($email_to);
        $this->email->subject('New Order Notification - ' . $confirm);
        $this->email->message($email_body);
        $this->email->send();

    }

    function neworder_notification($mail_body, $confirm, $production_term, $customer_type = 'WEB')
    {
        $this->db->select('notification_email');
        $this->db->from('sb_email_notifications');
        $this->db->where('notification_system', Emails_model::NEW_ORDER_SYSTEM);
        $email_array = $this->db->get()->result_array();

        if (count($email_array) > 0) {
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $config['wordwrap'] = TRUE;
            $this->email->initialize($config);


            $email_from = $this->config->item('email_notification_sender');
            $email_to = $email_array[0]['notification_email'];
            if (count($email_array) > 1) {
                $email_cc = array();
                for ($i = 1; $i < count($email_array); $i++) {
                    array_push($email_cc, $email_array[$i]['notification_email']);
                }
                $this->email->cc($email_cc);
            }

            $email_body = $mail_body;
            $this->email->from($email_from);
            $this->email->to($email_to);
            $subj = 'New Computer Order - ' . $confirm . ' - ' . $production_term;
            if ($customer_type == 'Mobile') {
                $subj = 'New Mobile Order - ' . $confirm . ' - ' . $production_term;
            } elseif ($customer_type == 'Tablet') {
                $subj = 'New Tablet Order - ' . $confirm . ' - ' . $production_term;
            }
            $this->email->subject($subj);
            $this->email->message($email_body);
            $this->email->send();

        }

    }

    function get_notificationemails_bysystem($system_name)
    {
        $this->db->select('notification_email');
        $this->db->from('sb_email_notifications');
        $this->db->where('notification_system', $system_name);
        $email_array = $this->db->get()->result_array();
        return $email_array;
    }

    function newemail_notification($email_type, $email_data)
    {
        $system = '';
        switch ($email_type) {
            case 'Questions':
                $system = Emails_model::EMAIL_QUESTIONS;
                break;
            case 'Custom_SB':
                $system = Emails_model::EMAIL_CUSTOMSB;
                break;
            case 'Leads':
                $system = Emails_model::EMAIL_LEADS;
                break;
            case 'Testimonials':
                $system = Emails_model::EMAIL_TESTIMONIALS;
                break;
            case 'Signups':
                $system = Emails_model::EMAIL_SIGNUPS;
                break;
            default:
                break;
        }
        if ($system != '') {
            $email_array = $this->get_notificationemails_bysystem($system);
            if (count($email_array) > 0) {
                $this->load->library('email');
                $config['charset'] = 'utf-8';
                $config['mailtype'] = 'html';
                $config['wordwrap'] = TRUE;
                $this->email->initialize($config);

                $email_from = $this->config->item('email_notification_sender');
                $email_to = $email_array[0]['notification_email'];
                if (count($email_array) > 1) {
                    $email_cc = array();
                    for ($i = 1; $i < count($email_array); $i++) {
                        array_push($email_cc, $email_array[$i]['notification_email']);
                    }
                    $this->email->cc($email_cc);
                }
                $email_body = $this->load->view('emails/email_notification_view', $email_data, TRUE);
                $this->email->from($email_from);
                $this->email->to($email_to);
                $this->email->subject('New Email Incoming Notification');
                $this->email->message($email_body);
                $this->email->send();

            }
        }
    }

    function check_quote_data($data)
    {
        $out = array('result' => 0, 'msg' => 'Unknown Error. Try Later', 'error_found' => 0);
        if ($data['postcode'] == '') {
            $out['msg'] = 'Enter Postcode';
        } elseif (!$data['item_id']) {
            $out['msg'] = 'Select Item';
        } elseif ($data['qty1'] + $data['qty2'] + $data['qty3'] + $data['qty4'] == 0) {
            $out['msg'] = 'Enter Item Quantity';
        } elseif ($data['email'] == '') {
            $out['msg'] = 'Enter Email';
        } elseif ($data['reemail'] == '') {
            $out['msg'] = 'Enter Email Confirm';
        } elseif ($data['email'] != $data['reemail']) {
            $out['msg'] = 'Confirmation of email address incorrect';
        } elseif (!valid_email_address($data['email'])) {
            $out['msg'] = 'Invalid email address';
        } else {
            $out['result'] = 1;
            $out['msg'] = '';
        }
        return $out;
    }

    function add_quote_request($data)
    {
        $out = array('result' => 0, 'msg' => 'Unknown Error. Try Later');
        $ci =& get_instance();
        $ci->load->model('businesscalendar_model', 'mcalend');
        $ci->load->model('vendors_model', 'mvendor');
        $ci->load->model('items_model', 'mitem');
        $ci->load->model('itemcolors_model', 'mcolors');
        $ci->load->model('prices_model', 'mprice');
        /* Total QTY */
        $qty = intval($data['qty1']) + intval($data['qty2']) + intval($data['qty3']) + intval($data['qty4']);
        $rushday_arr = explode('-', $data['rushdays']);
        $rushdays = $ci->mcalend->get_bussines_days(time(), $rushday_arr[0], $data['item_id']);
        /* Item Data */
        $item = $ci->mitem->get_item($data['item_id']);
        /* Production Term */
        $rush_term = 'Standard';
        if ($data['colorprint'] > 0) {
            $rush_term = $ci->mitem->get_rush_options($rushdays, $item['item_id']);
        }
        /* Get Vendor Zip */
        $vend_det = $ci->mvendor->get_vendor_detail($item['item_id']);
        $vendorzip = ($vend_det['vendor_zipcode'] == '' ? $this->config->item('zip') : $vend_det['vendor_zipcode']);
        $ship_rate = 0;
        $ship_method_name = '';
        if ($item['cartoon_qty'] != 0) {
            $weight = $item['item_weigth'];
            $numpack = (intval($item['cartoon_qty']) == 0 ? $this->min_qty_pack : $item['cartoon_qty']);
            /* Shipdates  */
            $startdeliv = time();
            /* Standart date */
            $ship = array();
            $options = array('zip' => $data['postcode'], 'numinpack' => $numpack, 'itemqty' => $qty, 'weight' => $weight, 'startdeliv' => $startdeliv, 'vendor_zip' => $vendorzip, 'item_length' => $item['cartoon_depth'], 'item_width' => $item['cartoon_width'], 'item_height' => $item['cartoon_heigh'], 'ship' => $ship, 'cnt_code' => $data['country'],);
            $rates = $this->func->calculate_shipcost($options); // ???

            if (isset($rates['ship']) && count($rates['ship']) != 0) {
                $srcrates = $rates['ship'];
                $outrate = $this->func->recalc_rates($srcrates, $item, $qty, $data['country']); // ???

                if (isset($outrate['GND']['ServiceCode'])) {
                    $ship_rate = $outrate['GND']['Rate'];
                    $ship_method_name = $outrate['GND']['ServiceName'];
                } elseif (isset($outrate['DA2']['ServiceCode'])) {
                    $ship_rate = $outrate['DA2']['Rate'];
                    $ship_method_name = $outrate['DA2']['ServiceName'];
                } elseif (isset($outrate['DP1']['ServiceCode'])) {
                    $ship_rate = $outrate['DP1']['Rate'];
                    $ship_method_name = $outrate['DP1']['ServiceName'];
                } elseif (isset($outrate['DM1']['ServiceCode'])) {
                    $ship_rate = $outrate['DM1']['Rate'];
                    $ship_method_name = $outrate['DM1']['ServiceName'];
                } elseif (isset($outrate['UPSStandard']['ServiceCode'])) {
                    $ship_rate = $outrate['UPSStandard']['Rate'];
                    $ship_method_name = $outrate['UPSStandard']['ServiceName'];
                } elseif (isset($outrate['11']['ServiceCode'])) {
                    $ship_rate = $outrate['11']['Rate'];
                    $ship_method_name = $outrate['11']['ServiceName'];
                } elseif (isset($outrate['UPSexpedited']['ServiceCode'])) {
                    $ship_rate = $outrate['UPSexpedited']['Rate'];
                    $ship_method_name = $outrate['UPSexpedited']['ServiceName'];
                } elseif (isset($outrate['UPSExpedited']['ServiceCode'])) {
                    $ship_rate = $outrate['UPSExpedited']['Rate'];
                    $ship_method_name = $outrate['UPSExpedited']['ServiceName'];
                } elseif (isset($outrate['UPSSaver']['ServiceCode'])) {
                    $ship_rate = $outrate['UPSSaver']['Rate'];
                    $ship_method_name = $outrate['UPSSaver']['ServiceName'];
                } elseif (isset($outrate['UPSExpress']['ServiceCode'])) {
                    $ship_rate = $outrate['UPSExpress']['Rate'];
                    $ship_method_name = $outrate['UPSSaver']['ServiceName'];
                }
            }
        }


        $cart = array('val1' => intval($data['qty1']), 'val2' => intval($data['qty2']), 'val3' => intval($data['qty3']), 'val4' => intval($data['qty4']), 'col1' => $data['col1'], 'col2' => $data['col2'], 'col3' => $data['col3'], 'col4' => $data['col4'], 'cost1' => 0, 'cost2' => 0, 'cost3' => 0, 'cost4' => 0,);
        /* Colors, prices */
        $colorstr = '';
        $saved = 0;
        $total = 0;
        $itemcost = 0;
        $setup = 0;
        $imprint = 0;
        $numcolors = 0;
        $totalqty = 0;
        for ($i = 1; $i < 5; $i++) {
            $totalqty += $cart['val' . $i];
            if ($cart['val' . $i] > 0) {
                if ($cart['col' . $i] != '0' && $cart['col' . $i] != '') {
                    /* Search color option */
                    if (empty($item['printshop_inventory_id'])) {
                        $color = $ci->mcolors->get_colorval_item($cart['col' . $i]);
                    } else {
                        $color = $ci->mcolors->get_colorval_inventory($cart['col' . $i]);
                    }
                    $colorstr .= $color . ",";
                    $numcolors++;
                }
                if ($item['item_template'] == 'Stressball') {
                    $price = $ci->mprice->get_item_pricebyval($item['item_id'], $cart['val' . $i]);
                } else {
                    $price = $ci->mprice->get_promo_pricebyval($item['item_id'], $cart['val' . $i]);
                }

                $priceprint = $ci->mprice->get_item_pricebytype($item['item_id'], 'imprint');
                $cost = round($price * $cart['val' . $i], 2);
                // $itemcost+=$cost;
                $cart['cost' . $i] = $cost;
                /* Get data about discount */
                $discount = 0;
                if ($item['item_template'] == 'Stressball') {
                    $discount = $ci->mprice->get_item_dicountprice($item['item_id'], $cart['val' . $i]);
                } else {
                    $discount = $ci->mprice->get_promo_dicountprice($item['item_id'], $cart['val' . $i]);
                }
                $saved += $discount;
                if ($data['colorprint'] == 2) {
                    $imprint += round($cart['val' . $i] * $priceprint, 2);
                }
            }
        }

        $setup = $ci->mprice->get_item_pricebytype($item['item_id'], 'setup') * ($data['colorprint']);
        if ($numcolors > 1) {
            $colorstr = 'Assorted';
        } else {
            $colorstr = substr($colorstr, 0, -1);
        }
        /* Recalc price, discount */
        if ($item['item_template'] == 'Stressball') {
            $price_item = $ci->mprice->get_item_pricebyval($item['item_id'], $totalqty);
        } else {
            $price_item = $ci->mprice->get_promo_pricebyval($item['item_id'], $totalqty);
        }
        $itemcost = round($price_item * $totalqty, 2);
        $itemsaved = ($cart['cost1'] + $cart['cost2'] + $cart['cost3'] + $cart['cost4']) - $itemcost;
        $saved += $itemsaved;
        $total = $itemcost + $setup + $imprint + $data['rush'] + $ship_rate;
        $tax = 0;
        if (substr($data['postcode'], 0, 2) == '07' || substr($data['postcode'], 0, 2) == '08') {
            $tax = round($total * ($this->config->item('tax') / 100), 2);
            if (time() >= $this->config->item('datenewtax')) {
                $tax = round($total * ($this->config->item('salestax') / 100), 2);
            }
            $total = $total + $tax;
        }
        $itemcolors = $ci->mcolors->get_colors_item($item['item_id']);
        $item_colors = array();
        foreach ($itemcolors as $row) {
            array_push($item_colors, $row['item_color']);
        }

        $option = array('colorprint' => $data['colorprint'], 'saved' => $saved, 'setup' => $setup, 'imprint' => $imprint, 'itemcost' => $itemcost, 'qty' => $qty, 'colors' => $colorstr, 'tax' => $tax, 'total' => $total, 'ship_rate' => $ship_rate, 'ship_method_name' => $ship_method_name, 'rush' => $data['rush'], 'rush_days' => $rush_term, 'item_id' => $item['item_id'], 'itemcolors' => $item_colors,);

        /* Prepare object to show */

        $dataemail = array('email_type' => 'Leads', 'email_sendermail' => $data['email'], 'email_subtype' => 'Quote', 'email_qty' => $qty, 'email_item_name' => $item['item_name'], 'email_total' => $total, 'quote_country' => $data['country'], 'quote_postcode' => $data['postcode'], 'email_other_info' => json_encode($option),);
        $res = $this->email_insert($dataemail);
        if (!$res) {
            $out['msg'] = 'Quota query message inserted with error';
            return $out;
        } else {
            if ($data['signin'] == 1) {
                $data = array('email_type' => 'Signups', 'email_sendermail' => $data['email'],);
                $res = $this->email_insert($data);
            }
        }
        $out['result'] = 1;
        return $out;
    }

    public function get_proofartdata($proof_num)
    {
        $out = array('result' => 0, 'msg' => 'Unknown Error');
        $this->db->select('email_id');
        $this->db->from('ts_emails');
        $this->db->where('proof_num', $proof_num);
        $maildat = $this->db->get()->row_array();
        if (!isset($maildat['email_id'])) {
            return $out;
        }
        $mail_id = $maildat['email_id'];
        $artdb = $this->config->item('system_prefix') . '.ts_artworks';
        $orderdb = $this->config->item('system_prefix') . '.ts_orders';
        $this->db->select('a.order_id, o.order_num, a.mail_id');
        $this->db->from("{$artdb} a");
        $this->db->join("{$orderdb} o", 'o.order_id=a.order_id', 'left');
        $this->db->where('a.mail_id', $mail_id);
        $res = $this->db->get()->row_array();
        if (isset($res['mail_id'])) {
            $out['result'] = 1;
            if ($res['order_num'] != '') {
                $out['outdoc'] = $res['order_num'];
            } else {
                $out['outdoc'] = '';
            }
        }
        return $out;
    }

    public function check_directmsg_params($data)
    {
        $out = array('result' => 0, 'msg' => 'All fields is required');
        $error = '';
        if (!isset($data['name']) || ltrim(rtrim($data['name'])) == '') {
            $error .= "Name is mandatory field" . PHP_EOL;
        }
        if (!isset($data['email']) || ltrim(rtrim($data['email'])) == '') {
            $error .= "Email is mandatory field" . PHP_EOL;
        } else {
            if (!valid_email_address($data['email'])) {
                $error .= "Email address is not valid" . PHP_EOL;
            }
        }
        if (!isset($data['subj']) || ltrim(rtrim($data['subj'])) == '') {
            $error .= "Message subject is mandatory field" . PHP_EOL;
        }
        if (!isset($data['msg']) || ltrim(rtrim($data['subj'])) == '') {
            $out['error'] .= "Message text is mandatory field" . PHP_EOL;
        }
        $out['msg'] = $error;
        if (empty($error)) {
            $out['result'] = 1;
        }
        return $out;
    }

    public function check_contactusmsg_params($data)
    {
        $out = ['result' => 0, 'msg' => ''];
        $error = '';
        $error_type = 0;
        if (!isset($data['sendername']) || ltrim(rtrim($data['sendername'])) == '') {
            $error .= "Name is mandatory field" . PHP_EOL;
        }
        if (!isset($data['senderemail']) || ltrim(rtrim($data['senderemail'])) == '') {
            $error .= "Email is mandatory field" . PHP_EOL;
        } else {
            if (!valid_email_address($data['senderemail'])) {
                $error .= "Email address is not valid" . PHP_EOL;
            }
        }
        /* if (!isset($data['subj']) || ltrim(rtrim($data['subj']))=='') {
            $error.="Message subject is mandatory field".PHP_EOL;
        } */
        if (!isset($data['senderphone']) || ltrim(rtrim($data['senderphone'])) == '') {
            $error .= "Phone is mandatory field" . PHP_EOL;
        }
        if (!isset($data['sendertxt']) || ltrim(rtrim($data['sendertxt'])) == '') {
            $error .= "Message text is mandatory field" . PHP_EOL;
        }
        // Captcha
        if (!isset($data['math_captcha']) || ltrim(rtrim($data['math_captcha'])) == '') {
            $error .= "Enter a math captcha response" . PHP_EOL;
        } else {
            $mathcaptcha_answer = $this->session->flashdata('mathcaptcha_answer');
            if ($data['math_captcha'] != $mathcaptcha_answer) {
                $error .= "Enter a valid math captcha response" . PHP_EOL;
                $error_type = 1;
            }
        }
        // restore secret answer
        $out['msg'] = $error;
        $out['error_type'] = $error_type;
        if (empty($error)) {
            $out['result'] = 1;
        }
        return $out;
    }

    public function check_contactusnobilemsg_params($data)
    {
        $out = ['result' => 0, 'msg' => ''];
        $error = '';
        $error_type = 0;
        if (!isset($data['sendername']) || ltrim(rtrim($data['sendername'])) == '') {
            $error .= "Name is mandatory field" . PHP_EOL;
        }
        if (!isset($data['senderemail']) || ltrim(rtrim($data['senderemail'])) == '') {
            $error .= "Email is mandatory field" . PHP_EOL;
        } else {
            if (!valid_email_address($data['senderemail'])) {
                $error .= "Email address is not valid" . PHP_EOL;
            }
        }
        /* if (!isset($data['subj']) || ltrim(rtrim($data['subj']))=='') {
            $error.="Message subject is mandatory field".PHP_EOL;
        } */
        if (!isset($data['senderphone']) || ltrim(rtrim($data['senderphone'])) == '') {
            $error .= "Phone is mandatory field" . PHP_EOL;
        }
        if (!isset($data['sendertxt']) || ltrim(rtrim($data['sendertxt'])) == '') {
            $error .= "Message text is mandatory field" . PHP_EOL;
        }
        // restore secret answer
        $out['msg'] = $error;
        $out['error_type'] = $error_type;
        if (empty($error)) {
            $out['result'] = 1;
        }
        return $out;
    }

    public function sendquestion($data)
    {
        $out = ['result' => 0, 'msg' => ''];
        if (!isset($data['questionpage']) || empty($data['questionpage'])) {
            $out['msg'] = 'Unknown page';
        } elseif (!isset($data['email']) || empty($data['email'])) {
            $out['msg'] = 'Email is mandatory field';
        } elseif (!valid_email_address($data['email'])) {
            $out['msg'] = 'Email address is not valid';
        } elseif (!isset($data['msgtxt']) || empty($data['msgtxt'])) {
            $out['msg'] = 'Message text is mandatory field';
        } else {
            // Try to add
            $pagename = $data['questionpage'];
            $page = '';
            if (array_key_exists($pagename, $this->pagenames)) {
                $page = $this->pagenames[$pagename];
            }
            if (!empty($data['questionsubpage'])) {
                $page .= ' - ' . $data['questionsubpage'];
            }
            $data = array('email_type' => 'Questions', 'email_subtype' => 'Question', 'email_sendermail' => $data['email'], 'email_text' => $data['msgtxt'], 'email_webpage' => $page,);
            $res = $this->email_insert($data);
            if ($res != 0) {
                $out['result'] = 1;
                $out['msg'] = '';

                if (isset($data['specialoffer']) && $data['specialoffer'] == 1) {
                    // Insert special offer
                    $dataspec = array('email_type' => 'Signups', 'email_sendermail' => $data['email'],);
                    $this->emails->email_insert($data);
                }
            }
        }
        return $out;
    }

    public function prepare_signupcontent($email_dat, $ordnum) {
        $email_dat_left=$email_dat_right=[];
        $j=0;
        foreach ($email_dat as $row) {
            $row['numpp']=$ordnum;
            if ($j<250) {
                $email_dat_left[]=$row;
            } else {
                $email_dat_right[]=$row;
            }
            $j++;
            $ordnum--;
        }
        return ['left' => $email_dat_left, 'right' => $email_dat_right];
    }

    public function get_count_parsedemails($options=[]) {
        $this->db->select('count(parsmessage_log_id) as cnt');
        $this->db->from('ts_parsmessage_log');
        // Filters
        if (isset($options['datestart'])) {
            $this->db->where('unix_timestamp(parsed_date) >= ',$options['datestart']);
        }
        if (isset($options['dateend'])) {
            $this->db->where('unix_timestamp(parsed_date) < ',$options['dateend']);
        }
        if (isset($options['filtr'])) {
            $this->db->like('upper(concat(message_from, message_subject))',$options['filtr']);
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_parserlogdata($search, $order_by, $direct, $offset, $limit) {
        $this->db->select('*');
        $this->db->from('ts_parsmessage_log');
        if (isset($search['datestart'])) {
            $this->db->where('unix_timestamp(parsed_date) >= ',$search['datestart']);
        }
        if (isset($search['dateend'])) {
            $this->db->where('unix_timestamp(parsed_date) < ',$search['dateend']);
        }
        if (isset($search['filtr'])) {
            $this->db->like('upper(concat(message_from, message_subject))',$search['filtr']);
        }
        $this->db->order_by($order_by, $direct);
        $this->db->limit($limit, $offset);
        $res=$this->db->get()->result_array();

        $out=array();
        foreach ($res as $row) {
            if (empty($row['message_subject'])) {
                $row['message_subject']=$this->empty_html_content;
            }
            $row['out_parsed_date']=date('m/d/y',strtotime($row['parsed_date']));
            if ($row['parsed_result']==1) {
                $row['out_parsed_result']='<span class=\'successparseed\'>SUCCESS</span>';
            } else {
                $row['out_parsed_result']='<span class=\'errorparsed\'>'.$row['parsed_error'].'</span>';
            }
            $out[]=$row;
        }
        return $out;

    }

    public function get_whitelist($options) {
        $this->db->select('wl.email_id, wl.sender, u.user_name');
        $this->db->from('ts_whitelist_emails wl');
        $this->db->join('users u','u.user_id=wl.user_id');
        if (isset($options['order_by'])) {
            if (isset($options['direction'])) {
                $this->db->order_by($options['order_by'], $options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function delete_whitelist($email_id) {
        $this->db->where('email_id',$email_id);
        $this->db->delete('ts_whitelist_emails');
        if ($this->db->affected_rows()==0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_whitelist_data($email_id) {
        $this->db->select('*');
        $this->db->from('ts_whitelist_emails');
        $this->db->where('email_id',$email_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function save_whitelist($sender, $user_id, $email_id) {
        $out=array('result'=> $this->error_result, 'msg'=> 'Error during update');
        if ($sender=='') {
            $out['msg']='Sender Email Empty';
        } elseif (!valid_email_address($sender)) {
            $out['msg']='Enter correct Email Address';
        } elseif($this->isWhiteListExist($sender, $email_id)) {
            $out['msg']='Enter Unique Email Address';
        } elseif ($user_id=='') {
            $out['msg']='Select User';
        } else {
            $this->db->set('sender',$sender);
            $this->db->set('user_id',$user_id);
            if ($email_id==0) {
                $this->db->insert('ts_whitelist_emails');
                if ($this->db->insert_id()!=0) {
                    $out['result']=$this->success_result;
                } else {
                    $out['msg']='Unknown Error. Try Later';
                }
            } else {
                $this->db->where('email_id',$email_id);
                $this->db->update('ts_whitelist_emails');
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    private function isWhiteListExist($sender, $email_id) {
        $this->db->select('count(*) as cnt');
        $this->db->from('ts_whitelist_emails');
        $this->db->where('sender',$sender);
        if ($email_id) {
            $this->db->where('email_id != ', $email_id);
        }
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function generate_quota() {
        $this->load->model('itemimages_model');
        $this->load->model('items_model');
        $this->load->helper(array('dompdf', 'file'));
        $options = array(
            'email_type' => 'Leads',
            'email_quota_link' => NULL,
            'brand' => 'ALL',
        );
        $mails_array = $this->get_emails($options, 'email_id', 'asc', 1, 0);

        /* Send message to user */
        foreach ($mails_array as $row) {
            $mail_id = $row['email_id'];
            $mail = $this->get_email_details($mail_id);

            $mail['colorprint'] = get_json_param($mail['email_other_info'], 'colorprint', 0);
            $mail['item_id']=get_json_param($mail['email_other_info'],'item_id',0);
            $mail['item_number']='';
            if (intval($mail['item_id'])!=0) {
                $itemdat=$this->items_model->get_item($mail['item_id']);
                if ($itemdat['result']==$this->success_result) {
                    $item_det = $itemdat['data'];
                    $mail['item_number']=$item_det['item_number'];
                    if ($mail['colorprint'] == 1) {
                        $mail['colorprint'] = '1 Color Imprinting';
                    } elseif ($mail['colorprint'] == 2) {
                        $mail['colorprint'] = '2 Color Imprinting';
                    } else {
                        $mail['colorprint'] = 'Blank, No Imprinting';
                    }
                    $mail['setup'] = get_json_param($mail['email_other_info'], 'setup', 0);
                    $mail['imprint'] = get_json_param($mail['email_other_info'], 'imprint', 0);
                    $mail['itemcost'] = get_json_param($mail['email_other_info'], 'itemcost', 0);
                    $itemcolors = get_json_param($mail['email_other_info'], 'itemcolors', []);
                    $colorstr = '';
                    foreach ($itemcolors as $itemcolor) {
                        $colorstr.=$itemcolor.',';
                    }
                    if (count($itemcolors)>0) {
                        $colorstr=substr($colorstr,0,-1);
                    }
                    $mail['colors'] = $colorstr;
                        // $mail['colors'] = get_json_param($mail['email_other_info'], 'colors', 0);
                    $mail['total'] = get_json_param($mail['email_other_info'], 'total', 0);
                    $mail['ship_rate'] = get_json_param($mail['email_other_info'], 'ship_rate', 0);
                    $mail['ship_method_name'] = get_json_param($mail['email_other_info'],'ship_method_name','');
                    $mail['tax'] = get_json_param($mail['email_other_info'], 'tax', 0);
                    $mail['rush'] = get_json_param($mail['email_other_info'], 'rush', 0);
                    $mail['rush_days'] = get_json_param($mail['email_other_info'], 'rush_days', 0);
                    if ($mail['brand']=='SB') {
                        $mail['saleprice'] = floatval(get_json_param($mail['email_other_info'],'sale_price',0));
                        $mail['price'] = floatval(get_json_param($mail['email_other_info'],'reg_price',0));
                        $mail['saved'] = (-1) * get_json_param($mail['email_other_info'], 'saved', 0);
                    } else {
//                        $mail['saleprice'] = round($mail['itemcost'] / intval($mail['email_qty']), 2);
//                        $mail['price'] = round(($mail['itemcost'] + $mail['saved']) / intval($mail['email_qty']), 2);
//                        $mail['saved'] = get_json_param($mail['email_other_info'], 'saved', 0);
                        $mail['saleprice'] = floatval(get_json_param($mail['email_other_info'],'sale_price',0));
                        $mail['price'] = floatval(get_json_param($mail['email_other_info'],'reg_price',0));
                        $mail['saved'] = (-1) * get_json_param($mail['email_other_info'], 'saved', 0);
                    }
                    $mail['imgpath']=$this->config->item('img_path');
                    $mail['itemimgpath']=$this->config->item('item_quote_images');
                    $item_id = get_json_param($mail['email_other_info'], 'item_id', 0);

                    if ($item_id != 0) {
                        /* Get Main Picture */
                        $img = $this->itemimages_model->get_item_images($item_id);
                        $mail['mainimg'] = '';
                        if (is_array($img)) {
                            $mail['mainimg'] = $img[0]['item_img_name'];
                        }
                    }
                    if (floatval($mail['ship_rate'])==0) {
                        $mail['shipinfo']=$this->load->view('quote/quote_shipempty_view',array(),TRUE);
                    } else {
                        if ($mail['ship_method_name']=='') {
                            // Unknown Shipping Method
                            $mail['shipinfo']=$this->load->view('quote/quote_shipcommon_view',$mail,TRUE);
                        } else {
                            if ($mail['quote_country']=='US') {
                                $mail['ziplabel']='Zip Code:';
                            } else {
                                $mail['ziplabel']='Postal Code:';
                            }
                            $mail['shipinfo']=$this->load->view('quote/quote_shipmethod_view',$mail,TRUE);
                        }
                    }

                    $html = $this->load->view('quote/quote_mail_dompdf_view', $mail, TRUE);
                    /* Create UNIQUE file name */
                    $file_name = 'BLUETRACK_Quote_'.date('ymd').$mail_id.'.pdf';
                    $file_out = $this->config->item('quotes') . $file_name;
                    pdf_create($html, $file_out, true);
                    if (file_exists($file_out)) {
                        /* Update email */
                        $upddata = array(
                            'email_id' => $mail_id,
                            'email_quota_link' => $this->config->item('quotes_relative') . $file_name,
                        );
                        $this->email_update($upddata);
                        /* Prepare Message for send */
                        $msg_options=array(
                            'item_name'=>$mail['email_item_name'],
                            'item_qty'=>intval($mail['email_qty']),
                        );
                        $content=$this->load->view('messages/quote_message_view',$msg_options,TRUE);
                        $msgbody=($content);
                        /* Send message to user */
                        $mail_options=array(
                            'touser'=>$mail['email_sendermail'],
                            'fromuser'=>$this->config->item('email_notification_sender'),
                            'subject'=>intval($mail['email_qty']).' '.$mail['email_item_name'] . ' Quote',
                            /* 'message'=>'Hi ! Here is the qoute you requested.',*/
                            'message'=>$msgbody,
                            'fileattach'=>$file_out,
                        );
                        if ($_SERVER['SERVER_NAME']!=='lift_stressballs.local') {
                            $this->send_quota($mail_options);
                        }
                    }
                }
            }
        }
    }

    public function send_quota($mail_options) {
        $this->load->library('email');

        $email_conf = array(
            'protocol'=>'sendmail',
            'charset'=>'utf-8',
            'mailtype'=>'html',
            'wordwrap'=>TRUE,
        );
        $this->email->initialize($email_conf);

        $this->email->to($mail_options['touser']);
        $this->email->from($mail_options['fromuser']);
        $this->email->subject($mail_options['subject']);
        $this->email->message($mail_options['message']);
        $this->email->attach($mail_options['fileattach']);
        $this->email->send();

        $this->email->clear(TRUE);
        return true;
    }


}

