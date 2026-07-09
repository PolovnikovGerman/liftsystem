<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Seo_model extends My_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function check_geoip($user_ip) {
        $this->db->select('count(geoip_id) as cnt, max(country_code) as country_code');
        $this->db->from('sb_geoips');
        $this->db->where('user_ip', $user_ip);
        $res = $this->db->get()->row_array();
        return $res;
    }

    public function get_geolocation($user_ip, $cron =0) {
        $out = ['result'=> $this->error_result];
        $api_key = $this->config->item('geo_apikey');
        // $d = @file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=$api_key&ip=$user_ip&format=json");
        $d = $this->get_geolocation2api($api_key, $user_ip);
        //Use backup server if cannot make a connection
        if (!$d) {
            return $out; // Failed to open connection
        } else {
            $data = json_decode($d,TRUE);
            if (isset($data['error'])) {
                return $out;
            }
            // Build data
            $result = [];
            $result['country_code2'] = ifset($data,'country_code2','');
            $result['country_name'] = ifset($data,'country_name','');
            $result['city'] = ifset($data,'city','');
            $result['state_prov'] = ifset($data,'state_prov','');
            $result['latitude'] = ifset($data,'latitude','');
            $result['longitude'] = ifset($data,'longitude','');
            $result['zipcode'] = ifset($data,'zipcode','');
            $country_id='';
            $this->load->model('shipping_model');
            if (ifset($result,'country_code2','')!=='') {
                $cntr=$this->shipping_model->get_country_bycode2($result['country_code2']);
                if (isset($cntr['country_id']) && $cntr['country_id']) {
                    $country_id=$cntr['country_id'];
                }
            }
            $out_array = array(
                'ip' => $user_ip,
                'country_code' => $result['country_code2'],
                'country_name' => $result['country_name'],
                'city_name' => $result['city'],
                'region_name' => $result['state_prov'],
                'latitude' => $result['latitude'],
                'longitude' => $result['longitude'],
                'country_id' => $country_id,
                'zipcode' => $result['zipcode'],
            );
            $out['geodata'] = $out_array;
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function get_geolocation2api($apiKey, $ip, $lang = "en", $fields = "*", $excludes = "") {
        // $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;
        // $url = "https://api.ip2location.io/?key={$apiKey}&ip=".$ip;
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;

        $cURL = curl_init();

        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        return curl_exec($cURL);
    }

    public function update_geoip($ipdata, $user_ip) {
        $usrdata=$this->ipdata_exist($user_ip);
        if ($usrdata['result']!==$this->success_result) {
            // Get Code of region
            if (!isset($ipdata['country_code'])) {
                log_message('error', 'GeoIP - COUNTRY NOT FOUND '.json_encode($ipdata));
            } elseif (empty($ipdata['country_code'])) {
                log_message('error', 'GeoIP - COUNTRY CODE EMPTY '.json_encode($ipdata));
            }
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
        $out=array('result'=>$this->error_result);
        $this->db->select('gi.*, cntr.country_id');
        $this->db->from('sb_geoips gi');
        $this->db->join('sb_countries cntr','cntr.country_iso_code_2=gi.country_code','left');
        $this->db->where('gi.user_ip',$userip);
        $res=$this->db->get()->row_array();
        if (isset($res['user_ip'])) {
            $out=$res;
            $out['result']=$this->success_result;
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

    public function fix_emptygeodata()
    {
        $dats = $this->db->select('*')->from('sb_geoips')->where('country_code','')->get()->result_array();
        if (count($dats)>0) {
            foreach ($dats as $dat) {
                $res = $this->get_geolocation($dat['user_ip'], 1);
                if ($res['result']==$this->success_result) {
                    $geodata = $res['geodata'];
                    $regcode = '';
                    if (!empty($geodata['region_name'])) {
                        $regcode = $this->get_statecode_byname($geodata['region_name']);
                    }

//                    'ip' => $user_ip,
//                'country_code' => $result['country_code2'],
//                'country_name' => $result['country_name'],
//                'city_name' => $result['city'],
//                'region_name' => $result['state_prov'],
//                'latitude' => $result['latitude'],
//                'longitude' => $result['longitude'],
//                'country_id' => $country_id,
//                'zipcode' => $result['zipcode'],
                    $this->db->where('geoip_id',$dat['geoip_id']);
                    $this->db->set('country_code',$geodata['country_code']);
                    $this->db->set('country_name',$geodata['country_name']);
                    $this->db->set('city_name',$geodata['city_name']);
                    $this->db->set('region_name',$geodata['region_name']);
//                    if (!empty($geodata['region_name'])) {
                        $this->db->set('region_code', $regcode);
//                    }
                    $this->db->set('latitude',(isset($geodata['latitude']) ? $geodata['latitude'] : NULL));
                    $this->db->set('longitude',(isset($geodata['longitude']) ? $geodata['longitude'] : NULL));
                    if (isset($geodata['zipcode'])) {
                        $this->db->set('zipcode',($geodata['zipcode']=='-' ? NULL : $geodata['zipcode']));
                    }
                    $this->db->update('sb_geoips');
                    echo 'IP '.$dat['user_ip'].' has been updated'.PHP_EOL;
                }
            }
        }
    }

}
