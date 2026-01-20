<?php $numpp = 1; ?>
<div class="datarow">
    <div class="remindermonth"><?=$date?></div>
</div>
<?php foreach ($orders as $order) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
        <div class="repeatremand_hide_dat">
            <i class="fa fa-eye"></i>
        </div>
        <div class="repeatremand_date_dat"><?=date('m/d', $order['order_date'])?></div>
        <div class="repeatremand_order_dat" data-task="<?=$order['order_id']?>"><?=$order['order_num']?></div>
        <div class="repeatremand_customer_dat"><?=$order['customer']?></div>
        <div class="repeatremand_qty_dat"><?=QTYOutput($order['qty'])?></div>
        <div class="repeatremand_item_dat truncateoverflowtext <?=$order['item_id']==$this->config->item('custom_id') ? 'customitemdat' : ''?>">
            <?=$order['item_number']?> - <?=$order['item_name']?>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
