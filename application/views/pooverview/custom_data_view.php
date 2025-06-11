<?php $numpp = 1;?>
<?php if ($cntstand > 0) : ?>
    <?php foreach ($stands as $stand) : ?>
        <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
            <div class="numpp"><?=$numpp?></div>
            <?php if ($stand['days'] >= 0) : ?>
                <div class="arrivedays"><?=$stand['days']?></div>
            <?php else: ?>
                <div class="arrivedays negative">(<?=abs($stand['days'])?>)</div>
            <?php endif; ?>
            <div class="eventdate"><?=$stand['eventdate']?></div>
            <div class="arrivedate"><?=$stand['arrive']?></div>
            <div class="approved <?=$stand['artclass']?>"><?=$stand['artstage']?></div>
            <div class="ordernum" data-order="<?=$stand['order_id']?>"><?=$stand['ordernum']?></div>
            <div class="customer"><?=$stand['customer']?></div>
            <div class="itemname"><?=$stand['itemname']?></div>
            <div class="itemqty"><?=QTYOutput($stand['itemqty'])?></div>
            <div class="remainqty"><?=QTYOutput($stand['remainqty'])?></div>
        </div>
        <?php $numpp++;?>
    <?php endforeach; ?>
<?php endif; ?>
