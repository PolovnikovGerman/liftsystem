<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Restapi extends CI_Controller {

    private $error_result=0;
    private $success_result=1;

    function __construct() {
        parent::__construct();
    }

    public function index() {
        show_404();
    }

    public function trackcodes() {
        $response=array(
            'error'=>'Empty Post Parameter',
            'result'=>$this->error_result,
        );
        $postdata=$this->input->post();
        $filelog=$this->config->item('upload_path_preload').'restapilog.txt';
        $fh=fopen($filelog,FOPEN_READ_WRITE_CREATE);
        $msg=date('d.m.Y H:i:s').' POST ';
        foreach ($postdata as $key=>$val) {
            $msg.=' KEY '.$key.' Value '.$val;
        }
        $msg.=PHP_EOL;
        fwrite($fh, $msg);
        fclose($fh);
        // Check params
        if (array_key_exists('order_num', $postdata) && array_key_exists('trackcode',$postdata)) {
            // Return data about USER
            $order_num=$postdata['order_num'];
            $trackcode=$postdata['trackcode'];
            $this->load->model('leadorder_model');
            $res=$this->leadorder_model->save_trackcode($order_num, $trackcode);
            $fh=fopen($filelog,FOPEN_READ_WRITE_CREATE);
            $msg=date('d.m.Y H:i:s').' Check Code Results ';
            foreach ($res as $key=>$val) {
                $msg.=' KEY '.$key.' Value '.$val;
            }
            $msg.=PHP_EOL;
            fwrite($fh, $msg);
            fclose($fh);
            $response['error']=$res['msg'];
            if ($res['result']==$this->success_result) {
                $response['error']='';
                $response['result']=$this->success_result;
            }
        }
        //
        echo json_encode($response);
        return TRUE;

    }

}
/* End of file restapi.php */
/* Location: ./application/controllers/restapi.php */