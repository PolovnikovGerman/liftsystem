<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = FALSE;
$config['APIVersion'] = '85.0';
$config['APIUsername'] = $config['Sandbox'] ? 'gopblu_1358172428_biz_api1.gmail.com' : 'sales_api1.bluetrack.com';
$config['APIPassword'] = $config['Sandbox'] ? '1358172461' : 'ZGNV35QCGMW8ZR68';
$config['APISignature'] = $config['Sandbox'] ? 'AUP00OULlbLvEyPCwhdJBTm-5vByAY5wqRoxcXs.h5-5AhDO8wqSS-ks' : 'AFcWxV21C7fd0v3bYYYRCpSSRl31AHLwGUIVC5P9Z8LxgbY0j03572D5';
$config['DeviceID'] = $config['Sandbox'] ? '' : '';
$config['ApplicationID'] = $config['Sandbox'] ? 'APP-80W284485P519543T' : 'APP-5LX65343F6899853U';
$config['DeveloperEmailAccount'] = $config['Sandbox'] ? 'polovnikov.g@gmail.com' : 'sales@bluetrack.com';
$config['test_server']='bluetrack.sys';

/*
$config['PayFlowUsername'] = $config['Sandbox'] ? 'tester' : 'PRODUCTION_USERNAME_GOGES_HERE';
$config['PayFlowPassword'] = $config['Sandbox'] ? 'Passw0rd~' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['PayFlowVendor'] = $config['Sandbox'] ? 'angelleye' : 'PRODUCTION_VENDOR_GOES_HERE';
$config['PayFlowPartner'] = $config['Sandbox'] ? 'PayPal' : 'PRODUCTION_PARTNER_GOES_HERE';
*/
/* End of file paypal.php */
/* Location: ./system/application/config/paypal.php */