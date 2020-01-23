<div class="specialcheckoutprice title">
    <div class="specialcheckoutprice_qty title">Quantity</div>
    <div class="specialcheckoutprice_price title">Price e.a</div>
    <div class="specialcheckoutprice_amount title">Amount</div>
    <div class="specialcheckoutprice_profit title">Profit</div>
    <div class="specialcheckoutprice_profitperc title">&percnt;</div>
</div>
<?php foreach ($prices as $row) { ?>
    <div class="specialcheckoutprice">
        <div class="specialcheckoutprice_qty view"><?= $row['price_qty'] ?></div>
        <div class="specialcheckoutprice_price view"><?= $row['price'] ?></div>
        <div class="specialcheckoutprice_amount view"><?=$row['amount']?></div>
        <div class="specialcheckoutprice_profit"><?=$row['profit_view']?></div>
        <div class="specialcheckoutprice_profitperc <?=$row['profit_class']?>"><?=$row['profit_perc']?></div>
    </div>
<?php } ?>
