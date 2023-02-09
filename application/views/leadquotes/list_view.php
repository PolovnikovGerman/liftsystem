<?php $numpp = 0;?>
<?php foreach ($lists as $list) { ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="leadquote_date"><?=date('d/m/y', $list['quote_date'])?></div>
        <div class="leadquote_number" data-quote="<?=$list['quote_id']?>">
            <div class="leadquote_numberbox">QB-<?=$list['quote_number']?></div>
        </div>
        <div class="leadquote_customer">Test Customer for Quote</div>
        <div class="leadquote_qty">1250</div>
        <div class="leadquote_item">Round Stress Balls</div>
        <div class="leadquote_revenue">385.16</div>
        <div class="leadquote_replica">Sean</div>
        <div class="leadquote_pdf">
            <div class="leadquote_pdflnk">pdf</div>
        </div>
    </div>
    <?php $numpp++; ?>
<?php } ?>
