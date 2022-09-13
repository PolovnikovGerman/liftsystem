<?php
/*

EXAMPLE:
// parameters are in order
// 1. Zip code shipping TO
// 2. A UPS service #, or leave empty for shop mode
// 3. Number of packages
// 4. Total weight of all packages in US pounds
// 5. Ship date (unix or string)
// 6. Residential (boolean)
// 7. Zip code
$this->load->library('United_parcel_service');
$resultArr = $this->united_parcel_service->get_rate(60504,'',1,2,'12/25/09',true,'07018');

*/
class United_parcel_service
{
    // ========== CHANGE THESE VALUES TO MATCH YOUR OWN ===========
    private $access_key;
    private $ups_account_username;// Your UPS Account Username
    private $ups_account_password;// Your UPS Account Password
    private $zip_code;// Zipcode you are shipping FROM
    private $ups_account_number;// Your UPS Account Number
    private $ups_transit_url;
    private $ups_rate_url;
    private $ups_address_valid;
    private $ups_track_url;
    private $_CI;
    private $_error_message;

    // ============================================================
    function __construct() {
        $this->_CI =& get_instance();
        $this->_CI->config->load('shipping');
        $this->access_key=$this->_CI->config->item('ups_access_key');
        $this->ups_account_username=$this->_CI->config->item('ups_account_username');
        $this->ups_account_password=$this->_CI->config->item('ups_account_password');
        $this->ups_account_number=$this->_CI->config->item('ups_account_number');
        $this->ups_rate_url=$this->_CI->config->item('ups_rate_url');
        $this->ups_transit_url=$this->_CI->config->item('ups_transit_url');
        $this->ups_address_valid=$this->_CI->config->item('ups_address_valid');
        $this->ups_track_url = $this->_CI->config->item('ups_track_url');
        $this->_error_message='The zip code you entered is invalid.  Please enter a new one '.PHP_EOL.'or'.PHP_EOL.' call us at 1-800-790-6090 if you continue to experience problems.';
    }



    public function ship_time($destination_zip, $destination_country, $number_of_packages, $weight, $ship_date, $from_zip='07012') {
            $access = $this->access_key;
            $userid = $this->ups_account_username;
            $passwd = $this->ups_account_password;
            $wsdl=$this->_CI->config->item('wsdl_path').'tnt/TNTWS.wsdl';
            $operation = "ProcessTimeInTransit";
            $endpointurl = $this->ups_transit_url;
            $outputFileName = $this->_CI->config->item('upload_path_preload')."TNTResult.xml";

            $weight=round($weight/$number_of_packages,1);

            try
            {

                $mode = array
                (
                    'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
                    'trace' => 1,
                );

                // initialize soap client
                $client = new SoapClient($wsdl , $mode);

                //set endpoint url
                $client->__setLocation( $endpointurl );

                //create soap header
                $usernameToken['Username'] = $userid;
                $usernameToken['Password'] = $passwd;
                $serviceAccessLicense['AccessLicenseNumber'] = $access;
                $upss['UsernameToken'] = $usernameToken;
                $upss['ServiceAccessToken'] = $serviceAccessLicense;

                $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
                $client->__setSoapHeaders($header);

                //create soap request
                $requestoption['RequestOption'] = 'TNT';
                $request['Request'] = $requestoption;

                /*$addressFrom['City'] = 'Roswell';*/
                $addressFrom['CountryCode'] = 'US';
                $addressFrom['PostalCode'] = $from_zip;
                $addressFrom['StateProvinceCode'] = $this->getstatefromzip($from_zip);
                $shipFrom['Address'] = $addressFrom;
                $request['ShipFrom'] = $shipFrom;


                $addressTo['CountryCode'] = $destination_country;
                $addressTo['PostalCode'] = $destination_zip;
                if ($destination_country=='US') {
                    $addressTo['StateProvinceCode'] = $this->getstatefromzip($destination_zip);
                }

                $shipTo['Address'] = $addressTo;
                $request['ShipTo'] = $shipTo;

                $pickup['Date'] = $ship_date;
                $request['Pickup'] = $pickup;

                $unitOfMeasurement['Code'] = 'LBS';
                $unitOfMeasurement['Description'] ='Pounds';
                $shipmentWeight['UnitOfMeasurement'] = $unitOfMeasurement;
                $shipmentWeight['Weight'] = $weight;
                $request['ShipmentWeight'] = $shipmentWeight;

                $request['TotalPackagesInShipment'] = $number_of_packages;

                $invoiceLineTotal['CurrencyCode'] = 'USD';
                $invoiceLineTotal['MonetaryValue'] = '100';
                $request['InvoiceLineTotal'] = $invoiceLineTotal;

                $request['MaximumListSize'] = '1';
                // $fw = fopen($outputFileName ,'w');
                // foreach ($request as $key=>$val) {
                    // fwrite($fw , "Request: ".$key.' Val '.$val. "\n");
                // }
                // fclose($fw);

                //get response
                $resp = $client->__soapCall($operation ,array($request));
                //get status
                
                if ($resp->Response->ResponseStatus->Code == '1' && isset($resp->TransitResponse)) {
                    $transit_result=array();
                    if (is_array($resp->TransitResponse->ServiceSummary)) {
                        foreach ($resp->TransitResponse->ServiceSummary as $row) {
                            $transit_result["{$row->Service->Code}"]=array(
                                'code'=>$row->Service->Code,
                                'transit_date'=>$row->EstimatedArrival->Arrival->Date.' '.$row->EstimatedArrival->Arrival->Time,
                                'transit_timestamp'=>strtotime($row->EstimatedArrival->Arrival->Date.' '.$row->EstimatedArrival->Arrival->Time),
                            );
                        }
                    } else {
                        $servCode=$resp->TransitResponse->ServiceSummary->Service->Code;
                        $transit_date=$resp->TransitResponse->ServiceSummary->EstimatedArrival->Arrival->Date.' '.$resp->TransitResponse->ServiceSummary->EstimatedArrival->Arrival->Time;
                        $transit_result[$servCode]=array(
                            'code'=>$servCode,
                            'transit_date'=>$transit_date,
                            'transit_timestamp'=>strtotime($transit_date),
                        );
                    }                    
                    return array('result'=>TRUE, 'times'=>$transit_result);
                } else {
                    if (isset($resp->TimeInTransitResponse->Response->Error->ErrorDescription)) {
                        // $errmsg=$resp->TimeInTransitResponse->Response->Error->ErrorDescription;
                        $fw = fopen($outputFileName ,'w');
                        fwrite($fw , "Exception: \n" . $resp->TimeInTransitResponse->Response->Error->ErrorDescription. "\n");
                        fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
                        fclose($fw);
                        $errmsg=$this->_error_message;
                    } else {
                        $errmsg='Error during execute Time in Transit calculation';
                    }
                    return array('result'=>FALSE,'msg'=>$errmsg);
                }


            }
            catch(Exception $e)
            {
                
                $fw = fopen($outputFileName ,'w');
                fwrite($fw , "Exception: \n" . $e . "\n");
                fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
                fclose($fw);


                /* print_r($e);*/
                return array('result'=>FALSE,'msg'=>$this->_error_message);

            }


    }

    public function ship_rates($destination_zip, $service_type, $number_of_packages, $weight, $ship_date, $residential,$from_zip,$shipto_cnt,$item_length, $item_width, $item_height) {
            //Configuration
            $access = $this->access_key;
            $userid = $this->ups_account_username;
            $passwd = $this->ups_account_password;
            /* WSDL file */
            $wsdl=$this->_CI->config->item('wsdl_path').'rate/RateWS.wsdl';
            $operation = "ProcessRate";

            $endpointurl=$this->ups_rate_url;
            $outputFileName = $this->_CI->config->item('upload_path_preload')."RateResult.xml";

            try {

                $mode = array
                    (
                    'soap_version' => 'SOAP_1_1', // use soap 1.1 client
                    'trace' => 1
                );

                // initialize soap client
                $client = new SoapClient($wsdl, $mode);
                //set endpoint url
                $client->__setLocation($endpointurl);

                //create soap header
                $usernameToken['Username'] = $userid;
                $usernameToken['Password'] = $passwd;
                $serviceAccessLicense['AccessLicenseNumber'] = $access;
                $upss['UsernameToken'] = $usernameToken;
                $upss['ServiceAccessToken'] = $serviceAccessLicense;

                $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);
                $client->__setSoapHeaders($header);


                //get response
                $resp = $client->__soapCall($operation, array($this->processRate($destination_zip, $service_type, $number_of_packages, $weight, $ship_date, $residential,$from_zip,$shipto_cnt,$item_length, $item_width, $item_height)));

                //save soap request and response to file
//                $fw = fopen($outputFileName, 'w');
//                fwrite($fw, "Request: \n" . $client->__getLastRequest() . "\n");
//                fwrite($fw, "Response: \n" . $client->__getLastResponse() . "\n");
//                fclose($fw);

                //get status
                // echo "Response Status: " . $resp->Response->ResponseStatus->Description . "\n";
                $shipping_types = array(
                    '01' => 'UPS Next Day Air',
                    '02' => '2nd Day Air',
                    '03' => ($shipto_cnt=='US' ? 'UPS Ground' : 'UPS Standard'),
                    '07' => 'UPS Express',
                    '08' => 'UPS Expedit',
                    '11' => 'UPS Standard',
                    '12' => 'UPS Three-Day Select',
                    '13' => 'Next Day PM',
                    '14' => 'Next Day AM',
                    '54' => 'UPS Worldwide Express Plus',
                    '59' => 'UPS Second Day Air AM',
                    '65' => 'UPS Saver',
                );

                if ($resp->Response->ResponseStatus->Code==1) {
                    $rates=array();                    
                    if (is_array($resp->RatedShipment)) {
                        foreach ($resp->RatedShipment as $row) {
                            $code=(string)$row->Service->Code;
                            $rate = (double)($row->TotalCharges->MonetaryValue);
                            $rates[$code]=array(
                                "ServiceCode"=>$code,
                                "ServiceName" => $shipping_types[$code],
                                "Rate"=>$rate,
                                "DeliveryDate"=>'',
                            );
                        }
                    } else {
                        $code=(string)$resp->RatedShipment->Service->Code;
                        $rate = (double)($resp->RatedShipment->TotalCharges->MonetaryValue);
                        $rates[$code]=array(
                            "ServiceCode"=>$code,
                            "ServiceName" => $shipping_types[$code],
                            "Rate"=>$rate,
                            "DeliveryDate"=>'',
                        );

                    }                    
                    return array('result'=>TRUE, 'rates'=>$rates);
                } else {
                    if (isset($resp->RatingServiceSelectionResponse->Response->Error->ErrorDescription)) {
                        // $errmsg=$resp->RatingServiceSelectionResponse->Response->Error->ErrorDescription;
                        $errmsg=$this->_error_message;
                    } else {
                        $errmsg='Error during Rate calculation';
                    }
                    return array('result'=>FALSE, 'msg'=>$errmsg);
                }

            } catch (Exception $e) {
                $fw = fopen($outputFileName, 'w');
                fwrite($fw, "Exception: \n" . $e . "\n");
                fwrite($fw, "Request: \n" . $client->__getLastRequest() . "\n");
                fclose($fw);
                /* print_r($e); */
                return array('result'=>FALSE, 'msg'=>$this->_error_message);
            }

    }

    function processRate($destination_zip, $service_type, $number_of_packages, $weight, $ship_date, $residential,$from_zip,$shipto_cnt,$item_length, $item_width, $item_height) {
      //create soap request
      $option['RequestOption'] = 'Shop';
      $request['Request'] = $option;

      $pickuptype['Code'] = '01';
      $pickuptype['Description'] = 'Daily Pickup';
      $request['PickupType'] = $pickuptype;

      $customerclassification['Code'] = '01';
      $customerclassification['Description'] = 'Classfication';
      $request['CustomerClassification'] = $customerclassification;

      $shipper['Name'] = 'Bluetrack';
      $shipper['ShipperNumber'] = $this->_CI->config->item('ups_account_number');
      $address['City'] = 'Clifton';
      $address['StateProvinceCode'] = 'NJ';
      $address['PostalCode'] = '07012';
      $address['CountryCode'] = 'US';
      $shipper['Address'] = $address;
      $shipment['Shipper'] = $shipper;

      $addressTo['PostalCode'] = $destination_zip;
      if ($shipto_cnt=='US') {
          $addressTo['StateProvinceCode'] = $this->getstatefromzip($destination_zip);
      }
      $addressTo['CountryCode'] = $shipto_cnt;
      $addressTo['ResidentialAddressIndicator'] = '';
      $shipto['Address'] = $addressTo;
      $shipment['ShipTo'] = $shipto;

      $addressFrom['PostalCode'] = $from_zip;
      $addressFrom['StateProvinceCode'] = $this->getstatefromzip($from_zip);
      $addressFrom['CountryCode'] = 'US';
      $shipfrom['Address'] = $addressFrom;
      $shipment['ShipFrom'] = $shipfrom;

      $service['Code'] = '03';
      $service['Description'] = 'Service Code';
      $shipment['Service'] = $service;

      $pakages=array();

      for ($i=0; $i<$number_of_packages;$i++) {
            $curpak=array();
            $packaging['Code'] = '02';
            $packaging['Description'] = 'Rate';
            $curpak['PackagingType'] = $packaging;
            if ($item_length>0 || $item_height>0 || $item_width>0) {
                $dunit['Code'] = 'IN';
                $dunit['Description'] = 'inches';
                if ($item_length>0) {
                    $dimensions['Length'] = $item_length;
                }
                if ($item_width>0) {
                    $dimensions['Width'] = $item_width;
                }
                if ($item_height>0) {
                    $dimensions['Height'] = $item_height;
                }
                $dimensions['UnitOfMeasurement'] = $dunit;
                $curpak['Dimensions'] = $dimensions;
            }
            $punit['Code'] = 'LBS';
            $punit['Description'] = 'Pounds';
            $packageweight['Weight'] = round($weight/$number_of_packages,1);
            $packageweight['UnitOfMeasurement'] = $punit;
            $curpak['PackageWeight'] = $packageweight;
            array_push($pakages,$curpak);
      }

      // $shipment['Package'] = array(	$package1 , $package2 );
      $shipment['Package'] = $pakages;
      $shipment['ShipmentServiceOptions'] = '';
      $shipment['LargePackageIndicator'] = '';
      $request['Shipment'] = $shipment;

      return $request;

    }




    public function get_transit($destination_zip, $destination_country, $number_of_packages, $weight, $ship_date, $from_zip='07012' ) {
            $data ="
                <?xml version=\"1.0\" ?>
                <AccessRequest xml:lang='en-US'>
                <AccessLicenseNumber>$this->access_key</AccessLicenseNumber>
                <UserId>$this->ups_account_username</UserId>
                <Password>$this->ups_account_password</Password>
                </AccessRequest>
                <?xml version=\"1.0\" ?>
                <TimeInTransitRequest xml:lang='en-US'>
                <Request>
                <TransactionReference>
                <CustomerContext>TNT_D Origin Country Code</CustomerContext>
                <XpciVersion>1.0002</XpciVersion>
                </TransactionReference>
                <RequestAction>TimeInTransit</RequestAction>
                </Request>
                <TransitFrom>
                <AddressArtifactFormat>
                    <CountryCode>US</CountryCode>
                    <PostcodePrimaryLow>$from_zip</PostcodePrimaryLow>
                </AddressArtifactFormat>
                </TransitFrom>
                <TransitTo>
                <AddressArtifactFormat>
                    <CountryCode>$destination_country</CountryCode>
                    <PostcodePrimaryLow>$destination_zip</PostcodePrimaryLow>
                </AddressArtifactFormat>
                </TransitTo>
                <ShipmentWeight>
                <UnitOfMeasurement>
                <Code>LBS</Code>
                <Description>Pounds</Description>
                </UnitOfMeasurement>
                <Weight>$weight</Weight>
                </ShipmentWeight>
                <TotalPackagesInShipment>$number_of_packages</TotalPackagesInShipment>
                <InvoiceLineTotal>
                <CurrencyCode>USD</CurrencyCode>
                <MonetaryValue>250.00</MonetaryValue>
                </InvoiceLineTotal>
                <PickupDate>$ship_date</PickupDate>
            </TimeInTransitRequest>";
        $ch = curl_init($this->ups_transit_url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result=curl_exec($ch);
        $data = strstr($result, '<?');
        $xml = new SimpleXMLElement($data);
        // var_dump($xml);

        if ($xml->Response->ResponseStatusCode == '1' && isset($xml->TransitResponse)) {
            $transit_result=array();
            foreach ($xml->TransitResponse->ServiceSummary as $row) {
                $transit_result["{$row->Service->Code}"]=array(
                    'code'=>$row->Service->Code,
                    'transit_date'=>$row->EstimatedArrival->Date.' '.$row->EstimatedArrival->Time,
                    'transit_timestamp'=>strtotime($row->EstimatedArrival->Date.' '.$row->EstimatedArrival->Time),
                );
            }
            return $transit_result;
        } else {
            return FALSE;
        }

    }


    public function get_rate($destination_zip, $service_type, $number_of_packages, $weight, $ship_date, $residential,$from_zip='07012') {

        $ci=&get_instance();

        $this->zip_code=$from_zip;

        $request_option = 'Rate';
        if ($service_type == '')
        {
            $request_option = 'Shop';
        }

        if (is_numeric($ship_date))
        {
            $shipDate = date('Y-m-d', $ship_date);
        }
        else
        {
            $shipDate = date('Y-m-d', strtotime($ship_date));
        }

        $resStr = "";
        if ($residential)
        {
            $resStr = "<ResidentialAddressIndicator/>";
        }

        if ($number_of_packages > 1)
        {
            $indPackWeight = $weight / $number_of_packages;
            $i = 0;
            $packageCode = '';
            do {
                $packageCode .= "
                    <Package>
                        <PackagingType><Code>02</Code></PackagingType>
                        <PackageWeight>
                            <UnitOfMeasurement><Code>LBS</Code></UnitOfMeasurement>
                            <Weight>$indPackWeight</Weight>
                        </PackageWeight>
                    </Package>
                ";
                $i++;
            }while($i < $number_of_packages);
        }
        else
        {
            $packageCode = "
                <Package>
                    <PackagingType><Code>02</Code></PackagingType>
                    <PackageWeight>
                        <UnitOfMeasurement><Code>LBS</Code></UnitOfMeasurement>
                        <Weight>".$weight."</Weight>
                    </PackageWeight>
                </Package>
            ";
        }

        $destinationState = $this->getstatefromzip($destination_zip);
        $from_zip = $this->getstatefromzip($this->zip_code);
        $data ="
            <?xml version=\"1.0\"?>
            <AccessRequest xml:lang=\"en-US\">
                <AccessLicenseNumber>" . $this->access_key . "</AccessLicenseNumber>
                <UserId>" . $this->ups_account_username . "</UserId>
                <Password>" . $this->ups_account_password . "</Password>
            </AccessRequest>
            <?xml version=\"1.0\"?>
            <RatingServiceSelectionRequest xml:lang=\"en-US\">
                <Request>
                    <TransactionReference>
                        <CustomerContext>Rate Request From " . $_SERVER['HTTP_HOST'] . "</CustomerContext>
                        <XpciVersion>1.0001</XpciVersion>
                    </TransactionReference>
                    <RequestAction>Rate</RequestAction>
                    <RequestOption>$request_option</RequestOption>
                </Request>
                <PickupType> <Code>01</Code> </PickupType>
                <Shipment>
                    <Shipper>
                        <Address>
                            <PostalCode>" . $this->zip_code . "</PostalCode>
                            <CountryCode>US</CountryCode>
                        </Address>
                        <ShipperNumber>" . $this->ups_account_number . "</ShipperNumber>
                    </Shipper>
                    <ShipTo>
                        <Address>
                        <PostalCode>$destination_zip</PostalCode>
                        <StateProvinceCode>$destinationState</StateProvinceCode>
                        <CountryCode>US</CountryCode>
                        $resStr
                        </Address>
                    </ShipTo>
                    <ShipFrom>
                        <Address>
                        <PostalCode>" . $this->zip_code . "</PostalCode>
                        <StateProvinceCode>$from_zip</StateProvinceCode>
                        <CountryCode>US</CountryCode>
                        </Address>
                    </ShipFrom>
                    <Service>
                        <Code>$service_type</Code>
                    </Service>
                    <ShipmentServiceOptions>
                        <OnCallAir>
                            <Schedule>
                                <PickupDay>$shipDate</PickupDay>
                            </Schedule>
                        </OnCallAir>
                    </ShipmentServiceOptions>
                    $packageCode
                    <RateInformation>
                        <NegotiatedRatesIndicator/>
                    </RateInformation>
                </Shipment>
            </RatingServiceSelectionRequest>
        ";

        $ch = curl_init($this->ups_rate_url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result=curl_exec($ch);
        //echo '<!-- '. $result. ' -->'; // uncomment to debug
        $data = strstr($result, '<?');
        $xml = new SimpleXMLElement($data);



        if ($xml->Response->ResponseStatusCode == '1')
        {
            $shipping_types = array(
                '01' => 'UPS Next Day Air',
                '02' => 'UPS Second Day Air',
                '03' => 'UPS Ground',
                '07' => 'UPS Worldwide Express',
                '08' => 'UPS Worldwide Expedited',
                '11' => 'UPS Standard',
                '12' => 'UPS Three-Day Select',
                '13' => 'Next Day Air Saver',
                '14' => 'UPS Next Day Air Early AM',
                '54' => 'UPS Worldwide Express Plus',
                '59' => 'UPS Second Day Air AM',
                '65' => 'UPS Saver'
            );

            $simplifiedArr = array();
            $index = 0;
            foreach ($xml->RatedShipment as $service)
            {
                $simplifiedArr[$index] = "{$service->TotalCharges->MonetaryValue}";
                $index++;
            }
            asort($simplifiedArr);
            foreach ($simplifiedArr as $key => $value)
            {
                $service = $xml->RatedShipment[$key]->children();

                $DeliveryDateStr = '';

                // $rate = number_format((double)($service->TotalCharges->MonetaryValue), 2);
                $rate = (double)($service->TotalCharges->MonetaryValue);
                $shipping_choices["{$service->Service->Code}"] = array("ServiceCode"=>(string)$service->Service->Code,"ServiceName" => $shipping_types["{$service->Service->Code}"], "Rate" => "{$rate}", "DeliveryDate" => $DeliveryDateStr);
            }

            return $shipping_choices;
        }
        else
        {
            return FALSE;
        }
    }

   function getstatefromzip($zip5) {
            $allstates = array('AK9950099929', 'AL3500036999', 'AR7160072999', 'AR7550275505', 'AZ8500086599', 'CA9000096199', 'CO8000081699', 'CT0600006999', 'DC2000020099', 'DC2020020599', 'DE1970019999', 'FL3200033999', 'FL3410034999', 'GA3000031999', 'HI9670096798', 'HI9680096899', 'IA5000052999', 'ID8320083899', 'IL6000062999', 'IN4600047999', 'KS6600067999', 'KY4000042799', 'KY4527545275', 'LA7000071499', 'LA7174971749', 'MA0100002799', 'MD2033120331', 'MD2060021999', 'ME0380103801', 'ME0380403804', 'ME0390004999', 'MI4800049999', 'MN5500056799', 'MO6300065899', 'MS3860039799', 'MT5900059999', 'NC2700028999', 'ND5800058899', 'NE6800069399', 'NH0300003803', 'NH0380903899', 'NJ0700008999', 'NM8700088499', 'NV8900089899', 'NY0040000599', 'NY0639006390', 'NY0900014999', 'OH4300045999', 'OK7300073199', 'OK7340074999', 'OR9700097999', 'PA1500019699', 'RI0280002999', 'RI0637906379', 'SC2900029999', 'SD5700057799', 'TN3700038599', 'TN7239572395', 'TX7330073399', 'TX7394973949', 'TX7500079999', 'TX8850188599', 'UT8400084799', 'VA2010520199', 'VA2030120301', 'VA2037020370', 'VA2200024699', 'VT0500005999', 'WA9800099499', 'WI4993649936', 'WI5300054999', 'WV2470026899', 'WY8200083199');

            foreach ($allstates as $ziprange)
            {

                if (($zip5 >= substr($ziprange, 2, 5)) && ($zip5 <= substr($ziprange, 7, 5)))
                {
                    return substr($ziprange, 0, 2);
                }
            }

            return;
    }
    
    public function validaddress($zipcode, $cntcode) {
        //Configuration
        $wsdl = $this->_CI->config->item('wsdl_path') . "addressvalidate/XAV.wsdl";
        $operation = "ProcessXAV";
        $endpointurl = $this->ups_address_valid;
        $outputFileName = $this->_CI->config->item('upload_path_preload') . "XOLTResult.xml";
        try {

            $mode = array
                (
                'soap_version' => 'SOAP_1_1', // use soap 1.1 client
                'trace' => 1
            );

            // initialize soap client
            $client = new SoapClient($wsdl, $mode);

            //set endpoint url
            $client->__setLocation($endpointurl);


            //create soap header
            $usernameToken['Username'] = $this->ups_account_username;
            $usernameToken['Password'] = $this->ups_account_password;
            $serviceAccessLicense['AccessLicenseNumber'] = $this->access_key;
            $upss['UsernameToken'] = $usernameToken;
            $upss['ServiceAccessToken'] = $serviceAccessLicense;
            
            $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);
            $client->__setSoapHeaders($header);


            //get response
            $resp = $client->__soapCall($operation, array($this->processXAV($zipcode, $cntcode)));
            // $this->_CI->firephp->log($resp,'Response');
            if ($resp->Response->ResponseStatus->Code == 1) {
                
                // if (isset($resp->Candidate) && (is_array($resp->Candidate)) && count($resp->Candidate)==1) {
                if (isset($resp->Candidate)) {
                    $return_array=array(
                        'result'=>TRUE,
                        'city'=>'',
                        'state'=>'',
                        'state_id'=>'',
                    );                    
                    if (isset($resp->Candidate->AddressKeyFormat->PoliticalDivision2)) {
                        $return_array['city']=$resp->Candidate->AddressKeyFormat->PoliticalDivision2;
                    }
                    if (isset($resp->Candidate->AddressKeyFormat->PoliticalDivision1)) {
                        $return_array['state']=$resp->Candidate->AddressKeyFormat->PoliticalDivision1;

                        $this->_CI->load->model('shipping_model');
                        $statedet=$this->_CI->shipping_model->get_stateups($return_array['state'], $cntcode);
                        if ($statedet['result']==1) {
                            $return_array['state_id']=$statedet['state_id'];
                        }
                    }
                    return $return_array;
                } else {
                    $return_array=array(
                        'result'=>FALSE,
                        // 'msg'=>$ex->getMessage(),
                    );                    
                }
            }
        } catch (Exception $ex) {
            $return_array=array(
                'result'=>FALSE,
                'msg'=>$ex->getMessage(),
            );
            return $return_array;
        }
    }
    
    function processXAV($zipcode, $cntcode) {
        //create soap request
        $option['RequestOption'] = '1';
        $request['Request'] = $option;

        $request['RegionalRequestIndicator'] = '';
        $request['MaximumCandidateListSize']='1';
        $addrkeyfrmt['ConsigneeName'] = 'San Antonio Center for Physical Therapy';
        $addrkeyfrmt['AddressLine'] = array(
            '18518 Hardy Oak Blvd',
        );
        $addrkeyfrmt['Region'] = '';
        $addrkeyfrmt['PoliticalDivision2'] = '';
 	$addrkeyfrmt['PoliticalDivision1'] = '';
 	$addrkeyfrmt['PostcodePrimaryLow'] = $zipcode;
 	$addrkeyfrmt['PostcodeExtendedLow'] = '';
 	$addrkeyfrmt['Urbanization'] = 'porto arundal';
 	$addrkeyfrmt['CountryCode'] = $cntcode;
 	$request['AddressKeyFormat'] = $addrkeyfrmt;
        
        return $request;
  }

    public function trackpackage($package) {
        //Configuration
        $wsdl = $this->_CI->config->item('wsdl_path') . "track/Track.wsdl";
        $operation = "ProcessTrack";
        $endpointurl = $this->ups_track_url;
        $outputFileName = $this->_CI->config->item('upload_path_preload') . "TrackResult.xml";

        try {

            $mode = array
            (
                'soap_version' => 'SOAP_1_1', // use soap 1.1 client
                'trace' => 1
            );

            // initialize soap client
            $client = new SoapClient($wsdl, $mode);

            //set endpoint url
            $client->__setLocation($endpointurl);


            //create soap header
            $usernameToken['Username'] = $this->ups_account_username;
            $usernameToken['Password'] = $this->ups_account_password;
            $serviceAccessLicense['AccessLicenseNumber'] = $this->access_key;
            $upss['UsernameToken'] = $usernameToken;
            $upss['ServiceAccessToken'] = $serviceAccessLicense;

            $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);
            $client->__setSoapHeaders($header);

            //get response
            $resp = $client->__soapCall($operation, array($this->processTrack($package)));
            //get status
            $responsecode=$resp->Response->ResponseStatus->Code;
            if ($responsecode!=1) {
                // Error During Tracking
                if (isset($resp->Response->ResponseStatus->Description)) {
                    $errmsg=$resp->Response->ResponseStatus->Description;
                } else {
                    $errmsg='Error during Tracking';
                }
                return array('result'=>FALSE, 'msg'=>$errmsg);
            }
            $return_array=array(
                'result'=>TRUE,
            );
            $packs=$resp->Shipment->Package;

            $out=array();
            $numpp=1;
            if (is_array($packs)) {
                foreach ($packs as $prow) {
                    $activ = $prow->Activity;
                    $adress='';
                    if (isset($activ->ActivityLocation->Address->City)) {
                        $adress.=$activ->ActivityLocation->Address->City.' ';
                    }
                    if (isset($activ->ActivityLocation->Address->StateProvinceCode)) {
                        $adress.=$activ->ActivityLocation->Address->StateProvinceCode.' ';
                    }
                    if (isset($activ->ActivityLocation->Address->CountryCode)) {
                        $adress.=$activ->ActivityLocation->Address->CountryCode.' ';
                    }
                    if (isset($activ->ActivityLocation->Address->PostalCode)) {
                        $adress.=$activ->ActivityLocation->Address->PostalCode;
                    }
                    $out[]=array(
                        'log_id'=>$numpp*-1,
                        'package_num'=>$prow->TrackingNumber,
                        'status'=>$activ->Status->Description,
                        'date'=>strtotime($activ->Date . ' ' . $activ->Time),
                        'address'=>$adress,
                    );
                    $numpp++;
                }
            } else {
                $activ = $packs->Activity;
                $adress='';
                if (isset($activ->ActivityLocation->Address->City)) {
                    $adress.=$activ->ActivityLocation->Address->City.' ';
                }
                if (isset($activ->ActivityLocation->Address->StateProvinceCode)) {
                    $adress.=$activ->ActivityLocation->Address->StateProvinceCode.' ';
                }
                if (isset($activ->ActivityLocation->Address->CountryCode)) {
                    $adress.=$activ->ActivityLocation->Address->CountryCode.' ';
                }
                if (isset($activ->ActivityLocation->Address->PostalCode)) {
                    $adress.=$activ->ActivityLocation->Address->PostalCode;
                }

                $out[]=array(
                    'log_id'=>$numpp*-1,
                    'package_num'=>$packs->TrackingNumber,
                    'status'=>$activ->Status->Description,
                    'date'=>strtotime($activ->Date . ' ' . $activ->Time),
                    'address'=>$adress,
                );
            }
            $return_array['tracklog']=$out;
            $return_array['trackcode']=$package;
            $return_array['system']='UPS';
            return $return_array;
        } catch (Exception $ex) {
            $return_array=array(
                'result'=>FALSE,
                'msg'=>$ex->getMessage(),
            );
            return $return_array;
        }
    }

    function processTrack($package) {
        //create soap request
        $req['RequestOption'] = '0';
        $tref['CustomerContext'] = 'Add description here';
        $req['TransactionReference'] = $tref;
        $request['Request'] = $req;
        $request['InquiryNumber'] = $package;
        $request['TrackingOption'] = '0';

        return $request;
    }

}