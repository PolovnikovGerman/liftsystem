<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Leadquote extends MY_Controller
{

    private $restore_orderdata_error = 'Connection Lost. Please, recall form';

    private $quotetemplates = [
        'Quote',
        'Proforma',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('leadquote_model');
    }
    // Add New Quote
    public function lead_addquote() {
        if ($this->isAjax()) {
            $mdata=array();
            $leadpost=$this->input->post();
            $this->load->model('leads_model');
            /* Get Tasks & user array */
            $lead_tasks=array();
            $session_id=$leadpost['session_id'];
            $lead_replic=usersession($session_id);
            $lead_usr=array();
            foreach ($lead_replic as $row) {
                array_push($lead_usr, $row['user_id']);
            }
            $usr_id=$this->USR_ID;
            $res=$this->leads_model->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
            $error=$res['msg'];
            if ($res['result']!=$this->error_result) {
                $error = '';
                $lead_id=$res['result'];
                $mdata['lead_id']=$res['result'];
                // Get new value of Lead
                $lead_data=$this->leads_model->get_lead($lead_id);
                usersession('leaddata',$lead_data);
                $qres=$this->leadquote_model->add_leadquote($lead_data, $usr_id, $this->USER_NAME);
                $error=$qres['msg'];
                if ($qres['result']==$this->success_result) {
                    $error = '';
                    $quotedata = $qres['quote'];
                    $quote_items = $qres['quote_items'];
                    $templlists = $this->quotetemplates;
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);

                    $shipstate = '';
                    $billstate = '';
                    if (!empty($quotedata['shipping_country'])) {
                        $shipstates = $this->shipping_model->get_country_states($quotedata['shipping_country']);
                        if (is_array($shipstates)) {
                            $stateoptions = [
                                'item' => 'shipping_state',
                                'states' => $shipstates,
                                'edit_mode' => 1,
                                'data' => $quotedata,
                            ];
                            $shipstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    };
                    if (!empty($quotedata['billing_country'])) {
                        $billstates = $this->shipping_model->get_country_states($quotedata['billing_country']);
                        if (is_array($billstates)) {
                            $stateoptions = [
                                'item' => 'billing_state',
                                'states' => $billstates,
                                'edit_mode' => 1,
                                'data' => $quotedata,
                            ];
                            $billstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    };
                    // Empty Tax view
                    $taxview = $this->load->view('leadpopup/quote_taxempty_view',[],TRUE);
                    // Prepare content
                    $items_views = [];
                    $item_subtotal = 0;
                    foreach ($quote_items as $quote_item) {
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => 1,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit'=>1,
                            'item_id'=>$quote_item['item_id'],
                        ];
                        $item_view=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        $item_subtotal+=$quote_item['item_subtotal'];
                        $items_views[] = [
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'view' => $item_view,
                        ];
                    }
                    $item_content = $this->load->view('leadpopup/items_content_view', ['data' => $items_views], TRUE);
                    $quotedata['items_subtotal'] = $item_subtotal;
                    $quotedata['quote_total'] = $item_subtotal;
                    $quote_session = 'quote'.uniq_link(15);
                    $sessiondata = [
                        'quote' => $quotedata,
                        'items' => $quote_items,
                        'shipping' => [],
                        'deleted' => [],
                    ];
                    usersession($quote_session, $sessiondata);
                    $lead_time = '';
                    if (!empty($quotedata['lead_time'])) {
                        $lead_times = json_decode($quotedata['lead_time'], true);
                        $timeoptions = [
                            'lead_times' => $lead_times,
                            'edit_mode' => 1,
                        ];
                        $lead_time = $this->load->view('leadpopup/quote_leadtime_edit', $timeoptions, TRUE);
                    }
                    $options = [
                        'quote_session' => $quote_session,
                        'quote_id' => $quotedata['quote_id'],
                        'lead_id' => $lead_id,
                        'data' => $quotedata,
                        'itemsview' => $item_content,
                        'templlists' => $templlists,
                        'countries' => $countries,
                        'edit_mode' => 1,
                        'shiprates' => '',
                        'lead_time' => $lead_time,
                        'shipstate' => $shipstate,
                        'billstate' => $billstate,
                        'taxview' => $taxview,
                    ];
                    $mdata['quotecontent'] = $this->load->view('leadpopup/quotedata_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    // Edit Quote
    public function quoteedit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Unknown Quote #';
            $postdata = $this->input->post();
            $quote_id = ifset($postdata, 'quote_id', 0);
            $edit_mode = ifset($postdata, 'edit_mode', 0);
            if (!empty($quote_id)) {
                $qres=$this->leadquote_model->get_leadquote($quote_id, $edit_mode);
                $error = $qres['msg'];
                if ($qres['result']==$this->success_result) {
                    $error = '';
                    // Prepare content
                    $quotedata = $qres['quote'];
//                    if ($quotedata['brand']=='SR') {
//                        $templlists = [
//                            'Supplier',
//                            'Proforma Invoice',
//                        ];
//                    } else {
//                        $templlists = [
//                            'Stressballs.com',
//                            'Bluetrack Health',
//                            'Proforma Invoice',
//                        ];
//                    }
                    $templlists = $this->quotetemplates;
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);
                    $shipstate = '';
                    $billstate = '';
                    if (!empty($quotedata['shipping_country'])) {
                        $shipstates = $this->shipping_model->get_country_states($quotedata['shipping_country']);
                        if (is_array($shipstates)) {
                            $stateoptions = [
                                'item' => 'shipping_state',
                                'states' => $shipstates,
                                'edit_mode' => $edit_mode,
                                'data' => $quotedata,
                            ];
                            $shipstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    };
                    if (!empty($quotedata['billing_country'])) {
                        $billstates = $this->shipping_model->get_country_states($quotedata['billing_country']);
                        if (is_array($billstates)) {
                            $stateoptions = [
                                'item' => 'billing_state',
                                'states' => $billstates,
                                'edit_mode' => $edit_mode,
                                'data' => $quotedata,
                            ];
                            $billstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    };
                    // Tax view
                    if ($quotedata['taxview']==0) {
                        // Empty Tax view
                        $taxview = $this->load->view('leadpopup/quote_taxempty_view',[],TRUE);
                    } else {
                        $taxoptions = [
                            'edit_mode' => $edit_mode,
                            'data' => $quotedata,
                        ];
                        $taxview = $this->load->view('leadpopup/quote_tax_edit', $taxoptions,TRUE);
                    }
                    $quote_items = $qres['items'];
                    $shippings = $qres['shippings'];
                    $items_views = [];
                    foreach ($quote_items as $quote_item) {
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => $edit_mode,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit'=> $edit_mode,
                            'item_id'=>$quote_item['item_id'],
                        ];
                        if ($edit_mode==0) {
                            $view=$this->load->view('leadpopup/items_data_view', $item_options, TRUE);
                        } else {
                            $view=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        }
                        $items_views[] = [
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'view' => $view,
                        ];
                    }
                    $item_content = $this->load->view('leadpopup/items_content_view', ['data' => $items_views], TRUE);
                    // Shipping view
                    $shiprates = '';
                    if (count($shippings) > 0) {
                        $shipoptions = [
                            'edit_mode' => $edit_mode,
                            'shippings' => $shippings,
                        ];
                        $shiprates = $this->load->view('leadpopup/quote_shiprates_view', $shipoptions, TRUE);
                    }
                    $quote_session = 'quote'.uniq_link(15);
                    $sessiondata = [
                        'quote' => $quotedata,
                        'items' => $quote_items,
                        'shipping' => $shippings,
                        'deleted' => [],
                    ];
                    usersession($quote_session, $sessiondata);
                    $lead_time = '';
                    if (!empty($quotedata['lead_time'])) {
                        $lead_times = json_decode($quotedata['lead_time'], true);
                        $timeoptions = [
                            'lead_times' => $lead_times,
                            'edit_mode' => $edit_mode,
                        ];
                        $lead_time = $this->load->view('leadpopup/quote_leadtime_edit', $timeoptions, TRUE);
                    }
                    $options = [
                        'quote_session' => $quote_session,
                        'quote_id' => $quote_id,
                        'lead_id' => $quotedata['lead_id'],
                        'data' => $quotedata,
                        'itemsview' => $item_content,
                        'templlists' => $templlists,
                        'countries' => $countries,
                        'edit_mode' => $edit_mode,
                        'shipstate' => $shipstate,
                        'billstate' => $billstate,
                        'shiprates' => $shiprates,
                        'lead_time' => $lead_time,
                        'taxview' => $taxview,
                    ];
                    $mdata['quotecontent'] = $this->load->view('leadpopup/quotedata_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Quote Parameter
    public function quoteparamchange() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->quoteparamchange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['totalcalc'] = $res['totalcalc'];
                    if ($res['totalcalc']==1) {
                        $this->leadquote_model->calc_quote_totals($session_id);
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                        $mdata['tax'] = $quote['sales_tax'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Address Parameter
    public function quoteaddresschange() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->quoteaddresschange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $mdata['shipstate'] = $res['shipstate'];
                    if ($res['shipstate']==1) {
                        $this->load->model('shipping_model');
                        $shipstates = $this->shipping_model->get_country_states($quote['shipping_country']);
                        if (is_array($shipstates)) {
                            $stateoptions = [
                                'item' => 'shipping_state',
                                'states' => $shipstates,
                                'edit_mode' => 1,
                                'data' => $quote,
                            ];
                            $mdata['stateview'] = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    }
                    $mdata['billstate'] = $res['billstate'];
                    if ($res['billstate']==1) {
                        $this->load->model('shipping_model');
                        $billstates = $this->shipping_model->get_country_states($quote['shipping_country']);
                        if (is_array($billstates)) {
                            $stateoptions = [
                                'item' => 'billing_state',
                                'states' => $billstates,
                                'edit_mode' => 1,
                                'data' => $quote,
                            ];
                            $mdata['stateview'] = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    }
                    $mdata['shiprebuild'] = $res['shiprebuild'];
                    if ($res['shiprebuild']==1) {
                        $mdata['shipping_zip'] = $quote['shipping_zip'];
                        $mdata['shipping_city'] = $quote['shipping_city'];
                        $mdata['shipping_state'] = $quote['shipping_state'];
                    }
                    $mdata['billrebuild'] = $res['billrebuild'];
                    if ($res['billrebuild']==1) {
                        $mdata['billing_zip'] = $quote['billing_zip'];
                        $mdata['billing_city'] = $quote['billing_city'];
                        $mdata['billing_state'] = $quote['billing_state'];
                    }
                    $mdata['taxview'] = $res['taxview'];
                    if ($res['taxview']==1) {
                        if ($quote['taxview']==0) {
                            // Empty Tax view
                            $taxview = $this->load->view('leadpopup/quote_taxempty_view',[],TRUE);
                        } else {
                            $taxoptions = [
                                'edit_mode' => 1,
                                'data' => $quote,
                            ];
                            $taxview = $this->load->view('leadpopup/quote_tax_edit', $taxoptions,TRUE);
                        }
                        $mdata['taxcontent'] = $taxview;
                    }
                    $mdata['calcship'] = $res['calcship'];
                    if ($res['calcship']==1) {
                        $this->leadquote_model->calc_quote_shipping($session_id);
                        // Show shipping costs
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['shipping_cost'] = $quote['shipping_cost'];
                        $shipping = $quotesession['shipping'];
                        $options = [
                            'shippings' => $shipping,
                            'edit_mode' => 1,
                        ];
                        $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);

                    }
                    $mdata['totalcalc'] = $res['totalcalc'];
                    if ($res['totalcalc']==1) {
                        $this->leadquote_model->calc_quote_totals($session_id);
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                        $mdata['tax'] = $quote['sales_tax'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Shipping Rate
    public function quoteratechange() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->quoteratechange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $this->leadquote_model->calc_quote_totals($session_id);
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $shipping = $quotesession['shipping'];
                    $options = [
                        'shippings' => $shipping,
                        'edit_mode' => 1,
                    ];
                    $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);
                    $mdata['shipping_cost'] = $quote['shipping_cost'];
                    $mdata['total'] = MoneyOutput($quote['quote_total']);
                    $mdata['tax'] = $quote['sales_tax'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quotetaxextemp() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->quotetaxextemp($quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $this->leadquote_model->calc_quote_totals($session_id);
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    if ($quote['tax_exempt']==1) {
                        $mdata['content'] = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                    } else {
                        $mdata['content'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                    }
                    $mdata['tax_reason'] = $quote['tax_reason'];
                    $mdata['total'] = MoneyOutput($quote['quote_total']);
                    $mdata['tax'] = $quote['sales_tax'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Lead time
    public function quoteleadtimechange() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->quoteleadtimechange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // Recalc shipping
                    $this->leadquote_model->calc_quote_shipping($session_id);
                    $this->leadquote_model->calc_quote_totals($session_id);
                    // Get data
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $shipping = $quotesession['shipping'];
                    $options = [
                        'shippings' => $shipping,
                        'edit_mode' => 1,
                    ];
                    $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);
                    $mdata['shipping_cost'] = $quote['shipping_cost'];
                    $mdata['rush_cost'] = $quote['rush_cost'];
                    $mdata['total'] = MoneyOutput($quote['quote_total']);
                    $mdata['tax'] = $quote['sales_tax'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Item Data parameter
    public function quoteitemchange() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->quoteitemchange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['totals'] = 0;
                    $mdata['refresh'] = 0;
                    $mdata['shipping'] = 0;
                    if ($res['shipcalc']==1) {
                        $this->leadquote_model->calc_quote_shipping($session_id);
                        $mdata['shipping'] = 1;
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['shipping_cost'] = $quote['shipping_cost'];
                        $shipping = $quotesession['shipping'];
                        $options = [
                            'shippings' => $shipping,
                            'edit_mode' => 1,
                        ];
                        $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);
                    }
                    if ($res['totalcalc']==1) {
                        $this->leadquote_model->calc_quote_totals($session_id);
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                        $mdata['tax'] = $quote['sales_tax'];
                        $mdata['totals'] = 1;
                    }
                    if ($res['item_refresh']==1) {
                        // Update content
                        $quotesession = usersession($session_id);
                        $quote_items = $quotesession['items'];
                        $item_content='';
                        foreach ($quote_items as $quote_item) {
                            if ($quote_item['quote_item_id']==$postdata['item']) {
                                $imprints=$quote_item['imprints'];
                                $imprint_options=[
                                    'quote_item_id'=>$quote_item['quote_item_id'],
                                    'imprints'=>$imprints,
                                    'edit_mode' => 1,
                                ];
                                $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                                $item_options=[
                                    'quote_item_id'=>$quote_item['quote_item_id'],
                                    'items'=>$quote_item['items'],
                                    'imprintview'=>$imprintview,
                                    'edit'=>1,
                                    'item_id'=>$quote_item['item_id'],
                                ];
                                $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                            }
                        }
                        $mdata['itemcontent'] = $item_content;
                        $mdata['refresh'] = 1;
                    } else {
                        $mdata['itemcolor_subtotal'] = MoneyOutput($res['item_subtotal']);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Add Item Color
    public function quoteitemaddcolor() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->additemcolor($quotesession, $postdata,  $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $item_content='';
                    $quote_item = $res['item'];
                    $imprints=$quote_item['imprints'];
                    $imprint_options=[
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'imprints'=>$imprints,
                        'edit_mode' => 1,
                    ];
                    $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                    $item_options=[
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'items'=>$quote_item['items'],
                        'imprintview'=>$imprintview,
                        'edit' => 1,
                        'item_id' => $quote_item['item_id'],
                    ];
                    $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                    $mdata['itemcontent'] = $item_content;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Open popup Imprint details
    public function quoteitemprintdetails() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session', 'unkn');
            $session = usersession($session_id);
            if (!empty($session)) {
                $res = $this->leadquote_model->prepare_print_details($session, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $details=$res['imprint_details'];
                    $quote = $res['quote'];
                    $quote_blank=$quote['quote_blank'];
                    $item_id=$res['item_id'];
                    /*if ($order_blank==0) {
                        $chkactiv=0;
                        foreach ($details as $row) {
                            if ($row['active']==1) {
                                $chkactiv=1;
                                break;
                            }
                        }
                        if ($chkactiv==0) {
                            $details[0]['active']=1;
                        }
                    } */
                    // Prepare View
                    $imptintid='imprintdetails'.uniq_link(15);
                    $options=array(
                        'details' => $details,
                        'item_number' => $res['item_number'],
                        'quote_blank' => $quote_blank,
                        'imprints' => $res['imprints'],
                        'numlocs' => count($res['imprints']),
                        'item_name' => $res['item_name'],
                        'imprintsession'=>$imptintid,
                    );
                    $mdata['content']=  $this->load->view('leadpopup/imprint_details_edit', $options, TRUE);

                    $imprintdetails=array(
                        'imprint_details' => $details,
                        'quote_blank' => $quote_blank,
                        'quote_item_id' => $postdata['item'],
                        'item_id' => $item_id,
                    );
                    usersession($imptintid, $imprintdetails);

                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Show Imprint details, repeat note for edit
    public function edit_repeatnote() {
        $postdata=$this->input->post();
        $quotesession_id = ifset($postdata, 'quotesession', 'unkn');
        $quotesession = usersession($quotesession_id);

        if (empty($quotesession)) {
            echo $this->restore_orderdata_error;
            die();
        }
        // Details
        $detail_id=ifset($postdata, 'details', 0);
        $imprintdetails=$postdata['imprintsession'];
        $imprint_details=usersession($imprintdetails);

        if (empty($imprint_details)) {
            echo $this->restore_orderdata_error;
            die();
        }
        $this->load->model('leadquote_model');
        $res=$this->leadquote_model->get_repeat_note($imprint_details, $detail_id, $imprintdetails);
        if ($res['result']==$this->error_result) {
            echo $res['msg'];
        } else {
            $note=$res['repeat_note'];
            $content=$this->load->view('leadpopup/repeat_note_edit', array('repeat_note'=>$note),TRUE);
            echo $content;
        }
        return TRUE;
    }

    // Save Imprint details, repeat note
    public function repeatnote_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession','unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession','unkn');
                $imprint_details=usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $this->load->model('leadquote_model');
                    $res=$this->leadquote_model->save_repeat_note($imprint_details, $postdata, $imprintsession_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Imprint details
    public function quoteprintdetails_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession', 'unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession','unkn');
                $imprint_details = usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $res = $this->leadquote_model->change_imprint_details($imprint_details, $postdata, $imprintsession_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mdata['fldname']=$res['fldname'];
                        $mdata['details']=$res['details'];
                        $mdata['newval']=$res['newval'];
                        if ($mdata['fldname']=='imprint_type') {
                            if ($mdata['newval']=='NEW') {
                                $mdata['setup'] =  number_format($res['setup'],2,'.','');
                            } else {
                                $mdata['class']=$res['class'];
                            }
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Imprint Details
    public function save_imprintdetails() {
        if ($this->isAjax()) {
            $mdata = [];
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession','unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession', 'unkn');
                $imprint_details = usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $res = $this->leadquote_model->save_imprint_details($imprint_details, $imprintsession_id, $quotesession, $quotesession_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        // Start
                        $this->leadquote_model->calc_quote_totals($quotesession_id);
                        $quotesession = usersession($quotesession_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                        $mdata['tax']=number_format(floatval($quote['sales_tax']),2,'.','');

                        $item_content='';
                        $quote_item = $res['item'];
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => 1,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit' => 1,
                            'item_id' => $quote_item['item_id'],
                        ];
                        $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        $mdata['itemcontent'] = $item_content;
                        $mdata['item_id'] = $quote_item['quote_item_id'];
                        // End
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function removeitem() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->removeitem($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $this->leadquote_model->calc_quote_shipping($session_id);
                    $this->leadquote_model->calc_quote_totals($session_id);
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $shippings = $quotesession['shipping'];
                    $lead_time = '';
                    if (!empty($quote['lead_time'])) {
                        $lead_times = json_decode($quote['lead_time'], true);
                        $timeoptions = [
                            'lead_times' => $lead_times,
                            'edit_mode' => 1,
                        ];
                        $lead_time = $this->load->view('leadpopup/quote_leadtime_edit', $timeoptions, TRUE);
                    }
                    // Shipping view
                    $shiprates = '';
                    if (count($shippings) > 0) {
                        $shipoptions = [
                            'edit_mode' => 1,
                            'shippings' => $shippings,
                        ];
                        $shiprates = $this->load->view('leadpopup/quote_shiprates_view', $shipoptions, TRUE);
                    }
                    $mdata['leadtime'] = $lead_time;
                    $mdata['shippingview'] = $shiprates;
                    $mdata['tax'] = $quote['sales_tax'];
                    $mdata['shipping_cost'] = $quote['shipping_cost'];
                    $mdata['rush_cost'] = $quote['rush_cost'];
                    $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                    $mdata['total'] = MoneyOutput($quote['quote_total']);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function show_itemsearch() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $error = '';
                $brand = $quotesession['quote']['brand'];
                $this->load->model('orders_model');
                $dboptions=array(
                    'exclude'=>array(-4, -5, -2),
                    'brand' => $brand,
                );
                $items=$this->orders_model->get_item_list($dboptions);
                $options=array(
                    'item_id'=>'',
                    'items_list'=>$items,
                    'quote_item_label'=>'',
                    'quote_items'=>'',
                    'quote_id' => $quotesession['quote']['quote_id'],
                );
                // $mdata['content']=$this->load->view('leadorder/order_itemedit_view', $options, TRUE);
                $mdata['content']=$this->load->view('leadpopup/quote_itemsearch_view', $options, TRUE);
                $mdata['showother']=0;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Add Item
    public function addquoteitem() {
        $mdata=[];
        $error=$this->restore_orderdata_error;
        $postdata=$this->input->post();
        $session_id = ifset($postdata, 'session', 'unkn');
        $quotesession=usersession($session_id);
        if (!empty($quotesession)) {
            $res = $this->leadquote_model->addquoteitem($postdata, $quotesession, $session_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $this->leadquote_model->calc_quote_shipping($session_id);
                $this->leadquote_model->calc_quote_totals($session_id);
                $quotesession = usersession($session_id);
                $quote = $quotesession['quote'];
                $items = $quotesession['items'];
                $shippings = $quotesession['shipping'];
                $items_views = [];
                foreach ($items as $quote_item) {
                    $imprints=$quote_item['imprints'];
                    $imprint_options=[
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'imprints'=>$imprints,
                        'edit_mode' => 1,
                    ];
                    $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                    $item_options=[
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'items'=>$quote_item['items'],
                        'imprintview'=>$imprintview,
                        'edit'=> 1,
                        'item_id'=>$quote_item['item_id'],
                    ];
                    $view=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                    $items_views[] = [
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'view' => $view,
                    ];
                }
                $mdata['item_content'] = $this->load->view('leadpopup/items_content_view', ['data' => $items_views], TRUE);
                $lead_time = '';
                if (!empty($quote['lead_time'])) {
                    $lead_times = json_decode($quote['lead_time'], true);
                    $timeoptions = [
                        'lead_times' => $lead_times,
                        'edit_mode' => 1,
                    ];
                    $lead_time = $this->load->view('leadpopup/quote_leadtime_edit', $timeoptions, TRUE);
                }
                $mdata['leadtime'] = $lead_time;
                // Shipping view
                $shiprates = '';
                if (count($shippings) > 0) {
                    $shipoptions = [
                        'edit_mode' => 1,
                        'shippings' => $shippings,
                    ];
                    $shiprates = $this->load->view('leadpopup/quote_shiprates_view', $shipoptions, TRUE);
                }
                $mdata['shippingview'] = $shiprates;
                $mdata['tax'] = $quote['sales_tax'];
                $mdata['shipping_cost'] = $quote['shipping_cost'];
                $mdata['rush_cost'] = $quote['rush_cost'];
                $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                $mdata['total'] = MoneyOutput($quote['quote_total']);

            }
        }
        $this->ajaxResponse($mdata, $error);
    }

    // Save Quote
    public function quotesave() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $lead_id = ifset($postdata,'lead',0);
                if (!empty($lead_id)) {
                    $res = $this->leadquote_model->savequote($quotesession, $lead_id, $this->USR_ID,  $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $mdata['quote_id'] = $res['quote_id'];
                        $error = '';
                        // Get leads list
                        $qdat = $this->leadquote_model->get_leadquotes($lead_id);
                        $lead_quotes = '';
                        if (count($qdat) > 0) {
                            $lead_quotes = $this->load->view('leadpopup/leadquotes_list_view',array('quotes'=>$qdat),TRUE);
                        }
                        $mdata['quotescontent'] = $lead_quotes;
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteduplicate() {
        if ($this->isAjax()) {
            $mdata= [];
            $error = 'Empty Quote';
            $postdata = $this->input->post();
            $quote_id = ifset($postdata,'quote_id',0);
            if (!empty($quote_id)) {
                $res = $this->leadquote_model->duplicatequote($quote_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $edit_mode = 1;
                    $error = '';
                    // Prepare content
                    $quotedata = $res['quote'];
//                    if ($quotedata['brand']=='SR') {
//                        $templlists = [
//                            'Supplier',
//                            'Proforma Invoice',
//                        ];
//                    } else {
//                        $templlists = [
//                            'Stressballs.com',
//                            'Bluetrack Health',
//                            'Proforma Invoice',
//                        ];
//                    }
                    $templlists = $this->quotetemplates;
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);
                    $shipstate = '';
                    $billstate = '';
                    if (!empty($quotedata['shipping_country'])) {
                        $shipstates = $this->shipping_model->get_country_states($quotedata['shipping_country']);
                        if (is_array($shipstates)) {
                            $stateoptions = [
                                'item' => 'shipping_state',
                                'states' => $shipstates,
                                'edit_mode' => $edit_mode,
                                'data' => $quotedata,
                            ];
                            $shipstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    };
                    if (!empty($quotedata['billing_country'])) {
                        $billstates = $this->shipping_model->get_country_states($quotedata['billing_country']);
                        if (is_array($billstates)) {
                            $stateoptions = [
                                'item' => 'billing_state',
                                'states' => $billstates,
                                'edit_mode' => $edit_mode,
                                'data' => $quotedata,
                            ];
                            $billstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                    };
                    // Tax view
                    if ($quotedata['taxview']==0) {
                        // Empty Tax view
                        $taxview = $this->load->view('leadpopup/quote_taxempty_view',[],TRUE);
                    } else {
                        $taxoptions = [
                            'edit_mode' => $edit_mode,
                            'data' => $quotedata,
                        ];
                        $taxview = $this->load->view('leadpopup/quote_tax_edit', $taxoptions,TRUE);
                    }
                    $quote_items = $res['items'];
                    $shippings = $res['shippings'];
                    $items_views = [];
                    foreach ($quote_items as $quote_item) {
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => $edit_mode,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit'=> $edit_mode,
                            'item_id'=>$quote_item['item_id'],
                        ];
                        if ($edit_mode==0) {
                            $view=$this->load->view('leadpopup/items_data_view', $item_options, TRUE);
                        } else {
                            $view=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        }
                        $items_views[] = [
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'view' => $view,
                        ];
                    }
                    $item_content = $this->load->view('leadpopup/items_content_view', ['data' => $items_views], TRUE);
                    // Shipping view
                    $shiprates = '';
                    if (count($shippings) > 0) {
                        $shipoptions = [
                            'edit_mode' => $edit_mode,
                            'shippings' => $shippings,
                        ];
                        $shiprates = $this->load->view('leadpopup/quote_shiprates_view', $shipoptions, TRUE);
                    }
                    $quote_session = 'quote'.uniq_link(15);
                    $sessiondata = [
                        'quote' => $quotedata,
                        'items' => $quote_items,
                        'shipping' => $shippings,
                        'deleted' => [],
                    ];
                    usersession($quote_session, $sessiondata);
                    $lead_time = '';
                    if (!empty($quotedata['lead_time'])) {
                        $lead_times = json_decode($quotedata['lead_time'], true);
                        $timeoptions = [
                            'lead_times' => $lead_times,
                            'edit_mode' => $edit_mode,
                        ];
                        $lead_time = $this->load->view('leadpopup/quote_leadtime_edit', $timeoptions, TRUE);
                    }
                    $options = [
                        'quote_session' => $quote_session,
                        'quote_id' => $quote_id,
                        'lead_id' => $quotedata['lead_id'],
                        'data' => $quotedata,
                        'itemsview' => $item_content,
                        'templlists' => $templlists,
                        'countries' => $countries,
                        'edit_mode' => $edit_mode,
                        'shipstate' => $shipstate,
                        'billstate' => $billstate,
                        'shiprates' => $shiprates,
                        'lead_time' => $lead_time,
                        'taxview' => $taxview,
                    ];
                    $mdata['quotecontent'] = $this->load->view('leadpopup/quotedata_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quotepdfdoc() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = 'Empty Quote #';
            $mdata = [];
            $quote_id = ifset($postdata, 'quote_id', 0);
            if (!empty($quote_id)) {
                $res = $this->leadquote_model->prepare_quotedoc($quote_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['docurl'] = $res['docurl'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteaddorder() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $error = 'Empty Quote';
            $quote_id = ifset($postdata, 'quote_id', 0);
            if (!empty($quote_id)) {
                $res = $this->leadquote_model->addneworder($quote_id, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // Prepare content
                    $leadsession='leadorder'.uniq_link(15);
                    // Generate new session
                    $options=array();
                    $options['current_page']=ifset($postdata,'page', 'art_tasks');
                    $options['leadsession']=$leadsession;
                    $orddata=$res['order'];

                    $options['order_id'] = 0;
                    $options['order_head'] = $this->load->view('leadorderdetails/head_order_view', $orddata, TRUE);
                    $data = $this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, 1);
                    $order_data = $this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                    $options['order_data'] = $order_data;
                    $options['leadsession'] = $leadsession;
                    $content = $this->load->view('leadorderdetails/placeorder_menu_edit', $options, TRUE);
                    // $mdata['content']=$content;
                    $head_options = [
                        'order_head' => $this->load->view('leadorderdetails/head_placeorder_edit', $orddata, TRUE),
                        'prvorder' => 0,
                        'nxtorder' => 0,
                        'order_id' => 0,
                    ];
                    $header = $this->load->view('leadorderdetails/head_edit', $head_options, TRUE);
                    $locking = '';
                    if ($res['order_system_type']=='old') {
                        $leadorder=array(
                            'order'=>$orddata,
                            'payments'=>$res['payments'],
                            'artwork'=>$res['artwork'],
                            'artlocations'=>$res['artlocations'],
                            'artproofs'=>$res['proofdocs'],
                            'message'=>$res['message'],
                            'order_system'=>$res['order_system_type'],
                            'locrecid'=>$locking,
                        );
                    } else {
                        $leadorder=array(
                            'order'=>$orddata,
                            'payments'=>$res['payments'],
                            'artwork'=>$res['artwork'],
                            'artlocations'=>$res['artlocations'],
                            'artproofs'=>$res['proofdocs'],
                            'message'=>$res['message'],
                            'contacts'=>$res['contacts'],
                            'order_items'=>$res['order_items'],
                            'order_system'=>$res['order_system_type'],
                            'shipping'=>$res['shipping'],
                            'shipping_address'=>$res['shipping_address'],
                            'billing'=>$res['order_billing'],
                            'charges'=>$res['charges'],
                            'delrecords'=>array(),
                            'locrecid'=>$locking,
                        );
                    }
                    usersession($leadsession, $leadorder);
                    $mdata['content']=$content;
                    $mdata['header']=$header;

                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quotepreparesend() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $error = 'Empty Lead #';
            if (ifset($postdata,'quote_id',0) > 0) {
                $res = $this->leadquote_model->prepare_emailmessage($postdata['quote_id']);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options = [
                        'from' => $res['brand']=='SR' ? $this->config->item('customer_notification_relievers') : $this->config->item('customer_notification_sender') ,
                        'to' => $res['email'],
                        'subject' => $res['subject'],
                        'message' => $res['message'],
                        'quote_id' => $postdata['quote_id'],
                    ];
                    $mdata['content'] = $this->load->view('leadpopup/quote_senddoc_view', $options,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quotesend() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $res = $this->leadquote_model->send_emailmessage($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}