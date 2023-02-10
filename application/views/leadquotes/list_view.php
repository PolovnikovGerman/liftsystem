<div class="leadquote_tabledat">
<?php $numpp = 0;?>
<?php foreach ($lists as $list) { ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="leadquote_date"><?=date('d/m/y', $list['quote_date'])?></div>
        <div class="leadquote_number" data-quote="<?=$list['quote_id']?>" data-lead="<?=$list['lead_id']?>">
            <div class="leadquote_numberbox"><?=$list['qnumber']?></div>
        </div>
        <div class="leadquote_customer"><?=$list['customer']?></div>
        <div class="leadquote_qty"><?=$list['item_qty']?></div>
        <div class="leadquote_item"><?=$list['item_name']?></div>
        <div class="leadquote_revenue"><?=empty($list['quote_total']) ? '' : MoneyOutput($list['quote_total'])?></div>
        <div class="leadquote_replica"><?=$list['replica']?></div>
        <div class="leadquote_pdf <?=$cnt < 25 ? 'extended' : ''?>" data-quote="<?=$list['quote_id']?>">
            <div class="leadquote_pdflnk">pdf</div>
        </div>
    </div>
    <?php $numpp++; ?>
<?php } ?>
</div>