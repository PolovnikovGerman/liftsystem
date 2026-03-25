<?php $numpp = 1; ?>
<?php foreach ($forms as $form) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-task="<?=$form['custom_quote_id']?>">
        <div class="newunassign_date_dat"><?=date('m/d/y', strtotime($form['date_add']));?></div>
        <div class="sbcustomform_customer_dat truncateoverflowtext"><?=empty($form['customer_company']) ? $form['customer_name'] : $form['customer_company']?></div>
        <div class="sbcustomform_qty_dat"><?=QTYOutput($form['quota_qty'])?></div>
        <div class="sbcustomform_item_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$form['shape_desription']?>">
            <?=$form['shape_desription']?>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
