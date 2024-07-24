<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template
{

    var $CI;
    var $config;
    var $template;
    var $master;
    var $regions = array(
        '_scripts' => array(),
        '_styles' => array(),
        '_title' => ''
    );
    var $output;
    var $js = array();
    var $css = array();
    var $parser = 'parser';
    var $parser_method = 'parse';
    var $parse_template = FALSE;
    private $error_result = 0;
    private $success_result = 1;

    function __construct() {
        $this->CI=&get_instance();
    }

    public function prepare_public_page($options) {
        $dat = array();
        $styles=[];
        if (isset($options['styles'])) {
            $styles=$options['styles'];
        }
        if ($_SERVER['SERVER_NAME']=='lifttest.stressballs.com') {
            $styles[]=array('style'=>'/css/page_view/testsite_view.css');
        }
        $scripts=[];
        if (isset($options['scripts'])) {
            $scripts=$options['scripts'];
        }

        $pagetitle = (isset($options['title']) ? $options['title'] : 'System');

        $head_options=[
            'styles'=>$styles,
            'scripts'=>$scripts,
            'title' => $pagetitle,
        ];


        $dat['head'] = $this->CI->load->view('public_pages/head_view', $head_options, TRUE);

        return $dat;

    }

    public function prepare_pagecontent($options=[]) {
        $dat = array();
        $this->CI->load->model('dashboard_model');
        $this->CI->load->model('menuitems_model');
        $this->CI->load->model('user_model');
        $total_view = '';
        $userchk = $this->CI->user_model->current_user();
        if ($userchk['result']==1) {
            $userdat = $userchk['data'];
            if ($userdat['user_logged_in']=='masteradmin') {
                $total_options = $this->CI->dashboard_model->get_totals('week');
                $total_view = $this->CI->load->view('page/dashboard_total_view', $total_options, TRUE);
            }
        }
        $brands = $this->CI->menuitems_model->get_userbrands($userdat['id']);
        $styles=[];
        if (isset($options['styles'])) {
            $styles=$options['styles'];
        }
        if ($this->CI->config->item('test_server')) {
            $styles[]=array('style'=>'/css/page_view/testsite_view.css');
        }
        $scripts=[];
        if (isset($options['scripts'])) {
            $scripts=$options['scripts'];
        }
        $brand  = $this->CI->menuitems_model->get_current_brand();
        // Build left menu
//        $menu_options = [
//            'activelnk'=>(isset($options['activelnk']) ? $options['activelnk'] : ''),
//            'permissions' => $this->CI->menuitems_model->get_user_permissions($options['user_id'], $brand),
//        ];
        // $menu_view = $this->CI->load->view('page/menu_new_view', $menu_options, TRUE);
        // Mobile menu
        $mobpermissions = $this->CI->menuitems_model->get_user_mobpermissions($options['user_id']);
        // Admin and Alerts
        $admin_permission = 0;
        $adminchk = $this->CI->menuitems_model->get_menuitem('/admin');
        $admin_old = 1;
        if ($adminchk['result']==$this->success_result) {
            $admin_permissionchk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $adminchk['menuitem']['menu_item_id']);
            if ($admin_permissionchk['result']==$this->success_result && $admin_permissionchk['permission']>0) {
                $admin_permission = 1;
                $admin_old = $adminchk['menuitem']['newver'];
            }
        }
        // Reports
        $reports_permissions = 0;
        $reports_old = 1;
        $reportchk = $this->CI->menuitems_model->get_menuitem('/analytics',0, $brand);

        if ($reportchk['result']==$this->success_result) {
            $report_permissionchk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $reportchk['menuitem']['menu_item_id']);
            if ($report_permissionchk['result']==$this->success_result && $report_permissionchk['permission']>0) {
                $reports_permissions = 1;
                $reports_old = $reportchk['menuitem']['newver'];
            }
        }
        // Resources
        $resource_permissions = 0;
        $resource_old = 1;
        $resourcechk = $this->CI->menuitems_model->get_menuitem('/resources',0, $brand);

        if ($resourcechk['result']==$this->success_result) {
            $resource_permissionchk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $resourcechk['menuitem']['menu_item_id']);
            if ($resource_permissionchk['result']==$this->success_result && $resource_permissionchk['permission']>0) {
                $resource_permissions = 1;
                $resource_old = $resourcechk['menuitem']['newver'];
            }
        }

        // Inventory
        $inventory_permissions = 0;
        $inventory_old = 1;
        $inventorychk = $this->CI->menuitems_model->get_menuitem('#printshopinventview',0, $brand);
        if ($inventorychk['result']==$this->success_result) {
            $inventorypermischk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $inventorychk['menuitem']['menu_item_id']);
            if ($inventorypermischk['result']==$this->success_result && $inventorypermischk['permission']>0) {
                $inventory_old = $inventorychk['menuitem']['newver'];
                $inventory_permissions = 1;
            }
        }
        // Balance
        $debt_permissions = 0;
        $debt_total = 0;
        $debtviewchk = $this->CI->menuitems_model->get_menuitem('#accreceiv',0, $brand);
        if ($debtviewchk['result']==$this->success_result) {
            $debtpermischk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $debtviewchk['menuitem']['menu_item_id']);
            if ($debtpermischk['result']==$this->success_result && $debtpermischk['permission']>0) {
                $debt_permissions = 1;
                $this->CI->load->model('dashboard_model');
                $debt_total = $this->CI->dashboard_model->get_debt_totals();
            }
        }

        $pagetitle = (isset($options['title']) ? '::'.$options['title'] : '');
        $gmaps = 0;
        if (!empty($this->CI->config->item('google_map_key'))) {
            $gmaps = ifset($options, 'gmaps', 0);
        }
        $head_options=[
            'styles'=>$styles,
            'scripts'=>$scripts,
            'title' => ($this->CI->config->item('system_name').$pagetitle),
            'gmaps' => $gmaps,
            'googlefont' => ifset($options,'googlefont', 0),
        ];
        if (ifset($options,'adaptive',0)==1) {
            $head_options['menu'] = $mobpermissions;
            $head_options['activelnk'] = (isset($options['activelnk']) ? $options['activelnk'] : '');
            $dat['head_view'] = $this->CI->load->view('page/head_adaptive_view', $head_options, TRUE);
        } else {
            $dat['head_view'] = $this->CI->load->view('page/head_view', $head_options, TRUE);
        }


        $topmenu_options = [
            'user_name' => $userdat['first_name'],
            'activelnk' => (isset($options['activelnk']) ? $options['activelnk'] : ''),
            'total_view' => $total_view,
            // 'menu_view' => $menu_view,
            'adminchk' => $admin_permission,
            'adminold' => $admin_old,
            'reportchk' => $reports_permissions,
            'reportsold' => $reports_old,
            'resourcechk' => $resource_permissions,
            'resourceold' => $resource_old,
            'inventorychk' => $inventory_permissions,
            'inventoryold' => $inventory_old,
            'test_server' => $this->CI->config->item('test_server'),
            'brand' => $brand,
            'brands' => $brands,
            'usrrole' => $userdat['user_logged_in'],
            'debtpermiss' => $debt_permissions,
            'debttotal' => $debt_total,
        ];
        if (ifset($options,'adaptive',0)==1) {
            $dat['header_view'] = $this->CI->load->view('page/header_adaptive_view', $topmenu_options, TRUE);
        } else {
            $dat['header_view'] = $this->CI->load->view('page/header_view', $topmenu_options, TRUE);
        }
        $leftoptions = [
            'brand' => $brand,
            'activelnk'=>(isset($options['activelnk']) ? $options['activelnk'] : ''),
            'permissions' => $this->CI->menuitems_model->get_user_permissions($options['user_id'], $brand),
        ];
        $dat['left_menu'] = $this->CI->load->view('page/left_menu_view', $leftoptions, TRUE);
        // $dat['popups_view'] = $this->CI->load->view('page/popups_view', [], TRUE);
        return $dat;
    }

    public function _prepare_leadorder_view($res,$user_id, $user_role='manager', $user_payment=0, $edit=0) {
        $this->CI->load->model('shipping_model');
        $this->CI->load->model('orders_model');
        $this->CI->load->model('leadorder_model');
        $this->CI->load->model('user_model');
        $usrdat=$this->CI->user_model->get_user_data($user_id);
        $ord_data=$res['order'];
        $ord_data['out_shipping']=($edit==1 ? $ord_data['shipping'] : ($ord_data['shipping']==0 ? '&mdash;' : MoneyOutput($ord_data['shipping'])));
        $ord_data['out_revenue']=($edit==1 ? $ord_data['revenue'] : ($ord_data['revenue']==0 ? '&mdash;' : MoneyOutput($ord_data['revenue'])));
        $ord_data['out_tax']=($edit==1 ? $ord_data['tax'] : ($ord_data['tax']==0 ? '&mdash;' : MoneyOutput($ord_data['tax'])));
        $ord_data['subtotal']=$this->CI->leadorder_model->oldorder_item_subtotal($ord_data);

        $art_data=$res['artwork'];
        $art_data['item_id']=$ord_data['item_id'];
        $art_data['item_number']=$ord_data['order_itemnumber'];
        $art_data['item_name']=$ord_data['order_items'];
        $art_data['update_message']='';
        $orddata=  array_merge($ord_data, $art_data);
        $weborder=0;
        $payments=$res['payments'];
        if ($ord_data['order_usr_repic']!='' && $ord_data['order_usr_repic']<0) {
            $orddata['replica_view']=$this->CI->load->view('leadorderdetails/website_replica_view', array('edit'=>$edit), TRUE);
            $weborder=1;
        } else {
            $repl_options=array(
                'data'=>$ord_data,
                'edit'=>$edit
            );
            $repl_options['users']=$this->CI->user_model->get_user_leadreplicas();
            $orddata['replica_view']=$this->CI->load->view('leadorder/edit_replica_view', $repl_options, TRUE);
        }
        $orddata['edit']=$edit;
        $orddata['payment_history']=$this->CI->load->view('leadorderdetails/payment_history_view', array('payments'=>$payments), TRUE);
        // Bottom View
        $ticketcnt=$res['numtickets'];
        if ($ticketcnt==0) {
            $ticketview=$this->CI->load->view('leadorderdetails/ticket_dataempty_view', array(),TRUE);
        } else {
            $tickdata=$res['ticket'];
            $ticketview=$this->CI->load->view('leadorderdetails/ticket_data_view', $tickdata, TRUE);
        }
        // Shipping Date
        $shipstatus=$this->CI->leadorder_model->_leadorderview_shipping_status($res);
        $trackcontent = '';
        $order_items=$res['order_items'];
        $numcolors = 0;
        foreach ($order_items as $order_item) {
            $numcolors+=count($order_item['items']);
        }
        // 1 tracking
        if ($numcolors==1) {
            $orderitem = $order_items[0];
            $itemdata = $orderitem['items'][0];
            $shipoptions = [
                'shipdate' => $shipstatus['order_status'],
                'item' => $orderitem['item_name'].' '.$itemdata['item_color'],
                'qty' => $itemdata['item_qty'],
                'order_item' => $orderitem['order_item_id'],
                'item_color' => $itemdata['item_id'],
            ];
            $tracktotal = 0;
            if (!empty($itemdata['trackings'])) {
                foreach ($itemdata['trackings'] as $tracking) {
                    $tracktotal+=$tracking['qty'];
                }
            }
            $resttrack = $itemdata['item_qty'] - $tracktotal;
            $shipoptions['remind'] = $resttrack;
            $shipoptions['completed'] = ($resttrack > 0 ? 0 : 1);
            $trackbody = '';
            if (!empty($itemdata['trackings'])) {
                $tbodyoptions = [
                    'trackings' => $itemdata['trackings'],
                    'completed' => ($resttrack > 0 ? 0 : 1),
                    'order_item' => $orderitem['order_item_id'],
                    'item_color' => $itemdata['item_id'],
                ];
                if ($edit==1) {
                    $trackbody = $this->CI->load->view('leadorderdetails/tracking_data_edit', $tbodyoptions, TRUE);
                } else {
                    $trackbody = $this->CI->load->view('leadorderdetails/tracking_data_view', $tbodyoptions, TRUE);
                }
            }
            $shipoptions['trackbody'] = $trackbody;
            if ($edit==1) {
                if ($resttrack==0) {
                    $trackcontent = $this->CI->load->view('leadorderdetails/tracking_view', $shipoptions, TRUE);
                } else {
                    $trackcontent = $this->CI->load->view('leadorderdetails/tracking_edit', $shipoptions, TRUE);
                }
            } else {
                $trackcontent = $this->CI->load->view('leadorderdetails/tracking_view', $shipoptions, TRUE);
            }
        } elseif ($numcolors > 1) {
            // Multihip
            $totalitems = 0;
            $tracktotal = 0;
            foreach ($order_items as $order_item) {
                $totalitems+=$order_item['item_qty'];
                $itemcolors = $order_item['items'];
                foreach ($itemcolors as $itemcolor) {
                    foreach ($itemcolor['trackings'] as $tracking) {
                        $tracktotal+=$tracking['qty'];
                    }
                }
            }
            $remains = $totalitems - $tracktotal;
            $completed = 1;
            if ($remains > 0) {
                $completed = 0;
            }
            $trackcontent = '<div class="trackingdataarea">';
            $numhead = 1;
            $trackcontent.='<div class="multitrackbodyarea">';
            foreach ($order_items as $order_item) {
                $itemcolors = $order_item['items'];
                foreach ($itemcolors as $itemcolor) {
                    $headoptions = [
                        'item' => $order_item['item_name'].' '.$itemcolor['item_color'],
                        'qty' => $itemcolor['item_qty'],
                        'order_item' => $order_item['order_item_id'],
                        'item_color' => $itemcolor['item_id'],
                        'headclass' => ($numhead==1 ? '' : 'middlehead'),
                        'completed' => $completed,
                    ];
                    if ($edit==1) {
                        if ($completed==1) {
                            $trackcontent.= $this->CI->load->view('leadorderdetails/multitrack_head_view', $headoptions, TRUE);
                        } else {
                            $trackcontent.= $this->CI->load->view('leadorderdetails/multitrack_head_edit', $headoptions, TRUE);
                        }
                    } else {
                        $trackcontent.= $this->CI->load->view('leadorderdetails/multitrack_head_view', $headoptions, TRUE);
                    }
                    $tbodyoptions = [
                        'trackings' => $itemcolor['trackings'],
                        'completed' => $completed,
                        'order_item' => $order_item['order_item_id'],
                        'item_color' => $itemcolor['item_id'],
                    ];
                    if ($edit==1) {
                        $trackcontent.=$this->CI->load->view('leadorderdetails/multitrack_data_edit', $tbodyoptions, TRUE);
                    } else {
                        $trackcontent.=$this->CI->load->view('leadorderdetails/multitrack_data_view', $tbodyoptions, TRUE);
                    }
                    $numhead++;
                }
            }
            $trackcontent.='</div>';
            $tfooteroptions = [
                'completed' => $completed,
                'remind' => $remains,
                'shipdate' => $shipstatus['order_status']
            ];
            $trackcontent.=$this->CI->load->view('leadorderdetails/multitrack_footer_view', $tfooteroptions, TRUE);
            $trackcontent.='</div>';
        }
        // Total Due
        $total_due=$res['total_due'];
        $dueoptions=array(
            'totaldue'=>$total_due,
        );
        if ($total_due==0 && $ord_data['payment_total']>0) {
            $dueoptions['class']='closed';
        } else {
            $dueoptions['class']='open';
            if ($total_due<0) {
                $dueoptions['class']='overflow';
            }
        }

        $dueview=$this->CI->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
        $bottom_options=array(
            'ticketview'=>$ticketview,
            'shippview'=> $trackcontent,
            'totaldueview'=>$dueview,
        );
        $orddata['taxalign']='';
        if ($edit==0)  {
            if ($ord_data['tax']==0) {
                $orddata['taxalign']='style="text-align: center;"';
            } else {
                $orddata['taxalign']='style="text-align: right;"';
            }
        }
        $profoptions=array(
            'profit_perc'=>$orddata['profit_perc'],
            'profit'=>$orddata['profit'],
            'profit_view'=>'',
            'order_id'=>$ord_data['order_id'],
            'bgcolor' => '#FFFFFF',
            'hitcolor' => '#000000',
            'edit_mode' => $edit,
        );
        if (!empty($orddata['profit_perc'])) {
            $classprof = orderProfitClass($orddata['profit_perc']);
            $profoptions['profit_class'] = $classprof;
            if ($classprof=='green') {
                $profoptions['bgcolor']='#00e947';
            } elseif ($classprof=='red') {
                $profoptions['bgcolor']='#ff0000';
                $profoptions['hitcolor']='#ffffff';
            } elseif ($classprof=='black') {
                $profoptions['bgcolor']='#000000';
                $profoptions['hitcolor']='#ffffff';
            } elseif ($classprof=='orange') {
                $profoptions['bgcolor']='#ea8a0e';
            } elseif ($classprof=='moroon') {
                $profoptions['bgcolor']='#6d0303';
                $profoptions['hitcolor']='#ffffff';
            }
        }
        if ($usrdat['profit_view']=='Points') {
            $profoptions['profit']=round(floatval($orddata['profit'])*$this->CI->config->item('profitpts'),0).' pts';
            $profoptions['profit_view']='points';
        }
        if (empty($orddata['profit_perc'])) {
            $bottom_options['profit_view']=$this->CI->load->view('leadorderdetails/profitproject_view', $profoptions, TRUE);
        } else {
            if ($profoptions['profit_view']=='points') {
                $bottom_options['profit_view']=$this->CI->load->view('leadorderdetails/profit_points_view', $profoptions, TRUE);
            } else {
                $bottom_options['profit_view']=$this->CI->load->view('leadorderdetails/profit_view', $profoptions, TRUE);
            }
        }

        $data['order_bottom']=$this->CI->load->view('leadorderdetails/order_bottom_view', $bottom_options, TRUE);
        if ($res['order_system_type']=='old') {
            $rushallow=$edit;
            $ord_data['customer_contact']=$art_data['customer_contact'];
            // Switch Template content
            $orddata['template_switch']='&nbsp;';
            if ($edit==0) {
                if ($res['unlocked']==1) {
                    $orddata['template_switch']=$this->CI->load->view('leadorderdetails/order_systemselect_view', array(), TRUE);
                }
                $orddata['item_view']=$this->CI->load->view('leadorderdetails/oldorder_item_view', $orddata, TRUE);
            } else {
                $dboptions=array(
                    'exclude'=>array(-4, -5, -2),
                );
                $orddata['itemslist']=$this->CI->orders_model->get_item_list($dboptions);
                $orddata['item_view']=$this->CI->load->view('leadorderdetails/oldorder_item_edit', $orddata, TRUE);
            }
            $data['order']=$this->CI->load->view('leadorderdetails/order_olddata_view', $orddata, TRUE);
        } else {
            $contacts=$res['contacts'];
            if ($edit==0) {
                $orddata['contacts']=$this->CI->load->view('leadorderdetails/contact_detail_view', array('data'=>$contacts), TRUE);
            } else {
                $orddata['contacts']=$this->CI->load->view('leadorderdetails/contact_detail_edit', array('data'=>$contacts), TRUE);
            }
            // $order_items=$res['order_items'];

            $content='';
            $subtotal=0;
            
            foreach ($order_items as $irow) {
                $imprints=$irow['imprints'];
                if ($orddata['order_blank']==1 && count($imprints)==1) {
                    $imprints[0]['imprint_description']='blank, no imprinting';
                }
                if ($edit==0) {
                    $imprintview=$this->CI->load->view('leadorderdetails/imprint_data_view', array('imprints'=>$imprints), TRUE);
                } else {
                    $ioptions=array(
                        'imprints'=>$imprints,
                        'order_item_id'=>$irow['order_item_id'],
                    );
                    $imprintview=$this->CI->load->view('leadorderdetails/imprint_data_edit', $ioptions, TRUE);
                }
                $showinvent = 0;
                if ($ord_data['brand']=='SR' && $irow['item_id']>0) {
                    $showinvent = 1;
                }
                $item_options=array(
                    'order_item_id'=>$irow['order_item_id'],
                    'item_id'=>$irow['item_id'],
                    'items'=>$irow['items'],
                    'imprintview'=>$imprintview,
                    'edit'=>$edit,
                    'showinvent' => $showinvent,
                    'brand' => $ord_data['brand'],
                );
                $subtotal+=($irow['imprint_subtotal']+$irow['item_subtotal']);
                if ($edit==1) {
                    if ($irow['item_id']=='') {
                        $item_options['itemslist'] = $res['itemslist'];
                        $content.=$this->CI->load->view('leadorderdetails/items_data_add', $item_options, TRUE);
                    } else {
                        $content.=$this->CI->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                    }
                } else {
                    $content.=$this->CI->load->view('leadorderdetails/items_data_view', $item_options, TRUE);
                }
            }
            $orddata['items']=$content;
            $subtotal+=($ord_data['mischrg_val1']+$ord_data['mischrg_val2']-$ord_data['discount_val']);

            $orddata['subtotal']=($subtotal==0 ? '&nbsp;' : MoneyOutput($subtotal));
            // Shipping
            $shipping=$res['shipping'];

            $shipping_address=$res['shipping_address'];

            $rushallow=$edit;
            $rushlist=ifset($shipping, 'out_rushlist', []);
            $rushoptions=array(
                'edit'=>$edit,
            );

            if (isset($rushlist['rush'])) {
                $rushoptions['rush']=$rushlist['rush'];
                $rushoptions['current']=$shipping['rush_idx'];
                $rushoptions['shipdate']=$shipping['shipdate'];
                if ($edit==1) {
                    // Check terms
                    foreach ($rushlist['rush'] as $rrow) {
                        if ($rrow['date'] == $shipping['shipdate'] && $rrow['rushterm']!='Standard') {
                            $rushallow=0;
                            break;
                        }
                    }
                }
            } else {
                $rushoptions['rush']=array();
                $rushoptions['current']='';
            }

            $rushview=$this->CI->load->view('leadorderdetails/rushlist_view', $rushoptions, TRUE);

            if (count($shipping_address)==1) {
                $shipcost=$shipping_address[0]['shipping_costs'];
                if ($edit==0) {
                    $cost_view=$this->CI->load->view('leadorderdetails/ship_cost_view', array('shipcost'=>$shipcost),TRUE);
                } else {
                    $costoptions=array(
                        'shipadr'=>$shipping_address[0]['order_shipaddr_id'],
                        'shipcost'=>$shipcost,
                    );
                    $cost_view=$this->CI->load->view('leadorderdetails/ship_cost_edit', $costoptions,TRUE);
                }
                $country_id=$shipping_address[0]['country_id'];
                if ($shipping_address[0]['taxview']==1) {
                    if ($edit==1) {
                        $taxview=$this->CI->load->view('leadorderdetails/tax_data_edit', $shipping_address[0], TRUE);
                    } else {
                        $taxview=$this->CI->load->view('leadorderdetails/tax_data_view', $shipping_address[0], TRUE);
                    }
                } else {
                    $taxview=$this->CI->load->view('leadorderdetails/tax_empty_view', array(), TRUE);
                }
                $cntres = $this->CI->shipping_model->get_country($country_id);
                $states=$this->CI->shipping_model->get_country_states($country_id);
                $shipoptions=array(
                    'shipping'=>$shipping,
                    'countries'=>$res['countries'],
                    'states'=>$states,
                    'shipadr'=>$shipping_address[0],
                    'shipcostview'=>$cost_view,
                    'order'=>$ord_data,
                    'rushview'=>$rushview,
                    'taxview'=>$taxview,
                    'shipcntcode' => ifset($cntres,'country_iso_code_2',''),
                    'shipaddress' => $this->CI->shipping_model->prepare_shipaddress($shipping_address[0]),
                );
                if ($edit==1) {
                    $orddata['shippingview']=$this->CI->load->view('leadorderdetails/single_ship_edit', $shipoptions, TRUE);
                } else {
                    $orddata['shippingview']=$this->CI->load->view('leadorderdetails/single_ship_view', $shipoptions, TRUE);
                }
            } else {
                $cost_view='';
                $numpp=1;
                foreach ($shipping_address as $srow) {
                    $srow['numpp']=$numpp;
                    $cost_view.=$this->CI->load->view('leadorderdetails/shipping_datarow_view', $srow, TRUE);
                    $numpp++;
                }
                $shipoptions=array(
                    'shipping'=>$shipping,
                    'shipcostview'=>$cost_view,
                    'order'=>$ord_data,
                    'rushview'=>$rushview,
                );
                if ($edit==1) {
                    $orddata['shippingview']=$this->CI->load->view('leadorderdetails/multi_ship_edit', $shipoptions, TRUE);
                } else {
                    $orddata['shippingview']=$this->CI->load->view('leadorderdetails/multi_ship_view', $shipoptions, TRUE);
                }
            }
            $dateoptions=array(
                'edit'=>$edit,
                'shipping'=>$shipping,
                'user_role' => $user_role,
            );
            if ($edit==1) {
                $orddata['shipdatesview']=$this->CI->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
            } else {
                $orddata['shipdatesview']=$this->CI->load->view('leadorderdetails/shipping_dates_view', $dateoptions, TRUE);
            }

            // Shipping data
            $orddata['shiptax']='&nbsp;';
            $billing=$res['order_billing'];

            $country_id=ifset($billing, 'country_id', '');
            $cntdata = $this->CI->shipping_model->get_country($country_id);
            $states=$this->CI->shipping_model->get_country_states($country_id);
            $billoptions=array(
                'billing'=>$billing,
                'countries'=>$res['countries'],
                'states'=>$states,
                'order'=>$ord_data,
                'financeview'=>$usrdat['finuser'],
                'country_code' => strtolower($cntdata['country_iso_code_2']),
                'billaddress' => $this->CI->shipping_model->prepare_billaddress($billing),
            );
            if ($edit==1) {
                if ($ord_data['order_id']==0) {
                    if ($ord_data['showbilladdress']==1) {
                        $leftcont=$this->CI->load->view('leadorderdetails/billsameadress_edit', $billoptions, TRUE);
                    } else {
                        $leftcont=$this->CI->load->view('leadorderdetails/billadress_edit', $billoptions, TRUE);
                    }
                    $billoptions['leftbilling']=$leftcont;
                    $orddata['billingview']=$this->CI->load->view('leadorderdetails/billing_data_new', $billoptions, TRUE);
                } else {
                    $orddata['billingview']=$this->CI->load->view('leadorderdetails/billing_data_edit', $billoptions, TRUE);
                }
            } else {
                $orddata['billingview']=$this->CI->load->view('leadorderdetails/billing_data_view', $billoptions, TRUE);
            }
            $charges=$res['charges'];
            $editalov=1;
            if ($total_due==0 && $ord_data['payment_total']>0) {
                $editalov=0;
            }
            if ($ord_data['balance_manage']==3) {
                $appoptions=array(
                    'balance_term'=>$ord_data['balance_term'],
                    'credit_appdue'=>$ord_data['credit_appdue'],
                    'appaproved'=>$ord_data['appaproved'],
                    'editalov'=>$editalov,
                );
                if ($edit==1) {
                    $appview=$this->CI->load->view('leadorderdetails/creditapp_edit', $appoptions, TRUE);
                } else {
                    $appview=$this->CI->load->view('leadorderdetails/creditapp_view', $appoptions, TRUE);
                }
            } else {
                $appview='&nbsp;';
            }

            $balancoptions=array(
                'balance_manage'=>$ord_data['balance_manage'],
                'creditapp_view'=>$appview,
                'editalov'=>$editalov,
            );
            if ($edit==1) {
                $balanceview=$this->CI->load->view('leadorderdetails/balance_manage_edit', $balancoptions, TRUE);
            } else {
                $balanceview=$this->CI->load->view('leadorderdetails/balance_manage_view', $balancoptions, TRUE);
            }
            $chargeoptions=array(
                'charges'=>$charges,
                'order'=>$ord_data,
                'balanceview'=>$balanceview,
                'financeview'=>$usrdat['finuser'],
                'payment_user' => $user_payment,
            );

            if ($edit==1) {
                if ($ord_data['order_id']>0) {
                    $orddata['chargeview']=$this->CI->load->view('leadorderdetails/charge_data_edit', $chargeoptions, TRUE);
                } else {
                    $orddata['chargeview']=$this->CI->load->view('leadorderdetails/charge_datafirst_edit', $chargeoptions, TRUE);
                }
            } else {
                $orddata['chargeview']=$this->CI->load->view('leadorderdetails/charge_data_view', $chargeoptions, TRUE);
            }

            // Miscs value
            $discount_options = $ord_data;
            $discount_options['mischrg1_class']=$discount_options['mischrg2_class']='input_border_gray';
            $discount_options['discnt_class']='empty_icon_file';
            $discount_options['discnt_title']='';
            if (abs($ord_data['mischrg_val1'])>0 && empty($ord_data['mischrg_label1'])) {
                $discount_options['mischrg1_class']='input_border_red';
            }
            if (abs($ord_data['mischrg_val2'])>0 && empty($ord_data['mischrg_label2'])) {
                $discount_options['mischrg2_class']='input_border_red';
            }
            if (abs($ord_data['discount_val'])>0 && empty($ord_data['discount_descript'])) {
                $discount_options['discnt_class']='discountdescription_red';
                $discount_options['discnt_title']='All Discounts Must Have Valid Reason Explaining Why';
            } elseif (abs($ord_data['discount_val'])>0 && !empty($ord_data['discount_descript'])) {
                $discount_options['discnt_class']='icon_file';
                $discount_options['discnt_title']=$ord_data['discount_descript'];
            }
            if ($edit==1) {
                $orddata['discounts_view']=$this->CI->load->view('leadorderdetails/discounts_data_edit', $discount_options, TRUE);
            } else {
                $orddata['discounts_view']=$this->CI->load->view('leadorderdetails/discounts_data_view', $discount_options, TRUE);
            }

            $orddata['chargeattemptview']='&nbsp;';
            if ($usrdat['finuser']) {
                $numatempt=$this->CI->leadorder_model->count_charges_attempts($ord_data['order_id']);
                if ($numatempt>0) {
                    $orddata['chargeattemptview']=$this->CI->load->view('leadorderdetails/charge_attempt_view', $ord_data, TRUE);
                }
            }
            $data['order']=$this->CI->load->view('leadorderdetails/order_newdata_view', $orddata, TRUE);
        }
        $message=$res['message'];
        $message['edit']=$edit;
        $data['messages_view']=$this->CI->load->view('leadorderdetails/message_view', $message, TRUE);



        $artdata=$art_data;
        $artdata['rushallow']=$rushallow;
        $locat=$res['artlocations'];
        $locat_view='';
        if ($orddata['order_blank']==1) {
            $blankopt=array(
                'view'=>'block',
            );
        } else {
            $blankopt=array(
                'view'=>'none',
            );
        }
        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/blank_view', $blankopt, TRUE);
        $artcolors=$artfonts='';
        foreach ($locat as $row) {
            switch ($row['art_type']) {
                case 'Logo':
                    if ($edit==0) {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_logo_view', $row, TRUE);
                    } else {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_logo_edit', $row, TRUE);
                    }
                    break;
                case 'Text':
                    if ($edit==0) {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_text_view', $row, TRUE);
                    } else {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_text_edit', $row, TRUE);
                    }
                    break;
                case 'Repeat':
                    if ($edit==0) {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_repeat_view', $row, TRUE);
                    } else {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_repeat_edit', $row, TRUE);
                    }
                    break;
                case 'Reference':
                    if ($edit==0) {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_reference_view', $row, TRUE);
                    } else {
                        $locat_view.=$this->CI->load->view('leadorderdetails/artlocs/artlocation_reference_edit', $row, TRUE);
                    }
                    break;
            }
            if (!empty($row['art_color1'])) {
                $artcolors.=$row['art_color1'].' ';
            }
            if (!empty($row['art_color2'])) {
                $artcolors.=$row['art_color2'].' ';
            }
            if (!empty($row['font'])) {
                $artfonts.=$row['font'].' ';
            }
        }

        $artdata['locat_view']=$locat_view;
        $proofdocs=$res['proofdocs'];
        $proofview=leadProfdocOut($proofdocs, $edit);
        $numoutprofdoc=ceil(count($proofdocs)/5);
        $artdata['profdocwidth']=$numoutprofdoc*145; // 160;
        $artdata['weborder']=$weborder;
        $artdata['artcolors']=$artcolors;
        $artdata['artfont']=$artfonts;
        $artdata['proofdoc_view']=$proofview;
        $artdata['edit'] = $edit;
        // Clay
        $claydocs=$res['claydocs'];
        $clayview=leadClaydocOut($claydocs, $edit);
        $artdata['claydoc_view']=$clayview;
        $artdata['claycnt'] = count($claydocs);
        $artdata['claydocswidth'] = ceil(count($claydocs)/3)*115;
        // Previews
        $previewdocs=$res['previewdocs'];
        $previewview=leadPreviewdocOut($previewdocs, $edit);
        $artdata['previewdoc_view']=$previewview;
        $artdata['previewcnt'] = count($previewdocs);
        $artdata['previewswidth'] = ceil(count($previewdocs)/3)*115;
        // Templates
        if ($ord_data['brand']=='SR') {
            $artdata['empty_url'] = $this->CI->config->item('sr_empty_template');
            $artdata['empty_title'] = $this->CI->config->item('sr_empty_title');
        } else {
            $artdata['empty_url'] = $this->CI->config->item('sb_empty_template');
            $artdata['empty_title'] = $this->CI->config->item('sb_empty_title');
        }
        $artdata['extendview'] = ifset($res, 'extendview',0);
        // Artwork View
        $data['artview']=$this->CI->load->view('leadorderdetails/artwork_view', $artdata, TRUE);
        return $data;
    }

    public function prepare_duplicateorder($options)
    {
        $dat = array();
        $styles=[];
        if (isset($options['styles'])) {
            $styles=$options['styles'];
        }
        if ($_SERVER['SERVER_NAME']=='lifttest.stressballs.com') {
            $styles[]=array('style'=>'/css/page_view/testsite_view.css');
        }
        $scripts=[];
        if (isset($options['scripts'])) {
            $scripts=$options['scripts'];
        }

        $head_options=[
            'styles'=>$styles,
            'scripts'=>$scripts,
            'gmaps' => ifset($options,'gmaps',0),
        ];
        $dat['head'] = $this->CI->load->view('duplcate_orders/head_view', $head_options, TRUE);
        return $dat;
    }
}