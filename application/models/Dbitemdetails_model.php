<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Dbitemdetails_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function change_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter', 'oldvalue' => ''];
        $entity = ifset($data,'entity','noname');
        $out['entity']=$entity;
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $key = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $out['oldvalue'] = $item[$fld];
                if ($fld=='item_number') {
                    $chkres = $this->_check_item_number($newval, $item['item_id'], $item['brand']);
                    $out['msg'] = $chkres['msg'];
                    if ($chkres['result'] == $this->success_result) {
                        $item[$fld] = $newval;
                        $session_data['item'] = $item;
                        usersession($session_id, $session_data);
                        $out['msg'] = '';
                        $out['result'] = $this->success_result;
                    }
                } else {
                    if ($fld=='item_sale' || $fld=='item_new' || $fld=='sellblank' || $fld=='sellcolor' || $fld=='sellcolors') {
                        $newval = 1;
                        if ($item[$fld]==1) {
                            $newval = 0;
                        }
                    }
                    $item[$fld]=$newval;
                    $session_data['item']=$item;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                }
            }
        } elseif ($entity=='similar') {
            $out['msg']='Item Simular Not Found';
            $items = ifset($session_data,'similar', []);
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
        } elseif ($entity=='vendor_item') {
            $out['msg']='Vendor Item Not Found';
            $vendor = ifset($session_data,'vendor_item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld, $vendor)) {
                $out['oldvalue'] = $vendor[$fld];
                $vendor[$fld] = $newval;
                $session_data['vendor_item']=$vendor;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='shipping') {
            $out['msg']='Item Shipping Parameter Not Found';
            $items = ifset($session_data,'prices', []);
            $idx = 0;
            foreach ($items as $item) {
                if ($item['promo_price_id']==$key) {
                    $items[$idx][$fld]=$newval;
                    $session_data['prices']=$items;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='colors') {
            $out['msg']='Item Option Not Found';
            $items = ifset($session_data,'colors', []);
            $idx = 0;
            foreach ($items as $item) {
                if ($item['item_color_id']==$key) {
                    $items[$idx][$fld]=$newval;
                    $session_data['colors']=$items;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='priceshow') {
            $out['msg']='Item Price Not Found';
            $items = ifset($session_data,'prices', []);
            $idx = 0;
            $found=0;
            foreach ($items as $item) {
                if ($item['promo_price_id']==$key) {
                    $items[$idx]['show_first']=1;
                    $found = 1;
                } else {
                    $items[$idx]['show_first'] = 0;
                }
                $idx++;
            }
            if ($found==1) {
                $session_data['prices']=$items;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='images') {
            $out['msg']='Item Image Not Found';
            $items = ifset($session_data,'images', []);
            $idx = 0;
            $found=0;
            foreach ($items as $item) {
                if ($item['item_img_id']==$key) {
                    $items[$idx][$fld] = $newval;
                    $found = 1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $session_data['images']=$items;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        }
        $out['fld'] = $fld;
        $out['newval'] = $newval;
        $out['entity'] = $entity;
        return $out;
    }

    public function check_vendor($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $vendor_name = ifset($postdata,'vendor_name','');
        $this->load->model('vendors_model');
        if (empty($vendor_name)) {
            $vendordat = [
                'vendor_item_id' => '',
                'vendor_item_vendor' => '',
                'vendor_item_number' => '',
                'vendor_item_name' => '',
                'vendor_item_blankcost' => '',
                'vendor_item_cost' => '',
                'vendor_item_exprint' => '',
                'vendor_item_setup' => '',
                'vendor_item_notes' => '',
                'vendor_item_zipcode' => '',
                'printshop_item_id' => '',
                'vendor_name' => '',
                'vendor_zipcode' => '',
            ];
            $session_data['vendor_item']=$vendordat;
            $session_data['vendor_price']=$this->vendors_model->newitem_vendorprices();
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
            $out['newvendor']=1;
        } else {
            $this->db->select('vendor_id, vendor_name, vendor_zipcode');
            $this->db->from('vendors');
            $this->db->where('vendor_name', $vendor_name);
            $this->db->where('vendor_type','Supplier');
            $this->db->where('vendor_status', 1);
            $chkres = $this->db->get()->result_array();
            if (count($chkres)>1) {
                $out['msg']='Non Unique Vendor name';
            } elseif (count($chkres)==0) {
                // New Vendor
                $vendordat = [
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
                $session_data['vendor_item']=$vendordat;
                $session_data['vendor_price']=$this->vendors_model->newitem_vendorprices();
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
                $out['newvendor']=1;
            } elseif (count($chkres)==1) {
                $vendordat = $session_data['vendor_item'];
                if ($vendordat['vendor_item_vendor']!=$chkres[0]['vendor_id']) {
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
                    $session_data['vendor_item']=$vendordat;
                    $session_data['vendor_price']=$this->vendors_model->newitem_vendorprices();
                    $out['newvendor']=1;
                    $out['result']=$this->success_result;
                    usersession($session_id, $session_data);
                } else {
                    $out['newvendor']=0;
                    $out['result']=$this->success_result;
                    usersession($session_id, $session_data);
                }
            }
        }
        return $out;
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
                'vendor_item_zipcode'=> '',
                'vendor_item_blankcost' => '',
                'vendor_name' => '',
                'vendor_zipcode' => '',
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
                    'vendor_item_zipcode'=>'',
                    'vendor_item_blankcost' => '',
                    'vendor_name' => '',
                    'vendor_zipcode' => '',
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
                    'vendor_name' => $res['vendor_name'],
                    'vendor_zipcode' => $res['vendor_zipcode'],
                ];
                $vendor_prices=$this->vendors_model->get_vedorprice_item($res['vendor_item_id'],0);
            }
        }
        $out['result']=$this->success_result;
        $out['data']=$data;
        $session_data['vendor_item']=$data;
        $session_data['vendor_price']=$vendor_prices;
        $out['vendor_prices']=$vendor_prices;
        usersession($session_id, $session_data);
        return $out;
    }

    public function change_price($data, $session_data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $out['entity']=$entity;
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $key = ifset($data,'idx',0);
        $this->load->model('prices_model');
        if ($entity=='vendor_price') {
            $out['msg']='Vendor Price Not Found';
            $items = ifset($session_data,'vendor_price', []);
            $idx = 0;
            $found=0;
            foreach ($items as $item) {
                if ($item['vendorprice_id']==$key) {
                    $found=1;
                    $items[$idx][$fld] = $newval;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $session_data['vendor_price']=$items;
                // Recalc Profit %
                $prices = $session_data['prices'];
                $session_data['prices'] = $this->prices_model->recalc_promo_profit($items, $prices);
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='vendor_specprice') {
            $out['msg']='Vendor Price Not Found';
            $items=ifset($session_data,'item',[]);
            $vendor_item = $session_data['vendor_item'];
            if (array_key_exists($fld, $vendor_item)) {
                $vendor_item[$fld] = $newval;
                // Calc Special promo
                $newitems = $this->prices_model->recalc_setup_profit($items, $vendor_item);
                $session_data['item'] = $newitems;
                $session_data['vendor_item'] = $vendor_item;
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='item_price') {
            $out['msg']='Price Not Found';
            $prices = ifset($session_data,'prices', []);
            $idx = 0;
            $found=0;
            foreach ($prices as $price) {
                if ($price['promo_price_id']==$key) {
                    $found=1;
                    $prices[$idx][$fld] = $newval;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                // Recalc Profit %
                $vendor_prices = $session_data['vendor_price'];
                $session_data['prices'] = $this->prices_model->recalc_promo_profit($vendor_prices, $prices);
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='item_specprice') {
            $out['msg']='Price Not Found';
            $items=ifset($session_data,'item',[]);
            if (array_key_exists($fld, $items)) {
                $items[$fld] = $newval;
                $vendor_item = $session_data['vendor_item'];
                // Calc Special promo
                $newitems = $this->prices_model->recalc_setup_profit($items, $vendor_item);
                $session_data['item'] = $newitems;
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function remove_inprint($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Area not found'];
        $key = ifset($data,'idx',0);
        $inprints = ifset($session_data, 'inprints', []);
        $found = 0;
        $newimprint = [];
        $deleted = ifset($session_data,'deleted',[]);
        foreach ($inprints as $inprint) {
            if ($inprint['item_inprint_id']==$key) {
                $found=1;
                if ($key > 0) {
                    $deleted[] = [
                        'entity' => 'inprints',
                        'id' => $key,
                    ];
                }
            } else {
                $newimprint[] = $inprint;
            }
        }
        if ($found==1) {
            $out['result'] = $this->success_result;
            $session_data['inprints'] = $newimprint;
            $session_data['deleted'] = $deleted;
            usersession($session_id, $session_data);
            $out['inprints'] = $newimprint;
        }
        return $out;
    }

    public function get_inprint_area($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Area not found'];
        $key = ifset($data,'idx',0);
        if ($key==0) {
            $out['result']=$this->success_result;
            usersession($session_id, $session_data);
            $out['inprint'] = [
                'item_inprint_id' => 0,
                'item_inprint_item' => 0,
                'item_inprint_location' => '',
                'item_inprint_size' => '',
                'item_inprint_view' => '',
                'item_imprint_mostpopular' => '',
            ];

        } else {
            $inprints = $session_data['inprints'];
            foreach ($inprints as $inprint) {
                if ($inprint['item_inprint_id']==$key) {
                    $out['result'] = $this->success_result;
                    $out['inprint'] = $inprint;
                    usersession($session_id, $session_data);
                    break;
                }
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
            $imprints = $session_data['inprints'];
            $key = $imprint['item_inprint_id'];
            if ($key==0) {
                // New imprint area
                $minidx = 0;
                foreach ($imprints as $row) {
                    if ($row['item_inprint_id'] < $minidx) {
                        $minidx = $row['item_inprint_id'];
                    }
                }
                $imprints[] = [
                    'item_inprint_id' => ($minidx - 1),
                    'item_inprint_item' => $imprint['item_inprint_item'],
                    'item_inprint_location' => $imprint['item_inprint_location'],
                    'item_inprint_size' => $imprint['item_inprint_size'],
                    'item_inprint_view' => $imprint['item_inprint_view'],
                    'item_imprint_mostpopular' => $imprint['item_imprint_mostpopular'],
                ];
                $out['result']=$this->success_result;
                $out['imprints']=$imprints;
                $session_data['inprints']=$imprints;
                usersession($session_id, $session_data);
                usersession($imprsession, NULL);
            } else {
                // Edit exist
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
        }
        return $out;
    }

    public function change_picture($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item Image Not found'];
        $images = ifset($session_data,'images', []);
        $key = ifset($data,'idx', 0);
        $found = 0;
        $idx = 0;
        foreach ($images as $image) {
            if ($image['item_img_id']==$key) {
                $found=1;
                break;
            }
            $idx++;
        }
        if ($found==1) {
            $images[$idx]['item_img_name'] = ifset($data,'newval','');
            $session_data['images'] = $images;
            usersession($session_id, $session_data);
            $out['result'] = $this->success_result;
            $out['images'] = $images;
        }
        return $out;
    }

    public function delete_picture($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item Image Not found'];
        // slider_images
        $images = ifset($session_data,'images', []);
        $deleted = ifset($session_data, 'deleted',[]);
        $key = ifset($data,'idx', 0);
        $found = 0;
        $newimages = [];
        $minidx = 0;
        $numpp = 1;
        foreach ($images as $image) {
            if ($image['item_img_id']==$key) {
                $found=1;
                if ($key > 0) {
                    $deleted[] = [
                        'entity' => 'images',
                        'id' => $key,
                    ];
                }
            } else {
                if ($image['item_img_id']<$minidx) {
                    $minidx = $image['item_img_id'];
                }
                $image['item_img_order'] = $numpp;
                $image['title'] = ($numpp==1 ? 'Main Pic' : 'Pic '.$numpp);
                $newimages[] = $image;
                $numpp++;
            }
        }
        if ($found==1) {
            // Add new image
            $minidx = $minidx -1;
            $newimages[] = [
                'item_img_id' => $minidx,
                'item_img_name' => '',
                'item_img_thumb' => '',
                'item_img_order' => $numpp,
                'item_img_big' => '',
                'item_img_medium' => '',
                'item_img_small' => '',
                'item_img_label' => '',
                'title' => 'Pic '.$numpp,
            ];
            $session_data['images'] = $newimages;
            $session_data['deleted'] = $deleted;
            usersession($session_id, $session_data);
            $out['result'] = $this->success_result;
            $out['images'] = $newimages;
        }
        return $out;
    }

    public function sort_picture_prepare($session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item Image Not found'];
        $images = ifset($session_data,'images', []);
        $imgsort = [];
        foreach ($images as $image) {
            if (!empty($image['item_img_name'])) {
                $imgsort[] = $image;
            }
        }
        $out['result'] = $this->success_result;
        usersession($session_id, $session_data);
        $out['images'] = $imgsort;
        return $out;
    }

    public function sort_picture_save($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item Image Not found'];
        $images = ifset($session_data,'images', []);
        $imgsort = [];
        foreach ($data as $key=>$val) {
            if (substr($key,0,5)=='sort_') {
                array_push($imgsort, $val);
            }
        }
        $newimages = [];
        $numpp = 1;
        foreach ($imgsort as $idx) {
            foreach ($images as $image) {
                if ($image['item_img_id']==$idx) {
                    if ($numpp==1) {
                        $image['title'] = 'Main Pic';
                    } else {
                        $image['title'] = 'Pic '.$numpp;
                    }
                    $newimages[] = $image;
                    $numpp++;
                }
            }
        }
        if (count($newimages) < $this->config->item('slider_images')) {
            $idx = 1;
            for ($i=count($newimages); $i<=$this->config->item('slider_images'); $i++) {
                $newimages[] = [
                    'item_img_id' => ($idx * -1),
                    'item_img_name' => '',
                    'item_img_thumb' => '',
                    'item_img_order' => $numpp,
                    'item_img_big' => '',
                    'item_img_medium' => '',
                    'item_img_small' => '',
                    'item_img_label' => '',
                    'title' => 'Pic '.$numpp,
                ];
                $idx++;
                $numpp++;
            }
        }
        $out['result'] = $this->success_result;
        $out['images'] = $newimages;
        $session_data['images'] = $newimages;
        usersession($session_id, $session_data);
        return $out;
    }

    public function save_itemdetails($session_data, $session_id, $usr_id, $usr_role) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        // Check
        $itemchk = $this->_check_itemdetails($session_data);
        $out['msg']=$itemchk['msg'];
        if ($itemchk['result']==$this->success_result) {
            $item = $session_data['item'];
            $vendor_item = $session_data['vendor_item'];
            $vendor_price = $session_data['vendor_price'];
            // Lets Go
            $vendorres = $this->_save_vendoritem($vendor_item, $vendor_price);
            $out['msg'] = $vendorres['msg'];
            if ($vendorres['result']==$this->success_result) {
                $item['vendor_item_id'] = $vendorres['vendor_item_id'];
                $datares = $this->_save_itemdata($item, $usr_id);
                $out['msg'] = $datares['msg'];
                if ($datares['result']==$this->success_result) {
                    $item_id = $datares['item_id'];
                    // Item options
                    $this->_save_item_colors($session_data['colors'], $item_id);
                    // Item Iages
                    $this->_save_item_images($session_data['images'], $item_id);
                    // Inprints
                    $this->_save_item_inprints($session_data['inprints'], $item_id);
                    // Prices
                    $this->_save_item_prices($session_data['prices'], $item_id);
                    // Similar
                    $this->_save_item_similar($session_data['similar'], $item_id);
                    // Deleted objects
                    $this->_clean_details($session_data['deleted']);
                    $out['result'] = $this->success_result;
                }
            }
        }
        return $out;
    }

    private function _clean_details($deleted) {
        foreach ($deleted as $row) {
            if ($row['entity']=='inprints') {
                $this->db->where('item_inprint_id', $row['id']);
                $this->db->delete('sb_item_inprints');
            } elseif ($row['entity']=='images') {
                $this->db->where('item_img_id', $row['id']);
                $this->db->delete('sb_item_images');
            }
        }
        return true;
    }

    private function _save_item_similar($similars, $item_id) {
        foreach ($similars as $similar) {
            if (!empty($similar['item_similar_similar'])) {
                $this->db->set('item_similar_item', $item_id);
                $this->db->set('item_similar_similar', $similar['item_similar_similar']);
                if ($similar['item_similar_id'] > 0 ) {
                    $this->db->where('item_similar_id', $similar['item_similar_id']);
                    $this->db->update('sb_item_similars');
                } else {
                    $this->db->insert('sb_item_similars');
                }
            } else {
                if ($similar['item_similar_id'] > 0) {
                    $this->db->where('item_similar_id', $similar['item_similar_id']);
                    $this->db->delete('sb_item_similars');
                }
            }
        }
        return true;
    }

    public function _save_item_prices($prices, $item_id) {
        foreach ($prices as $price) {
            if (intval($price['item_qty']) > 0 && (floatval($price['price']) > 0 || floatval($price['sale_price']) > 0)) {
                $this->db->set('item_id', $item_id);
                $this->db->set('item_qty', $price['item_qty']);
                $this->db->set('price', $price['price']);
                $this->db->set('sale_price', $price['sale_price']);
                $this->db->set('profit', $price['profit']);
                $this->db->set('show_first', $price['show_first']);
                $this->db->set('shipbox', $price['shipbox']);
                $this->db->set('shipweight', $price['shipweight']);
                if ($price['promo_price_id'] > 0) {
                    $this->db->where('promo_price_id', $price['promo_price_id']);
                    $this->db->update('sb_promo_price');
                } else {
                    $this->db->insert('sb_promo_price');
                }
            }
        }
        return true;
    }

    private function _save_item_inprints($inprints, $item_id) {
        $full_path = $this->config->item('imprintimages').$item_id.'/';
        createPath($full_path);
        $short_path = $this->config->item('imprintimages_relative').$item_id.'/';
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        foreach ($inprints as $inprint) {
            if (!empty($inprint['item_inprint_location']) && !empty($inprint['item_inprint_view'])) {
                if (stripos($inprint['item_inprint_view'],$path_preload_short)!==FALSE) {
                    $imagesrc = str_replace($path_preload_short, $path_preload_full, $inprint['item_inprint_view']);
                    $imagedetails = extract_filename($inprint['item_inprint_view']);
                    $filename = uniq_link(15,'chars').'.'.$imagedetails['ext'];
                    $res = @copy($imagesrc, $full_path.$filename);
                    $inprint['item_inprint_view']='';
                    if ($res) {
                        $inprint['item_inprint_view']=$short_path.$filename;
                    }
                }
                if ($inprint['item_inprint_view']!=='') {
                    $this->db->set('item_inprint_item', $item_id);
                    $this->db->set('item_inprint_location', $inprint['item_inprint_location']);
                    $this->db->set('item_inprint_size', $inprint['item_inprint_size']);
                    $this->db->set('item_inprint_view', $inprint['item_inprint_view']);
                    if ($inprint['item_inprint_id'] > 0 ) {
                        $this->db->where('item_inprint_id', $inprint['item_inprint_id']);
                        $this->db->update('sb_item_inprints');
                    } else {
                        $this->db->insert('sb_item_inprints');
                    }
                }
            }
        }
        return true;
    }

    private function _save_item_images($images, $item_id) {
        $full_path = $this->config->item('itemimages').$item_id.'/';
        createPath($full_path);
        $short_path = $this->config->item('itemimages_relative').$item_id.'/';
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        $numpp = 1;
        foreach ($images as $image) {
            if (!empty($image['item_img_name'])) {
                if (stripos($image['item_img_name'],$path_preload_short)!==FALSE) {
                    // New Image
                    $imagesrc = str_replace($path_preload_short, $path_preload_full, $image['item_img_name']);
                    $imagedetails = extract_filename($image['item_img_name']);
                    $filename = uniq_link(15,'chars').'.'.$imagedetails['ext'];
                    $res = @copy($imagesrc, $full_path.$filename);
                    $image['item_img_name']='';
                    if ($res) {
                        $image['item_img_name']=$short_path.$filename;
                    }
                }
                if (!empty($image['item_img_name'])) {
                    $this->db->set('item_img_item_id', $item_id);
                    $this->db->set('item_img_name', $image['item_img_name']);
                    $this->db->set('item_img_label', $image['item_img_label']);
                    $this->db->set('item_img_order', $numpp);
                    if ($image['item_img_id'] > 0) {
                        $this->db->where('item_img_id', $image['item_img_id']);
                        $this->db->update('sb_item_images');
                    } else {
                        $this->db->insert('sb_item_images');
                    }
                    $numpp++;
                }
            }
        }
    }

    private function _save_item_colors($colors, $item_id) {
        foreach ($colors as $color) {
            if (!empty($color['item_color'])) {
                $this->db->set('item_color_itemid', $item_id);
                $this->db->set('item_color', $color['item_color']);
                if ($color['item_color_id'] > 0) {
                    $this->db->where('item_color_id', $color['item_color_id']);
                    $this->db->update('sb_item_colors');
                } else {
                    $this->db->insert('sb_item_colors');
                }
            } else {
                if ($color['item_color_id'] > 0) {
                    $this->db->where('item_color_id', $color['item_color_id']);
                    $this->db->delete('sb_item_colors');
                }
            }
        }
        return true;
    }

    private function _save_itemdata($item, $user_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item Details Save - Unknown Error'];
        // Check new images
        $full_path = $this->config->item('contents_images_relative');
        createPath($full_path);
        $short_path = $this->config->item('contents_images');
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        if (!empty($item['printlocat_example_img']) && stripos($item['printlocat_example_img'],$path_preload_short)!==FALSE) {
            $imagesrc = str_replace($path_preload_short, $path_preload_full, $item['printlocat_example_img']);
            $imagedetails = extract_filename($item['printlocat_example_img']);
            $filename = uniq_link(15,'chars').'.'.$imagedetails['ext'];
            $res = @copy($imagesrc, $full_path.$filename);
            $item['printlocat_example_img']='';
            if ($res) {
                $item['printlocat_example_img']=$short_path.$filename;
            }
        }
        $full_path = $this->config->item('item_template_relative');
        createPath($full_path);
        $short_path = $this->config->item('item_template');
        if (!empty($item['item_vector_img']) && stripos($item['item_vector_img'],$path_preload_short)!==FALSE) {
            $imagesrc = str_replace($path_preload_short, $path_preload_full, $item['item_vector_img']);
            $imagedetails = extract_filename($item['item_vector_img']);
            $filename = uniq_link(15,'chars').'.'.$imagedetails['ext'];
            $res = @copy($imagesrc, $full_path.$filename);
            $item['item_vector_img']='';
            if ($res) {
                $item['item_vector_img']=$short_path.$filename;
            }
        }
        // Save
        $this->db->set('item_number', $item['item_number']);
        $this->db->set('item_name', $item['item_name']);
        $this->db->set('item_active', $item['item_active']);
        $this->db->set('item_new', $item['item_new']);
//        $this->db->set('item_template', $item['item_template']);
        $this->db->set('item_lead_a', $item['item_lead_a']);
        $this->db->set('item_lead_b', empty($item['item_lead_b']) ? null : intval($item['item_lead_b']));
        $this->db->set('item_lead_c', empty($item['item_lead_c']) ? null : intval($item['item_lead_c']));
        $this->db->set('item_material', $item['item_material']);
//        $this->db->set('item_weigth', floatval($item['item_weigth']));
        $this->db->set('item_size', $item['item_size']);
        $this->db->set('item_keywords', $item['item_keywords']);
        $this->db->set('item_url', $item['item_url']);
        $this->db->set('item_meta_title', $item['item_meta_title']);
        $this->db->set('item_metadescription', $item['item_metadescription']);
        $this->db->set('item_metakeywords', $item['item_metakeywords']);
        $this->db->set('item_description1', $item['item_description1']);
        $this->db->set('item_description2',$item['item_description2']);
        $this->db->set('item_vector_img', $item['item_vector_img']);
        $this->db->set('vendor_item_id', $item['vendor_item_id']);
//        $this->db->set('common_terms', $item['common_terms']);
//        $this->db->set('bottom_text', $item['bottom_text']);
        $this->db->set('options', $item['options']);
//        $this->db->set('cartoon_qty', $item['cartoon_qty']);
//        $this->db->set('cartoon_width', $item['cartoon_width']);
//        $this->db->set('cartoon_heigh', $item['cartoon_heigh']);
//        $this->db->set('cartoon_depth', $item['cartoon_depth']);
//        $this->db->set('boxqty', $item['boxqty']);
//        $this->db->set('charge_pereach', $item['charge_pereach']);
//        $this->db->set('charge_perorder', $item['charge_perorder']);
//        $this->db->set('faces', $item['faces']);
//        $this->db->set('special', $item['special']);
//        $this->db->set('category_id', $item['category_id']);
//        $this->db->set('special_checkout', $item['special_checkout']);
//        $this->db->set('special_shipping', $item['special_shipping']);
//        $this->db->set('special_setup', $item['special_setup']);
//        $this->db->set('item_source', $item['item_source']);
//        $this->db->set('printshop_inventory_id', $item['printshop_inventory_id']);
//        $this->db->set('update_template',$item['update_template']);
//        $this->db->set('imprint_update', $item['imprint_update']);
//        $this->db->set('item_sequence', $item['item_sequence']);
        $this->db->set('item_sale', $item['item_sale']);
        $this->db->set('printlocat_example_img', $item['printlocat_example_img']);
//        $this->db->set('itemcolor_example_img', $item['itemcolor_example_img']);
//        $this->db->set('outstock', $item['outstock']);
//        $this->db->set('outstock_banner', $item['outstock_banner']);
//        $this->db->set('outstock_link', $item['outstock_link']);
        // $this->db->set('shipping_info', $item['shipping_info']);
        $this->db->set('note_material', $item['note_material']);
        $this->db->set('sellblank', $item['sellblank']);
        $this->db->set('sellcolor', $item['sellcolor']);
        $this->db->set('sellcolors', $item['sellcolors']);
        $this->db->set('brand', $item['brand']);
        if ($item['item_id']<=0) {
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
        if (ifset($item['item_price_id'],0) !== 0) {
            $this->db->set('item_price_print', $item['item_price_print']);
            $this->db->set('item_sale_print', $item['item_sale_print']);
            $this->db->set('profit_print', $item['profit_print']);
            $this->db->set('item_price_setup', $item['item_price_setup']);
            $this->db->set('item_sale_setup', $item['item_sale_setup']);
            $this->db->set('profit_print', $item['profit_print']);
            $this->db->set('profit_setup', $item['profit_setup']);
            if ($item['item_price_id'] > 0) {
                $this->db->where('item_price_itemid', $item['item_price_id']);
                $this->db->update('sb_item_prices');
            } else {
                $this->db->set('item_price_itemid', $out['item_id']);
                $this->db->insert('sb_item_prices');
            }
        }
        return $out;
    }


    private function _save_vendoritem($vendor_item, $vendor_prices)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Vendor Item Save - Unknown Error'];
        if ($vendor_item['vendor_item_vendor'] < 0) {
            // Save vendor
            $this->db->set('vendor_name', $vendor_item['vendor_name']);
            $this->db->set('', $vendor_item['vendor_item_zipcode']);
            $this->db->insert('vendors');
            $vendor_id = $this->db->insert_id();
            if ($vendor_id == 0) {
                $out['msg'] = 'Error during Insert Vendor Data';
                return $out;
            } else {
                $vendor_item['vendor_item_vendor'] = $vendor_id;
            }
        }
        // save vendor item
        $this->db->set('vendor_item_number', $vendor_item['vendor_item_number']);
        $this->db->set('vendor_item_name', $vendor_item['vendor_item_name']);
        $this->db->set('vendor_item_exprint', $vendor_item['vendor_item_exprint']);
        $this->db->set('vendor_item_setup', $vendor_item['vendor_item_setup']);
        $this->db->set('vendor_item_notes', $vendor_item['vendor_item_notes']);
        $this->db->set('vendor_item_zipcode', $vendor_item['vendor_item_zipcode']);
        if ($vendor_item['vendor_item_id'] > 0 ) {
            $this->db->where('vendor_item_id', $vendor_item['vendor_item_id']);
            $this->db->update('sb_vendor_items');
            $vendor_item_id = $vendor_item['vendor_item_id'];
        } else {
            $this->db->insert('sb_vendor_items');
            $vendor_item_id = $this->db->insert_id();
        }
        foreach ($vendor_prices as $vendor_price) {
            if (intval($vendor_price['vendorprice_qty']) > 0 && floatval($vendor_price['vendorprice_color']) > 0) {
                $this->db->set('vendor_item_id', $vendor_item_id);
                $this->db->set('vendorprice_qty', intval($vendor_price['vendorprice_qty']));
                $this->db->set('vendorprice_color', floatval($vendor_price['vendorprice_color']));
                if ($vendor_price['vendorprice_id'] > 0) {
                    $this->db->where('vendorprice_id', $vendor_price['vendorprice_id']);
                    $this->db->update('sb_vendor_prices');
                } else {
                    $this->db->insert('sb_vendor_prices');
                }
            } else {
                if ($vendor_price['vendorprice_id'] > 0) {
                    $this->db->where('vendorprice_id', $vendor_price['vendorprice_id']);
                    $this->db->delete('sb_vendor_prices');
                }
            }
        }
        if ($vendor_item_id > 0) {
            $out['result'] = $this->success_result;
            $out['vendor_item_id'] = $vendor_item_id;
        }
        return $out;
    }

    private function _check_itemdetails($data) {
        $out=['result'=>$this->success_result, 'msg' =>''];
        $out_mgs='';
        $item = $data['item'];
        if (empty(ifset($item,'item_number',''))) {
            $out_mgs.='Item # required.'.PHP_EOL;
        } else {
            $numchkres = $this->_check_item_number($item['item_number'], $item['item_id'], $item['brand']);
            if ($numchkres['result']==$this->error_result) {
                $out_mgs.='Item Number is not unique'.PHP_EOL;
            }
        }
        /* Add Unique check */
        if (empty(ifset($item,'item_name',''))) {
            $out_mgs.='Item name required'.PHP_EOL;
        }

        if (intval(ifset($item,'item_lead_a',0))==0) {
            $out_mgs.='Lead A required'.PHP_EOL;
        }

        if (empty(ifset($item,'item_material',''))) {
            // if ($item['item_template']==$this->STRESSBALL_TEMPLATE) {
                $out_mgs.='Item material required'.PHP_EOL;
            // }
        }
//        if (empty(ifset($item,'item_weigth',0))) {
//            $out_mgs.='Item weight required'.PHP_EOL;
//        }
        if (empty(ifset($item,'item_size',''))) {
            $out_mgs.='Item size required'.PHP_EOL;
        }
        if (empty(ifset($item,'item_keywords',''))) {
            $out_mgs.='Internal Keywords required'.PHP_EOL;
        }
        if (empty(ifset($item,'item_url',''))) {
            $out_mgs.='Page URL is required field'.PHP_EOL;
        }
        if (empty(ifset($item,'item_meta_title',''))) {
            $out_mgs.='Meta title required'.PHP_EOL;
        }
        if (empty(ifset($item,'item_metadescription',''))) {
            $out_mgs.='Meta description required'.PHP_EOL;
        }
        if (empty(ifset($item,'item_metakeywords',''))) {
            $out_mgs.='Meta keywords required'.PHP_EOL;
        }
        if (empty(ifset($item,'item_description1',''))) {
            $out_mgs.='Attributes (row 1) required'.PHP_EOL;
        }
        if (isset($item['item_description2']) && empty($item['item_description2'])) { // && $item['item_template']=='Stressball'
            $out_mgs.='Attributes (row 2) required'.PHP_EOL;
        }
//        if (isset($item['cartoon_qty']) && empty($item['cartoon_qty'])) {
//            $out_mgs.='Shipping Information - Carton Qty - required'.PHP_EOL;
//        } elseif (!is_numeric($item['cartoon_qty'])) {
//            $out_mgs.='Shipping Information - Carton Qty - must be a number';
//        }
//        if (isset($item['cartoon_width']) && empty($item['cartoon_width'])) {
//            $out_mgs.='Shipping Information - Carton Width - required'.PHP_EOL;
//        } elseif (!is_numeric($item['cartoon_width'])) {
//            $out_mgs.='Shipping Information - Carton Width - must be a number'.PHP_EOL;
//        }
//        if (isset($item['cartoon_heigh']) && empty($item['cartoon_heigh'])) {
//            $out_mgs.='Shipping Information - Carton Height - required'.PHP_EOL;
//        } elseif (!is_numeric($item['cartoon_heigh'])) {
//            $out_mgs.='Shipping Information - Carton Height - must be a number'.PHP_EOL;
//        }
//        if (isset($item['cartoon_depth']) && empty($item['cartoon_depth'])) {
//            $out_mgs.='Shipping Information - Carton Depth - required'.PHP_EOL;
//        } elseif (!is_numeric($item['cartoon_depth'])) {
//            $out_mgs.='Shipping Information - Carton Depth - must be a number'.PHP_EOL;
//        }
//        if (!is_numeric($item['charge_pereach'])) {
//            $out_mgs.='Shipping Information - Special Shipping charge per each  - must be a number'.PHP_EOL;
//        }
//        if (!is_numeric($item['charge_perorder'])) {
//            $out_mgs.='Shipping Information - Special Shipping charge per order  - must be a number'.PHP_EOL;
//        }
//        if ($item['item_source']==$this->Inventory_Source && empty($item['printshop_inventory_id'])) {
//            $out_mgs.='Choose Inventory Item'.PHP_EOL;
//        }
//        if ($item['outstock']==1 && empty($item['outstock_banner'])) {
//            $out_mgs.='Empty Out of Stock Banner'.PHP_EOL;
//        }

        $vendor_item = $data['vendor_item'];
        if (empty($vendor_item['vendor_item_id'])) {
            $out_mgs.='Vendor Item not Entered';
        }
        if (empty($vendor_item['vendor_item_vendor'])) {
            $out_mgs.='Vendor not Entered';
        }
        $vendor_prices = $data['vendor_price'];
        $vchk =0;
        foreach ($vendor_prices as $vendor_price) {
            if (intval($vendor_price['vendorprice_qty'])>0 && floatval($vendor_price['vendorprice_color'])>0) {
                $vchk=1;
                break;
            }
        }
        if ($vchk==0) {
            $out_mgs.='Vendor Item Price not Entered';
        }
        if (!empty($out_mgs)) {
            $out['result']=$this->error_result;
            $out['msg']=$out_mgs;
        }
        return $out;
    }

    // Check uniq number
    private function  _check_item_number($item_number, $item_id, $brand) {
        $out = ['result' => $this->error_result, 'msg' => 'Item # not unique'];
        $this->db->select('count(item_id) as cnt');
        $this->db->from('sb_items');
        $this->db->where('item_number', $item_number);
        $this->db->where('item_id != ', $item_id);
        $this->db->where('brand', $brand);
        $res = $this->db->get()->row_array();
        if ($res['cnt']==0) {
            $out['result'] = $this->success_result;
        }
        return $out;
    }
}