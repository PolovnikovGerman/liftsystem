<div class="itemprice_profitqtyarea">
    <div class="content-row">
        <div class="itemprice_profittitle">Profit:</div>
        <?php foreach ($prices as $price) { ?>
            <div class="itemprice_profitval"><?=empty($price['profit']) ? '' : MoneyOutput($price['profit'],0)?></div>
        <?php } ?>
    </div>
    <div class="content-row">
        <div class="itemprice_profittitle">%:</div>
        <?php foreach ($prices as $price) { ?>
            <div class="itemprice_profitperc <?=$price['profit_class']?>"><?=empty($price['profit_perc']) ? '' : $price['profit_perc'].'%'?></div>
        <?php } ?>
    </div>
</div>
<div class="itemprice_profitextraarea">
    <div class="content-row">
        <div class="itemprice_profitval"><?=empty($item['profit_print']) ? '' : MoneyOutput($item['profit_print'])?></div>
        <div class="itemprice_profitval"><?=empty($item['profit_setup']) ? '' : MoneyOutput($item['profit_setup'])?></div>
        <div class="itemprice_profitval"><?=empty($item['profit_repeat']) ? '' : MoneyOutput($item['profit_repeat'])?></div>
    </div>
    <div class="content-row">
        <div class="itemprice_profitperc <?=$item['profit_print_class']?>"><?=empty($item['profit_print_perc']) ? '' : $item['profit_print_perc'].'%'?></div>
        <div class="itemprice_profitperc <?=$item['profit_setup_class']?>"><?=empty($item['profit_setup_perc']) ? '' : $item['profit_setup_perc'].'%'?></div>
        <div class="itemprice_profitperc <?=$item['profit_repeat_class']?>"><?=empty($item['profit_repeat_perc']) ? '' : $item['profit_repeat_perc'].'%'?></div>
    </div>
</div>
<div class="itemprice_profitrusharea">
    <div class="content-row">
        <div class="itemprice_profitval"><?=empty($item['profit_rush1']) ? '' : MoneyOutput($item['profit_rush1'])?></div>
        <div class="itemprice_profitval"><?=empty($item['profit_rush2']) ? '' : MoneyOutput($item['profit_rush2'])?></div>
    </div>
    <div class="content-row">
        <div class="itemprice_profitperc <?=$item['profit_rush1_class']?>"><?=empty($item['profit_rush1_perc']) ? '' : $item['profit_rush1_perc'].'%'?></div>
        <div class="itemprice_profitperc <?=$item['profit_rush2_class']?>"><?=empty($item['profit_rush2_perc']) ? '' : $item['profit_rush2_perc'].'%'?></div>
    </div>
</div>
<div class="itemprice_profitpantonearea">
    <div class="content-row">
        <div class="itemprice_profitval"><?=empty($item['profit_pantone']) ? '' : MoneyOutput($item['profit_pantone'])?></div>
    </div>
    <div class="content-row">
        <div class="itemprice_profitperc <?=$item['profit_pantone_class']?>"><?=empty($item['profit_pantone_perc']) ? '' : $item['profit_pantone_perc'].'%'?></div>
    </div>
</div>
