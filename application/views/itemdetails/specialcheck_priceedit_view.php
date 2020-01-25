<div class="specialcheckoutprice title">
    <div class="specialcheckoutprice_qty title">Quantity</div>
    <div class="specialcheckoutprice_price title">Price e.a</div>
    <div class="specialcheckoutprice_amount title">Amount</div>
    <div class="specialcheckoutprice_profit title">Profit</div>
    <div class="specialcheckoutprice_profitperc title">&percnt;</div>
</div>
<?php $numpp = 0; ?>
<?php foreach ($prices as $row) { ?>
    <div class="specialcheckoutprice">
        <div class="specialcheckoutprice_qty">
            <input type="text" class="specpriceqty specpriceinput" data-fld="price_qty" data-entity="special_price" data-idx="<?=$row['item_specprice_id']?>" value="<?=$row['price_qty']?>"/>
        </div>
        <div class="specialcheckoutprice_price">
            <input type="text" class="specpriceval specpriceinput" data-fld="price" data-entity="special_price" data-idx="<?=$row['item_specprice_id']?>" value="<?=$row['price'] ?>"/>
        </div>
        <div class="specialcheckoutprice_amount" data-idx="<?=$row['item_specprice_id']?>">
            <?=(floatval($row['amount'])==0 ? '' : '$'.number_format($row['amount'],2,'.',''))?>
        </div>
        <div class="specialcheckoutprice_profit" data-idx="<?=$row['item_specprice_id']?>">
            <?=$row['profit']?>
        </div>
        <div class="specialcheckoutprice_profitperc <?=$row['profit_class']?>" data-idx="<?=$row['item_specprice_id']?>">
            <?=$row['profit_percent']?>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>
