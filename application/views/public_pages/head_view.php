<meta charset="UTF-8">
<title><?=$title?></title>
<link rel="stylesheet" href="/css/page_view/public.css">
<link rel="stylesheet" href="/css/page_view/bootstrap.min.css">
<?php if (isset($styles) && is_array($styles)) { ?>
    <?php foreach ($styles as $row) { ?>
        <link rel="stylesheet" href="<?=$row['style']?>?v=<?=getsitecssversion()?>">
    <?php } ?>
<?php } ?>
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/signin/signin.js?r="<?=getsitejsversion()?>></script>
