<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Dbitemdetails_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function change_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter', 'oldvalue' => ''];
        $entity = ifset($data,'entity','noname');
        $out['entity']=$entity;
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $key = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $out['oldvalue'] = $item[$fld];
                if ($fld=='item_number') {
                    $chkres = $this->_check_item_number($newval, $item['item_id']);
                    $out['msg'] = $chkres['msg'];
                    if ($chkres['result'] == $this->success_result) {
                        $item[$fld] = $newval;
                        $session_data['item'] = $item;
                        usersession($session_id, $session_data);
                        $out['msg'] = '';
                        $out['result'] = $this->success_result;
                    }
                } else {
                    if ($fld=='item_sale' || $fld=='item_new') {
                        $newval = 1;
                        if ($item[$fld]==1) {
                            $newval = 0;
                        }
                    }
                    $item[$fld]=$newval;
                    $session_data['item']=$item;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    $out['fld'] = $fld;
                    $out['newval'] = $newval;
                    $out['entity'] = 'item';
                }
            }
        } elseif ($entity=='similar') {
            $out['msg']='Item Simular Not Found';
            $items = ifset($session_data,'similar', []);
            $idx = 0;
            foreach ($items as $item) {
                if ($item['item_similar_id']==$key) {
                    $items[$idx][$fld]=$newval;
                    $session_data['simular']=$items;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        }
        return $out;
    }

    // Check uniq number
    private function  _check_item_number($item_number, $item_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item # not unique'];
        $this->db->select('count(item_id) as cnt');
        $this->db->from('sb_items');
        $this->db->where('item_number', $item_number);
        $this->db->where('item_id != ', $item_id);
        $res = $this->db->get()->row_array();
        if ($res['cnt']==1) {
            $out['result'] = $this->success_result;
        }
        return $out;
    }
}