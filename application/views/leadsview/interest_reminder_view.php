<?php $numpp = 1; ?>
<div class="datarow">
    <div class="remindermonth">
        <select name="reminder_year">
            <?php foreach ($years as $year) : ?>
            <option value="<?=$year['key']?>" <?=$year['key']==$yeardate ? 'selected="selected"' : ''?>><?=$year['value']?></option>
            <?php endforeach; ?>
        </select>
        <span>, </span>
        <select name="reminder_month">
            <?php foreach ($months as $month) : ?>
                <option value="<?=$month['key']?>" <?=$month['key']==$monthdate ? 'selected="selected"' : ''?>><?=$month['value']?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php foreach ($orders as $order) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?> <?=$order['hide_reminder']==0 ? '' : 'hideorder'?>" data-order="<?=$order['order_id']?>">
        <div class="repeatremand_hide_dat" data-task="<?=$order['order_id']?>">
            <i class="fa fa-eye"></i>
        </div>
        <div class="repeatremand_date_dat"><?=date('m/d', $order['order_date'])?></div>
        <div class="repeatremand_order_dat <?=$order['hide_reminder']==0 ? 'active' : ''?> <?=$order['item_id']==$this->config->item('custom_id') ? 'customitemdat' : ''?>"
             data-task="<?=$order['order_id']?>"><?=$order['order_num']?></div>
        <div class="repeatremand_customer_dat truncateoverflowtext <?=$order['item_id']==$this->config->item('custom_id') ? 'customitemdat' : ''?>"
             data-event="hover" data-css="itemdetailsballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto"
             data-textcolor="#000" data-timer="4000" data-delay="1000" data-balloon="<?=$order['customer']?>">
            <?=$order['customer']?>
        </div>
        <div class="repeatremand_qty_dat <?=$order['item_id']==$this->config->item('custom_id') ? 'customitemdat' : ''?>"><?=QTYOutput($order['qty'])?></div>
        <div class="repeatremand_item_dat truncateoverflowtext <?=$order['item_id']==$this->config->item('custom_id') ? 'customitemdat' : ''?>" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$order['item_number']?> - <?=$order['item_name']?>">
            <?=$order['item_number']?> - <?=$order['item_name']?>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
