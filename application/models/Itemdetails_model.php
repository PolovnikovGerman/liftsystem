<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Itemdetails_model extends My_Model
{


    function __construct()
    {
        parent::__construct();
    }

    public function change_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $idx = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $item[$fld]=$newval;
                $session_data['item']=$item;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function change_specialcheck_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $idx = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $item[$fld]=$newval;
                $session_data['item']=$item;
                usersession($session_id, $session_data);
                firephplog($session_data,'Session');
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

}