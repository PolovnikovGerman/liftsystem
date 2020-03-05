<div class="orders-profittotal-row">
    <div class="profitorder_total">Totals of Search:</div>
    <div class="profitorder_totaldata">
        <div class="orders"><?= $numorders ?></div>
        <div class="qty"><?= $qty ?></div>
        <div class="itemcolors">&nbsp;</div>
        <div class="revenue" title="<?= $revenue ?>"><?= $show_revenue ?></div>
        <div class="shipping" title="<?= $shipping ?>"><?= $show_shipping ?></div>
        <div class="shipdate">&nbsp;</div>
        <div class="tax" title="<?= $tax ?>"><?= $show_tax ?></div>
        <div class="cog" title="<?= $cog ?>"><?= $show_cog ?></div>
        <div class="profitval <?= $profit_class ?>" title="<?= $profit ?>"><?= $show_profit ?></div>
        <div class="profitperc <?= $profit_class ?>"><?= $profit_perc ?></div>
    </div>
</div>