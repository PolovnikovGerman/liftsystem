<?php

class UPS_service
{
    private $_CI;
    private $ups_clientid;
    private $ups_secret;
    private $ups_oath_url;
    private $ups_tokenrefresh_url;
    private $ups_rate_url;

    private $ups_tnt_url;
    private $shiper;
    function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->config->load('shipping');
        $this->ups_clientid = $this->_CI->config->item('ups_clientid');
        $this->ups_secret = $this->_CI->config->item('ups_secret');
        $this->ups_oath_url = $this->_CI->config->item('ups_oath_url');
        $this->ups_tokenrefresh_url = $this->_CI->config->item('ups_tokenrefresh_url');
        $this->ups_rate_url = $this->_CI->config->item('ups_rate_url');
        $this->ups_tnt_url = $this->_CI->config->item('ups_transit_url');
        $this->shiper = $this->_CI->config->item('ups_shiper');
    }

    public function generateToken($sessionId) {
        $postData = 'grant_type=1&custom_claims=' . urlencode(json_encode(['sessionid' => $sessionId]));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ups_oath_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->ups_clientid . ':' . $this->ups_secret),
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: ups_language_preference=fr_CA'
            ),
        ));
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            $out['error'] = 1;
            $out['msg'] = $error;
        } else {
            $apiResponse = json_decode($response,TRUE);
            if (isset($apiResponse['response'])) {
                $out = $apiResponse['response'];
            } else {
                $out = $apiResponse;
            }
            $out['error'] = 0;
        }
        return $out;
    }

    public function refreshToken($token) {
        $curl = curl_init();
        $payload = "grant_type=refresh_token&refresh_token=".$token;

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded",
                'Authorization: Basic ' . base64_encode($this->ups_clientid . ':' . $this->ups_secret),
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_URL => $this->ups_tokenrefresh_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            $out = [
                'error' => 1,
                'code' => $error,
            ];
        } else {
            $apiResponse = json_decode($response, true);
            if (isset($apiResponse['response'])) {
                $out = $apiResponse['response'];
            } else {
                $out = $apiResponse;
            }
            $out['error'] = 0;
        }
        return $out;
    }

    public function getRates($token, $shipTo, $shipFrom, $numPack, $packDimens, $packWeight ) {
        $query = array(
            // "additionalinfo" => "timeintransit"
        );

        $curl = curl_init();

        $payload = array(
            "RateRequest" => array(
                "Request" => array(
                    "TransactionReference" => array(
                        "CustomerContext" => "CustomerContext",
                        "TransactionIdentifier" => "TransactionIdentifier"
                    )
                ),
                "Shipment" => array(
                    "Shipper" => $this->shiper,
                    "ShipTo" => $shipTo,
                    "ShipFrom" => $shipFrom,
                    "PaymentDetails" => array(
                        "ShipmentCharge" => array(
                            "Type" => "01",
                            "BillShipper" => array(
                                "AccountNumber" => $this->_CI->config->item('ups_account_number'),
                            )
                        )
                    ),
                    "Service" => array(
                        "Code" => "03",
                        "Description" => "Ground"
                    ),
                    "ShipmentTotalWeight" => array(
                        "UnitOfMeasurement" => array(
                            "Code" => "LBS",
                            "Description" => "Pounds"
                        ),
                        "Weight" => $packWeight,
                    ),
                    "NumOfPieces" => $numPack,
                    "Package" => $packDimens,
                )
            )
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
                "transId: string",
                "transactionSrc: testing"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => $this->ups_rate_url."?" . http_build_query($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        $out = [];
        if ($error) {
            $out['error'] = 1;
            $out['msg'] = $error;
        } else {
            $apiResponse = json_decode($response, true);
            if (isset($apiResponse['response'])) {
                $errordat = $apiResponse['response'];
                $out['error'] = 2;
                $msgdat = $errordat['errors'][0];
                $out['msg'] = 'Rating calc error, code '.$msgdat['code'].' - '.$msgdat['message'];
            } else {
                $ratesdat = $apiResponse['RateResponse'];
                $outrates = [];
                foreach ($ratesdat['RatedShipment'] as $item) {
                    $newrate = [];
                    $newrate['service_code'] = $item['Service']['Code'];
                    $newrate['service'] = $item['Service']['Description'];
                    $newrate['billing_weigh'] = $item['BillingWeight']['Weight'];
                    $newrate['rate'] = $item['TotalCharges']['MonetaryValue'];
                    $outrates[] = $newrate;
                }
                $out['error'] = 0;
                $out['rates'] = $outrates;
            }
        }
        return $out;
    }

    public function timeInTransit($token, $shipFrom, $shipTo, $weight, $numpacks, $shipmentprice, $shipdate, $shiptime) {
        $curl = curl_init();

        $payload = array(
            "originCountryCode" => $shipFrom['CountryCode'],
            "originStateProvince" => ifset($shipFrom,'StateProvinceCode', ""),
            "originCityName" => $shipFrom['City'],
            "originTownName" => "",
            "originPostalCode" => $shipFrom['PostalCode'],
            "destinationCountryCode" => $shipTo['CountryCode'],
            "destinationStateProvince" => ifset($shipTo,'StateProvinceCode', ""),
            "destinationCityName" => $shipTo['City'],
            "destinationTownName" => "",
            "destinationPostalCode" => $shipTo['PostalCode'],
            "weight" => $weight,
            "weightUnitOfMeasure" => "LBS",
            "shipmentContentsValue" => "{$shipmentprice}",
            "shipmentContentsCurrencyCode" => "USD",
            "billType" => "03",
            "shipDate" => $shipdate,
            "shipTime" => $shiptime,
            "residentialIndicator" => "",
            "avvFlag" => true,
            "numberOfPackages" => "{$numpacks}"
        );
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
                "transId: string",
                "transactionSrc: testing"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => $this->ups_tnt_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);
        $out = [];
        if ($error) {
            $out['error'] = 1;
            $out['msg'] = "TNT execute Error #:" . $error;
        } else {
            // echo $response;
            $apiResponse = json_decode($response, true);
            if (isset($apiResponse['emsResponse'])) {
                $out['error'] = 0;
                $out['msg'] = "";
                $services = $apiResponse['emsResponse']['services'];
                $outservices = [];
                foreach ($services as $service) {
                    $newservice = [
                        'code' => $service['serviceLevel'],
                        'description' => $service['serviceLevelDescription'],
                        'deliverydate' => $service['deliveryDate'],
                        'deliverytime' => $service['deliveryTime'],
                        'bisnessdays' => $service['businessTransitDays'],
                    ];
                    $outservices[] = $newservice;
                }
                $out['services'] = $outservices;
            } else {
                $out['error'] = 2;
                $msg = ifset($apiResponse, 'validationList', 'Invalid Parameters');
                if (is_array($msg)) {
                    $errmsg = '';
                    if (isset($msg['invalidFieldList'])) {
                        $errmsg.='Invalid Parameter '.$msg['invalidFieldList'][0].' ';
                    }
                    if (isset($msg['invalidFieldListCodes'])) {
                        $errmsg.='Error code # '.$msg['invalidFieldListCodes'][0];
                    }
                    if (!empty($errmsg)) {
                        $msg = $errmsg;
                    } else {
                        $msg = 'Invalid Calculation';
                    }
                }
                $out['msg'] = $msg;
            }
        }
        return $out;
    }

    public function getNegotRates($token, $shipTo, $shipFrom, $numPack, $packDimens, $packWeight ) {
        $query = array(
            // "additionalinfo" => "timeintransit"
        );

        $curl = curl_init();

        $payload = array(
            "RateRequest" => array(
                "Request" => array(
                    "TransactionReference" => array(
                        "CustomerContext" => "CustomerContext",
                    )
                ),
                "Shipment" => array(
                    "Shipper" => $this->shiper,
                    "ShipTo" => $shipTo,
                    "ShipFrom" => $shipFrom,
                    "PaymentDetails" => array(
                        "ShipmentCharge" => array(
                            "Type" => "01",
                            "BillShipper" => array(
                                "AccountNumber" => $this->_CI->config->item('ups_account_number'),
                            )
                        )
                    ),
                    "ShipmentRatingOptions" => array(
                        "TPFCNegotiatedRatesIndicator" => "Y",
                        "NegotiatedRatesIndicator" => "Y"
                    ),
                    "Service" => array(
                        "Code" => "03",
                        "Description" => "Ground"
                    ),
                    "ShipmentTotalWeight" => array(
                        "UnitOfMeasurement" => array(
                            "Code" => "LBS",
                            "Description" => "Pounds"
                        ),
                        "Weight" => $packWeight,
                    ),
                    "NumOfPieces" => $numPack,
                    "Package" => $packDimens,
                )
            )
        );
        echo json_encode($payload).PHP_EOL;
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
                "transId: string",
                "transactionSrc: testing"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => $this->ups_rate_url."?" . http_build_query($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        $out = [];
        if ($error) {
            $out['error'] = 1;
            $out['msg'] = $error;
        } else {
            $apiResponse = json_decode($response, true);
            if (isset($apiResponse['response'])) {
                $errordat = $apiResponse['response'];
                $out['error'] = 2;
                $msgdat = $errordat['errors'][0];
                $out['msg'] = 'Rating calc error, code '.$msgdat['code'].' - '.$msgdat['message'];
            } else {
                $ratesdat = $apiResponse['RateResponse'];
                $outrates = [];
                foreach ($ratesdat['RatedShipment'] as $item) {
                    $newrate = [];
                    $newrate['service_code'] = $item['Service']['Code'];
                    $newrate['service'] = $item['Service']['Description'];
                    $newrate['billing_weigh'] = $item['BillingWeight']['Weight'];
                    $newrate['rate'] = $item['TotalCharges']['MonetaryValue'];
                    $outrates[] = $newrate;
                }
                $out['error'] = 0;
                $out['rates'] = $outrates;
            }
        }
        return $out;
    }


}