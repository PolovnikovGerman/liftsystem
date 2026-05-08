<?php $nrow = 0; ?>
<?php $curdate = '';?>
<?php $showcurdate = 0; ?>
<?php foreach ($quotes as $quote): ?>
    <?php if ($quote['rowtype']=='total') : ?>
        <div class="leadquotetabl-tr yeartotal">
            <div class="yeartotal-year"><?=$quote['year']?></div>
            <div class="yeartotal-quotes"><?=$quote['total']?> quotes</div>
        </div>
    <?php else : ?>
        <div class="leadquotetabl-tr <?=$nrow%2==0 ? 'whitedatarow' : 'greydatarow';?>">
            <?php if ($curdate!==date('m/d', $quote['quote_date'])) : ?>
            <?php $curdate = date('m/d', $quote['quote_date']); ?>
            <?php $showcurdate = 1; ?>
            <?php endif; ?>
            <div class="leadquotetabl-date">
                <?php if ($showcurdate==1) : ?>
                    <?=$curdate?>
                <?php else : ?>
                ---
                <?php endif; ?>
            </div>
            <div class="leadquotetabl-quote" data-quote="<?=$quote['quote_id']?>">
                <div class="leadquotetabl-quotebox <?=$quote['orders'] > 0 ? 'blueactive' : ''?>"><?=$quote['brand']=='SR' ? '' : 'QB-'?><?=$quote['quote_number']?><?=$quote['brand']=='SR' ? '-QS' : ''?></div>
            </div>
            <div class="leadquotetabl-doc" data-quote="<?=$quote['quote_id']?>"><i class="fa fa-file-pdf-o"></i></div>
            <div class="leadquotetabl-web"><?=$quote['quote_source']=='WEB' ? $quote['quote_source'] : '&nbsp;'?></div>
            <div class="leadquotetabl-qty"><?=$quote['item_qty']?></div>
            <div class="leadquotetabl-prints truncateoverflowtext"><?=$quote['imprints']?></div>
            <div class="leadquotetabl-descr truncateoverflowtext"><?=$quote['item_name']?></div>
            <div class="leadquotetabl-subtotal"><?=MoneyOutput($quote['quote_total'])?></div>
        </div>
        <?php $nrow++; ?>
        <?php $showcurdate = 0; ?>
    <?php endif; ?>
<?php endforeach; ?>
