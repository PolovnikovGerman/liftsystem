<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Masterinventory extends MY_Controller
{
    private $maxlength=183;
    private $container_type = 'C';
    private $express_type = 'E';
    private $container_with=60;

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
            $mdata['bodylist'] = $this->load->view('masterinvent/inventory_body_view',[], TRUE);
            if (count($data['list'])==0) {
                $mdata['left_content']=$this->load->view('masterinvent/inventorylist_emptydata_view',[],TRUE);
                $mdata['express_content'] = '';
                $mdata['container_content'] = '';
                $mdata['right_content'] = '';
            } else {
                $expand=0;
                if (count($data['list'])<=39) {
                    $expand = 1;
                }
                $content_options = [
                    'lists' => $data['list'],
                    'showmax' => $showmax,
                    'expand' => $expand,
                ];
                $mdata['left_content']=$this->load->view('masterinvent/inventorydata_left_view', $content_options,TRUE);
                $mdata['right_content'] = $this->load->view('masterinvent/inventorydata_right_view', $content_options,TRUE);
                // On boats
                $colors = $data['colors'];
                $onboats = $this->inventory_model->get_data_onboat($inventory_type, $this->container_type);
                $containers_view='';
                foreach ($onboats as $onboat) {
                    $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $this->container_type);
                    $boptions=array(
                        'data'=>$details,
                        'onboat_container'=>$onboat['onboat_container'],
                        'onboat_status'=>$onboat['onboat_status'],
                    );
                    $containers_view.=$this->load->view('masterinvent/onboat_container_view', $boptions, TRUE);
                }
                $slider_width=60*(count($onboats));
                $margin=$this->maxlength-$slider_width;
                $boatoptions=array(
                    'width'=>$slider_width,
                    'margin'=>($margin>0 ? 0 : $margin),
                    'boatcontent'=>$containers_view,
                );
                $mdata['container_content']=$this->load->view('masterinvent/onboatdata_view', $boatoptions, TRUE);

                // $mdata['width'] = $slider_width-240;
                $mdata['margin']=$margin;
                $mdata['express_content'] = '&nbsp;';

            }
            // $mdata['instock'] = empty($data['type_instock']) ? '' : QTYOutput($data['type_instock']);
            // $mdata['available'] = empty($data['type_available']) ? '' : QTYOutput($data['type_available']);
            // $mdata['maximum'] = empty($data['type_maximum']) ? '' : QTYOutput($data['type_maximum']);
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

    public function add_color_income() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $error = '';
                $mdata['content'] = $this->load->view('masterinvent/prices_addmanual_view',['color' => $coloritem], TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function save_color_income() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $options = [];
            $options['income_date'] = ifset($postdata, 'income_date', '');
            // $options['income_recnum'] = ifset($postdata, 'income_recnum', '');
            $options['income_desript'] = ifset($postdata, 'income_desript', '');
            $options['income_price'] = ifset($postdata, 'income_price', 0);
            $options['income_qty'] = ifset($postdata, 'income_qty', 0);
            $options['user_id'] = $this->USR_ID;
            $res = $this->inventory_model->save_color_manualincome($coloritem, $options);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $tablebody = $this->load->view('masterinvent/prices_table_body',['lists' => $res['lists']], TRUE);
                $options = [
                    'tablebody' => $tablebody,
                    'totals' => $res['totals'],
                    'item' => $res['itemdata'],
                ];
                $mdata['content'] = $this->load->view('masterinvent/prices_body_view', $options, TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function add_color_outcome() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $error = '';
                $mdata['content'] = $this->load->view('masterinvent/history_addmanual_view',['color' => $coloritem], TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function save_color_outcome() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $options = [];
            $options['outcome_date'] = ifset($postdata, 'outcome_date', '');
            // $options['outcome_recnum'] = ifset($postdata, 'outcome_recnum', '');
            $options['outcome_descript'] = ifset($postdata, 'outcome_descript', '');
            $options['outcome_qty'] = ifset($postdata, 'outcome_qty', 0);
            $options['user_id'] = $this->USR_ID;
            $res = $this->inventory_model->save_color_manualoutcome($coloritem, $options);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $options = [
                    'lists' => $res['lists'],
                    'item' => $res['itemdata'],
                ];
                $mdata['content'] = $this->load->view('masterinvent/history_body_view', $options, TRUE);
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
            $doc_source = ifset($postdata, 'doc_source', '');
            $mdata = [];
            $error = 'Unknown document type';
            if (!empty($doc_type)) {
                $error = '';
                if ($doc_type=='proof') {
                    $mdata['content'] = $this->load->view('masterinvent/masteritem_prooftempl_view',['doc_url' => $doc_url, 'doc_source' => $doc_source], TRUE);
                } elseif ($doc_type=='plate') {
                    $mdata['content'] = $this->load->view('masterinvent/masteritem_platetempl_view',['doc_url' => $doc_url, 'doc_source' => $doc_source], TRUE);
                } elseif ($doc_type=='box') {
                    $mdata['content'] = $this->load->view('masterinvent/masteritem_boxtempl_view',['doc_url' => $doc_url, 'doc_source' => $doc_source], TRUE);
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
                    if ($fld=='color_status') {
                        if ($newval==1) {
                            $mdata['activebnt'] = '<i class="fa fa-check-circle-o" aria-hidden="true"></i>';
                            $mdata['inactivebnt'] = '<i class="fa fa-circle-o" aria-hidden="true"></i>';
                        } else {
                            $mdata['activebnt'] = '<i class="fa fa-circle-o" aria-hidden="true"></i>';
                            $mdata['inactivebnt'] = '<i class="fa fa-check-circle-o" aria-hidden="true"></i>';
                        }
                    }
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

    public function get_colorimage() {
        $getdata = $this->input->get();
        $color_id = ifset($getdata,'c', 0);
        $res = $this->inventory_model->get_inventory_mastercolor($color_id,0);
        $error = $res['msg'];
        if ($res['result']==$this->success_result) {
            $itemdat = $res['colordata'];
            $msg = $this->load->view('masterinvent/mastercolor_details_view', $itemdat, TRUE);
        } else {
            $msg = $error;
        }
        echo $msg;

    }

    public function export_inventory() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_label = ifset($postdata, 'inventory_label','UNK');
            $inventory_filter = 0;
            $mdata = [];
            $error = 'Unknown Inventory Type';
            if (!empty($inventory_type)) {
                $data = $this->inventory_model->get_masterinvent_list($inventory_type, $inventory_filter);
                $this->load->model('exportexcell_model');
                $res = $this->exportexcell_model->export_master_inventory($data['list'], $inventory_label);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['url'] = $res['url'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function changecontainer() {
        if ($this->isAjax()) {
            $error = '';
            $mdata = [];
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = ifset($postdata,'inventory_filter', 0);
            $onboat_container = ifset($postdata,'container',0);
            $onboat_type = ifset($postdata, 'onboat_type', $this->container_type);
            // Get colors
            $sessionid = uniq_link(14);
            $colors = $this->inventory_model->get_inventory_colors($inventory_type, $inventory_filter);
            if ($onboat_container==0) {
                $total = 0;
                $onboat_date = time();
                $freight_price = 0;
                // Old containers
                $onboats = $this->inventory_model->get_data_onboat($inventory_type, $onboat_type);
                $viewwidth=(count($onboats)+1)*$this->container_with;
                $mdata['width']=$viewwidth;
                $marginleft=($viewwidth>$this->maxlength ? ($this->maxlength-$viewwidth) : 0);
                $mdata['marginleft']= $marginleft;
                $head_options = [
                    'onboat_status' => 0,
                    'onboat_container' => 0,
                    'onboat_date' => $onboat_date,
                    'freight_price' => $freight_price,
                ];
                $mdata['containerhead'] = $this->load->view('masterinvent/onboat_containerhead_view', $head_options, TRUE);
                $container = $this->inventory_model->new_onboatcontainer($colors, $onboat_type);
                $rawcontent = $this->load->view('masterinvent/onboat_container_edit',['data' => $container,'session_id' => $sessionid], TRUE);
                $mdata['content'] = '<div class="onboacontainerarea" data-container="0"><div class="onboacontainerdata editdata" data-container="0">'.$rawcontent.'</div></div>';
            } else {
                $totals = $this->inventory_model->get_onboattotals($onboat_container, $onboat_type);
                $container = $this->inventory_model->get_onboatdetails($onboat_container, $colors, $onboat_type,  1);
                $total = $totals['total'];
                $onboat_date = $totals['onboat_date'];
                $freight_price = $totals['freight_price'];
                $mdata['content'] = $this->load->view('masterinvent/onboat_container_edit',['data' => $container,'session_id' => $sessionid], TRUE);
            }


            $mdata['managecontent']=$this->load->view('masterinvent/container_edit_view',[], TRUE); // onboat_editmanage
            $sessiondata = [
                'total' => $total,
                'onboat_date' => $onboat_date,
                'freight_price' => $freight_price,
                'container' => $container,
                'onboat_container' => $onboat_container,
                'onboat_type' => $onboat_type,
            ];
            usersession($sessionid, $sessiondata);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function changecontainer_param() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Edit time expire - reload page';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session_id);
            if (!empty($sessiondata)) {
                $error = 'Empty Inventory Color / Item';
                $color_id = ifset($postdata, 'color',0);
                $item_id = ifset($postdata, 'item', 0);
                if ($item_id!==0 && $color_id!==0) {
                    $entity = ifset($postdata,'entity', 'qty');
                    $newval = ifset($postdata, 'newval', 0);
                    $res = $this->inventory_model->changecontainer_param($sessiondata, $color_id, $item_id, $entity, $newval, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mdata['container'] = $sessiondata['onboat_container'];
                        if ($entity=='qty') {
                            $mdata['itemtval'] = $res['itemtval']==0 ? '&nbsp;' : QTYOutput($res['itemtval']);
                            $mdata['total'] = $res['total']==0 ? '&nbsp;' : QTYOutput($res['total']);
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function containerchange_cancel() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = ifset($postdata,'inventory_filter', 0);

            // Prepare header view
            $onboats = $this->inventory_model->get_data_onboat($inventory_type, $this->container_type);
            $boathead_view='';
            foreach ($onboats as $onboat) {
                $boathead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $onboat, TRUE);
            }
            // Build head content
            $slider_width=60*count($onboats);
            $margin = $this->maxlength-$slider_width;
            $margin=($margin>0 ? 0 : $margin);
            // $width_edit = 58;
            $boatoptions=array(
                'data'=>$onboats,
                'container_view' => $boathead_view,
                'width' => $slider_width,
                'margin' => $margin,
            );
            $mdata['onboat_header']=$this->load->view('masterinvent/onboathead_view', $boatoptions, TRUE);
            // Prepare body content
            $colors = $this->inventory_model->get_inventory_colors($inventory_type, $inventory_filter);
            $containers_view='';
            foreach ($onboats as $onboat) {
                $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $this->container_type);
                $boptions=array(
                    'data'=>$details,
                    'onboat_container'=>$onboat['onboat_container'],
                    'onboat_status'=>$onboat['onboat_status'],
                );
                $containers_view.=$this->load->view('masterinvent/onboat_container_view', $boptions, TRUE);
            }
            $slider_width=60*(count($onboats));
            $margin=$this->maxlength-$slider_width;
            $boatoptions=array(
                'width'=>$slider_width,
                'margin'=>($margin>0 ? 0 : $margin),
                'boatcontent'=>$containers_view,
            );
            $mdata['onboat_content']=$this->load->view('masterinvent/onboatdata_view', $boatoptions, TRUE);
            $this->ajaxResponse($mdata, $error);

        }
        show_404();
    }

    public function containerchange_save() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Edit time expire - reload page';
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session_id);
            if (!empty($sessiondata)) {
                $onboat_container = $sessiondata['onboat_container'];
                $container = $sessiondata['container'];
                $freight_price = $sessiondata['freight_price'];
                $onboat_date = $sessiondata['onboat_date'];
                $onboat_type = $sessiondata['onboat_type'];
                $res = $this->inventory_model->inventory_container_save($onboat_container, $onboat_type, $container, $onboat_date, $freight_price);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $inventory_type = ifset($postdata,'inventory_type',0);
                    $inventory_filter = ifset($postdata,'inventory_filter', 0);
                    // Prepare header view
                    $onboats = $this->inventory_model->get_data_onboat($inventory_type, $this->container_type);
                    $boathead_view='';
                    foreach ($onboats as $onboat) {
                        $boathead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $onboat, TRUE);
                    }
                    // Build head content
                    $slider_width=60*count($onboats);
                    $margin = $this->maxlength-$slider_width;
                    $margin=($margin>0 ? 0 : $margin);
                    // $width_edit = 58;
                    $boatoptions=array(
                        'data'=>$onboats,
                        'container_view' => $boathead_view,
                        'width' => $slider_width,
                        'margin' => $margin,
                    );
                    $mdata['onboat_header']=$this->load->view('masterinvent/onboathead_view', $boatoptions, TRUE);
                    // Prepare body content
                    $colors = $this->inventory_model->get_inventory_colors($inventory_type, $inventory_filter);
                    $containers_view='';
                    foreach ($onboats as $onboat) {
                        $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $this->container_type);
                        $boptions=array(
                            'data'=>$details,
                            'onboat_container'=>$onboat['onboat_container'],
                            'onboat_status'=>$onboat['onboat_status'],
                        );
                        $containers_view.=$this->load->view('masterinvent/onboat_container_view', $boptions, TRUE);
                    }
                    $slider_width=60*(count($onboats));
                    $margin=$this->maxlength-$slider_width;
                    $boatoptions=array(
                        'width'=>$slider_width,
                        'margin'=>($margin>0 ? 0 : $margin),
                        'boatcontent'=>$containers_view,
                    );
                    $mdata['onboat_content']=$this->load->view('masterinvent/onboatdata_view', $boatoptions, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}