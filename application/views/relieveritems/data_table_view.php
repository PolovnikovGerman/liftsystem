<div class="tabledataarea">
<?php $numpp=0;?>
<?php foreach ($items as $item) { ?>
    <div class="tabrow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$expand==1 ? 'expand' : ''?>">
        <div class="dataview" data-item="<?=$item['item_id']?>" data-event="hover" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="right"
             data-balloon="{ajax} /dbitems/itemmainimage?v=<?=$item['item_id']?>">
            <i class="fa fa-search" aria-hidden="true"></i>
        </div>
        <div class="numberpp"><?=$item['numpp']?></div>
        <div class="status <?=$item['rowclass']?>"><?=$item['status']?></div>
        <div class="relivitemedit" data-item="<?=$item['item_id']?>">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="itemnumber <?=$item['rowclass']?>"><?=$item['item_number']?></div>
        <div class="itemname <?=$item['rowclass']?>"><?=$item['item_name']?></div>
        <div class="missinfo <?=$item['misclas']?>"><?=$item['misinfo']?></div>
    </div>
    <?php $numpp++;?>
<?php } ?>
</div>