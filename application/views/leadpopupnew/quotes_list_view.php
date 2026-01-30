<?php $nrow = 0; ?>
<?php foreach ($quotes as $quote): ?>
    <?php if ($quote['rowtype']=='total') : ?>
        <div class="leadquotetabl-tr yeartotal">
            <div class="yeartotal-year"><?=$quote['year']?></div>
            <div class="yeartotal-quotes"><?=$quote['total']?> quotes</div>
        </div>
    <?php else : ?>
        <div class="leadquotetabl-tr <?=$nrow%2==0 ? 'whitedatarow' : 'greydatarow';?>">
            <div class="leadquotetabl-date"><?=date('m/d', $quote['quote_date'])?></div> <!-- leadquotetabl-td -->
            <div class="leadquotetabl-quote">
                <div class="leadquotetabl-quotebox <?=$quote['orders'] > 0 ? 'blueactive' : ''?>"><?=$quote['brand']=='SR' ? '' : 'QB-'?><?=$quote['quote_number']?><?=$quote['brand']=='SR' ? '-QS' : ''?></div>
            </div>
            <div class="leadquotetabl-web"><?=$quote['quote_source']=='WEB' ? $quote['quote_source'] : '&nbsp;'?></div>
            <div class="leadquotetabl-qty"><?=$quote['item_qty']?></div>
            <div class="leadquotetabl-prints"><?=$quote['imprints']?></div>
            <div class="leadquotetabl-descr truncateoverflowtext"><?=$quote['item_name']?></div>
            <div class="leadquotetabl-subtotal"><?=MoneyOutput($quote['quote_total'])?></div>
        </div>
        <?php $nrow++; ?>
    <?php endif; ?>
<?php endforeach; ?>
