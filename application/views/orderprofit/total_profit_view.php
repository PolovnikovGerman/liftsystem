<div class="orders-profitalltotal-row">
    <div class="profitorder_total">Totals of Search:</div>
    <div class="profitorder_totaldata">
        <div class="orders"><?= $numorders ?></div>
        <div class="qty"><?= $qty ?></div>
        <div class="itemcolors">&nbsp;</div>
        <div class="revenue" data-balloon="<?=$revenue?>" data-css="searchtotal_tooltip" data-bgcolor="#fff" data-event="hover" data-position="up"><?= $show_revenue ?></div>
        <div class="balance" data-balloon="<?=$balance?>" data-css="searchtotal_tooltip" data-bgcolor="#fff" data-event="hover" data-position="up"><?= $show_balance ?></div>
        <div class="shipping" data-balloon="<?=$shipping?>" data-css="searchtotal_tooltip" data-bgcolor="#fff" data-event="hover" data-position="up"><?= $show_shipping ?></div>
        <div class="shipdate">&nbsp;</div>
        <div class="tax" data-balloon="<?=$tax?>" data-css="searchtotal_tooltip" data-bgcolor="#fff" data-event="hover" data-position="up"><?= $show_tax ?></div>
        <div class="cog" data-balloon="<?=$cog?>" data-css="searchtotal_tooltip" data-bgcolor="#fff" data-event="hover" data-position="up"><?= $show_cog ?></div>
        <div class="profitval <?= $profit_class ?>" data-balloon="<?=$profit?>"  data-css="searchtotal_tooltip" data-bgcolor="#fff" data-event="hover" data-position="up"><?= $show_profit ?></div>
        <div class="profitperc <?= $profit_class ?>"><?= $profit_perc ?></div>
    </div>
</div>