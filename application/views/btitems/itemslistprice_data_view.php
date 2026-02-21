<?php $numpp=0;?>
<?php foreach ($items as $item) : ?>
    <div class="tabrow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="dataview" data-event="hover" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="right"
             data-balloon="{ajax} /dbitems/itemmainimage?v=<?=$item['item_id']?>" data-delay="1000" data-timer="4000">
            <i class="fa fa-search" aria-hidden="true"></i>
        </div>
        <div class="numberpp"><?=$item['numpp']?></div>
        <div class="status <?=$item['rowclass']?>"><?=$item['status']?></div>
        <div class="btitemedit" data-item="<?=$item['item_id']?>">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="suplier <?=$item['rowclass']?> <?=$item['vendorclass']?>"><?=$item['vendor']?></div>
        <div class="itemnumber <?=$item['rowclass']?>"><?=$item['item_number']?></div>
        <div class="itemnameprice <?=$item['rowclass']?>"><?=$item['item_name']?></div>
        <?php foreach ($prices as $price) : ?>
            <div class="itemprice <?=$item['profitclass'.$price['base']]?>">
                <?=empty($item['profitprc'.$price['base']]) ? '&nbsp;' : $item['profitprc'.$price['base']].'%'?>
            </div>
        <?php endforeach; ?>
        <div class="lastupdate truncateoverflowtext <?=$item['updclass']?>"><?=$item['lastupdate']?></div>
    </div>
    <?php $numpp++;?>
<?php endforeach; ?>