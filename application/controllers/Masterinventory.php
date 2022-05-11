<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Masterinventory extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
    }

    public function get_inventory_list() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = ifset($postdata,'inventory_filter',0);
            $showmax = ifset($postdata,'showmax', 0);
            $mdata=[];

            $data = $this->inventory_model->get_masterinvent_list($inventory_type, $inventory_filter);
            if (count($data['list'])==0) {
                $mdata['content']=$this->load->view('masterinvent/inventorylist_emptydata_view',[],TRUE);
            } else {
                $mdata['content']=$this->load->view('masterinvent/inventorylist_data_view',['lists' => $data['list'],'showmax' => $showmax],TRUE);
            }
            $mdata['instock'] = empty($data['type_instock']) ? '' : QTYOutput($data['type_instock']);
            $mdata['available'] = empty($data['type_available']) ? '' : QTYOutput($data['type_available']);
            $mdata['maximum'] = empty($data['type_maximum']) ? '' : QTYOutput($data['type_maximum']);
            $this->ajaxResponse($mdata,'');
        }
        show_404();
    }

    public function get_color_inventory() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $res = $this->inventory_model->get_masterinventory_color($coloritem);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['wintitle'] = $this->load->view('masterinvent/prices_head_view', $res['itemdata'],TRUE);
                    $tablebody = $this->load->view('masterinvent/prices_table_body',['lists' => $res['lists']], TRUE);
                    $options = [
                        'tablebody' => $tablebody,
                        'totals' => $res['totals'],
                        'item' => $res['itemdata'],
                    ];
                    $mdata['winbody'] = $this->load->view('masterinvent/prices_body_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function get_color_history() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $res = $this->inventory_model->get_masterinventory_colorhistory($coloritem);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['wintitle'] = $this->load->view('masterinvent/history_head_view', $res['itemdata'],TRUE);
                    $options = [
                        'lists' => $res['lists'],
                        'item' => $res['itemdata'],
                    ];
                    $mdata['winbody'] = $this->load->view('masterinvent/history_body_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function get_color_showused() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $showused = ifset($postdata,'showused', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $res = $this->inventory_model->get_masterinventory_color($coloritem, $showused);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('masterinvent/prices_table_body',['lists' => $res['lists']], TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function get_item_inventory() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $item = ifset($postdata,'item', 0);
            $editmode = ifset($postdata,'editmode', 0);
            $invtype = ifset($postdata,'inventory_type', 0);
            $mdata = [];
            $res = $this->inventory_model->get_masterinventory_item($item, $invtype);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['wintitle'] = $this->load->view('masterinvent/masteritem_head_view', $res['itemdata'], TRUE);
                if ($editmode==0) {
                    $mdata['winbody'] = $this->load->view('masterinvent/masteritem_body_view',$res['itemdata'], TRUE);
                    $mdata['winfooter'] = $this->load->view('masterinvent/masteritem_footer_view',$res['itemdata'], TRUE);
                } else {
                    $mdata['winbody'] = $this->load->view('masterinvent/masteritem_body_edit',$res['itemdata'], TRUE);
                    $mdata['winfooter'] = $this->load->view('masterinvent/masteritem_footer_edit',$res['itemdata'], TRUE);
                }

            }

            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function masteritem_newdoc() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $doc_url = ifset($postdata,'doc_url','');
            $doc_type = ifset($postdata, 'doc_type', '');
            $mdata = [];
            $error = 'Unknown document type';
            if (!empty($doc_type)) {
                $error = '';
                if ($doc_type=='proof') {
                    $mdata['content'] = $this->load->view('masterinvent/masteritem_prooftempl_view',['doc_url' => $doc_url], TRUE);
                } elseif ($doc_type=='plate') {
                    $mdata['content'] = $this->load->view('masterinvent/masteritem_platetempl_view',['doc_url' => $doc_url], TRUE);
                } elseif ($doc_type=='box') {
                    $mdata['content'] = $this->load->view('masterinvent/masteritem_boxtempl_view',['doc_url' => $doc_url], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function masteritem_save() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $options=[];
            $options['inventory_item_id'] = ifset($postdata,'item',0);
            $options['inventory_type_id'] = ifset($postdata, 'inventory_type',0);
            $options['item_name'] = ifset($postdata, 'item_name', '');
            $options['item_unit'] = ifset($postdata, 'item_unit', '');
            // Upload section
            $options['proofflag'] = ifset($postdata, 'proofflag', 0);
            $options['proofsrc'] = ifset($postdata, 'proofsrc', '');
            $options['proofname'] = ifset($postdata, 'proofname','');
            $options['plateflag'] = ifset($postdata, 'plateflag', 0);
            $options['platesrc'] = ifset($postdata, 'platesrc', '');
            $options['platename'] = ifset($postdata, 'platename', '');
            $options['boxflag'] = ifset($postdata, 'boxflag', 0);
            $options['boxsrc'] = ifset($postdata, 'boxsrc', '');
            $options['boxname'] = ifset($postdata, 'boxname', '');
            $mdata = [];
            $res = $this->inventory_model->masterinventory_item_save($options);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function get_inventory_color() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $item = ifset($postdata,'item',0);
            $color = ifset($postdata,'color',0);
            $editmode = ifset($postdata,'editmode',0);
            $mdata=[];
            $res = $this->inventory_model->get_inventory_mastercolor($color, $item);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['wintitle'] = $this->load->view('masterinvent/mastercolor_head_view', $res['colordata'], TRUE);
                if ($editmode==0) {
                    // View mode
                    $options = [
                        'color' => $res['colordata'],
                        'vendors' => $res['vendordat'],
                    ];
                    $mdata['winbody'] = $this->load->view('masterinvent/mastercolor_body_view',$options, TRUE);
                    $mdata['winfooter'] = $this->load->view('masterinvent/mastercolor_footer_view',$res['colordata'], TRUE);
                } else {
                    $session_id = 'invcolor'.uniq_link(10);
                    usersession($session_id, ['color' => $res['colordata'],'vendors' => $res['vendordat']]);
                    $this->load->model('vendors_model');
                    $vendlist = $this->vendors_model->get_vendors();
                    $options = [
                        'color' => $res['colordata'],
                        'vendors' => $res['vendordat'],
                        'vendorlists' => $vendlist,
                        'session' => $session_id,
                    ];
                    $mdata['winbody'] = $this->load->view('masterinvent/mastercolor_body_edit',$options, TRUE);
                    $mdata['winfooter'] = $this->load->view('masterinvent/mastercolor_footer_edit',$res['colordata'], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_color_change() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unkn');
            $fld = ifset($postdata,'fld','unkn');
            $newval = ifset($postdata, 'newval','');
            $error = 'Edit Session lifetime expired';
            $mdata = [];
            $sessiondat = usersession($session_id);
            if (!empty($sessiondat)) {
                $res = $this->inventory_model->mastercolor_change($sessiondat, $fld, $newval, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_colorvendor_change() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unkn');
            $fld = ifset($postdata,'fld','unkn');
            $newval = ifset($postdata, 'newval','');
            $vendlist = ifset($postdata,'vendlist',0);
            $error = 'Edit Session lifetime expired';
            $mdata = [];
            $sessiondat = usersession($session_id);
            if (!empty($sessiondat)) {
                $res = $this->inventory_model->mastercolor_vendorchange($sessiondat, $vendlist, $fld, $newval, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function mastercolor_image_change() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unkn');
            $doc_url = ifset($postdata,'doc_url','');
            $doc_src = ifset($postdata, 'doc_src','');
            $error = 'Edit Session lifetime expired';
            $mdata = [];
            $sessiondat = usersession($session_id);
            if (!empty($sessiondat)) {
                $res = $this->inventory_model->mastercolor_updateimg($sessiondat, $doc_url, $doc_src, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('masterinvent/mastercolor_imageedit_view', ['doc_url' => $doc_url,'doc_src' => $doc_src], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function mastercolor_save() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unkn');
            $error = 'Edit Session lifetime expired';
            $mdata = [];
            $sessiondat = usersession($session_id);
            if (!empty($sessiondat)) {
                // Save data
                $res = $this->inventory_model->masterinventory_color_save($sessiondat, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}