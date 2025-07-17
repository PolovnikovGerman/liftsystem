<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?=ifset($_ci_vars,'title', 'Lift System')?></title>
<link rel="shortcut icon" href="/img/lift_favicon_180x180.ico" type="image/ico">
<!-- IOS -->
<link rel="apple-touch-icon" href="/img/lift_favicon_180x180.ico">
<link href='/css/page_view/bootstrap.min.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="/css/page_view/font-awesome.min.css">
<link rel="stylesheet" href="/css/page_view/main_new.css?v=<?=getsitecssversion()?>">
<link rel="stylesheet" href="/css/jsflash/flash.css">
<link rel="stylesheet" href="/css/mb_ballons/mb.balloon.css"/>
<?php foreach ($styles as $row) { ?>
    <link rel="stylesheet" href="<?=$row['style']?>?v=<?=getsitecssversion()?>">
<?php } ?>
<link rel="stylesheet" href="/css/page_view/jquery.qtip.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/css/page_view/simplebar.min.css"/>

<script src="/js/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>

<script src="/js/bootstrap.min.js"></script>
<script src="/js/adminpage/page.js?v=<?=getsitejsversion()?>"></script>
<script src="/js/jsflash/flash.js"></script>
<script src="/js/mb_balloons/jquery.mb.balloon.js"></script>
<?php foreach ($scripts as $row) { ?>
    <script src="<?=$row['src']?>?v=<?=getsitejsversion()?>"></script>
<?php } ?>
<script src="/js/adminpage/jquery.qtip.js?v=<?=getsitejsversion()?>"></script>
<script type="text/javascript" src="/js/adminpage/simplebar.min.js"></script>
<?php if ($gmaps==1) { ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=$this->config->item('google_map_key')?>&libraries=places&v=weekly&callback=initAutocomplete" async></script>
<?php } ?>
<?php if (isset($outscripts)) { ?>
    <?php foreach($outscripts as $row) {?>
        <script type="text/javascript" src="<?=$row['src']?>"></script>
    <?php } ?>
<?php } ?>

