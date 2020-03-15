<?php
if (!function_exists('calculate_shipcost')) {
    function calculate_shipcost($options)
    {
        /* Calc Ship Rates */
        $ci=&get_instance();
        $zip = $options['zip'];
        $numinpack = (isset($options['numinpack']) ? $options['numinpack'] : 1);
        $itemweight = (isset($options['weight']) ? $options['weight'] : '0.010');
        $qty = (isset($options['itemqty']) ? $options['itemqty'] : 250);
        $startdeliv = (isset($options['startdeliv']) ? $options['startdeliv'] : time());
        $vendorzip = (isset($options['vendor_zip']) ? $options['vendor_zip'] : $ci->config->item('zip'));
        $item_length = (isset($options['item_length']) ? $options['item_length'] : 0);
        $item_width = (isset($options['item_width']) ? $options['item_width'] : 0);
        $item_height = (isset($options['item_height']) ? $options['item_height'] : 0);
        $ship = $options['ship'];
        $cnt_code = (isset($options['cnt_code']) ? $options['cnt_code'] : 'US');
        /* Calculate REST of full cartoon */
        if (intval($numinpack) == 0) {
            $ci->load->config('shipping');
            $numinpack = $ci->config->item('default_inpack');
        }

        $numpack = floor($qty / $numinpack);

        $rest = $qty - ($numpack * $numinpack);
        /* Total Parameters for Transit */
        $transitpack = $numpack + 1;

        /* if ($numpack<1) {
            $numpack=1;
            $rest=0;
        }*/
        $transitweigth = $itemweight * $qty;

        $ci->load->library('United_parcel_service');

        $out = array();

        $daydiff = round((time() - $startdeliv) / (24 * 60 * 60), 0);
        // $daydiff=BankDays($startdeliv, strtotime(date('Y-m-d')));
        if (abs($daydiff) > 10) {
            $startdeliv = strtotime(date('Y-m-d') . ' + 1 day');
        }

        $code = '';
        $upsserv = new United_parcel_service();
        $transit_arr = $upsserv->ship_time($zip, $cnt_code, $transitpack, $transitweigth, date('Ymd', $startdeliv), $vendorzip);

        if ($transit_arr['result'] == FALSE) {
            $out['result'] = FALSE;
            $out['error'] = $transit_arr['msg'];
            $out['error_code'] = 'TNT';
        } else {
            $transit_arr = $transit_arr['times'];
            $out['times'] = $transit_arr;
            $ratescalc = 1;
            $errmsg = '';
            $incrkf = 0;
            if ($numpack != 0) {
                if ($numpack > 20) {
                    $incrkf = $numpack / 20;
                    $numpack = 20;
                }
                // $fullResult = $upsserv->ship_rates($zip,'',$numpack,$itemweight*$numinpack*$numpack, $startdeliv ,true, $vendorzip,$cnt_code, $item_length, $item_width, $item_height);
                $fullResult = $upsserv->ship_rates($zip, '', $numpack, $itemweight * $numinpack * $numpack, $startdeliv, true, $vendorzip, $cnt_code, $item_length, $item_width, $item_height);
                if ($fullResult['result'] == FALSE) {
                    $ratescalc = 0;
                    $errmsg = $fullResult['msg'];
                }
            } else {
                $fullResult = array();
            }

            if ($rest != 0 && $ratescalc == 1) {
                $restweight = $itemweight * $rest;
                $restResult = $upsserv->ship_rates($zip, '', 1, $restweight, $startdeliv, true, $vendorzip, $cnt_code, 0, 0, 0);
                if ($restResult['result'] == FALSE) {
                    $ratescalc = 0;
                    $errmsg = $restResult['msg'];
                }
            } else {
                $restResult = array();
            }

            if ($ratescalc == 0) {
                $out['result'] = FALSE;
                $out['error'] = $errmsg;
                $out['error_code'] = 'RATES';
            } else {
                $out['result'] = TRUE;
                $code = "";
                $codes = array();
                if (!empty($fullResult)) {
                    $fullrates = $fullResult['rates'];
                    foreach ($fullrates as $row) {
                        $delivdate = '';
                        if ($row['ServiceCode'] == '03') {
                            if (isset($transit_arr['GND'])) {
                                array_push($codes, 'GND');
                                $delivdate = $transit_arr['GND']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                $ship['GND'] = array('ServiceCode' => 'GND', // 'ServiceName' =>$row['ServiceName'],
                                    'ServiceName' => 'Ground', // 'Rate' =>$row['Rate'],
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                // $ship['deliv']=$delivdate;
                                $code .= "GND|";
                            } elseif (isset ($transit_arr['G'])) {
                                array_push($codes, 'GND');
                                $delivdate = $transit_arr['G']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                $ship['GND'] = array('ServiceCode' => 'GND', 'ServiceName' => 'Ground',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "GND|";
                            }
                            /* Canadian */
                            /* UPS Standard */
                        } elseif ($row['ServiceCode'] == '11') {
                            if (isset($transit_arr['03'])) {
                                $delivdate = $transit_arr['03']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'UPSStandard');
                                $ship['UPSStandard'] = array('ServiceCode' => '11', 'ServiceName' => 'Ground',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "11|";
                            }
                        } elseif ($row['ServiceCode'] == '02') {
                            if (isset($transit_arr['2DA'])) {
                                $delivdate = $transit_arr['2DA']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'DA2');
                                $ship['DA2'] = array('ServiceCode' => '2DA', 'ServiceName' => $row['ServiceName'],
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "2DA|";
                            } elseif (isset($transit_arr['02'])) {
                                $delivdate = $transit_arr['02']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'DA2');
                                $ship['DA2'] = array('ServiceCode' => '2DA', 'ServiceName' => '2nd Day Air',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "2DA|";
                            }
                        } elseif ($row['ServiceCode'] == '13') {
                            if (isset($transit_arr['1DP'])) {
                                $delivdate = $transit_arr['1DP']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'DP1');
                                $ship['DP1'] = array('ServiceCode' => '1DP', 'ServiceName' => $row['ServiceName'],
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "1DP|";
                            }
                        } elseif ($row['ServiceCode'] == '01') {
                            // 1 Day Air
                            if (isset($transit_arr['01'])) {
                                $delivdate = $transit_arr['01']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'DP1');
                                $ship['DP1'] = array('ServiceCode' => '1DP', 'ServiceName' => 'Next Day PM',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "1DP|";
                            }
                        } elseif ($row['ServiceCode'] == '14') {
                            if (isset($transit_arr['1DM'])) {
                                $delivdate = $transit_arr['1DM']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'DA1');
                                $ship['DA1'] = array('ServiceCode' => '1DM', 'ServiceName' => $row['ServiceName'],
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "1DA|";
                            }
                        } elseif ($row['ServiceCode'] == '08') {
                            if (isset($transit_arr['05'])) {
                                $delivdate = $transit_arr['05']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'UPSExpedited');
                                $ship['UPSExpedited'] = array('ServiceCode' => '08', 'ServiceName' => 'Expedited',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "08|";
                            }
                            /* UPS Worldwide Express */
                        } elseif ($row['ServiceCode'] == '07') {
                            if (isset($transit_arr['01'])) {
                                $delivdate = $transit_arr['01']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'UPSExpress');
                                $ship['UPSExpress'] = array('ServiceCode' => '07', 'ServiceName' => 'Express',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "07|";
                            }
                            /* UPS Saver */
                        } elseif ($row['ServiceCode'] == '65') {
                            if (isset($transit_arr['28'])) {
                                $delivdate = $transit_arr['28']['transit_timestamp'];
                                if (abs($daydiff) > 10) {
                                    // Make changes in deliv date
                                    $delivdate = fixdeliv_different($delivdate, $daydiff);
                                }
                                array_push($codes, 'UPSSaver');
                                $ship['UPSSaver'] = array('ServiceCode' => '65', 'ServiceName' => 'Saver',
                                    'Rate' => ($incrkf == 0 ? $row['Rate'] : round($row['Rate'] * $incrkf, 2)), 'DeliveryDate' => $delivdate, 'current' => 0,);
                                $code .= "65|";
                            }
                            /* UPS Worldwide Expedited */
                        }
                    }
                }
                /* Check Rest Rates */
                if (!empty($restResult)) {
                    $restrates = $restResult['rates'];
                    foreach ($restrates as $row) {
                        $delivdate = '';
                        if ($row['ServiceCode'] == '03') {
                            if (isset($transit_arr['GND'])) {
                                if (!in_array('GND', $codes)) {
                                    array_push($codes, 'GND');
                                    $delivdate = $transit_arr['GND']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    $ship['GND'] = array('ServiceCode' => 'GND',
                                        'ServiceName' => 'Ground', 'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "GND|";
                                } else {
                                    $ship['GND']['Rate'] = $ship['GND']['Rate'] + $row['Rate'];
                                }
                            } elseif (isset ($transit_arr['G'])) {
                                if (!in_array('GND', $codes)) {
                                    array_push($codes, 'GND');
                                    $delivdate = $transit_arr['G']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    $ship['GND'] = array('ServiceCode' => 'GND', 'ServiceName' => 'Ground',
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "GND|";
                                } else {
                                    $ship['GND']['Rate'] = $ship['GND']['Rate'] + $row['Rate'];
                                }
                            }
                            /* Canadian */
                            /* UPS Standard */
                        } elseif ($row['ServiceCode'] == '11') {
                            if (isset($transit_arr['03'])) {
                                if (!in_array('UPSStandard', $codes)) {
                                    $delivdate = $transit_arr['03']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'UPSStandard');
                                    $ship['UPSStandard'] = array('ServiceCode' => '11', 'ServiceName' => 'Ground',
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $ship['deliv'] = $delivdate;
                                    $code .= "11|";
                                } else {
                                    $ship['UPSStandard']['Rate'] = $ship['UPSStandard']['Rate'] + $row['Rate'];
                                }
                            }
                        } elseif ($row['ServiceCode'] == '02') {
                            if (isset($transit_arr['2DA'])) {
                                if (!in_array('DA2', $codes)) {
                                    $delivdate = $transit_arr['2DA']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'DA2');
                                    $ship['DA2'] = array('ServiceCode' => '2DA', 'ServiceName' => $row['ServiceName'], 'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "2DA|";
                                } else {
                                    $ship['DA2']['Rate'] = $ship['DA2']['Rate'] + $row['Rate'];
                                }
                            } elseif (isset($transit_arr['02'])) {
                                if (!in_array('DA2', $codes)) {
                                    $delivdate = $transit_arr['02']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'DA2');
                                    $ship['DA2'] = array('ServiceCode' => '2DA', 'ServiceName' => '2nd Day Air',
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "2DA|";
                                } else {
                                    $ship['DA2']['Rate'] = $ship['DA2']['Rate'] + $row['Rate'];
                                }
                            }
                        } elseif ($row['ServiceCode'] == '13') {
                            if (isset($transit_arr['1DP'])) {
                                if (!in_array('DP1', $codes)) {
                                    $delivdate = $transit_arr['1DP']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'DP1');
                                    $ship['DP1'] = array('ServiceCode' => '1DP', 'ServiceName' => $row['ServiceName'], 'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "1DP|";
                                } else {
                                    $ship['DP1']['Rate'] = $ship['DP1']['Rate'] + $row['Rate'];
                                }
                            }
                        } elseif ($row['ServiceCode'] == '01') {
                            // 1 Day Air
                            if (isset($transit_arr['01'])) {
                                if (!in_array('DP1', $codes)) {
                                    $delivdate = $transit_arr['01']['transit_timestamp'];
                                    if ($daydiff > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'DP1');
                                    $ship['DP1'] = array('ServiceCode' => '1DP', 'ServiceName' => 'Next Day PM', /* $row['ServiceName'], */
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "1DP|";
                                } else {
                                    $ship['DP1']['Rate'] = $ship['DP1']['Rate'] + $row['Rate'];
                                }
                            }
                        } elseif ($row['ServiceCode'] == '14') {
                            if (isset($transit_arr['1DM'])) {
                                if (!in_array('DA1', $codes)) {
                                    $delivdate = $transit_arr['1DM']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'DA1');
                                    $ship['DA1'] = array('ServiceCode' => '1DM', 'ServiceName' => $row['ServiceName'], 'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "1DA|";
                                } else {
                                    $ship['DA1']['Rate'] = $ship['DA1']['Rate'] + $row['Rate'];
                                }
                            }
                        } elseif ($row['ServiceCode'] == '08') {
                            if (isset($transit_arr['05'])) {
                                if (!in_array('UPSExpedited', $codes)) {
                                    $delivdate = $transit_arr['05']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'UPSExpedited');
                                    $ship['UPSExpedited'] = array('ServiceCode' => '08', 'ServiceName' => 'Expedited', /* $row['ServiceName'], */
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "08|";
                                } else {
                                    $ship['UPSExpedited']['Rate'] = $ship['UPSExpedited']['Rate'] + $row['Rate'];
                                }
                            }
                            /* UPS Worldwide Express */
                        } elseif ($row['ServiceCode'] == '07') {
                            if (isset($transit_arr['01'])) {
                                if (!in_array('UPSExpress', $codes)) {
                                    $delivdate = $transit_arr['01']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'UPSExpress');
                                    $ship['UPSExpress'] = array('ServiceCode' => '07', 'ServiceName' => 'Express',           /* $row['ServiceName'],*/
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "07|";
                                } else {
                                    $ship['UPSExpress']['Rate'] = $ship['UPSExpress']['Rate'] + $row['Rate'];
                                }
                            }
                            /* UPS Saver */
                        } elseif ($row['ServiceCode'] == '65') {
                            if (isset($transit_arr['28'])) {
                                if (!in_array('UPSSaver', $codes)) {
                                    $delivdate = $transit_arr['28']['transit_timestamp'];
                                    if (abs($daydiff) > 10) {
                                        // Make changes in deliv date
                                        $delivdate = fixdeliv_different($delivdate, $daydiff);
                                    }
                                    array_push($codes, 'UPSSaver');
                                    $ship['UPSSaver'] = array('ServiceCode' => '65', 'ServiceName' => 'Saver', /* $row['ServiceName'], */
                                        'Rate' => $row['Rate'], 'DeliveryDate' => $delivdate, 'current' => 0,);
                                    $code .= "65|";
                                } else {
                                    $ship['UPSSaver']['Rate'] = $ship['UPSSaver']['Rate'] + $row['Rate'];
                                }
                            }
                            /* UPS Worldwide Expedited */
                        }
                    }
                }
            }
        }
        $out['ship'] = $ship;
        $out['code'] = $code;
        // $out['codes'] = $codes;
        return $out;
    }
}

if (!function_exists('recalc_rates')) {
    function recalc_rates($ship,$item,$qty,$cnt_code='US', $country_id='223') {
        $ci=&get_instance();
        $ci->load->model('shipping_model');

        $methods=$ci->shipping_model->get_ship_methods($country_id);

        $idxmethods=array();
        foreach ($methods as $row) {
            array_push($idxmethods, $row['ups_code']);
        }

        $add_price=$item['charge_perorder']+($qty*$item['charge_pereach']);

        if (isset($ship['GND']['Rate'])) {
            $disc=0;
            $ship_meth='';
            if (in_array('GND', $idxmethods)) {
                $idxmtd=  array_search('GND', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
                $ship_meth=$methods[$idxmtd]['shipping_method_name'];
            }
            $rate=$ship['GND']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['GND']['Rate']=$rate;
            if ($ship_meth!='') {
                // $ship['GND']['ServiceName']=$ship_meth;
            }
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
            if ($ship_meth!='') {
                // $ship['DA2']['ServiceName']=$ship_meth;
            }
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
            if ($ship_meth!='') {
                // $ship['DP1']['ServiceName']=$ship_meth;
            }
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
            if ($ship_meth!='') {
                // $ship['DA1']['ServiceName']=$ship_meth;
            }
        }
        if (isset($ship['UPSExpedited'])) {
            $disc=0;
            if (in_array('08', $idxmethods)) {
                $idxmtd=  array_search('08', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
            }
            $rate=$ship['UPSExpedited']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['UPSExpedited']['Rate']=$rate;
        }
        if (isset($ship['UPSSaver'])) {
            $disc=0;
            if (in_array('65', $idxmethods)) {
                $idxmtd=  array_search('65', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
            }
            $rate=$ship['UPSSaver']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['UPSSaver']['Rate']=$rate;
        }
        if (isset($ship['UPSStandard'])) {
            $disc=0;
            if (in_array('11', $idxmethods)) {
                $idxmtd=  array_search('11', $idxmethods);
                $disc=$methods[$idxmtd]['method_percent'];
            }
            $rate=$ship['UPSStandard']['Rate'];
            $rate=round($rate*(100+$disc)/100 ,2)+$add_price;
            $ship['UPSStandard']['Rate']=$rate;
        }

        return $ship;
    }
}

if (!function_exists('fixdeliv_different')) {
    function fixdeliv_different($delivdate, $daydiff) {
        $ci=&get_instance();
        if ($daydiff>0) {
            $newdeliv=strtotime(date('Y-m-d H:i:s', $delivdate). ' - '.$daydiff.'days');
        } else {
            $newdeliv=strtotime(date('Y-m-d H:i:s', $delivdate). ' + '.abs($daydiff).'days');
        }
        $calendar_id=$ci->config->item('bank_calendar');
        $start=date("Y-m-d",$newdeliv);
        $last_date=strtotime(date("Y-m-d", strtotime($start)) . " +365 days");
        $ci->db->select('line_date as date',FALSE);
        $ci->db->from("calendar_lines");
        $ci->db->where('calendar_id',$calendar_id);
        $ci->db->where("line_date between '".strtotime($start)."' and '".$last_date."'");
        $cal=$ci->db->get()->result_array();
        $calend=array();
        foreach ($cal as $row) {
            array_push($calend, $row['date']);
        }
        $dat=strtotime(date("Y-m-d", strtotime($start)));
        $i=1;$cnt=1;
        while ($i <= 1) {
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
}

?>