<div class="pohinfdaytitletbl">
    <div class="infday-date"><?=date('D - M j, Y', $date)?></div>
    <div class="infday-infpo"><?=$total?> POs (<?=$regular?> Regular, <?=$custom?> Custom)</div>
</div>
<?php if (count($details)>0):?>
<div class="pohinfday-tblbody">
<?php $numpp=0; ?>
<?php foreach ($details as $detail):?>
    <div class="pohinfday-tr <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
<!--        <div class="pohinfday-td date">--><?php //=$numpp==0 ? date('m/d', $detail['create_date']) : '&mdash;'?><!--</div>-->
        <div class="pohinfday-td time"><?=date('G:i', $detail['create_date'])?></div>
        <div class="pohinfday-td price"><?=TotalOutput($detail['amount_sum'])?></div>
        <div class="pohinfday-td vendor <?=$detail['vendorclass']?>"><?=$detail['vendor_name']?></div>
        <div class="pohinfday-td order" data-order="<?=$detail['order_id']?>"><?=$detail['order_num']?></div>
        <div class="pohinfday-td item <?=$detail['itemclass']?>"><?=$detail['itemname']?></div>
        <div class="pohinfday-td qty"><?=$detail['shipped']?></div>
    </div>
    <?php $numpp++; ?>
<?php endforeach;?>
</div>
<?php endif; ?>
