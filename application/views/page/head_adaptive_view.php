<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?=ifset($_ci_vars,'title', 'Lift System')?></title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="/css/page_view/font-awesome.min.css">
<link rel="stylesheet" href="/css/page_view/main_adaptive.css?v=<?=getsitecssversion()?>">
<link rel="stylesheet" href="/css/jsflash/flash.css">
<?php foreach ($styles as $row) { ?>
    <link rel="stylesheet" href="<?=$row['style']?>?v=<?=getsitecssversion()?>">
<?php } ?>
<link rel="stylesheet" href="/css/page_view/jquery.qtip.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="/js/adminpage/page.js?v=<?=getsitejsversion()?>"></script>
<script src="/js/jsflash/flash.js"></script>
<?php foreach ($scripts as $row) { ?>
    <script src="<?=$row['src']?>?v=<?=getsitejsversion()?>"></script>
<?php } ?>
<script src="/js/adminpage/jquery.qtip.js"></script>
<?php if ($gmaps==1) { ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=$this->config->item('googlemapapi')?>&libraries=places&v=weekly" async></script>
<?php } ?>