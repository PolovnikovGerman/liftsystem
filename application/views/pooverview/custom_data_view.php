<?php $numpp = 1;?>
<?php //if ($cntrush > 0) : ?>
<!--    <div class="sectiontitle rush">Rush</div>-->
<!--    --><?php //foreach ($rushs as $rush) : ?>
<!--        <div class="datarow --><?php //=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?><!--">-->
<!--            <div class="numpp">--><?php //=$numpp?><!--</div>-->
<!--            --><?php //if ($rush['rushterm']==2) : ?>
<!--                <div class="rush1"><i class="fa fa-star"></i></div>-->
<!--            --><?php //else : ?>
<!--                <div class="rush1">&nbsp;</div>-->
<!--            --><?php //endif; ?>
<!--            <div class="rush3"><i class="fa fa-star"></i></div>-->
<!--            <div class="approved --><?php //=$rush['artclass']?><!--">--><?php //=$rush['artstage']?><!--</div>-->
<!--            <div class="eventdate">--><?php //=$rush['eventdate']?><!--</div>-->
<!--            <div class="arrivedate">--><?php //=$rush['arrive']?><!--</div>-->
<!--            <div class="arrivedays">--><?php //=$rush['days']?><!--</div>-->
<!--            <div class="ordernum" data-order="--><?php //=$rush['order_id']?><!--">--><?php //=$rush['ordernum']?><!--</div>-->
<!--            <div class="itemname">--><?php //=$rush['itemname']?><!--</div>-->
<!--            <div class="itemqty">--><?php //=$rush['itemqty']?><!--</div>-->
<!--            <div class="remainqty">--><?php //=$rush['remainqty']?><!--</div>-->
<!--        </div>-->
<!--        --><?php //$numpp++;?>
<!--    --><?php //endforeach; ?>
<?php //endif; ?>
<?php if ($cntstand > 0) : ?>
<!--    <div class="sectiontitle standard">Standard</div>-->
    <?php foreach ($stands as $stand) : ?>
        <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
            <div class="numpp"><?=$numpp?></div>
            <div class="arrivedays"><?=show_negative_value($stand['days'])?></div>
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
