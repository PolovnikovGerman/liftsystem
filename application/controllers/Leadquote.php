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
                    $mdata['newitem'] = $qres['newitem'];
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
                    $shipcode = '';
                    $bilcode = '';
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
                        $cntdat = $this->shipping_model->get_country($quotedata['shipping_country']);
                        $shipcode = strtolower(ifset($cntdat,'country_iso_code_2',''));
                    };
                    if (!empty($quotedata['billing_country'])) {
                        $billstates = $this->shipping_model->get_country_states($quotedata['billing_country']);
                        if (is_array($billstates)) {
                            $stateoptions = [
                                'item' => 'billing_state',
                                'states' => $billstates,
                                'edit_mode' => 0,
                                'data' => $quotedata,
                            ];
                            $billstate = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                        }
                        $cntdat = $this->shipping_model->get_country($quotedata['billing_country']);
                        $bilcode = strtolower(ifset($cntdat,'country_iso_code_2',''));
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
                        if (empty($quote_item['item_id'])) {
                            $this->load->model('orders_model');
                            $dboptions=array(
                                'exclude'=>array(-4, -5, -2),
                                'brand' => ($quotedata['brand']=='SR') ? 'SR' : 'BT',
                            );
                            $item_options['itemslist']=$this->orders_model->get_item_list($dboptions);
                            $item_view=$this->load->view('leadpopup/items_data_add', $item_options, TRUE);
                        } else {
                            $item_subtotal+=$quote_item['item_subtotal'];
                            $item_view=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        }

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
                    $shipratesview = '';
                    if (!empty($quotedata['shipping_zip'])) {
                        $this->leadquote_model->calc_quote_shipping($quote_session);
                        $this->leadquote_model->calc_quote_totals($quote_session);
                        $sessiondata = usersession($quote_session);
                        $quotedata = $sessiondata['quote'];
                        $quote_items = $sessiondata['items'];
                        $shippings = $sessiondata['shipping'];
                        if (count($shippings) > 0) {
                            $shipoptions = [
                                'edit_mode' => 1,
                                'shippings' => $shippings,
                            ];
                            $shipratesview = $this->load->view('leadpopup/quote_shiprates_view', $shipoptions, TRUE);
                        }
                        if (!empty($quotedata['shipping_state']) && $quotedata['taxview']==1) {
                            $taxoptions = [
                                'edit_mode' => 1,
                                'data' => $quotedata,
                            ];
                            $taxview = $this->load->view('leadpopup/quote_tax_edit', $taxoptions,TRUE);
                        }
                    }

                    $lead_time = '';
                    if (!empty($quotedata['lead_time'])) {
                        $lead_times = json_decode($quotedata['lead_time'], true);
                        $timeoptions = [
                            'lead_times' => $lead_times,
                            'edit_mode' => 1,
                        ];
                        $lead_time = $this->load->view('leadpopup/quote_leadtime_edit', $timeoptions, TRUE);
                    }
                    $mapuse = (empty($this->config->item('google_map_key')) ? 0 : 1);
                    $billaddress = $this->_prepare_billaddress($quotedata);
                    $shipaddress = $this->_prepare_shipaddress($quotedata);
                    $options = [
                        'quote_session' => $quote_session,
                        'quote_id' => $quotedata['quote_id'],
                        'lead_id' => $lead_id,
                        'data' => $quotedata,
                        'itemsview' => $item_content,
                        'templlists' => $templlists,
                        'countries' => $countries,
                        'edit_mode' => 1,
                        'shiprates' => $shipratesview,
                        'lead_time' => $lead_time,
                        'shipstate' => $shipstate,
                        'billstate' => $billstate,
                        'taxview' => $taxview,
                        'shipcode' => $shipcode,
                        'bilcode' => $bilcode,
                        'mapuse' => $mapuse,
                        'billaddress' => $billaddress,
                        'shipaddress' => $shipaddress,

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
                    $templlists = $this->quotetemplates;
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);
                    $shipstate = '';
                    $billstate = '';
                    $shipcode = '';
                    $bilcode = '';
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
                        $cntdat = $this->shipping_model->get_country($quotedata['shipping_country']);
                        $shipcode = strtolower(ifset($cntdat,'country_iso_code_2',''));
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
                        $cntdat = $this->shipping_model->get_country($quotedata['billing_country']);
                        $bilcode = strtolower(ifset($cntdat,'country_iso_code_2',''));
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
                    $mapuse = (empty($this->config->item('google_map_key')) ? 0 : 1);
                    $billaddress = $this->_prepare_billaddress($quotedata);
                    $shipaddress = $this->_prepare_shipaddress($quotedata);
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
                        'shipcode' => $shipcode,
                        'bilcode' => $bilcode,
                        'mapuse' => $mapuse,
                        'billaddress' => $billaddress,
                        'shipaddress' => $shipaddress,
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
                    $mdata['shipcountry'] = $res['shipcountry'];
                    if ($res['shipcountry']==1) {
                        // Change ship address line 1
                        $mdata['countrycode'] = $res['countrycode'];
                        $mapuse = (empty($this->config->item('google_map_key')) ? 0 : 1);
                        $mdata['address_view'] = $this->load->view('leadpopup/shipaddr_edit_view',['data' => $quote, 'mapuse' => $mapuse,],TRUE);
                    }
                    $mdata['bilcountry'] = $res['bilcountry'];
                    if ($res['bilcountry']==1) {
                        $mdata['countrycode'] = $res['countrycode'];
                        $mdata['address_view'] = $this->load->view('leadpopup/billaddr_edit_view',['data' => $quote],TRUE);
                    }
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
                    $mdata['shipaddress'] = $this->_prepare_shipaddress($quote);
                    $mdata['billaddress'] = $this->_prepare_billaddress($quote);
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

    public function inventoryitemcolor()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $quoteitem_id = ifset($postdata, 'item',0);
                $itemstatus = ifset($postdata, 'itemstatus',0);
                if (empty($quoteitem_id)) {
                    $error = 'Select Order Item';
                } else {
                    $res = $this->leadquote_model->quoteiteminventory($quotesession, $quoteitem_id, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $options = [
                            'onboats' => $res['onboats'],
                            'invents' => $res['invents'],
                            'itemstatus' => $itemstatus,
                        ];
                        $mdata['content'] = $this->load->view('leadpopup/itemcolor_inventory_view', $options, TRUE);
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
                        'custom' => ($item_id==$this->config->item('custom_id') || $item_id==$this->config->item('other_id')) ? 1 : 0,
                    );
                    $mdata['content']=  $this->load->view('leadpopup/imprint_details_edit', $options, TRUE);

                    $imprintdetails=array(
                        'imprint_details' => $details,
                        'quote_blank' => $quote_blank,
                        'quote_item_id' => $postdata['item'],
                        'item_id' => $item_id,
                        'brand' => $quote['brand'],
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
                            // if ($mdata['newval']=='NEW') {
                                $mdata['setup'] =  number_format($res['setup'],2,'.','');
                            // } else {
                                $mdata['class']=$res['class'];
                            // }
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteprintdetails_blankquote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession', 'unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession', 'unkn');
                $imprint_details = usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $res = $this->leadquote_model->change_imprint_details($imprint_details, $postdata, $imprintsession_id);
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
                        $quotesession = usersession($quotesession_id);
                        $item_content='';
                        $quote_items = $quotesession['items']; //$res['item'];
                        $quote = $quotesession['quote'];
                        foreach ($quote_items as $quote_item) {
                            $imprints = $quote_item['imprints'];
                            $imprint_options = [
                                'quote_item_id' => $quote_item['quote_item_id'],
                                'imprints' => $imprints,
                                'edit_mode' => 1,
                            ];
                            $imprintview = $this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                            $item_options = [
                                'quote_item_id' => $quote_item['quote_item_id'],
                                'items' => $quote_item['items'],
                                'imprintview' => $imprintview,
                                'edit' => 1,
                                'item_id' => $quote_item['item_id'],
                            ];
                            if (empty($quote_item['item_id'])) {
                                $this->load->model('orders_model');
                                $dboptions = array(
                                    'exclude' => array(-4, -5, -2),
                                    'brand' => ($quote['brand'] == 'SR') ? 'SR' : 'BT',
                                );
                                $item_options['itemslist'] = $this->orders_model->get_item_list($dboptions);
                                $item_view = $this->load->view('leadpopup/items_data_add', $item_options, TRUE);
                            } else {
                                // $item_subtotal+=$quote_item['item_subtotal'];
                                $item_view = $this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                            }
                            $item_content.= $item_view;
                        }
                        $mdata['itemcontent'] = $item_content;
                        $mdata['shiprebuild'] = $res['shiprebuild'];
                        if ($res['shiprebuild']==1) {
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
                        }
                        $mdata['calcship'] = $res['calcship'];
                        if ($res['calcship']==1) {
                            $this->leadquote_model->calc_quote_shipping($quotesession_id);
                            // Show shipping costs
                            $quotesession = usersession($quotesession_id);
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
                            $this->leadquote_model->calc_quote_totals($quotesession_id);
                            $quotesession = usersession($quotesession_id);
                            $quote = $quotesession['quote'];
                            $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                            $mdata['total'] = MoneyOutput($quote['quote_total']);
                            $mdata['tax'] = $quote['sales_tax'];
                        }
                        $mdata['quote_repcontact'] = $quote['quote_repcontact'];
                        $mdata['mischrg_label1'] = $quote['mischrg_label1'];
                        $mdata['mischrg_value1'] = $quote['mischrg_value1'];
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
                $res = $this->leadquote_model->removeitem($postdata, $quotesession, $session_id, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $this->leadquote_model->calc_quote_shipping($session_id);
                    $this->leadquote_model->calc_quote_totals($session_id);
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $quote_items = $quotesession['items'];
                    $mdata['newitem'] = 0;
                    $item_content = '';
                    foreach ($quote_items as $quote_item) {
                        $imprints = $quote_item['imprints'];
                        $imprint_options = [
                            'quote_item_id' => $quote_item['quote_item_id'],
                            'imprints' => $imprints,
                            'edit_mode' => 1,
                        ];
                        $imprintview = $this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options = [
                            'quote_item_id' => $quote_item['quote_item_id'],
                            'items' => $quote_item['items'],
                            'imprintview' => $imprintview,
                            'edit' => 1,
                            'item_id' => $quote_item['item_id'],
                        ];
                        if (empty($quote_item['item_id'])) {
                            $this->load->model('orders_model');
                            $dboptions = array(
                                'exclude' => array(-4, -5, -2),
                                'brand' => ($quote['brand'] == 'SR') ? 'SR' : 'BT',
                            );
                            $item_options['itemslist'] = $this->orders_model->get_item_list($dboptions);
                            $item_view = $this->load->view('leadpopup/items_data_add', $item_options, TRUE);
                            $mdata['newitem'] = 1;
                        } else {
                            $item_view = $this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        }
                        $item_content.= $item_view;
                    }
                    $mdata['itemcontent'] = $item_content;
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
                    //
                    $mdata['quote_repcontact'] = $quote['quote_repcontact'];
                    $mdata['mischrg_label1'] = $quote['mischrg_label1'];
                    $mdata['mischrg_value1'] = $quote['mischrg_value1'];

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
                $itemsview = '';
                $quotesession=usersession($session_id);
                $quote_items = $quotesession['items'];
                $quotedata = $quotesession['quote'];
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
                    if (empty($quote_item['item_id'])) {
                        $this->load->model('orders_model');
                        $dboptions=array(
                            'exclude'=>array(-4, -5, -2),
                            'brand' => ($quotedata['brand']=='SR') ? 'SR' : 'BT',
                        );
                        $item_options['itemslist']=$this->orders_model->get_item_list($dboptions);
                        $item_view=$this->load->view('leadpopup/items_data_add', $item_options, TRUE);
                    } else {
                        $item_view=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                    }
                    $itemsview.=$item_view;
                }
                $mdata['item_content'] = $itemsview;
            }
        }
        $this->ajaxResponse($mdata, $error);
    }

    // Same billing address
    public function billingsame() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $res = $this->leadquote_model->billingsame($quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['billingsame'] = $res['billingsame'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Update autofill address
    public function update_autoaddress() {
        $postdata = $this->input->post();
        $error = $this->restore_orderdata_error;
        $session_id = ifset($postdata,'session','unknw');
        $quotesession = usersession($session_id);
        $mdata = [];
        if (!empty($quotesession)) {
            $res = $this->leadquote_model->update_autoaddress($postdata, $quotesession, $session_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $quotesession = usersession($session_id);
                $quote = $quotesession['quote'];
                if ($postdata['address_type']=='billing') {
                    $mdata['country'] = $quote['billing_country'];
                    $mdata['address_1'] = $quote['billing_address1'];
                    $mdata['city'] = $quote['billing_city'];
                    $mdata['state'] = $quote['billing_state'];
                    $mdata['zip'] = $quote['billing_zip'];
                    $mdata['billaddress'] = $this->_prepare_billaddress($quote);
                } else {
                    $mdata['country'] = $quote['shipping_country'];
                    $mdata['address_1'] = $quote['shipping_address1'];
                    $mdata['city'] = $quote['shipping_city'];
                    $mdata['state'] = $quote['shipping_state'];
                    $mdata['zip'] = $quote['shipping_zip'];
                    $mdata['shipaddress'] = $this->_prepare_shipaddress($quote);
                }
                $mdata['bilstate'] = $res['bilstate'];
                $mdata['shipstate'] = $res['shipstate'];
                if ($res['bilstate']==1) {
                    $states = $res['states'];
                    $stateoptions = [
                        'item' => 'billing_state',
                        'states' => $states,
                        'edit_mode' => 1,
                        'data' => $quote,
                    ];
                    $mdata['stateview'] = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                }
                if ($res['shipstate']==1) {
                    $states = $res['states'];
                    $stateoptions = [
                        'item' => 'shipping_state',
                        'states' => $states,
                        'edit_mode' => 1,
                        'data' => $quote,
                    ];
                    $mdata['stateview'] = $this->load->view('leadpopup/quote_states_view', $stateoptions, TRUE);
                }
                $mdata['ship'] = $res['ship'];
                if ($res['ship']==1) {
                    $this->leadquote_model->calc_quote_shipping($session_id);
                    $this->leadquote_model->calc_quote_totals($session_id);
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    if ($quote['taxview']==0) {
                        $mdata['taxview'] = $this->load->view('leadpopup/quote_taxempty_view',[],TRUE);
                    } else {
                        $taxoptions = [
                            'edit_mode' => 1,
                            'data' => $quote,
                        ];
                        $mdata['taxview'] = $this->load->view('leadpopup/quote_tax_edit', $taxoptions,TRUE);
                    }
                    $mdata['total'] = MoneyOutput($quote['quote_total']);
                    $mdata['tax'] = $quote['sales_tax'];
                    $mdata['shipping_cost'] = $quote['shipping_cost'];
                    // Ship options
                    $shipping = $quotesession['shipping'];
                    $options = [
                        'shippings' => $shipping,
                        'edit_mode' => 1,
                    ];
                    $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);
                }
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
                        // Get data, save to new session
                        $qres=$this->leadquote_model->get_leadquote($res['quote_id'], 1);
                        $error = $qres['msg'];
                        if ($qres['result']==$this->success_result) {
                            $quote_session = 'quote' . uniq_link(15);
                            $quotedata = $qres['quote'];
                            $quote_items = $qres['items'];
                            $shippings = $qres['shippings'];
                            $sessiondata = [
                                'quote' => $quotedata,
                                'items' => $quote_items,
                                'shipping' => $shippings,
                                'deleted' => [],
                            ];
                            usersession($quote_session, $sessiondata);
                            $mdata['session_id'] = $quote_session;
                            $error = '';
                            // Get leads list
                            $qdat = $this->leadquote_model->get_leadquotes($lead_id);
                            $lead_quotes = '';
                            if (count($qdat) > 0) {
                                $lead_quotes = $this->load->view('leadpopup/leadquotes_list_view', array('quotes' => $qdat), TRUE);
                            }
                            $mdata['quotescontent'] = $lead_quotes;
                        }
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
                    $templlists = $this->quotetemplates;
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);
                    $shipstate = '';
                    $billstate = '';
                    $shipcode = '';
                    $bilcode = '';
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
                        $cntdat = $this->shipping_model->get_country($quotedata['shipping_country']);
                        $shipcode = strtolower(ifset($cntdat,'country_iso_code_2',''));
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
                        $cntdat = $this->shipping_model->get_country($quotedata['billing_country']);
                        $bilcode = strtolower(ifset($cntdat,'country_iso_code_2',''));
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
                    $mapuse = (empty($this->config->item('google_map_key')) ? 0 : 1);
                    $billaddress = $this->_prepare_billaddress($quotedata);
                    $shipaddress = $this->_prepare_shipaddress($quotedata);
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
                        'shipcode' => $shipcode,
                        'bilcode' => $bilcode,
                        'mapuse' => $mapuse,
                        'billaddress' => $billaddress,
                        'shipaddress' => $shipaddress,
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
                    $data = $this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT,1);
                    $order_data = $this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                    $options['order_data'] = $order_data;
                    $options['leadsession'] = $leadsession;
                    $options['mapuse'] = empty($this->config->item('google_map_key')) ? 0 : 1;
                    if (!empty($res['item_error'])) {
                        $options['item_error'] = $res['item_error'];
                        $options['item_error_msg'] = $res['item_error_msg'];
                    }
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
                            'claydocs' => $res['claydocs'],
                            'previewdocs' => $res['previewdocs'],
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
                            'claydocs' => $res['claydocs'],
                            'previewdocs' => $res['previewdocs'],
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
            $res = $this->leadquote_model->send_emailmessage($postdata, $this->USR_ID);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $this->load->model('leads_model');
                $lead_history=$this->leads_model->get_lead_history($res['lead_id']);
                $mdata['history']=$this->load->view('leadpopup/history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_imprint_details($quotesession, $postdata, $session_id, $itemstatus='old') {
        $out = ['result' => $this->error_result, 'msg' => 'Unknown Error'];
        $res = $this->leadquote_model->prepare_print_details($quotesession, $postdata, $session_id);
        $out['msg'] = $res['msg'];
        if ($res['result']==$this->success_result) {
            $out['result'] = $this->success_result;
            $details = $res['imprint_details'];
            $quote = $res['quote'];
            $quote_blank = $quote['quote_blank'];
            $item_id = $res['item_id'];
            // Prepare View
            $imptintid = 'imprintdetails' . uniq_link(15);
            $options = array(
                'details' => $details,
                'item_number' => $res['item_number'],
                'quote_blank' => $quote_blank,
                'imprints' => $res['imprints'],
                'numlocs' => count($res['imprints']),
                'item_name' => $res['item_name'],
                'imprintsession' => $imptintid,
                'custom' => ($item_id == $this->config->item('custom_id') || $item_id == $this->config->item('other_id')) ? 1 : 0,
            );
            $out['content'] = $this->load->view('leadpopup/imprint_details_edit', $options, TRUE);
            $imprintdetails = array(
                'imprint_details' => $details,
                'quote_blank' => $quote_blank,
                'quote_item_id' => $postdata['item'],
                'item_id' => $item_id,
                'itemstatus' => $itemstatus,
                'brand' => $quote['brand'],
            );
            usersession($imptintid, $imprintdetails);
        }
        return $out;
    }

    private function _prepare_billaddress($quotedata) {
        $billaddress = '';
        if (!empty($quotedata['billing_contact'])) {$billaddress.=$quotedata['billing_contact'].PHP_EOL;}
        if (!empty($quotedata['billing_company'])) {$billaddress.=$quotedata['billing_company'].PHP_EOL;}
        if (!empty($quotedata['billing_address1'])) {$billaddress.=$quotedata['billing_address1'].PHP_EOL;}
        if (!empty($quotedata['billing_address2'])) {$billaddress.=$quotedata['billing_address2'].PHP_EOL;}
        $adrline = 0;
        if (!empty($quotedata['billing_city'])) {$billaddress.=$quotedata['billing_city'].', '; $adrline = 1;}
        if (!empty($quotedata['billing_state'])) {$billaddress.=$quotedata['billing_state'].' ';$adrline = 1;}
        if (!empty($quotedata['billing_zip'])) {$billaddress.=$quotedata['billing_zip'];$adrline = 1;}
        if ($adrline ==1) {
            $billaddress.=PHP_EOL;
        }
        return $billaddress;
    }

    private function _prepare_shipaddress($quotedata) {
        $shipaddress = '';
        if (!empty($quotedata['shipping_contact'])) {$shipaddress.=$quotedata['shipping_contact'].PHP_EOL;}
        if (!empty($quotedata['shipping_company'])) {$shipaddress.=$quotedata['shipping_company'].PHP_EOL;}
        if (!empty($quotedata['shipping_address1'])) {$shipaddress.=$quotedata['shipping_address1'].PHP_EOL;}
        if (!empty($quotedata['shipping_address2'])) {$shipaddress.=$quotedata['shipping_address2'].PHP_EOL;}
        $adrline = 0;
        if (!empty($quotedata['shipping_city'])) {$shipaddress.=$quotedata['shipping_city'].', '; $adrline = 1;}
        if (!empty($quotedata['shipping_state'])) {$shipaddress.=$quotedata['shipping_state'].' ';$adrline = 1;}
        if (!empty($quotedata['shipping_zip'])) {$shipaddress.=$quotedata['shipping_zip'];$adrline = 1;}
        if ($adrline ==1) {
            $shipaddress.=PHP_EOL;
        }
        return $shipaddress;
    }

    public function savenewquoteitem()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'quotesession','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $quote_item_id = ifset($postdata,'quoteitem_id',0);
                $item_id = ifset($postdata, 'item_id',0);
                if (empty($item_id)) {
                    $error = 'Select Item';
                } elseif (empty($quote_item_id)) {
                    $error = 'Select Quote Item';
                } else {
                    $res = $this->leadquote_model->savenewquoteitem($quotesession, $item_id, $quote_item_id, $session_id, $this->USR_ID);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $item = $res['item'];
                        // Prepare out
                        $special = 0;
                        if ($item['item_id']<0) {
                            $special = 1;
                        }
                        $mdata['special'] = $special;

                        $options = [
                            'quote_item_id' => $quote_item_id,
                            'item_id' => $item_id,
                            'item_color' => $item['items'][0]['item_color'],
                            'colors' => $item['items'][0]['colors'],
                            'qty' => $item['item_qty'],
                            'price' => $item['base_price'],
                        ];
                        if ($special==0) {
                            $mdata['outcolors'] = $item['items'][0]['out_colors'];
                        } else {
                            $mdata['outcolors'] = '&nbsp;';
                        }
                        $mdata['qty'] = $this->load->view('leadpopup/additem_qty_view', $options, TRUE); // $item['item_qty']
                        $mdata['price'] = $this->load->view('leadpopup/additem_price_view', $options, TRUE); // $item['base_price']
                        $mdata['subtotal'] = MoneyOutput($item['item_subtotal']);
                        $mdata['brand'] = $res['brand'];

                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventoryitem()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'quotesession','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $quoteitem_id = ifset($postdata, 'quoteitem_id',0);
                $itemstatus = ifset($postdata, 'itemstatus',0);
                if (empty($quoteitem_id)) {
                    $error = 'Select Order Item';
                } else {
                    $res = $this->leadquote_model->quoteiteminventory($quotesession, $quoteitem_id, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $options = [
                            'onboats' => $res['onboats'],
                            'invents' => $res['invents'],
                            'itemstatus' => $itemstatus,
                        ];
                        $mdata['content'] = $this->load->view('leadpopup/itemcolor_inventory_view', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function savenewitemparam()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata=$this->input->post();
            $session_id = ifset($postdata,'quotesession','unknw');
            $quotesession = usersession($session_id);
            if (empty($quotesession)) {
                $error=$this->restore_orderdata_error;
            } else {
                $quoteitem_id = ifset($postdata, 'quoteitem_id',0);
                $paramname = ifset($postdata,'paramname','');
                $newval = ifset($postdata, 'newval', '');
                if (empty($quoteitem_id)) {
                    $error = 'Select Quote Item';
                } elseif (empty($paramname)) {
                    $error = 'Empty Parameter';
                } else {
                    $res = $this->leadquote_model->savenewitemparam($quotesession, $quoteitem_id, $paramname, $newval, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $options = [
                            'quote_item_id' => $quoteitem_id,
                            'item_id' => $res['item_id'],
                            'item_color' => $res['color'],
                            'colors' => $res['colors'],
                            'qty' => $res['item_qty'],
                            'price' => $res['base_price'],
                        ];
                        $mdata['outcolors'] = $res['color'];
                        $mdata['qty'] = $this->load->view('leadpopup/additem_qty_view', $options, TRUE); // $item['item_qty']
                        $mdata['price'] = $this->load->view('leadpopup/additem_price_view', $options, TRUE); // $item['base_price']
                        $mdata['subtotal'] = MoneyOutput($res['item_subtotal']);
                        $mdata['brand'] = $res['brand'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function newquoteimprints()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'quotesession', 'unknw');
            $quotesession = usersession($session_id);
            if (empty($quotesession)) {
                $error = $this->restore_orderdata_error;
            } else {
                $quoteitem_id = ifset($postdata, 'item', 0);
                if (empty($quoteitem_id)) {
                    $error = 'Select Quote Item';
                } else {
                    $res = $this->leadquote_model->newitemimprint($quotesession, $quoteitem_id, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        // Re init session variables
                        $quotesession = usersession($session_id);
                        $imprdata = $this->_prepare_imprint_details($quotesession, $postdata, $session_id, 'new');
                        $error = $imprdata['msg'];
                        if ($imprdata['result']==$this->success_result) {
                            $error = '';
                            $mdata['imprintview'] = $imprdata['content'];
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function cancelnewitem()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'quotesession', 'unknw');
            $quotesession = usersession($session_id);
            if (empty($quotesession)) {
                $error = $this->restore_orderdata_error;
            } else {
                $quoteitem_id = ifset($postdata,'quoteitem_id',0);
                if (empty($quoteitem_id)) {
                    $error = 'Select Quote Item';
                } else {
                    $res = $this->leadquote_model->cancelnewitem($quotesession, $quoteitem_id, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mdata['newitem'] = 0;
                        $quote_items = $res['items'];
                        $quote = $res['quote'];
                        $item_content = '';
                        foreach ($quote_items as $quote_item) {
                            $imprints = $quote_item['imprints'];
                            $imprint_options = [
                                'quote_item_id' => $quote_item['quote_item_id'],
                                'imprints' => $imprints,
                                'edit_mode' => 1,
                            ];
                            $imprintview = $this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                            $item_options = [
                                'quote_item_id' => $quote_item['quote_item_id'],
                                'items' => $quote_item['items'],
                                'imprintview' => $imprintview,
                                'edit' => 1,
                                'item_id' => $quote_item['item_id'],
                            ];
                            if (empty($quote_item['item_id'])) {
                                $this->load->model('orders_model');
                                $dboptions = array(
                                    'exclude' => array(-4, -5, -2),
                                    'brand' => ($quote['brand'] == 'SR') ? 'SR' : 'BT',
                                );
                                $item_options['itemslist'] = $this->orders_model->get_item_list($dboptions);
                                $item_view = $this->load->view('leadpopup/items_data_add', $item_options, TRUE);
                                $mdata['newitem'] = 1;
                            } else {
                                // $item_subtotal+=$quote_item['item_subtotal'];
                                $item_view = $this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                            }
                            $item_content.= $item_view;
                        }
                        $mdata['itemcontent'] = $item_content;
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}