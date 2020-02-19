<?php $nrow = 0; ?>
<?php foreach ($email_dat as $row) { ?>
    <div class="quotes_tabrow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?> <?=$row['rowclass']?>" data-email="<?=$row['email_id']?>">
        <div class="quote_ordnum"><?=$row['ordnum']?></div>
        <div class="quote_brand" data-quoteid="<?=$row['email_id']?>"><?=$row['inclicon']?></div>
        <div class="quote_status <?=$row['assign_class']?>" data-quoteid="<?=$row['email_id']?>">
            <div class="quote_replica"><?=$row['lead_number']?></div>
        </div>
        <div class="quote_date"><?=$row['email_date']?></div>
        <div class="quote_customer"> <?=$row['email_sender']?></div>
        <div class="quote_email"><?=$row['email_sendermail']?></div>
        <div class="quote_phone"><?=$row['email_senderphone']?></div>
        <div class="quote_type"><?=$row['email_subtype']?></div>
        <div class="quote_qty"><?=$row['email_qty']?></div>
        <div class="quote_item"><?=$row['email_item_name']?></div>
        <div class="quote_total">
            <div class="quote_total_sum"><?=$row['email_total']?></div>
            <div class="quote_attachlnk">
                <a class="openquotadoc" href="javascript:void(0);" data-link="<?=$row['email_quota_link']?>"><img src="/img/leads/list.png"/></a>
            </div>
        </div>
    </div>
    <?php $nrow++; ?>
<?php } ?>
