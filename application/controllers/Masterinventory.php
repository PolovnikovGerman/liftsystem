<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Masterinventory extends MY_Controller
{
    private $maxlength=183;
    private $container_type = 'C';
    private $express_type = 'E';
    private $container_with=60;
    private $empty_html_content='&nbsp;';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
    }

    public function get_inventory_head() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = intval(ifset($postdata,'inventory_filter',0));
            $showmax = ifset($postdata,'showmax', 0);
            $inventory_item = ifset($postdata, 'inventory_item', 0);
            $inventory_color = ifset($postdata, 'inventory_color', 0);
            $addsearch = [];
            if ($inventory_item!==0 || $inventory_color!==0) {
                $addsearch = [
                    'inventory_color_id' => $inventory_color,
                    'inventory_item_id' => $inventory_item,
                ];
            }
            $totals = $this->inventory_model->get_inventory_totals($inventory_type, $inventory_filter, $addsearch);
            $mdata=[];
            $error = '';
            // Get OnBoats
            $onboats = $this->inventory_model->get_data_onboat($inventory_type, $this->container_type, $inventory_filter);
            $boathead_view='';
            $boathead_totals = '';
            $boatlinks_view = '';
            foreach ($onboats as $onboat) {
                $boathead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $onboat, TRUE);
                $boathead_totals.=$this->load->view('masterinvent/onboat_containertotals_view', $onboat, TRUE);
                $boatlinks_view.=$this->load->view('masterinvent/onboat_containerlinks_view', $onboat, TRUE);
            }
            // Build head containers  content
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
            $mdata['onboat_content'] = $this->load->view('masterinvent/onboathead_view', $boatoptions, TRUE);
            $boatoptions['container_view'] = $boathead_totals;
            $mdata['onboat_totals'] = $this->load->view('masterinvent/onboathead_view', $boatoptions, TRUE);
            $linkoptions = [
                'data'=>$onboats,
                'container_view' => $boatlinks_view,
                'width' => $slider_width,
                'margin' => $margin,
            ];
            $mdata['onboat_links'] = $this->load->view('masterinvent/onboatlinks_view', $linkoptions, TRUE);
            $mdata['container_leftview'] = ($margin < 0 ? 1 : 0);
            // Prepare Expres
            $expresses = $this->inventory_model->get_data_onboat($inventory_type, $this->express_type, $inventory_filter);
            $expresshead_view = '';
            $expresslinks_view = '';
            $expresshead_totals = '';
            foreach ($expresses as $express) {
                $expresshead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $express, TRUE);
                $expresslinks_view.=$this->load->view('masterinvent/onboat_containerlinks_view', $express, TRUE);
                $expresshead_totals.=$this->load->view('masterinvent/onboat_containertotals_view', $express, TRUE);
            }
            // Build head containers  content
            $slider_width=60*count($expresses);
            $margin = $this->maxlength-$slider_width;
            $margin=($margin>0 ? 0 : $margin);
            // $width_edit = 58;
            $expressoptions=array(
                'data'=>$expresses,
                'container_view' => $expresshead_view,
                'width' => $slider_width,
                'margin' => $margin,
            );
            $mdata['express_content'] = $this->load->view('masterinvent/onboathead_view', $expressoptions, TRUE);
            $expressoptions['container_view'] = $expresshead_totals;
            $mdata['express_totals'] = $this->load->view('masterinvent/onboathead_view', $expressoptions, TRUE);
            $linkoptions = [
                'data'=>$onboats,
                'container_view' => $expresslinks_view,
                'width' => $slider_width,
                'margin' => $margin,
            ];
            $mdata['express_links'] = $this->load->view('masterinvent/onboatlinks_view', $linkoptions, TRUE);
            $mdata['express_leftview'] = ($margin < 0 ? '1' : 0);
            // Totals
            $mdata['masterinventpercent'] = $totals['itempercent'];
            $mdata['masterinventorymaximum'] = empty($totals['max']) ? $this->empty_html_content : QTYOutput($totals['max']);
            $mdata['masterinventinstock'] = empty($totals['instock']) ? $this->empty_html_content : QTYOutput($totals['instock']);
            $mdata['masterinventreserv'] = empty($totals['reserved']) ? $this->empty_html_content : QTYOutput($totals['reserved']);
            $mdata['masterinventavailab'] = empty($totals['available']) ? $this->empty_html_content : QTYOutput($totals['available']);
            $mdata['maxsum'] = empty($totals['maxsum']) ? $this->empty_html_content : MoneyOutput($totals['maxsum']);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function get_inventory_list() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = ifset($postdata,'inventory_filter',0);
            $showmax = ifset($postdata,'showmax', 0);
            $inventory_item = intval(ifset($postdata, 'inventory_item', 0));
            $inventory_color = intval(ifset($postdata, 'inventory_color', 0));
            $addsearch = [];
            if ($inventory_item!==0 || $inventory_color!==0) {
                $addsearch = [
                    'inventory_color_id' => $inventory_color,
                    'inventory_item_id' => $inventory_item,
                ];
            }

            $mdata=[];

            $data = $this->inventory_model->get_masterinvent_list($inventory_type, $inventory_filter, $addsearch);
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
                $colors = $data['colors'];
                // On boats
                $onboats = $this->inventory_model->get_data_onboat($inventory_type, $this->container_type, $inventory_filter);
                $containers_view='';
                foreach ($onboats as $onboat) {
                    $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $this->container_type, $inventory_filter);
                    $boptions=array(
                        'data'=>$details,
                        'onboat_container'=>$onboat['onboat_container'],
                        'onboat_status'=>$onboat['onboat_status'],
                        'type' => $this->container_type,
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
                // Express
                $expresses = $this->inventory_model->get_data_onboat($inventory_type, $this->express_type, $inventory_filter);
                $express_view='';
                foreach ($expresses as $onboat) {
                    $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $this->express_type, $inventory_filter);
                    $boptions=array(
                        'data'=>$details,
                        'onboat_container'=>$onboat['onboat_container'],
                        'onboat_status'=>$onboat['onboat_status'],
                        'type' => $this->express_type,
                    );
                    $express_view.=$this->load->view('masterinvent/onboat_container_view', $boptions, TRUE);
                }
                $slider_width=60*(count($expresses));
                $margin=$this->maxlength-$slider_width;
                $expressoptions=array(
                    'width'=>$slider_width,
                    'margin'=>($margin>0 ? 0 : $margin),
                    'boatcontent'=>$express_view,
                );
                $mdata['express_content']=$this->load->view('masterinvent/onboatdata_view', $expressoptions, TRUE);

            }
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
                $onboats = $this->inventory_model->get_data_onboat($inventory_type, $onboat_type, $inventory_filter);
                $viewwidth=(count($onboats)+1)*$this->container_with;
                $mdata['width']=$viewwidth;
                $marginleft=($viewwidth>$this->maxlength ? ($this->maxlength-$viewwidth) : 0);
                $mdata['marginleft']= $marginleft;
                $head_options = [
                    'onboat_status' => 0,
                    'onboat_container' => 0,
                    'onboat_type' => $onboat_type,
                    'onboat_date' => $onboat_date,
                    'freight_price' => $freight_price,
                ];
                $mdata['containerhead'] = $this->load->view('masterinvent/onboat_containerhead_view', $head_options, TRUE);
                $mdata['containertotal'] = $this->load->view('masterinvent/onboat_containertotals_view', $head_options, TRUE);
                $container = $this->inventory_model->new_onboatcontainer($colors, $onboat_type);
                $rawcontent = $this->load->view('masterinvent/onboat_container_edit',['data' => $container,'session_id' => $sessionid], TRUE);
                $mdata['content'] = '<div class="onboacontainerarea" data-container="0"><div class="onboacontainerdata editdata" data-container="0">'.$rawcontent.'</div></div>';
            } else {
                $chktotals = $this->inventory_model->get_onboattotals($onboat_container, $onboat_type);
                $error = $chktotals['msg'];
                if ($chktotals['result']==$this->success_result) {
                    $error = '';
                    $totals = $chktotals['data'];
                    $container = $this->inventory_model->get_onboatdetails($onboat_container, $colors, $onboat_type, $inventory_filter,  1);
                    $total = $totals['total'];
                    $onboat_date = $totals['onboat_date'];
                    $freight_price = $totals['freight_price'];
                    $mdata['freight_price'] = $freight_price;
                    $mdata['content'] = $this->load->view('masterinvent/onboat_container_edit',['data' => $container,'session_id' => $sessionid], TRUE);
                }
            }
            if ($error=='') {
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
            }
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

    public function changecontainer_header() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Edit time expire - reload page';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session_id);
            if (!empty($sessiondata)) {
                $entity = ifset($postdata,'entity', 'qty');
                $newval = ifset($postdata, 'newval', 0);
                $res = $this->inventory_model->changecontainer_header($sessiondata, $entity, $newval, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
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
            $onboat_type = ifset($postdata, 'onboat_type','C');
            // Prepare header view
            $onboats = $this->inventory_model->get_data_onboat($inventory_type, $onboat_type, $inventory_filter);
            $boathead_view='';
            $boatlinks_view = '';
            foreach ($onboats as $onboat) {
                // $onboat['type'] = 'C';
                $boathead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $onboat, TRUE);
                $boatlinks_view.=$this->load->view('masterinvent/onboat_containerlinks_view', $onboat, TRUE);
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
            $linkoptions = [
                'data'=>$onboats,
                'container_view' => $boatlinks_view,
                'width' => $slider_width,
                'margin' => $margin,
            ];
            $mdata['onboat_links'] = $this->load->view('masterinvent/onboatlinks_view', $linkoptions, TRUE);

            // Prepare body content
            $colors = $this->inventory_model->get_inventory_colors($inventory_type, $inventory_filter);
            $containers_view='';
            foreach ($onboats as $onboat) {
                $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $onboat_type, $inventory_filter);
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
                    $onboats = $this->inventory_model->get_data_onboat($inventory_type, $onboat_type, $inventory_filter);
                    $boathead_view='';
                    $boatlinks_view = '';
                    foreach ($onboats as $onboat) {
                        $boathead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $onboat, TRUE);
                        $boatlinks_view.=$this->load->view('masterinvent/onboat_containerlinks_view', $onboat, TRUE);
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
                    $linkoptions = [
                        'data'=>$onboats,
                        'container_view' => $boatlinks_view,
                        'width' => $slider_width,
                        'margin' => $margin,
                    ];
                    $mdata['onboat_links'] = $this->load->view('masterinvent/onboatlinks_view', $linkoptions, TRUE);
                    // Prepare body content
                    $colors = $this->inventory_model->get_inventory_colors($inventory_type, $inventory_filter);
                    $containers_view='';
                    foreach ($onboats as $onboat) {
                        $details = $this->inventory_model->get_onboatdetails($onboat['onboat_container'], $colors, $onboat_type, $inventory_filter);
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

    public function container_arrive() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Container Not Found';
            $postdata = $this->input->post();
            $onboat_container = ifset($postdata,'onboat_container',0);
            $onboat_type = ifset($postdata, 'onboat_type','');
            if ($onboat_container > 0 && !empty($onboat_type)) {
                $chktotals = $this->inventory_model->get_onboattotals($onboat_container, $onboat_type);
                $error = $chktotals['msg'];
                if ($chktotals['result']==$this->success_result) {
                    // Container exist
                    $totals = $chktotals['data'];
                    $res = $this->inventory_model->onboat_arrived($onboat_container, $onboat_type, $totals, $this->USR_ID);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        // Rebuild header
                        $inventory_type = ifset($postdata,'inventory_type',0);
                        $inventory_filter = ifset($postdata,'inventory_filter', 0);

                        // Prepare header view
                        $onboats = $this->inventory_model->get_data_onboat($inventory_type, $this->container_type, $inventory_filter);
                        $boathead_view='';
                        foreach ($onboats as $onboat) {
                            $onboat['type'] = 'C';
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
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_boat_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Container #';
            $postdate=$this->input->post();
            $onboat_container = ifset($postdate, 'onboat_container',0);
            $onboat_type = ifset($postdate, 'onboat_type','');
            if ($onboat_container!==0 && !empty($onboat_type)) {
                $res = $this->inventory_model->onboat_download($onboat_container, $onboat_type);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['url']=$res['url'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function changeaddcost() {
        if ($this->isAjax()) {
            $mdata=array();
            // $error='';
            $postdate=$this->input->post();
            $inventory_type = ifset($postdate, 'inventory_type',0);
            $addcost = ifset($postdate, 'addcost',0);
            $res = $this->inventory_model->inventorytype_addcost($inventory_type, $addcost);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $addcost = $res['addcost'];
                $totals = $this->inventory_model->get_inventory_totals($inventory_type);
                $addval = $totals['available'] * $addcost;
                $addstr = $addval == 0 ? '-' : MoneyOutput($addval);
                // $str = 'ea  ('.$addval==0 ? '-' : MoneyOutput($addval).')';
                $mdata['content'] = 'ea  ('.$addstr.')';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}