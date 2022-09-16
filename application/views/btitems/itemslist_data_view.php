<?php $numpp=0;?>
<?php foreach ($items as $item) { ?>
    <div class="tabrow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="dataview" data-item="<?=$item['item_id']?>">
            <i class="fa fa-search" aria-hidden="true"></i>
        </div>
        <div class="numberpp"><?=$item['numpp']?></div>
        <div class="status <?=$item['rowclass']?>"><?=$item['status']?></div>
        <div class="btitemedit" data-item="<?=$item['item_id']?>">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="subcategory <?=$item['rowclass']?>"><?=$item['category']?></div>
        <div class="itemnumber <?=$item['rowclass']?>"><?=$item['item_number']?></div>
        <div class="itemname <?=$item['rowclass']?>"><?=$item['item_name']?></div>
        <div class="suplier <?=$item['rowclass']?> <?=$item['vendorclass']?>"><?=$item['vendor']?></div>
        <div class="missinfo <?=$item['rowclass']?> <?=$item['misclas']?>"><?=$item['misinfo']?></div>
    </div>
    <?php $numpp++;?>
<?php } ?>

