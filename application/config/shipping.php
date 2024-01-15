<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Shipping Parameters */
$config['country_code']='US';
$config['zip']='07012';
$config['city']='Clifton';
$config['ship_service_code']='00';
$config['rate_char']='Regular+Daily+Pickup';
$config['ups_resident']='01';
$config['tax']=7;
$config['default_inpack']=400;
$config['default_pack_width']=24;
$config['default_pack_heigth']=14;
$config['default_pack_depth']=13;
/* Library UPS configs */
$config['ups_access_key']=getenv('UPS_ACCESS_KEY');
$config['ups_account_username']=getenv('UPS_ACCOUNT_USERNAME');
$config['ups_account_password']= getenv('UPS_ACCOUNT_PASSWORD');
$config['ups_account_number']=getenv('UPS_ACCOUNT_NUMBER');
// $config['ups_transit_url']='https://onlinetools.ups.com/ups.app/xml/TimeInTransit';
/* Current Version */
//$config['ups_transit_url']='https://wwwcie.ups.com/webservices/TimeInTransit';
//$config['ups_rate_url']='https://wwwcie.ups.com/webservices/Rate';
// New Config
$config['ups_transit_url']='https://onlinetools.ups.com/webservices/TimeInTransit';
$config['ups_rate_url']='https://onlinetools.ups.com/webservices/Rate';
switch ($_SERVER['SERVER_NAME']) {
    case 'bluetrack.sys':
        $config['ups_transit_url']='https://onlinetools.ups.com/webservices/TimeInTransit';
        $config['ups_rate_url']='https://onlinetools.ups.com/webservices/Rate';
        break;
}
$config['ups_track_url']='https://onlinetools.ups.com/webservices/Track';
$config['wsdl_path']=BASEPATH.'../wsdl/';
$config['ups_address_valid']='https://onlinetools.ups.com/webservices/XAV';
/* Fedex Configs */
//$config['fedex_key']='PYwTLC8HpkdMpigr';
//$config['fedex_account']='510087240';
//$config['fedex_meter']='100278692';
//$config['fedex_password']='14gQ0MfporKJkhRcLNA7EmVKm';

// Env for UPS_SERVICE lib
$config['ups_access_key']=getenv('UPS_ACCESS_KEY');
$config['ups_account_username']=getenv('UPS_ACCOUNT_USERNAME');
$config['ups_account_password']= getenv('UPS_ACCOUNT_PASSWORD');
$config['ups_account_number']=getenv('UPS_ACCOUNT_NUMBER');

$config['ups_clientid'] = getenv('UPS_CLIENTID');
$config['ups_secret'] = getenv('UPS_SECRET');
$config['ups_oath_url'] = getenv('UPS_OATHTOKEN_URL');
$config['ups_tokenrefresh_url'] = getenv('UPS_TOKENREFRESH_URL');
$config['ups_transit_url']=getenv('UPS_TRANSIT_URL');
$config['ups_rate_url']=getenv('UPS_RATE_URL');

$config['ups_shiper'] = array(
    "Name" => "BLUETRACK, Inc.",
    "ShipperNumber" => $config['ups_account_number'],
    "Address" => array(
        "AddressLine" => array(
            "855 Bloomfield Ave"
        ),
        "City" => $config['city'],
        "StateProvinceCode" => "NJ",
        "PostalCode" => $config['zip'],
        "CountryCode" => $config['country_code'],
    ),
);

