<div class="outcomedetails_area">
    <div class="datarow">
        <div class="detailstitle-qty">QTY</div>
        <div class="detailstitle-price">Price Ea</div>
        <div class="detailstitle-total">Total</div>
    </div>
    <?php foreach($details as $detail) { ?>
    <div class="datarow <?=$detail['totalrow']==1 ? 'detailstotalrow' : ''?>">
        <div class="detailsdata-qty"><?=$detail['qty']?></div>
        <div class="detailsdata-price"><?=number_format($detail['price'],3)?></div>
        <div class="detailsdata-total"><?=number_format($detail['total'],2)?></div>
    </div>
    <?php } ?>
</div>