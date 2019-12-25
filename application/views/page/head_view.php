<meta charset="UTF-8">
<title>Lift System</title>
<link rel="stylesheet" href="/css/page_view/main.css">
<?php foreach ($styles as $row) { ?>
    <link rel="stylesheet" href="<?=$row['style']?>?v=<?=getsitecssversion()?>">
<?php } ?>
<script src="/js/jquery.min.js"></script>
<script src="/js/adminpage/page.js"></script>
<?php foreach ($scripts as $row) { ?>
    <script src="<?=$row['src']?>?v=<?=getsitejsversion()?>"></script>
<?php } ?>
