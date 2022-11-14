<?php $numpp=1;?>
<?php foreach ($datas as $data) { ?>
    <div class="datarow <?=$numpp%2==0 ? 'greyrow' : 'whiterow'?>">
        <div class="category_name"><?=$data['category_name']?></div>
        <div class="amountvalue <?=$data['amount_class']?>"><?=$data['amount_out']?></div>
        <div class="percentvalue"><?=$data['amount_perc']?></div>
    </div>
    <?php $numpp++;?>
<?php } ?>
