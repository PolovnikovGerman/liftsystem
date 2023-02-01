<?php foreach ($quotes as $quote) { ?>
    <div class="datarow" data-leadquote="<?=$quote['quote_id']?>">
        <div class="leadquotedatelist"><?=date('m/d', $quote['quote_date'])?></div>
        <div class="leadquotenumberlist" data-leadquote="<?=$quote['quote_id']?>">
            <?=$quote['brand']=='SR' ? '' : 'QB-' ?><?=$quote['quote_number']?><?=$quote['brand']=='SR' ? '-QS' : '' ?>
        </div>
        <div class="leadquotedetailslist">
            <?=$quote['item_qty']?> <?=$quote['item_name']?>
        </div>
        <div class="leadquotetotalslist"><?=MoneyOutput($quote['quote_total'])?></div>
    </div>
<?php } ?>
