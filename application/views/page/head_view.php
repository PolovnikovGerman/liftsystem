<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?=ifset($_ci_vars,'title', 'Lift System')?></title>
<link href='/css/page_view/bootstrap.min.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="/css/page_view/font-awesome.min.css">
<link rel="stylesheet" href="/css/page_view/main.css?v=<?=getsitecssversion()?>">
<link rel="stylesheet" href="/css/jsflash/flash.css">
<?php foreach ($styles as $row) { ?>
    <link rel="stylesheet" href="<?=$row['style']?>?v=<?=getsitecssversion()?>">
<?php } ?>
<link rel="stylesheet" href="/css/page_view/jquery.qtip.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="/js/jquery.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/adminpage/page.js?v=<?=getsitejsversion()?>"></script>
<script src="/js/jsflash/flash.js"></script>
<?php foreach ($scripts as $row) { ?>
    <script src="<?=$row['src']?>?v=<?=getsitejsversion()?>"></script>
<?php } ?>
<script src="/js/adminpage/jquery.qtip.js"></script>