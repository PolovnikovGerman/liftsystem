<?php $numpp = 1; ?>
<?php if ($cntrush > 0 ) : ?>
    <div class="sectiontitle rush">Rush</div>
    <?php foreach ($rushs as $rush) : ?>
        <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
            <div class="numpp"><?=$numpp?></div>
            <?php if ($rush['rushterm']==2) : ?>
                <div class="rush1"><i class="fa fa-star"></i></div>
            <?php else: ?>
                <div class="rush1">&nbsp;</div>
            <?php endif; ?>
            <div class="rush3"><i class="fa fa-star"></i></div>
            <div class="approved <?=$rush['artclass']?>"><?=$rush['artstage']?></div>
            <div class="vendor"><?=$rush['vendor']?></div>
            <div class="ordernum" data-order="<?=$rush['order_id']?>"><?=$rush['ordernum']?></div>
            <div class="itemname"><?=$rush['itemname']?></div>
            <div class="itemqty"><?=$rush['itemqty']?></div>
            <div class="remainqty"><?=$rush['remainqty']?></div>
        </div>
        <?php $numpp++; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php if ($cntstand > 0) : ?>
    <div class="sectiontitle standard">Standard</div>
    <?php foreach ($stands as $stand) : ?>
        <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
            <div class="numpp"><?=$numpp?></div>
            <div class="rush1">&nbsp;</div>
            <div class="rush3">&nbsp;</div>
            <div class="approved <?=$stand['artclass']?>"><?=$stand['artstage']?></div>
            <div class="vendor"><?=$stand['vendor']?></div>
            <div class="ordernum" data-order="<?=$stand['order_id']?>"><?=$stand['ordernum']?></div>
            <div class="itemname"><?=$stand['itemname']?></div>
            <div class="itemqty"><?=$stand['itemqty']?></div>
            <div class="remainqty"><?=$stand['remainqty']?></div>
        </div>
        <?php $numpp++; ?>
    <?php endforeach; ?>
<?php endif; ?>
