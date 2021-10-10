<div class="content-row">
    <div class="pricedatlabel profitval">Profit $$:</div>
    <?php foreach ($prices as $price) { ?>
        <div class="pricedatvalue profitperc">
            <div class="viewparam"><?=empty($price['profit']) ? '&nbsp;' : MoneyOutput($price['profit'],0)?></div>
        </div>
    <?php } ?>
    <div class="pricedatlabel specprice">Prints</div>
    <div class="pricedatlabel specprice">Setup</div>
</div>
<div class="content-row">
    <div class="pricedatlabel profitperc">Profit %:</div>
    <?php foreach ($prices as $price) { ?>
        <div class="pricedatvalue profitperc">
            <div class="viewparam <?=$price['profit_class']?>"><?=empty($price['profit_perc']) ? '&nbsp;' : $price['profit_perc']?></div>
        </div>
    <?php } ?>
    <div class="pricedatvalue specprofit">
        <div class="viewparam <?=$item['profit_print_class']?>"><?=empty($item['profit_print_perc']) ? '&nbsp;' : $item['profit_print_perc']?></div>
    </div>
    <div class="pricedatvalue specprofit">
        <div class="viewparam <?=$item['profit_setup_class']?>"><?=empty($item['profit_setup_perc']) ? '&nbsp;' : $item['profit_setup_perc']?></div>
    </div>
</div>
