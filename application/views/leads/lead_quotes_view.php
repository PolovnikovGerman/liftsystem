<?php foreach ($quotes as $row) { ?>
    <div class="lead_onlinequotrow">
        <div class="lead_quotedate"><?=$row['email_date']?></div>
        <div class="lead_quoteseparat">&mdash;</div>
        <div class="lead_quoteqty"><?=$row['email_qty']?></div>
        <div class="lead_quoteitem"><?=$row['email_item_name']?></div>
        <div class="lead_quoteseparat">&mdash;</div>
        <div class="lead_quoteprice"><?=$row['email_total']?></div>
        <div class="lead_popup_quotechck" id="qutview<?=$row['email_id']?>">
            <img src="/img/list.png" alt="View Quest" title="Click to view Quote"/>
        </div>
    </div>
<?php } ?>