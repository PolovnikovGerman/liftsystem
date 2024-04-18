<?php
$config['js_version'] = '2.02259';
$config['css_version']= '2.02259';
/* Default Profit percent */
$config['default_profit']=40;
$config['default_brand']=1;
$config['default_brand_name']='BT';
// $config['netprofit_start']=1349038800;
$config['netprofit_start']=strtotime('10/01/2012 00:00:00');
$config['netprofit_bank_start']=strtotime('12/29/2014 00:00:00');
// $config['balance_start']=946677600; // ('01/01/2000 00:00:00');
$config['balance_start']=strtotime('01/01/2000 00:00:00');
$config['default_ccfee']=3;
/* Short name */
$config['system_name']="Lift System";
$config['redraw_system_name']='Redraw System';
$config['redraw_role']='redraw';
$config['system_permissions']='System';
/* Bank calendar */
$config['bank_calendar']=3;
/* Auth Payment Fee */
$config['auth_amexfee']=3.5;
$config['auth_vmdfee']=0;
/* Paypal Fee */
$config['paypal_amexfee']=3.25;
$config['paypal_vmdfee']=2.2;
$config['datenewfee']= strtotime('2023-03-09');
$config['paypal_amexfeenew'] = 2.11;
$config['paypal_vmdfeenew']=2.11;
// $config['art_dept_email']="logosbt@gmail.com";
/* CONSTANTS */
/* VIRTUAL ITEMS */
$config['other_id']=-1;
$config['multy_id']=-2;
$config['custom_id']=-3;
$config['custom_proof_time'] = 30;
$config['other_proof_time'] = 7;
$config['custom_itemnum']='00-ZZ000';
$config['minimal_profitperc']=20;
switch($_SERVER['SERVER_NAME']){
    case 'tempsys.net':
    case 'tempsys.golden-team.org':
    case 'lifttest.stressballs.com':
        $config['sean_email']='polovnikov.g@gmail.com';
        $config['sage_email']='to_german@yahoo.com';
        $config['taisen_email']='polovnikov.german@gmail.com';
        $config['email_notification_sender']='support@golden-team.org';
        $config['redraw_email']='redraw@golden-team.org';
        $config['art_dept_email']="art@golden-team.org";
        $config['art_srdept_email']="art-sr@golden-team.org";
        $config['taisenkatakura_email']='taisen@test.ru';
        $config['customer_notification_sender']='sales@bluetrack.com';
        $config['customer_notification_relievers'] = 'sales@stressrelievers.com';
        $config['developer_email']='polovnikov.g@gmail.com';
        $config['newprooflnk']='http://test.bluetrack.com/proofview/?doc=';
        break;
    default :
        $config['sean_email']='sean@bluetrack.com';
        $config['sage_email']='sage@bluetrack.com';
        $config['taisen_email']='taisen@bluetrack.com';
        $config['email_notification_sender']='sales@bluetrack.com';
        $config['redraw_email']='redraw@bluetrack.com';
        $config['art_dept_email']="art@bluetrack.com";
        $config['art_srdept_email']="art@stressrelievers.com";
        $config['customer_notification_sender']='sales@bluetrack.com';
        $config['customer_notification_relievers'] = 'sales@stressrelievers.com';
        $config['taisenkatakura_email']='taisenkatakura321@yahoo.com';
        // $config['developer_email']='polovnikov.g@gmail.com';
        $config['developer_email']='german.polovnikov@bluetrack.com';
        $config['newprooflnk']='https://www.bluetrack.com/proofview/?doc=';
        break;
}
$config['netexportsecure']=getenv('netexportsecure');
$config['netexportdata']=getenv('netexportdata');
$config['clayexportdata'] = getenv('clayexportdata');
$config['email_setup']=array(
    'protocol'=>'sendmail',
    'charset'=>'utf8',
    'wordwrap'=> TRUE,
);
switch($_SERVER['SERVER_NAME']){
    case 'lift.local':
        $config['artorderdaily_to']='polovnikov.g@gmail.com';
        $config['artorderdaily_cc']=array(
            'polovnikov.german@gmail.com',
            'to_german@yahoo.com',
        );
        break;
    default :
        $config['artorderdaily_to']='sean@bluetrack.com';
        $config['artorderdaily_cc']=array(
            'sage@bluetrack.com',
            'art@bluetrack.com',
            'robert@bluetrack.com',
            /* 'darrell.martin@bluetrack.com', */
        );
        break;
}
$config['profitpts']=0.08;
$config['goalestim']=1.20;
$config['cmpprofitpts']=0.10;
$config['leadpts']=0.025;
$config['order_system']='Bluetrack ';
$config['default_paysystem']='paypal';
$config['localserver']=array(
    'tempsys.net',
    'tempsys.golden-team.org',
    'lift_stressballs.local',
    'lift.local',
);
$config['salestax']=7;
$config['datenewtax']= strtotime('2018-07-23');
$config['salesnewtax']=6.625;
$config['outsalestax']=6.625;
$config['report_vendors']=array(
    'Ariel',
    'Alpi',
    'Mailine',
    'Pinnacle',
    'Jetline',
);
// Delivery Services
$config['delivery_service']=array(
    'UPS', /* 'FedEx', 'DHL', 'US Postal',*/ 'Local Truck', 'Freight', 'Other',
);
// Track Service
$config['tracking_service']=array(
    'UPS', 'FedEx', 'DHL', 'US Postal',
);
// Default QTY
$config['defqty_common']=150;
$config['defqty_custom']=1000;
// Minimal timeout of lock - 20 min
$config['max_lock_time']=180;
// Timeout for edit - 10 min - JS - X 1000
$config['loctimeout']=130; // 130
$config['loctimeout_local']=6000; //1200;
// Default Inventory Vendor && Payment Method
$config['inventory_vendor']=getenv('INVENTORY_VENDOR');
$config['inventory_paymethod']=5;
// Minimal # of weeks
$config['min_stockperc']=33;
$config['invoutstock']=25;
$config['invlowstock']=50;
// Minimal part of Week to show
$config['show_rolerptweek']=13;
// Event Date - add 12 Hours
$config['event_time']=43200;
$config['logip']=array(
    '91.218.99.68',
    '127.0.0.1',
    '91.219.52.152',
    '188.163.96.162',
);
$config['maxstoretime']=2592000;
$config['bonus_500']=1;
$config['bonus_1000']=2;
$config['bonus_1200']=5;
$config['bonus_price']=5;
$config['bonus_week_base']=150;
$config['netprofit_debt_start']=0; //-160000;
$config['bonus_time']=strtotime('2019-03-04');
/* Diff for pricing - in Cents */
$config['price_diff']=3;
$config['dbview_perpage'] = 200;
$config['meta_title']='BLUETRACK, Inc.';
$config['meta_keywords']='blue track, blue trac, blue trak, blutrack, blu track, blue tracks, bluetrack company';
$config['meta_description']='BLUETRACK is an industry leading manufacturer and seller of stress balls and other promotional products. Since 2001, Blue Track has developed a long history of providing excellent quality products with exceptional customer service. Headquartered in Clifton, NJ, with close proximity to New York City, BLUETRACK serves customers in all 50 states and several countries around the world.';
$config['bottom_text']="BLUETRACK is America’s source for stress balls with an ever expanding line of over 1200 in-stock customizable stress relievers and the ability to manufacture just about any custom shaped stress ball you can think of. With a focus on delivering high value to our customers, that which we define as top quality for a reasonable price, and a commitment to excellent customer service there is no wonder why we have grown to become an industry leader in the field. Whether you need your stress balls delivered in two weeks or two days we are your source with our standard 5-7 business day and available next business day rush production options. Delight in our larger imprint sizes and in our commitment to quality through our three guarantees of timeliness, price and satisfaction. So whether you are just browsing or know that you are ready to place an order, feel free to contact us today and we would be more than happy to assist you";
$config['max_slider_galleryitems'] = 12;
$config['max_slider_casestudy'] = 4;
$config['cardflds'] = array(
        array('idx' => 'Art Upload', 'name' => 'Art Upload'),
        array('idx' => 'order_qty', 'name' => 'Item Qty'),
        array('idx' => 'ship_firstname', 'name' => 'Shipping:First name'),
        array('idx' => 'ship_lastname', 'name' => 'Shipping:Last Name'),
        array('idx' => 'ship_company', 'name' => 'Shipping:Company'),
        array('idx' => 'ship_street1', 'name' => 'Shipping:Address 1'),
        array('idx' => 'ship_street2', 'name' => 'Shipping:Address 2'),
        array('idx' => 'ship_cityname', 'name' => 'Shipping:City'),
        array('idx' => 'ship_state', 'name' => 'Shipping:State'),
        array('idx' => 'ship_zipcode', 'name' => 'Shipping:Zip'),
        array('idx' => 'ship_country', 'name' => 'Shipping:Country'),
        array('idx' => 'phonenum', 'name' => 'Contact:Phone'),
        array('idx' => 'emailaddr', 'name' => 'Contact:Email'),
        array('idx' => 'firstname', 'name' => 'Billing:First Name'),
        array('idx' => 'lastname', 'name' => 'Billing:Last Name'),
        array('idx' => 'company', 'name' => 'Billing:Company'),
        array('idx' => 'address1', 'name' => 'Billing:Address 1'),
        array('idx' => 'address2', 'name' => 'Billing:Address 2'),
        array('idx' => 'cityname', 'name' => 'Billing:City'),
        array('idx' => 'state', 'name' => 'Billing:State'),
        array('idx' => 'zipcode', 'name' => 'Billing:Zip'),
        array('idx' => 'country', 'name' => 'Billing:Country'),
        array('idx' => 'ship_method', 'name' => 'Shipping Method'),
        array('idx' => 'cctype', 'name' => 'CC:Type'),
        array('idx' => 'ccnumber', 'name' => 'CC:Number'),
        array('idx' => 'ccexpmonth', 'name' => 'CC:Exp. month'),
        array('idx' => 'ccexpyear', 'name' => 'CC:Exp. year'),
        array('idx' => 'ccverification', 'name' => 'CC:CVS2'),
);
    
$config['normal_price_base']=array(
    25, 75, 150, 250, 500, 1000, 2500, 5000, 10000, 20000,        
);

$config['perpage_orders'] = ['250','500','1000'];
$config['leads_perpage'] = 250;
$config['quotes_perpage'] = 250;
$config['item_specialchars'] = [
    'item_size', 'item_name', 'item_description1', 'item_description2', 'item_metadescription', 'item_metakeywords', 'item_meta_title',
];
$config['cardflds'] = [
    array('idx' => 'Art Upload', 'name' => 'Art Upload'),
    array('idx' => 'order_qty', 'name' => 'Item Qty'),
    array('idx' => 'ship_firstname', 'name' => 'Shipping:First name'),
    array('idx' => 'ship_lastname', 'name' => 'Shipping:Last Name'),
    array('idx' => 'ship_company', 'name' => 'Shipping:Company'),
    array('idx' => 'ship_street1', 'name' => 'Shipping:Address 1'),
    array('idx' => 'ship_street2', 'name' => 'Shipping:Address 2'),
    array('idx' => 'ship_cityname', 'name' => 'Shipping:City'),
    array('idx' => 'ship_state', 'name' => 'Shipping:State'),
    array('idx' => 'ship_zipcode', 'name' => 'Shipping:Zip'),
    array('idx' => 'ship_country', 'name' => 'Shipping:Country'),
    array('idx' => 'phonenum', 'name' => 'Contact:Phone'),
    array('idx' => 'emailaddr', 'name' => 'Contact:Email'),
    array('idx' => 'firstname', 'name' => 'Billing:First Name'),
    array('idx' => 'lastname', 'name' => 'Billing:Last Name'),
    array('idx' => 'company', 'name' => 'Billing:Company'),
    array('idx' => 'address1', 'name' => 'Billing:Address 1'),
    array('idx' => 'address2', 'name' => 'Billing:Address 2'),
    array('idx' => 'cityname', 'name' => 'Billing:City'),
    array('idx' => 'state', 'name' => 'Billing:State'),
    array('idx' => 'zipcode', 'name' => 'Billing:Zip'),
    array('idx' => 'country', 'name' => 'Billing:Country'),
    array('idx' => 'ship_method', 'name' => 'Shipping Method'),
    array('idx' => 'cctype', 'name' => 'CC:Type'),
    array('idx' => 'ccnumber', 'name' => 'CC:Number'),
    array('idx' => 'ccexpmonth', 'name' => 'CC:Exp. month'),
    array('idx' => 'ccexpyear', 'name' => 'CC:Exp. year'),
    array('idx' => 'ccverification', 'name' => 'CC:CVS2'),
];
$config['orders_perpage'] = [100, 250, 500, ];
$config['notification_systems'] = [
    'New Orders',
    'Emails - Questions',
    'Emails - Custom SB',
    'Emails - Leads',
    'Emails - Testimonials',
    'Emails - Signups',
    'Emails - Proof Requests',
    'Researcher Report',
];
$config['img_path']=BASEPATH.'../img/';
$config['item_quote_images'] = 'http://'.$_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_NAME']=='bluetrack.com' || $_SERVER['SERVER_NAME']=='www.bluetrack.com') {
    $config['item_quote_images'] = 'https://'.$_SERVER['SERVER_NAME'];
}
$config['geo_apikey'] = getenv('GEOIP_KEY');
// $config['googlemapapi'] = getenv('GOOGLEMAP_KEY');
$config['message_subject']='Bluetrack.com Research Question';
$config['test_server'] = getenv('TEST_SERVER');
if ($config['test_server']==1) {
    $config['mail_research']='to_german@yahoo.com';
    $config['mail_sales']='sales@golden-team.org';
    $config['mail_direct_from']='direct@golden-team.org';
    $config['mail_artdepart']="art@golden-team.org";
    $config['mail_artdepart_cc']=array(
        'sage@golden-team.org',
        'sean@golden-team.org'
    );
    $config['mail_research_from']='research@golden-team.org';
    // $config['email_notification_sender']='grey@golden-team.org';
    // $config['customer_notification_sender']='sales@golden-team.org';
    $config['proofrequest_notification']='proof-req@golden-team.org';
} else {
    $config['mail_research']='sean@bluetrack.com';
    $config['mail_sales']='sales@bluetrack.com';
    $config['mail_direct_from']='direct@bluetrack.com';
    $config['mail_artdepart']="art@bluetrack.com";
    $config['mail_artdepart_cc']=array(
        'sage@bluetrack.com',
        'sean@bluetrack.com'
    );
    $config['mail_research_from']='research@bluetrack.com';
    // $config['email_notification_sender']='grey@bluetrack.com';
    // $config['customer_notification_sender']='sales@bluetrack.com';
    $config['proofrequest_notification']='proof-req@bluetrack.com';
}
$config['prooflnk']='https://www.bluetrack.com/proofs/';
$config['newprooflnk']='https://www.bluetrack.com/proofview/?doc=';
$config['debug_mode'] = (getenv('TEST_SERVER')==1 ? '1' : 0);
$config['default_country'] = 223;
$config['google_map_key'] = getenv('GOOGLEMAPAPI_KEY');
$config['srrepeat_cost'] = 12;