<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipcalculate_model extends MY_Model
{
    private $box_empty_weight = 25;

    function __construct()
    {
        parent::__construct();
    }

    public function prepare_ship_packages($shipqty, $item_id, $itemweight) {
        $this->load->model('shipping_model');
        $shipboxes = $this->shipping_model->get_itemshipbox($item_id,0);
        $maxshipbox = count($shipboxes)-1;
        $packages = [];
        $numpackages = 0;
        if ($maxshipbox==0) {
            $ceilpart = floor($shipqty/$shipboxes[0]['box_qty']);
            $boxweight = $itemweight * $shipboxes[0]['box_qty'];
            for ($i=0; $i < $ceilpart; $i++) {
                $packages[] = [
                    "PackagingType" => [
                        "Code" => "02",
                        "Description" => "Packaging"
                    ],
                    "Dimensions" => [
                        "UnitOfMeasurement" => [
                            "Code" => "IN",
                            "Description" => "Inches"
                        ],
                        "Length" => "{$shipboxes[0]['box_length']}",
                        "Width" => "{$shipboxes[0]['box_width']}",
                        "Height" => "{$shipboxes[0]['box_height']}"
                    ],
                    "PackageWeight" => [
                        "UnitOfMeasurement" => [
                            "Code" => "LBS",
                            "Description" => "Pounds"
                        ],
                        "Weight" => "{$boxweight}"
                    ],
                ];
                $numpackages++;
            }
            $restqty = $shipqty - $ceilpart * $shipboxes[0]['box_qty'];
            if ($restqty > 0) {
                //
                $packages[] = [
                    "PackagingType" => [
                        "Code" => "02",
                        "Description" => "Packaging"
                    ],
                    "Dimensions" => [
                        "UnitOfMeasurement" => [
                            "Code" => "IN",
                            "Description" => "Inches"
                        ],
                        "Length" => "{$shipboxes[0]['box_length']}",
                        "Width" => "{$shipboxes[0]['box_width']}",
                        "Height" => "{$shipboxes[0]['box_height']}"
                    ],
                    "PackageWeight" => [
                        "UnitOfMeasurement" => [
                            "Code" => "LBS",
                            "Description" => "Pounds"
                        ],
                        "Weight" => "{$boxweight}"
                    ],
                ];
                $numpackages++;
            }
        } else {
            $restqty = $shipqty;
            foreach ($shipboxes as $shipbox) {
                $ceilpart = floor($shipqty / $shipbox['box_qty']);
                if ($ceilpart > 0) {
                    $boxweight = $itemweight * $shipbox['box_qty'];
                    for ($i=0; $i < $ceilpart; $i++) {
                        $packages[] = [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Packaging"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "IN",
                                    "Description" => "Inches"
                                ],
                                "Length" => "{$shipbox['box_length']}",
                                "Width" => "{$shipbox['box_width']}",
                                "Height" => "{$shipbox['box_height']}"
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "LBS",
                                    "Description" => "Pounds"
                                ],
                                "Weight" => "{$boxweight}"
                            ],
                        ];
                        $numpackages++;
                    }
                    $restqty = $shipqty - ($ceilpart * $shipbox['box_qty']);
                    $shipqty = 0;
                }
            }

            if ($restqty > 0) {
                for ($i=$maxshipbox; $i >= 0; $i--) {
                    $shipbox = $shipboxes[$i];
                    if ($shipbox['box_qty'] >= $restqty) {
                        $boxweight = $itemweight * $restqty;
                        $packages[] = [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Packaging"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "IN",
                                    "Description" => "Inches"
                                ],
                                "Length" => "{$shipbox['box_length']}",
                                "Width" => "{$shipbox['box_width']}",
                                "Height" => "{$shipbox['box_height']}"
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "LBS",
                                    "Description" => "Pounds"
                                ],
                                "Weight" => "{$boxweight}"
                            ],
                        ];
                        $numpackages++;
                        $restqty = $restqty - $shipbox['box_qty'];
                        if ($restqty <= 0 ) {
                            break;
                        }
                    }
                }
            }
        }
        return [
            'packages' => $packages,
            'numpackages' => $numpackages,
        ];
    }

    public function prepare_ship_custompackages($shipqty) {
        $packages = [];
        $numpackages = 0;
        $boxqty = $this->config->item('default_inpack');
        $itemweight = $this->box_empty_weight / $boxqty;
        $ceilpart = floor($shipqty/$boxqty);
        $boxweight = $itemweight * $boxqty;
        $box_width = $this->config->item('default_pack_width');
        $box_length = $this->config->item('default_pack_depth');
        $box_height = $this->config->item('default_pack_heigth');
        for ($i=0; $i < $ceilpart; $i++) {
            $packages[] = [
                "PackagingType" => [
                    "Code" => "02",
                    "Description" => "Packaging"
                ],
                "Dimensions" => [
                    "UnitOfMeasurement" => [
                        "Code" => "IN",
                        "Description" => "Inches"
                    ],
                    "Length" => "{$box_length}",
                    "Width" => "{$box_width}",
                    "Height" => "{$box_height}"
                ],
                "PackageWeight" => [
                    "UnitOfMeasurement" => [
                        "Code" => "LBS",
                        "Description" => "Pounds"
                    ],
                    "Weight" => "{$boxweight}"
                ],
            ];
            $numpackages++;
        }
        $restqty = $shipqty - $ceilpart * $boxqty;
        if ($restqty > 0) {
            //
            $packages[] = [
                "PackagingType" => [
                    "Code" => "02",
                    "Description" => "Packaging"
                ],
                "Dimensions" => [
                    "UnitOfMeasurement" => [
                        "Code" => "IN",
                        "Description" => "Inches"
                    ],
                    "Length" => "{$box_length}",
                    "Width" => "{$box_width}",
                    "Height" => "{$box_height}"
                ],
                "PackageWeight" => [
                    "UnitOfMeasurement" => [
                        "Code" => "LBS",
                        "Description" => "Pounds"
                    ],
                    "Weight" => "{$boxweight}"
                ],
            ];
            $numpackages++;
        }
        return [
            'packages' => $packages,
            'numpackages' => $numpackages,
        ];
    }

    public function calculate_upsshipcost($options) {
        $out=['result' => $this->error_result, 'msg' => 'Error During Calc Ship rates'];
        $this->load->config('shipping');
        $this->load->model('calendars_model');
        $itemweight = ifset($options, 'weight', '0.010');
        $qty = ifset($options, 'itemqty', 250);
        $startdeliv = ifset($options, 'startdeliv', time());
        $cnt_code = (isset($options['target_country']) ? $options['target_country'] : 'US');
        $package_price = ifset($options, 'package_price', 100);

        $shipTo = $options['shipTo'];
        $shipFrom = $options['shipFrom'];
        // Calculate REST of full cartoon
        $tntpacks = ifset($options, 'numpackages', 1);
        $earlier = new DateTime(date('Y-m-d'));
        $later = new DateTime(date('Y-m-d', $startdeliv));
        $daydiff = $later->diff($earlier)->format("%r%a");

        $token = usersession('token');
        $out['error_code'] = 'Authorization';
        $tokenres = $this->_UpsAuthToken($token);
        $out['msg'] = $tokenres['msg'];
        if ($tokenres['result']==$this->success_result) {
            $token = $tokenres['token'];
            // Get Times in transit
            $oldstart = 0;
            if (abs($daydiff) > 10) {
                $oldstart = $startdeliv;
                $startdeliv = strtotime(date('Y-m-d'));
            }
            $tntweigth = $itemweight * $qty;
            $ratescalc = 0;
            $this->load->library('UPS_service');
            $upsservice = new UPS_service();

            $transitres = $upsservice->timeInTransit($token, $shipFrom['Address'], $shipTo['Address'], $tntweigth, $tntpacks, $package_price,  date('Y-m-d', $startdeliv), '10:00:00');
            $out['msg'] = 'Invalid Parameters';// $transitres['msg'];
            $out['error_code'] = 'TNT';
            if ($transitres['error']==0) {
                // All ok
                $times = $transitres['services'];
                // Calc rates
                $packDimens = $options['packages'];
                $rateres = $upsservice->getRates($token, $shipTo, $shipFrom, $tntpacks,  $packDimens, $tntweigth);
                if ($rateres['error'] > 0) {
                    $out['msg'] = $rateres['msg'];
                    $out['error_code'] = 'Rates';
                } else {
                    $out['result'] = $this->success_result;
                    $rates = $rateres['rates'];
                    // Make merged array
                    $ship=[];
                    $codes = [];
                    $calendar_id=$this->config->item('bank_calendar');
                    if ($cnt_code=='US') {
                        foreach ($rates as $rate) {
                            $transit = 0;
                            if ($rate['service_code']=='03') {
                                // Ground
                                foreach ($times as $time) {
                                    if ($time['code']=='GND') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'GND');
                                    if ($time['deliverytime'] > '16:00:00') {
                                        $newdate  = $this->calendars_model->get_business_date(strtotime($time['deliverydate']),1, $options['item_id']);
                                        $time['deliverydate'] = date('Y-m-d', $newdate);
                                        $time['deliverytime'] = '16:00:00';
                                    }
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['GND'] = [
                                        'ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='02') {
                                // Two days
                                foreach ($times as $time) {
                                    if ($time['code']=='2DA') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'DA2');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['DA2'] = [
                                        'ServiceCode' => 'DA2', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => '2nd Day Air', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='14') {
                                // UPS Next Day Air Early
                                foreach ($times as $time) {
                                    if ($time['code']=='1DM') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'DA1');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['DA1'] = array(
                                        'ServiceCode' => '1DM',
                                        'ServiceName' => 'Next Day AM',
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    );
                                }
                            } elseif ($rate['service_code']=='13') {
                                // UPS Next Day Air Saver
                                foreach ($times as $time) {
                                    if ($time['code']=='1DP') {
                                        $transit=1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'DP1');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['DP1'] = array(
                                        'ServiceCode' => '1DP',
                                        'ServiceName' => 'Next Day PM',
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    );
                                }
                            }
                        }
                    } elseif ($cnt_code=='CA') {
                        foreach ($rates as $rate) {
                            $transit = 0;
                            if ($rate['service_code']=='11') {
                                // Ground
                                foreach ($times as $time) {
                                    if ($time['code']=='03') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'GND');
                                    if ($time['deliverytime'] > '16:00:00') {
                                        $newdate  = $this->calendars_model->get_business_date(strtotime($time['deliverydate']),1, $options['item_id']);
                                        $time['deliverydate'] = date('Y-m-d', $newdate);
                                        $time['deliverytime'] = '16:00:00';
                                    }
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['GND'] = [
                                        'ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='08') {
                                // UPS Expedited
                                foreach ($times as $time) {
                                    if ($time['code']=='05') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpedited');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpedited'] = [
                                        'ServiceCode' => '08', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Expedited', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='65') {
                                // Saver
                                foreach ($times as $time) {
                                    if ($time['code']=='28') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSSaver');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSSaver'] = [
                                        'ServiceCode' => '65', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Saver', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='07') {
                                // UPSWorExpress
                                foreach ($times as $time) {
                                    if ($time['code']=='29') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpress');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpress'] = [
                                        'ServiceCode' => '07', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Express', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            }
                        }
                    } else {
                        foreach ($rates as $rate) {
                            if ($rate['service_code']=='07') {
                                // UPSWorExpress
                                foreach ($times as $time) {
                                    if ($time['code']=='01') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($time['code']=='29') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpress');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpress'] = [
                                        'ServiceCode' => '07', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Express', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='08') {
                                // UPS Expedited
                                foreach ($times as $time) {
                                    if ($time['code']=='05') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSExpedited');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSExpedited'] = [
                                        'ServiceCode' => '08', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Expedited', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='11') {
                                // Ground
                                foreach ($times as $time) {
                                    if ($time['code']=='04') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($time['code']=='11') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($time['code']=='82') {
                                        $transit = 1;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'GND');
                                    if ($time['deliverytime'] > '16:00:00') {
                                        $newdate  = $this->calendars_model->get_business_date(strtotime($time['deliverydate']),1, $options['item_id']);
                                        $time['deliverydate'] = date('Y-m-d', $newdate);
                                        $time['deliverytime'] = '16:00:00';
                                    }
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['GND'] = [
                                        'ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            } elseif ($rate['service_code']=='54') {
                                foreach ($times as $time) {
                                    if ($time['code']=='54') {
                                        $transit = 1;
                                        break;
                                    }
                                    if ($transit==1) {
                                        array_push($codes, 'ExpressPlus');
                                        $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                        if (abs($daydiff) > 10) {
                                            $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                        }
                                        $ship['ExpressPlus'] = [
                                            'ServiceCode' => '54', // 'ServiceName' =>$row['ServiceName'],
                                            'ServiceName' => 'Express Plus', // 'Rate' =>$row['Rate'],
                                            'Rate' => round($rate['rate'], 2),
                                            'DeliveryDate' => $delivdate,
                                            'current' => 0,
                                        ];
                                    }
                                }
                            } elseif ($rate['service_code']=='65') {
                                // Saver
                                foreach ($times as $time) {
                                    if ($time['code']=='28') {
                                        $transit = 1;
                                        break;
                                    }
                                }
                                if ($transit==1) {
                                    array_push($codes, 'UPSSaver');
                                    $delivdate = strtotime($time['deliverydate'].' '.$time['deliverytime']);
                                    if (abs($daydiff) > 10) {
                                        $delivdate = $this->recalc_arrive_date($oldstart, $time['bisnessdays'], $calendar_id);
                                    }
                                    $ship['UPSSaver'] = [
                                        'ServiceCode' => '65', // 'ServiceName' =>$row['ServiceName'],
                                        'ServiceName' => 'Saver', // 'Rate' =>$row['Rate'],
                                        'Rate' => round($rate['rate'], 2),
                                        'DeliveryDate' => $delivdate,
                                        'current' => 0,
                                    ];
                                }
                            }
                        }
                    }
                    $out['ship'] = $ship;
                    $out['code'] = $codes;
                }
            }
        }
        return $out;
    }

    public function recalc_rates($ship,$item, $qty, $brand, $country_id=223) {
        $this->load->model('shipping_model');
        $methods=$this->shipping_model->get_ship_methods($country_id, $brand);
        $idxmethods=array();
        foreach ($methods as $row) {
            array_push($idxmethods, $row['ups_code']);
        }
        $add_price=$item['charge_perorder']+($qty*$item['charge_pereach']);
        if (isset($ship['GND']['Rate'])) {
            $disc=0;
            if (in_array('GND', $idxmethods)) {
                $idxmtd=  array_search('GND', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
            }
            $rate=$ship['GND']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['GND']['Rate']=$rate;
        }
        if (isset($ship['DA2']['Rate'])) {
            $disc=0;
            $ship_meth='';
            if (in_array('2DA', $idxmethods)) {
                $idxmtd=  array_search('2DA', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
                $ship_meth=$methods[$idxmtd]['shipping_method_name'];
            }
            $rate=$ship['DA2']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['DA2']['Rate']=$rate;
        }
        if (isset($ship['DP1']['Rate'])) {
            $disc=0;
            $ship_meth='';
            if (in_array('1DM', $idxmethods)) {
                $idxmtd=  array_search('1DM', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
                $ship_meth=$methods[$idxmtd]['shipping_method_name'];
            }
            $rate=$ship['DP1']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['DP1']['Rate']=$rate;
        }
        if (isset($ship['DA1']['Rate'])) {
            $disc=0;
            $ship_meth='';
            if (in_array('1DP', $idxmethods)) {
                $idxmtd=  array_search('1DP', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
                $ship_meth=$methods[$idxmtd]['shipping_method_name'];
            }
            $rate=$ship['DA1']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['DA1']['Rate']=$rate;
        }
        return $ship;
    }

    public function recalc_arrive_date($delivdate, $tnt_days, $calendar_id = 0) {
        if ($calendar_id==0) {
            $calendar_id=$this->config->item('bank_calendar');
        }
        $start=date("Y-m-d",$delivdate);
        $last_date=strtotime(date("Y-m-d", strtotime($start)) . " +365 days");
        $this->db->select('line_date as date',FALSE);
        $this->db->from("calendar_lines");
        $this->db->where('calendar_id',$calendar_id);
        $this->db->where("line_date between '".strtotime($start)."' and '".$last_date."'");
        $cal=$this->db->get()->result_array();
        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['date']);
        }
        $i=1;$cnt=1;
        while ($i <= $tnt_days) {
            $dat=strtotime(date("Y-m-d", strtotime($start)) . " +".$cnt." days");
            $day=$dat;
            if (date('w',$dat)!=0 && date('w',$dat)!=6 && !in_array($day, $calend)) {
                $i++;
            }
            $cnt++;
        }
        $newdeliv=$dat;
        return $newdeliv;
    }

    private function _UpsAuthToken($token) {
        $out = ['result' => $this->error_result, 'msg' => 'Error during Token generation'];
        $this->load->library('UPS_service');
        $upsservice = new UPS_service();
        $sessionId = session_id();
        if ($token) {
            $tokenresult = $upsservice->refreshToken($token);
            if ($tokenresult['error']==1) {
                $out['msg'] = 'Authorize Error. Code '.$tokenresult['code'];
            } else {
                if (!isset($tokenresult['errors'])) {
                    $out['result'] = $this->success_result;
                    $out['token'] = $token;
                    $out['session'] = $sessionId;
                    usersession('token', $token);
                }
            }
        }
        $tokenresult = $upsservice->generateToken($sessionId);
        if ($tokenresult['error']==1) {
            $out['msg'] = $tokenresult['msg'];
        } else {
            if (isset($tokenresult['errors'])) {
                $errors = $tokenresult['errors'][0];
                $out['msg'] = 'Error Code '.$errors['code'].' - '.$errors['message'];
            } else {
                $out['result'] = $this->success_result;
                $out['token'] = $tokenresult['access_token'];
                $out['session'] = $sessionId;
                usersession('token', $tokenresult['access_token']);
            }
        }
        return $out;
    }

}