<div class="orders-profitalltotal-row">
    <div class="profitorder_total">Totals of Search:</div>
    <div class="profitorder_totaldata">
        <div class="orders profitotaltooltip" href="<?=$order_tooltip?>"><?= $data['numorders'] ?></div>
        <div class="orders-new"><?= $data['numorders_new'] ?> (<?=$data['numorders_detail_newperc']?>%)</div>
        <div class="orders-repeat"><?= $data['numorders_repeat'] ?> (<?=$data['numorders_detail_repeatperc']?>%)</div>
        <div class="orders-blank"><?= $data['numorders_blank'] ?> (<?=$data['numorders_detail_blankperc']?>%)</div>
        <div class="qty profitotaltooltip"  href="<?=$qty_tooltip?>"><?= $data['qty'] ?></div>
        <div class="revenue profitotaltooltip"  href="<?=$revenue_tooltip?>" ><?= $data['show_revenue'] ?></div>
        <div class="shipping profitotaltooltip" href="<?=$shipping_tooltip?>" ><?= $data['show_shipping'] ?></div>
        <div class="tax profitotaltooltip" href="<?=$tax_tooltip?>"><?= $data['show_tax'] ?></div>
        <div class="cog profitotaltooltip" href="<?=$cog_tooltip?>"><?= $data['show_cog'] ?></div>
        <div class="profitval  profitotaltooltip <?= $data['profit_class'] ?>" href="<?=$profit_tooltip?>"><?= $data['show_profit'] ?></div>
        <div class="profitperc <?= $data['profit_class'] ?>"><?= $data['profit_perc'] ?></div>
    </div>
</div>