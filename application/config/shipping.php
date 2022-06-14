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
$config['ups_access_key']='0DB6A3DB0A2D50C1';
$config['ups_account_username']='Bluetrackship';
$config['ups_account_password']= '2002Phoenix!'; // '100443!retroblue';
$config['ups_account_number']='084YR7';
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

// $config['']