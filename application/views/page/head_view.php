<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?=ifset($_ci_vars,'title', 'Lift System')?></title>
<link rel="shortcut icon" href="/img/lift_favicon_180x180.ico" type="image/ico">
<!-- IOS -->
<link rel="apple-touch-icon" href="/img/lift_favicon_180x180.ico">
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous"> -->
<link href='/css/page_view/bootstrap.min.css' rel='stylesheet' type='text/css'>
<!-- <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'> -->
<link rel="stylesheet" href="/css/page_view/font-awesome.min.css">
<link rel="stylesheet" href="/css/page_view/main.css?v=<?=getsitecssversion()?>">
<link rel="stylesheet" href="/css/jsflash/flash.css">
<link rel="stylesheet" href="/css/mb_ballons/mb.balloon.css"/>
<?php foreach ($styles as $row) { ?>
    <link rel="stylesheet" href="<?=$row['style']?>?v=<?=getsitecssversion()?>">
<?php } ?>
<link rel="stylesheet" href="/css/page_view/jquery.qtip.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="/js/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script> -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/adminpage/page.js?v=<?=getsitejsversion()?>"></script>
<script src="/js/jsflash/flash.js"></script>
<script src="/js/mb_balloons/jquery.mb.balloon.js"></script>
<?php foreach ($scripts as $row) { ?>
    <script src="<?=$row['src']?>?v=<?=getsitejsversion()?>"></script>
<?php } ?>
<script src="/js/adminpage/jquery.qtip.js?v=<?=getsitejsversion()?>"></script>
<?php if ($gmaps==1) { ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=$this->config->item('google_map_key')?>&libraries=places&v=weekly&callback=initAutocomplete" async></script>
<?php } ?>
<?php if ($googlefont==1) { ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
<?php } ?>

<?php if (isset($outscripts)) { ?>
    <?php foreach($outscripts as $row) {?>
        <script type="text/javascript" src="<?=$row['src']?>"></script>
    <?php } ?>
<?php } ?>

