<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbitemdetails extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dbitemdetails_model');
    }

    // Edit parameter
    public function change_parameter() {
        if ($this->isAjax()) {
            $postdata=$this->input->post();
            $error=$this->session_error;
            $mdata=[];
            $session_id=ifset($postdata, 'session_id','defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->dbitemdetails_model->change_parameter($session_data, $postdata, $session_id);
                $error=$res['msg'];
                $mdata['oldvalue'] = $res['oldvalue'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($res['entity']=='item' && ($res['fld']=='item_sale' || $res['fld']=='item_new')) {
                        if ($res['newval']==0) {
                            $mdata['newcheck'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                        } else {
                            $mdata['newcheck'] = '<i class="fa fa-square" aria-hidden="true"></i>';
                        }
                    }
//                    if ($res['entity']=='item_images') {
//                        // Build new slider
//                        $img_options=array(
//                            'images'=>$res['images'],
//                            'pos'=>0,
//                            'edit'=>0,
//                            'limit'=>$this->config->item('slider_images'),
//                            'video'=> '', // $video,
//                            'audio'=> '', // $audio,
//                            'faces'=> '', // $faces,
//                        );
//                        $mdata['content']=$this->load->view('itemdetails/pictures_slider_view',$img_options,TRUE);
//                    } elseif ($res['entity']=='item_prices') {
//                        $profit = $res['profit'];
//                        $mdata['profitdat'] = $this->load->view('itemdetails/stressball_profit_view', array('prices' => $profit, 'price_types' => $this->config->item('price_types')), TRUE);
//                        $mdata['research']=$res['research'];
//                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}