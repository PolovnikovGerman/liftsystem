<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = getenv('Sandbox');
$config['APIVersion'] = getenv('APIVersion');
$config['APIUsername'] = getenv('APIUsername');
$config['APIPassword'] = getenv('APIPassword');
$config['APISignature'] = getenv('APISignature');
$config['DeviceID'] = '';
$config['ApplicationID'] = getenv('ApplicationID');
$config['DeveloperEmailAccount'] = getenv('DeveloperEmailAccount');
$config['APIUsernameSR'] = getenv('APIUsernameSR');
$config['APIPasswordSR'] = getenv('APIPasswordSR');
$config['APISignatureSR'] = getenv('APISignatureSR');
$config['APIVersionSR'] = getenv('APIVersionSR');
/* End of file paypal.php */
/* Location: ./system/application/config/paypal.php */
