<div class="itemprice_profitqtyarea">
    <div class="content-row">
        <div class="itemprice_profittitle">Profit:</div>
        <?php foreach ($prices as $price) { ?>
            <div class="itemprice_profitval"><?=empty($price['profit']) ? '' : ProfitOutput($price['profit'],0)?></div>
        <?php } ?>
    </div>
    <div class="content-row">
        <div class="itemprice_profittitle">%:</div>
        <?php foreach ($prices as $price) { ?>
            <div class="itemprice_profitperc <?=$price['profit_class']?>"><?=empty($price['profit_perc']) ? '' : $price['profit_perc']?></div>
        <?php } ?>
    </div>
</div>
<div class="itemprice_profitextraarea">
    <div class="content-row">
        <div class="itemprice_profitval"><?=empty($item['profit_print']) ? '' : ProfitOutput($item['profit_print'],2)?></div>
        <div class="itemprice_profitval"><?=empty($item['profit_setup']) ? '' : ProfitOutput($item['profit_setup'],0)?></div>
        <div class="itemprice_profitval"><?=empty($item['profit_repeat']) ? '' : ProfitOutput($item['profit_repeat'],0)?></div>
    </div>
    <div class="content-row">
        <div class="itemprice_profitperc <?=$item['profit_print_class']?>"><?=empty($item['profit_print_perc']) ? '' : $item['profit_print_perc']?></div>
        <div class="itemprice_profitperc <?=$item['profit_setup_class']?>"><?=empty($item['profit_setup_perc']) ? '' : $item['profit_setup_perc']?></div>
        <div class="itemprice_profitperc <?=$item['profit_repeat_class']?>"><?=empty($item['profit_repeat_perc']) ? '' : $item['profit_repeat_perc']?></div>
    </div>
</div>
<div class="itemprice_profitrusharea">
    <div class="content-row">
        <div class="itemprice_profitval"><?=empty($item['profit_rush1']) ? '' : ProfitOutput($item['profit_rush1'],0)?></div>
        <div class="itemprice_profitval"><?=empty($item['profit_rush2']) ? '' : ProfitOutput($item['profit_rush2'],0)?></div>
    </div>
    <div class="content-row">
        <div class="itemprice_profitperc <?=$item['profit_rush1_class']?>"><?=empty($item['profit_rush1_perc']) ? '' : $item['profit_rush1_perc']?></div>
        <div class="itemprice_profitperc <?=$item['profit_rush2_class']?>"><?=empty($item['profit_rush2_perc']) ? '' : $item['profit_rush2_perc']?></div>
    </div>
</div>
<div class="itemprice_profitpantonearea">
    <div class="content-row">
        <div class="itemprice_profitval"><?=empty($item['profit_pantone']) ? '' : ProfitOutput($item['profit_pantone'],0)?></div>
    </div>
    <div class="content-row">
        <div class="itemprice_profitperc <?=$item['profit_pantone_class']?>"><?=empty($item['profit_pantone_perc']) ? '' : $item['profit_pantone_perc']?></div>
    </div>
</div>
