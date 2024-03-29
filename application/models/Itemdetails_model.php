<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Itemdetails_model extends My_Model
{

    private $STRESSBALL_TEMPLATE='Stressball';
    private $Inventory_Source='Stock';
    private $IMPRINT_NUMBER=12;

    function __construct()
    {
        parent::__construct();
    }

    public function change_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $out['entity']=$entity;
        $profitrecalc = 0;
        $fld = ifset($data,'fld','noname');
        $out['fldname']=$fld;
        $newval=ifset($data,'newval');
        $key = ifset($data,'idx',0);
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
        } elseif ($entity=='imprints') {
            $out['msg']='Imprint Location Not found';
            $imprints = $session_data['imprints'];
            $idx = 0;
            foreach ($imprints as $row) {
                if ($row['item_inprint_id']==$key) {
                    $imprints[$idx][$fld]=$newval;
                    $session_data['imprints']=$imprints;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='item_images') {
            $out['msg']='Item Image Not Found';
            $images = $session_data['item_images'];
            $idx = 0;
            foreach ($images as $item) {
                if ($item['item_img_id']==$key) {
                    $images[$idx][$fld]=$newval;
                    $newimg = $this->_rebuid_images_params($images);
                    $session_data['item_images']=$newimg;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    $out['images']=$newimg;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='item_prices') {
            $out['msg']='Price Not Found';
            $profitrecalc = 1;
            $prices=$session_data['item_prices'];
            $item = $session_data['item'];
            if ($item['item_template']=='Stressball') {
                if (array_key_exists($fld, $prices)) {
                    $prices[$fld] = $newval;
                    $out['msg'] = '';
                    $out['result'] = $this->success_result;
                    $session_data['item_prices'] = $prices;
                    usersession($session_id, $session_data);

                    $pricetype = str_replace(['item_price_', 'item_sale_'], '', $fld);
                    // Other vendor prices
                    $other_prices = $session_data['research_prices'];
                    $base = 0;
                    if (floatval($prices['item_sale_' . $pricetype]) != 0) {
                        $base = floatval($prices['item_sale_' . $pricetype]);
                    } elseif (floatval($prices['item_price_' . $pricetype]) != 0) {
                        $base = floatval($prices['item_price_' . $pricetype]);
                    }
                    $research = [];
                    $this->load->model('otherprices_model');
                    for ($i = 1; $i < 5; $i++) {
                        // if ($other_prices[$i])
                        if ($base > 0) {
                            $ridx = $i - 1;
                            if (floatval($other_prices[$ridx]['other_vendorprice_price_' . $pricetype]) > 0) {
                                $research_dat = $this->otherprices_model->price_otherview($other_prices[$ridx]['other_vendorprice_price_' . $pricetype], $pricetype, $base);
                                $research[] = ['id' => $pricetype . $i, 'price' => $research_dat['price'], 'priceclass' => $research_dat['class']];
                            } else {
                                $research[] = array('id' => $pricetype . $i, 'price' => 'n/a', 'priceclass' => 'empty_price');
                            }
                        } else {
                            $research[] = array('id' => $pricetype . $i, 'price' => 'n/a', 'priceclass' => 'empty_price');
                        }
                    }
                    $out['research'] = $research;
                }
            } else {
                if ($fld=='item_sale_print' || $fld=='item_sale_setup' || $fld=='item_price_print' || $fld=='item_price_setup') {
                    $commonprice = $session_data['common_prices'];
                    $commonprice[$fld]=$newval;
                    $session_data['common_prices'] = $commonprice;
                    usersession($session_id, $session_data);
                    $out['msg'] = '';
                    $out['result'] = $this->success_result;
                } else {
                    $found = 0;
                    $idx = 0;
                    foreach ($prices as $row) {
                        if ($row['promo_price_id']==$key) {
                            $found=1;
                            break;
                        }
                        $idx++;
                    }
                    if ($found==1) {
                        // Price found
                        $prices[$idx][$fld]=$newval;
                        $out['msg'] = '';
                        $out['result'] = $this->success_result;
                        $session_data['item_prices'] = $prices;
                        usersession($session_id, $session_data);
                    }
                }
            }
        } elseif ($entity=='simular') {
            $out['msg']='Item Simular Not Found';
            $items = $session_data['simular'];
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
        } elseif ($entity=='colors') {
            $out['msg']='Item Option Not Found';
            $colors = $session_data['item_colors'];
            $idx = 0;
            foreach ($colors as $row) {
                if ($row['item_color_id']==$key) {
                    $out['result']=$this->success_result;
                    $colors[$idx][$fld]=$newval;
                    $session_data['item_colors']=$colors;
                    usersession($session_id, $session_data);
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='vendor') {
            $vendor = $session_data['vendor'];
            if (array_key_exists($fld, $vendor)) {
                $vendor[$fld]=$newval;
                $session_data['vendor']=$vendor;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
                if ($fld=='vendor_item_cost') {
                    $profitrecalc = 1;
                } elseif ($fld=='vendor_item_setup') {
                    $profitrecalc = 1;
                } elseif ($fld=='vendor_item_exprint') {
                    $profitrecalc = 1;
                }
            }
        } elseif ($entity=='vendor_prices') {
            $out['msg']='Vendor price Not Found';
            $vendor_prices = $session_data['vendor_prices'];
            $idx = 0;
            $found = 0;
            foreach ($vendor_prices as $row) {
                if ($row['vendorprice_id']==$key) {
                    $out['result']=$this->success_result;
                    $vendor_prices[$idx][$fld]=$newval;
                    $session_data['vendor_prices']=$vendor_prices;
                    usersession($session_id, $session_data);
                    $found = 1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $profitrecalc = 1;
            }
        }
        $out['profitrecalc'] = $profitrecalc;
        if ($profitrecalc==1) {
            $prices=$session_data['item_prices'];
            $item = $session_data['item'];
            $vend_prices = $session_data['vendor_prices'];
            $vendor = $session_data['vendor'];
            $vendor_prices = [];
            foreach ($vend_prices as $vrow) {
                if (!empty($vrow['vendorprice_qty'])) {
                    $vendor_prices[] = $vrow;
                }
            }
            $this->load->model('prices_model');
            if ($item['item_template']=='Stressball') {
                // Add base price
                $prices['base_cost'] = $prices['vendor_item_exprint'] = $prices['vendor_item_setup'] = 0;
                if (!empty($vendor['vendor_item_cost'])) {
                    $prices['base_cost'] = $vendor['vendor_item_cost'];
                }
                if (!empty($vendor['vendor_item_exprint'])) {
                    $prices['vendor_item_exprint'] = $vendor['vendor_item_exprint'];
                }
                if (!empty($vendor['vendor_item_setup'])) {
                    $prices['vendor_item_setup'] = $vendor['vendor_item_setup'];
                }
                $profit = $this->prices_model->recalc_stress_profit($prices, $vendor_prices, $this->config->item('price_types'));
                $itemprices = $session_data['item_prices'];
                foreach ($this->config->item('price_types') as $pricetype) {
                    $itemprices['profit_'.$pricetype['type']] = $profit['profit_'.$pricetype['type']];
                }
                $itemprices['profit_print'] = $profit['profit_print'];
                $itemprices['profit_setup'] = $profit['profit_setup'];
                $session_data['item_prices'] = $itemprices;
                usersession($session_id, $session_data);
                $out['profit'] = $profit;
            } else {
                $commonprice = $session_data['common_prices'];
                // Add base price
                $commonprice['base_cost'] = $commonprice['vendor_item_exprint'] = $commonprice['vendor_item_setup'] = 0;
                if (!empty($vendor['vendor_item_cost'])) {
                    $commonprice['base_cost'] = $vendor['vendor_item_cost'];
                }
                if (!empty($vendor['vendor_item_exprint'])) {
                    $commonprice['vendor_item_exprint'] = $vendor['vendor_item_exprint'];
                }
                if (!empty($vendor['vendor_item_setup'])) {
                    $commonprice['vendor_item_setup'] = $vendor['vendor_item_setup'];
                }
                $profits = $this->prices_model->recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $newprofit = [];
                foreach ($profits as $profit) {
                    if ($profit['type']=='qty') {
                        $idx = 0;
                        foreach ($prices as $price) {
                            if ($price['promo_price_id']==$profit['price_id']) {
                                $prices[$idx]['profit']=$profit['profit'];
                                break;
                            }
                            $idx++;
                        }
                    } elseif ($profit['type']=='print') {
                        $commonprice['profit_print'] = $profit['profit'];
                    } elseif ($profit['type']=='setup') {
                        $commonprice['profit_setup'] = $profit['profit'];
                    }
                    if ($profit['type']=='qty') {
                        $newprofit['profit_' . $profit['base']] = $profit['profit'];
                        $newprofit['profit_' . $profit['base'] . '_perc'] = $profit['profit_perc'];
                        $newprofit['profit_' . $profit['base'] . '_class'] = $profit['profit_class'];
                    } elseif ($profit['type']=='print') {
                        $newprofit['profit_print'] = $profit['profit'];
                        $newprofit['profit_print_perc'] = $profit['profit_perc'];
                        $newprofit['profit_print_class'] = $profit['profit_class'];
                    } elseif ($profit['type']=='setup') {
                        $newprofit['profit_setup'] = $profit['profit'];
                        $newprofit['profit_setup_perc'] = $profit['profit_perc'];
                        $newprofit['profit_setup_class'] = $profit['profit_class'];
                    }
                }
                $session_data['item_prices'] = $prices;
                usersession($session_id, $session_data);
                $out['profit'] = $newprofit;
                // $out['profitrecalc'] = 0;
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
                $out['msg']='';
                $out['result']=$this->success_result;
                $out['type']='item';
            }
        } elseif ($entity=='prices') {
            $out['msg']='Price Not Found';
            $prices = $session_data['prices'];
            $found=0;
            $priceidx = 0;
            foreach ($prices as $row) {
                if ($row['item_specprice_id']==$idx) {
                    $found=1;
                    break;
                }
                $priceidx++;
            }
            if ($found==1) {
                $out['type']='price';
                $out['result']=$this->success_result;
                if ($fld=='price') {
                    $prices[$priceidx]['price']=floatval($newval);
                } else {
                    $prices[$priceidx]['price_qty']=intval($newval);
                }
                $out['amount']=$out['profit']=$out['profit_percent']=$out['profit_class']='';
                // Amount
                $amount=intval($prices[$priceidx]['price_qty'])*floatval($prices[$priceidx]['price']);
                if ($amount!=0) {
                    $vendor_prices = $session_data['vendor_prices'];
                    $prices[$priceidx]['amount']=$amount;
                    $out['amount']=MoneyOutput($amount);
                    $this->load->model('prices_model');
                    $profitdat = $this->prices_model->recalc_special_profit($vendor_prices, $prices[$priceidx]['price_qty'], $prices[$priceidx]['price']);
                    if (floatval($profitdat)!=0) {
                        $profit=floatval($profitdat);
                        $profit_perc=$profit/($amount)*100;
                        $out['profit_class']=profit_bgclass($profit_perc);
                        $out['profit']=round($profit,2);
                        $out['profit_percent']=round($profit_perc,0).'%';
                        $prices[$priceidx]['profit']=round($profit,2);
                        $prices[$priceidx]['profit_percent']=round($profit_perc,0);
                        $prices[$priceidx]['profit_class']=profit_bgclass($profit_perc);
                    }
                }
                $session_data['prices']=$prices;
                usersession($session_id, $session_data);
            }
        }
        return $out;
    }

    public function save_specialcheckout($session_data, $specsession_data, $session_id, $specsession_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $item = $session_data['item'];
        $specitem = $specsession_data['item'];
        // Update Main Item
        $item['special_checkout']=$specitem['special_checkout'];
        $item['special_shipping']=$specitem['special_shipping'];
        $item['special_setup']=$specitem['special_setup'];
        $session_data['item']=$item;
        $session_data['special_prices']=$specsession_data['prices'];
        usersession($session_id, $session_data);
        usersession($specsession_id, NULL);
        $out['result']=$this->success_result;
        return $out;
    }

    public function save_shipping($session_data, $postdata, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $item = $session_data['item'];
        // Change params
        $item['item_weigth']=floatval(ifset($postdata,'item_weigth',0));
        $item['cartoon_qty']=intval(ifset($postdata,'cartoon_qty',0));
        $item['cartoon_width']=floatval(ifset($postdata,'cartoon_width',0));
        $item['cartoon_heigh']=floatval(ifset($postdata,'cartoon_heigh',0));
        $item['cartoon_depth']=floatval(ifset($postdata,'cartoon_depth',0));
        $item['charge_pereach']=floatval(ifset($postdata,'charge_pereach',0));
        $item['charge_perorder']=floatval(ifset($postdata,'charge_perorder',0));
        $item['boxqty']=intval(ifset($postdata,'boxqty',0));
        $session_data['item']=$item;
        usersession($session_id, $session_data);
        $out['result']=$this->success_result;
        return $out;
    }

    public function change_commonterm($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Term Not Found'];
        $idx = ifset($postdata, 'idx',0);
        $newval = ifset($postdata,'newval','');
        $found = 0;
        $key=0;
        $terms = $session_data['commons'];
        foreach ($terms as $row) {
            if ($idx==$row['term_id']) {
                $found=1;
                break;
            }
            $key++;
        }
        if ($found==1) {
            $terms[$key]['common_term']=$newval;
            $session_data['commons']=$terms;
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function save_commonterm($commonsession_data, $commonsession, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Term Not Found'];
        $terms = $commonsession_data['commons'];
        $session_data['commons']=$terms;
        usersession($session_id, $session_data);
        usersession($commonsession, NULL);
        $out['result']=$this->success_result;
        return $out;
    }

    public function del_imprintlocation($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Imprint Location Not Found'];
        $imprints = $session_data['imprints'];
        $deleted = $session_data['deleted'];
        $key = ifset($postdata, 'imprint_key', 0);
        $minidx = 0;
        $found = 0;
        $newlocation = [];
        foreach ($imprints as $row) {
            if ($row['item_inprint_id']==$key) {
                $found=1;
                if ($key>0) {
                    $deleted[]=[
                        'entity' => 'imprints',
                        'key' => $key,
                    ];
                }
            } else {
                if (!empty($row['item_inprint_location'])) {
                    $newlocation[] = $row;
                    if ($minidx>$row['item_inprint_id']) {
                        $minidx=$row['item_inprint_id'];
                    }
                }
            }
        }
        if ($found==1) {
            $out['result']=$this->success_result;
            // New imprints
            $newkey=$minidx-1;
            for ($i=count($newlocation); $i < $this->IMPRINT_NUMBER; $i++) {
                $newlocation[]=[
                    'item_inprint_id'=>$newkey,
                    'item_inprint_location'=>'',
                    'item_inprint_size'=>'',
                    'item_inprint_view'=>'',
                    'item_imprint_mostpopular' => 0,
                ];
                $newkey--;
            }
            $out['imprints']=$newlocation;
            $session_data['imprints']=$newlocation;
            $session_data['deleted']=$deleted;
            usersession($session_id, $session_data);
        }
        return $out;
    }

    public function edit_imprintlocation($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $imprints = $session_data['imprints'];
        $key = ifset($postdata,'imprint_key',0);
        foreach ($imprints as $row) {
            if ($row['item_inprint_id']==$key) {
                $out['result']=$this->success_result;
                $out['imprint']=$row;
            }
        }
        return $out;
    }

    public function change_imprintlocation($postdata, $imprsession_data, $imprsession) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $imprint = $imprsession_data['imprint'];
        $fld = ifset($postdata,'fld', 'emptyfield');
        if (array_key_exists($fld, $imprint)) {
            $newval =ifset($postdata,'newval','');
            $imprint[$fld]=$newval;
            $out['result']=$this->success_result;
            $out['newfld']=$fld;
            if ($fld=='item_inprint_view') {
                $out['imprintview_src']=$imprint['item_inprint_view'];
            }
            $imprsession_data['imprint']=$imprint;
            usersession($imprsession, $imprsession_data);
        }
        return $out;
    }

    public function save_imprint($imprsession_data, $imprsession, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $imprint = $imprsession_data['imprint'];
        if (empty($imprint['item_inprint_location'])) {
            $out['msg']='Empty Imprint Title';
        } elseif (empty($imprint['item_inprint_size'])) {
            $out['msg']='Empty Imprint Size';
        } elseif (empty($imprint['item_inprint_view'])) {
            $out['msg']='Empty Imprint View';
        } else {
            $out['msg']='Imprint Location Not Found';
            $imprints = $session_data['imprints'];
            $key = $imprint['item_inprint_id'];
            $found = 0;
            $idx = 0;
            foreach ($imprints as $row) {
                if ($row['item_inprint_id']==$key) {
                    $found = 1;
                    $imprints[$idx]=$imprint;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $out['result']=$this->success_result;
                $out['imprints']=$imprints;
                $session_data['imprints']=$imprints;
                usersession($session_id, $session_data);
                usersession($imprsession, NULL);
            }
        }
        return $out;
    }

    public function del_itemimage($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Item Image not Found'];
        $key = ifset($postdata,'idx',0);
        $idx = 0;
        $item_images = $session_data['item_images'];
        $deleted = $session_data['deleted'];
        $images = [];
        $found=0;
        foreach ($item_images as $row) {
            if ($row['item_img_id']==$key) {
                $found=1;
                if ($key>0) {
                    $deleted[]=[
                        'entity' => 'item_images',
                        'key' => $key,
                    ];
                }
            } else {
                $images[] = $row;
            }
        }
        if ($found==1) {
            $out['result']=$this->success_result;
            $newimages = $this->_rebuid_images_params($images);
            $session_data['item_images']=$newimages;
            $session_data['deleted']=$deleted;
            usersession($session_id, $session_data);
            $out['images']=$newimages;
        }
        return $out;
    }

    private function _rebuid_images_params($images) {
        $i=1;
        $newimg=[];
        foreach ($images as $row) {
            if (!empty($row['src'])) {
                $newimg[]=[
                    'item_img_id'=>($row['item_img_id']< 0 ? $i*(-1) : $row['item_img_id']),
                    'item_img_item_id'=>$row['item_img_item_id'],
                    'src'=>$row['src'],
                    'name'=>($i==1 ? 'Main Pic' : 'Pic '.$i),
                    'item_img_order'=>$i,
                ];
                $i++;
            }
        }
        $limit=$this->config->item('slider_images');
        for ($i=0; $i<$limit; $i++) {
            if (!isset($newimg[$i])) {
                $newidx = $i+1;
                $newimg[]=[
                    'item_img_id'=>$newidx*(-1),
                    'item_img_item_id'=>'',
                    'src'=>'',
                    'name'=>($newidx==1 ? 'Main Pic' : 'Pic '.$newidx),
                    'item_img_order'=>$newidx,
                ];
            }
        }
        return $newimg;
    }

    public function check_vendor_item($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $vendor_item_number = ifset($postdata,'number','');
        $this->load->model('vendors_model');
        if (empty($vendor_item_number)) {
            $data=[
                'vendor_item_id'=>'',
                'vendor_item_vendor'=>'',
                'vendor_item_number'=>'',
                'vendor_item_name'=>'',
                'vendor_item_cost'=>'',
                'vendor_item_exprint'=>'',
                'vendor_item_setup'=>'',
                'vendor_item_notes'=>'',
                'vendor_name'=>'',
                'vendor_item_zipcode'=> '',
                'vendor_item_blankcost' => '',
            ];
            $vendor_prices=$this->vendors_model->newitem_vendorprices();
        } else {
            $res = $this->vendors_model->chk_vendor_item($vendor_item_number);
            if (ifset($res,'vendor_item_id',0)==0) {
                $data=[
                    'vendor_item_id'=>-1,
                    'vendor_item_vendor'=>'',
                    'vendor_item_number'=>$vendor_item_number,
                    'vendor_item_name'=>$vendor_item_number,
                    'vendor_item_cost'=>'',
                    'vendor_item_exprint'=>'',
                    'vendor_item_setup'=>'',
                    'vendor_item_notes'=>'',
                    'vendor_name'=>'',
                    'vendor_item_zipcode'=>'',
                    'vendor_item_blankcost' => '',
                ];
                $vendor_prices=$this->vendors_model->newitem_vendorprices();
            } else {
                $data=[
                    'vendor_item_id'=>$res['vendor_item_id'],
                    'vendor_item_vendor'=>$res['vendor_item_vendor'],
                    'vendor_item_number'=>$res['vendor_item_number'],
                    'vendor_item_name'=>$res['vendor_item_name'],
                    'vendor_item_cost'=>$res['vendor_item_cost'],
                    'vendor_item_exprint'=>$res['vendor_item_exprint'],
                    'vendor_item_setup'=>$res['vendor_item_setup'],
                    'vendor_item_notes'=>$res['vendor_item_notes'],
                    'vendor_name'=>$res['vendor_name'],
                    'vendor_item_zipcode'=>$res['vendor_item_zipcode'],
                    'vendor_item_blankcost'=>$res['vendor_item_blankcost'],
                ];
                $vendor_prices=$this->vendors_model->get_vedorprice_item($res['vendor_item_id'],0);
            }
        }
        $out['result']=$this->success_result;
        $out['data']=$data;
        $session_data['vendor']=$data;
        $session_data['vendor_prices']=$vendor_prices;
        $out['vendor_prices']=$vendor_prices;
        usersession($session_id, $session_data);
        return $out;
    }

    public function check_vendor($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $vendor_name = ifset($postdata,'vendor_name','');
        $vendordat = $session_data['vendor'];

        $this->load->model('vendors_model');
        if (empty($vendor_name)) {
            $vendordat=[
                'vendor_item_id'=>'',
                'vendor_item_vendor'=>'',
                'vendor_item_number'=>'',
                'vendor_item_name'=>'',
                'vendor_item_blankcost'=>'',
                'vendor_item_cost'=>'',
                'vendor_item_exprint'=>'',
                'vendor_item_setup'=>'',
                'vendor_item_notes'=>'',
                'vendor_item_zipcode'=>'',
                'printshop_item_id'=>'',
                'vendor_name'=>'',
                'vendor_zipcode'=>'',
            ];
            $session_data['vendor']=$vendordat;
            $session_data['vendor_prices']=$this->vendors_model->newitem_vendorprices();
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
            $out['newvendor']=1;
        } else {
            // Get new ID
            $this->db->select('vendor_id, vendor_name, vendor_zipcode');
            $this->db->from('vendors');
            $this->db->where('vendor_name', $vendor_name);
            $chkres = $this->db->get()->result_array();
            if (count($chkres)>1) {
                $out['msg']='Non Unique Vendor name';
            } elseif (count($chkres)==1) {
                if ($chkres[0]['vendor_id']!==$vendordat['vendor_item_vendor']) {
                    // New Vendor
                    $vendordat=[
                        'vendor_item_id'=>'',
                        'vendor_item_vendor'=>$chkres[0]['vendor_id'],
                        'vendor_item_number'=>'',
                        'vendor_item_name'=>'',
                        'vendor_item_blankcost'=>'',
                        'vendor_item_cost'=>'',
                        'vendor_item_exprint'=>'',
                        'vendor_item_setup'=>'',
                        'vendor_item_notes'=>'',
                        'vendor_item_zipcode'=>$chkres[0]['vendor_zipcode'],
                        'printshop_item_id'=>'',
                        'vendor_name'=>$chkres[0]['vendor_name'],
                        'vendor_zipcode'=>'',
                    ];
                    $session_data['vendor']=$vendordat;
                    $session_data['vendor_prices']=$this->vendors_model->newitem_vendorprices();
                    $out['newvendor']=1;
                    $out['result']=$this->success_result;
                    usersession($session_id, $session_data);
                } else {
                    $out['newvendor']=0;
                    $out['result']=$this->success_result;
                    usersession($session_id, $session_data);
                }
            } else {
                // New Vendor
                $vendordat=[
                    'vendor_item_id'=>'',
                    'vendor_item_vendor'=>-1,
                    'vendor_item_number'=>'',
                    'vendor_item_name'=>'',
                    'vendor_item_blankcost'=>'',
                    'vendor_item_cost'=>'',
                    'vendor_item_exprint'=>'',
                    'vendor_item_setup'=>'',
                    'vendor_item_notes'=>'',
                    'vendor_item_zipcode'=>'',
                    'printshop_item_id'=>'',
                    'vendor_name'=>$vendor_name,
                    'vendor_zipcode'=>'',
                ];
                $session_data['vendor']=$vendordat;
                $session_data['vendor_prices']=$this->vendors_model->newitem_vendorprices();
                $out['newvendor']=1;
                $out['result']=$this->success_result;
                usersession($session_id, $session_data);
            }
        }
        return $out;
    }

    public function save_outstockdetails($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Empty Banner'];
        if (isset($postdata['outstock_banner'])) {
            $item = $session_data['item'];
            $item['outstock_banner'] = $postdata['outstock_banner'];
            if (isset($postdata['outstock_link']) && !empty($postdata['outstock_link'])) {
                $out['msg'] = 'Link not correct';
                if (valid_url($postdata['outstock_link'])) {
                    $item['outstock_link']=$postdata['outstock_link'];
                    $out['result']=$this->success_result;
                }
            } else {
                $item['outstock_link']='';
                $out['result']=$this->success_result;
            }
            $session_data['item']=$item;
        }
        usersession($session_id, $session_data);
        return $out;
    }

    // Save item data
    public function save_itemdata($session_data, $session_id, $user_id, $user_role) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $item = $session_data['item'];
        // Check data
        $itemchk = $this->_check_itemdata($item);
        $out['msg']=$itemchk['msg'];
        if ($itemchk['result']==$this->success_result) {
            $old_seq = 0;
            if ($item['item_id']>0) {
                $this->db->select('item_sequence')->from('sb_items')->where('item_id', $item['item_id']);
                $itemres = $this->db->get()->row_array();
                $old_seq = $itemres['item_sequence'];
            }
            // Save Vendor Item
            $vendor = $session_data['vendor'];
            $vendres = $this->_save_vendor($vendor);
            $out['msg']=$vendres['msg'];
            if ($vendres['result']==$this->success_result) {
                $item['vendor_item_id']=$vendres['vendor_item_id'];
                if ($item['outstock']==0) {
                    $item['outstock_banner']=$item['outstock_link']=NULL;
                } else {
                    if (!empty($item['outstock_banner'])) {
                        $pathsrc=$this->config->item('upload_path_preload');
                        $pathsrc_sh=$this->config->item('pathpreload');
                        $imgpath = $this->config->item('itemimages');
                        if (stripos($item['outstock_banner'], $pathsrc_sh)!==FALSE) {
                            $filedet=extract_filename($item['outstock_banner']);
                            $newname='banner_'.time().'.'.$filedet['ext'];
                            $srcbanner = str_replace($pathsrc_sh, $pathsrc, $item['outstock_banner']);
                            $res = @copy($srcbanner, $imgpath.$newname);
                            if (file_exists($imgpath.$newname)) {
                                $item['outstock_banner']=$this->config->item('itemimages_relative').$newname;
                            } else {
                                $item['outstock_banner']=NULL;
                            }
                        }
                    }
                }
                // Save item value
                $res = $this->_save_iteminfo($item, $user_id);
                $out['msg']=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $item_id = $res['item_id'];
                    $terms = $session_data['commons'];
                    // Save commons
                    $termres = $this->_save_itemterms($terms, $item_id);
                    $out['msg']=$termres['msg'];
                    if ($termres['result']==$this->success_result) {
                        $imprints = $this->_prepare_imprintlocation($session_data);
                        $imprres = $this->save_imprintlocations($imprints, $item_id);
                        $out['msg']=$imprres['msg'];
                        if ($imprres['result']==$this->success_result) {
                            $item_images = $this->_prepare_itemimages($session_data, $item_id);
                            $imgres = $this->save_item_images($item_images, $item_id);
                            $out['msg']=$imgres['msg'];
                            if ($imgres['result']==$this->success_result) {
                                $prices = $session_data['item_prices'];
                                if ($item['item_template']==$this->STRESSBALL_TEMPLATE) {
                                    $itmres = $this->save_prices($prices,$item_id);
                                } else {
                                    $commonprices = $session_data['common_prices'];
                                    $this->save_prices($commonprices, $item_id);
                                    $itmres = $this->save_promo_prices($prices,$item_id);
                                }
                                $out['msg']=$itmres['msg'];
                                if ($itmres['result']==$this->success_result) {
                                    // Simular
                                    $simular = $session_data['simular'];
                                    $simres = $this->save_simular($simular, $item_id);
                                    $out['msg']=$simres['msg'];
                                    if ($simres['result']==$this->success_result) {
                                        $colors = $session_data['item_colors'];
                                        $colres = $this->save_colors($colors, $item_id);
                                        $out['msg']=$colres['msg'];
                                        if ($colres['result']==$this->success_result) {
                                            $vendor_prices = $session_data['vendor_prices'];
                                            $vpres = $this->_save_vendor_prices($vendor_prices, $item['vendor_item_id']);
                                            $out['msg']=$vpres['msg'];
                                            if ($vpres['result']==$this->success_result) {
                                                $deleted = $session_data['deleted'];
                                                $this->_remove_old_data($deleted);
                                                $out['result']=$this->success_result;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $out;
    }

    private function _check_itemdata($detal) {
        $out=['result'=>$this->success_result, 'msg' =>''];
        $out_mgs='';
        if (empty(ifset($detal,'item_number',''))) {
            $out_mgs.='Item # required.'.PHP_EOL;
        } else {
            $this->db->select('count(item_id) as cnt')->from('sb_items')->where('item_number',$detal['item_number'])->where('brand ',$detal['brand'])->where('item_id != ',$detal['item_id']);
            $det = $this->db->get()->row_array();
            if ($det['cnt']>0) {
                $out_mgs.='Item Number is not unique'.PHP_EOL;
            }
        }
        /* Add Unique check */
        if (empty(ifset($detal,'item_name',''))) {
            $out_mgs.='Item name required'.PHP_EOL;
        }

        if (intval(ifset($detal,'item_lead_a',0))==0) {
            $out_mgs.='Lead A required'.PHP_EOL;
        }

        if (empty(ifset($detal,'item_material',''))) {
            if ($detal['item_template']==$this->STRESSBALL_TEMPLATE) {
                $out_mgs.='Item material required'.PHP_EOL;
            }
        }
        if (empty(ifset($detal,'item_weigth',0))) {
            $out_mgs.='Item weight required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_size',''))) {
            $out_mgs.='Item size required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_keywords',''))) {
            $out_mgs.='Internal Keywords required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_url',''))) {
            $out_mgs.='Page URL is required field'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_meta_title',''))) {
            $out_mgs.='Meta title required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_metadescription',''))) {
            $out_mgs.='Meta description required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_metakeywords',''))) {
            $out_mgs.='Meta keywords required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_description1',''))) {
            $out_mgs.='Attributes (row 1) required'.PHP_EOL;
        }
        if (isset($detal['item_description2']) && empty($detal['item_description2']) && $detal['item_template']=='Stressball') {
            $out_mgs.='Attributes (row 2) required'.PHP_EOL;
        }
        if (isset($detal['cartoon_qty']) && empty($detal['cartoon_qty'])) {
            $out_mgs.='Shipping Information - Carton Qty - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_qty'])) {
            $out_mgs.='Shipping Information - Carton Qty - must be a number';
        }
        if (isset($detal['cartoon_width']) && empty($detal['cartoon_width'])) {
            $out_mgs.='Shipping Information - Carton Width - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_width'])) {
            $out_mgs.='Shipping Information - Carton Width - must be a number'.PHP_EOL;
        }
        if (isset($detal['cartoon_heigh']) && empty($detal['cartoon_heigh'])) {
            $out_mgs.='Shipping Information - Carton Height - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_heigh'])) {
            $out_mgs.='Shipping Information - Carton Height - must be a number'.PHP_EOL;
        }
        if (isset($detal['cartoon_depth']) && empty($detal['cartoon_depth'])) {
            $out_mgs.='Shipping Information - Carton Depth - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_depth'])) {
            $out_mgs.='Shipping Information - Carton Depth - must be a number'.PHP_EOL;
        }
        if (!is_numeric($detal['charge_pereach'])) {
            $out_mgs.='Shipping Information - Special Shipping charge per each  - must be a number'.PHP_EOL;
        }
        if (!is_numeric($detal['charge_perorder'])) {
            $out_mgs.='Shipping Information - Special Shipping charge per order  - must be a number'.PHP_EOL;
        }
        if ($detal['item_source']==$this->Inventory_Source && empty($detal['printshop_inventory_id'])) {
            $out_mgs.='Choose Inventory Item'.PHP_EOL;
        }
        if ($detal['outstock']==1 && empty($detal['outstock_banner'])) {
            $out_mgs.='Empty Out of Stock Banner'.PHP_EOL;
        }
        if (!empty($out_mgs)) {
            $out['result']=$this->error_result;
            $out['msg']=$out_mgs;
        }
        return $out;
    }

    private function _save_iteminfo($item, $user_id) {
        $this->db->set('item_number', $item['item_number']);
        $this->db->set('item_name', htmlspecialchars_decode($item['item_name']));
        $this->db->set('item_active', $item['item_active']);
        $this->db->set('item_new', $item['item_new']);
        $this->db->set('item_template', $item['item_template']);
        $this->db->set('item_lead_a', $item['item_lead_a']);
        $this->db->set('item_lead_b', empty($item['item_lead_b']) ? null : intval($item['item_lead_b']));
        $this->db->set('item_lead_c', empty($item['item_lead_c']) ? null : intval($item['item_lead_c']));
        $this->db->set('item_material', $item['item_material']);
        $this->db->set('item_weigth', floatval($item['item_weigth']));
        $this->db->set('item_size', htmlspecialchars_decode($item['item_size']));
        $this->db->set('item_keywords', $item['item_keywords']);
        $this->db->set('item_url', $item['item_url']);
        $this->db->set('item_meta_title', htmlspecialchars_decode($item['item_meta_title']));
        $this->db->set('item_metadescription', htmlspecialchars_decode($item['item_metadescription']));
        $this->db->set('item_metakeywords', htmlspecialchars_decode($item['item_metakeywords']));
        $this->db->set('item_description1', htmlspecialchars_decode($item['item_description1']));
        $this->db->set('item_description2',htmlspecialchars_decode($item['item_description2']));
        $this->db->set('item_vector_img', $item['item_vector_img']);
        $this->db->set('vendor_item_id', $item['vendor_item_id']);
        $this->db->set('common_terms', $item['common_terms']);
        $this->db->set('bottom_text', $item['bottom_text']);
        $this->db->set('options', $item['options']);
        $this->db->set('cartoon_qty', $item['cartoon_qty']);
        $this->db->set('cartoon_width', $item['cartoon_width']);
        $this->db->set('cartoon_heigh', $item['cartoon_heigh']);
        $this->db->set('cartoon_depth', $item['cartoon_depth']);
        $this->db->set('boxqty', $item['boxqty']);
        $this->db->set('charge_pereach', $item['charge_pereach']);
        $this->db->set('charge_perorder', $item['charge_perorder']);
        $this->db->set('faces', $item['faces']);
        $this->db->set('special', $item['special']);
        $this->db->set('category_id', $item['category_id']);
        $this->db->set('special_checkout', $item['special_checkout']);
        $this->db->set('special_shipping', $item['special_shipping']);
        $this->db->set('special_setup', $item['special_setup']);
        $this->db->set('item_source', $item['item_source']);
        $this->db->set('printshop_inventory_id', $item['printshop_inventory_id']);
        $this->db->set('update_template',$item['update_template']);
        $this->db->set('imprint_update', $item['imprint_update']);
        $this->db->set('item_sequence', $item['item_sequence']);
        $this->db->set('item_sale', $item['item_sale']);
        $this->db->set('printlocat_example_img', $item['printlocat_example_img']);
        $this->db->set('itemcolor_example_img', $item['itemcolor_example_img']);
        $this->db->set('outstock', $item['outstock']);
        $this->db->set('outstock_banner', $item['outstock_banner']);
        $this->db->set('outstock_link', $item['outstock_link']);
        // $this->db->set('shipping_info', $item['shipping_info']);
        if ($item['item_id']==0) {
            $this->db->set('create_user', $user_id);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('update_user', $user_id);
            $this->db->insert('sb_items');
            $newrec = $this->db->insert_id();
            if ($newrec==0) {
                $out=['result'=> $this->error_result, 'msg'=>'Error during add item data'];
            } else {
                $out=['result'=>$this->success_result, 'msg'=> '', 'item_id'=>$newrec];
            }
        } else {
            $this->db->set('update_user', $user_id);
            $this->db->where('item_id', $item['item_id']);
            $this->db->update('sb_items');
            $out=['result'=>$this->success_result, 'msg'=> '', 'item_id'=>$item['item_id']];
        }
        return $out;
    }

    private function _save_itemterms($terms, $item_id) {
        foreach ($terms as $row) {
            if ($row['term_id']>0) {
                $this->db->where('term_id', $row['term_id']);
                if (empty($row['common_term'])) {
                    $this->db->delete('sb_item_commonterms');
                } else {
                    $this->db->set('common_term', $row['common_term']);
                    $this->db->update('sb_item_commonterms');
                }
            } else {
                if (!empty($row['common_term'])) {
                    $this->db->set('item_id', $item_id);
                    $this->db->set('common_term', $row['common_term']);
                    $this->db->insert('sb_item_commonterms');
                }
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    private function _prepare_imprintlocation($session_data) {
        $full_path = $this->config->item('imprint_images_relative');
        createPath($full_path);
        $short_path = $this->config->item('imprint_images');
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        $imprints = $session_data['imprints'];
        $idx = 0;
        foreach ($imprints as $imprint) {
            if (!empty($imprint['item_inprint_view']) && stripos($imprint['item_inprint_view'],$path_preload_short)!==FALSE) {
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $imprint['item_inprint_view']);
                $imagedetails = extract_filename($imprint['item_inprint_view']);
                $filename = uniq_link(15,'chars').'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $full_path.$filename);
                $imprints[$idx]['item_inprint_view']='';
                if ($res) {
                    $imprints[$idx]['item_inprint_view']=$short_path.$filename;
                }
            }
            $idx++;
        }
        return $imprints;
    }

    private function save_imprintlocations($imprints, $item_id) {
        foreach ($imprints as $item) {
            if (!empty($item['item_inprint_location'])) {
                $this->db->set('item_inprint_location', $item['item_inprint_location']);
                $this->db->set('item_inprint_size', $item['item_inprint_size']);
                $this->db->set('item_inprint_view', $item['item_inprint_view']);
                $this->db->set('item_imprint_mostpopular', $item['item_imprint_mostpopular']);
                if ($item['item_inprint_id']>0) {
                    $this->db->where('item_inprint_id', $item['item_inprint_id']);
                    $this->db->update('sb_item_inprints');
                } else {
                    $this->db->set('item_inprint_item', $item_id);
                    $this->db->insert('sb_item_inprints');
                }
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;

    }

    private function _prepare_itemimages($session_data, $item_id) {
        $full_path = $this->config->item('item_images_relative');
        createPath($full_path);
        $short_path = $this->config->item('item_images');
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        $images = $session_data['item_images'];
        $idx = 0;
        foreach ($images as $image) {
            if (!empty($image['src']) && stripos($image['src'],$path_preload_short)!==FALSE) {
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $image['src']);
                $imagedetails = extract_filename($image['src']);
                $filename = uniq_link(15,'chars').'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $full_path.$filename);
                $images[$idx]['src']='';
                if ($res) {
                    $images[$idx]['src']=$short_path.$filename;
                }
            }
            $idx++;
        }
        return $images;
    }

    private function save_item_images($item_images, $item_id) {
        $idx = 1;
        foreach ($item_images as $image) {
            if (!empty($image['src'])) {
                $this->db->set('item_img_name', $image['src']);
                $this->db->set('item_img_order', $idx);
                if ($image['item_img_id']>0) {
                    $this->db->where('item_img_id',$image['item_img_id']);
                    $this->db->update('sb_item_images');
                } else {
                    $this->db->set('item_img_item_id', $item_id);
                    $this->db->insert('sb_item_images');
                }
                $idx++;
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    private function _remove_old_data($deleted) {
        foreach ($deleted as $row) {
            if ($row['entity']=='imprints') {
                $this->db->where('item_inprint_id', $row['key']);
                $this->db->delete('sb_item_inprints');
            } elseif ($row['entity']=='item_images') {
                $this->db->where('item_img_id', $row['key']);
                $this->db->delete('sb_item_images');
            }
        }
    }

    private function save_prices($prices,$item_id) {
        $price_types = $this->config->item('price_types');
        foreach ($price_types as $item) {
            $type=$item['type'];
            $this->db->set('item_price_'.$type,(empty($prices['item_price_'.$type]) ? NULL : floatval($prices['item_price_'.$type])));
            $this->db->set('item_sale_'.$type,(empty($prices['item_sale_'.$type]) ? NULL : floatval($prices['item_sale_'.$type])));
            $this->db->set('profit_'.$type, (empty($prices['profit_'.$type])? NULL : floatval($prices['profit_'.$type])));
        }
        // item_price_print: "0.35"
        // item_price_setup: "50.00"
        $this->db->set('item_price_print',(empty($prices['item_price_print']) ? NULL : floatval($prices['item_price_print'])));
        $this->db->set('item_sale_print',(empty($prices['item_sale_print']) ? NULL : floatval($prices['item_sale_print'])));
        $this->db->set('profit_print', (empty($prices['profit_print'])? NULL : floatval($prices['profit_print'])));
        $this->db->set('item_price_setup',(empty($prices['item_price_setup']) ? NULL : floatval($prices['item_price_setup'])));
        $this->db->set('item_sale_setup',(empty($prices['item_sale_setup']) ? NULL : floatval($prices['item_sale_setup'])));
        $this->db->set('profit_setup', (empty($prices['profit_setup'])? NULL : floatval($prices['profit_setup'])));
        if ($prices['item_price_id']<0) {
            $this->db->set('item_price_itemid', $item_id);
            $this->db->insert('sb_item_prices');
        } else {
            $this->db->where('item_price_id', $prices['item_price_id']);
            $this->db->update('sb_item_prices');
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    private function save_promo_prices($promo_price, $item_id) {
        foreach ($promo_price as $promo) {
            if (intval($promo['item_qty'])!=0) {
                /* Exist */
                $base=0;
                if (floatval($promo['sale_price'])!=0) {
                    $base=floatval($promo['sale_price']);
                } elseif (floatval($promo['price'])!=0) {
                    $base=floatval($promo['price']);
                }
                if ($base!=0) {
                    // $this->db->select('get_profit_qty(' . $base . ' , ' . $item_id . ' , ' . $promo['item_qty']. ' ) as itm_profit', FALSE);
                    // $prof = $this->db->get()->row_array();
                    // if ($prof['itm_profit']) {
                    //    $this->db->set('profit',$prof['itm_profit']);
                    // } else {
                    //    $this->db->set('profit',NULL);
                    // }
                    $this->db->set('profit',$promo['profit']);
                } else {
                    $this->db->set('profit',NULL);
                }
                $this->db->set('item_qty',$promo['item_qty']);
                $this->db->set('price',(floatval($promo['price'])==0 ? NULL : floatval($promo['price'])));
                $this->db->set('sale_price',(floatval($promo['sale_price'])==0 ? NULL : floatval($promo['sale_price'])));
                if ($promo['promo_price_id']>0) {
                    $this->db->where('promo_price_id',$promo['promo_price_id']);
                    $this->db->update('sb_promo_price');
                } else {
                    $this->db->set('item_id',$item_id);
                    $this->db->insert('sb_promo_price');
                }
            } else {
                if ($promo['promo_price_id']>0) {
                    $this->db->where('promo_price_id',$promo['promo_price_id']);
                    $this->db->delete('sb_promo_price');
                }
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    private function save_simular($simular, $item_id) {
        foreach ($simular as $row) {
            if (empty($row['item_similar_similar'])) {
                $this->db->where('item_similar_id', $row['item_similar_id']);
                $this->db->delete('sb_item_similars');
            } else {
                $this->db->set('item_similar_similar', $row['item_similar_similar']);
                if ($row['item_similar_id']<0) {
                    $this->db->set('item_similar_item', $item_id);
                    $this->db->insert('sb_item_similars');
                } else {
                    $this->db->where('item_similar_id', $row['item_similar_id']);
                    $this->db->update('sb_item_similars');
                }
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    private function save_colors($colors, $item_id) {
        foreach ($colors as $row) {
            if (empty($row['item_color'])) {
                if ($row['item_color_id']>0) {
                    $this->db->where('item_color_id', $row['item_color_id']);
                    $this->db->delete('sb_item_colors');
                }
            } else {
                $this->db->set('item_color', $row['item_color']);
                if ($row['item_color_id']>0) {
                    $this->db->where('item_color_id', $row['item_color_id']);
                    $this->db->update('sb_item_colors');
                } else {
                    $this->db->set('item_color_itemid', $item_id);
                    $this->db->insert('sb_item_colors');
                }
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    private function _save_vendor($vendor) {
        $out=['result'=>$this->error_result, 'msg'=> 'Empty Vendor Item'];
        if (!empty($vendor['vendor_item_id'])) {
            $this->db->set('vendor_item_vendor', $vendor['vendor_item_vendor']);
            $this->db->set('vendor_item_number', $vendor['vendor_item_number']);
            $this->db->set('vendor_item_name', $vendor['vendor_item_name']);
            $this->db->set('vendor_item_blankcost', $vendor['vendor_item_blankcost']);
            $this->db->set('vendor_item_cost', $vendor['vendor_item_cost']);
            $this->db->set('vendor_item_exprint', $vendor['vendor_item_exprint']);
            $this->db->set('vendor_item_setup', $vendor['vendor_item_setup']);
            $this->db->set('vendor_item_notes', $vendor['vendor_item_notes']);
            $this->db->set('vendor_item_zipcode', $vendor['vendor_item_zipcode']);
            if ($vendor['vendor_item_id']>0) {
                $this->db->where('vendor_item_id', $vendor['vendor_item_id']);
                $this->db->update('sb_vendor_items');
                $out['result']=$this->success_result;
                $out['vendor_item_id']=$vendor['vendor_item_id'];
            } else {
                $this->db->insert('sb_vendor_items');
                $newitem=$this->db->insert_id();
                if ($newitem>0) {
                    $out['result']=$this->success_result;
                    $out['vendor_item_id']=$newitem;
                }
            }
        }
        return $out;
    }

    private function _save_vendor_prices($vendor_prices, $item_id) {
        foreach ($vendor_prices as $vrow) {
            if (empty($vrow['vendorprice_qty'])) {
                if ($vrow['vendorprice_id']>0) {
                    $this->db->where('vendorprice_id', $vrow['vendorprice_id']);
                    $this->db->delete('sb_vendor_prices');
                }
            } else {
                $this->db->set('vendorprice_qty', $vrow['vendorprice_qty']);
                $this->db->set('vendorprice_val', $vrow['vendorprice_val']);
                $this->db->set('vendorprice_color', $vrow['vendorprice_color']);
                if ($vrow['vendorprice_id']>0) {
                    $this->db->where('vendorprice_id', $vrow['vendorprice_id']);
                    $this->db->update('sb_vendor_prices');
                } else {
                    $this->db->set('vendor_item_id', $item_id);
                    $this->db->insert('sb_vendor_prices');
                }
            }
        }
        $out=['result'=>$this->success_result, 'msg'=> ''];
        return $out;
    }

    public function save_imagesorder($data, $session_data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $images=array();
        $i=1;
        foreach ($data as $key=>$value) {
            if ($key!=='session_id') {
                $imgdata=explode('|', $value);
                $images[]=array(
                    'item_img_id'=>str_replace('it', '',$key),
                    'item_img_item_id' => $imgdata[0],
                    'src'=>($imgdata[1]=='/img/dbitems/thamb-bg.png' ? '' :$imgdata[1]),
                    'name'=>($i==1 ? 'Main Pic' : 'Pic '.$i),
                    'item_img_order'=>$i,
                );
                $i++;
            }
        }
        $session_data['item_images'] = $images;
        usersession($session_id, $session_data);
        $out['result'] = $this->success_result;
        $out['images'] = $images;
        return $out;
    }
}