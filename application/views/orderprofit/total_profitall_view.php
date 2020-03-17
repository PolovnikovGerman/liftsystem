<div class="orders-profitalltotal-row">
    <div class="profitorder_total">Totals of Search:</div>
    <div class="profitorder_totaldata">
        <div class="orders profitotaltooltip" data-viewsrc="<?=$order_tooltip?>"><?= $data['numorders'] ?></div>
        <div class="orders-new"><?= $data['numorders_new'] ?> (<?=$data['numorders_detail_newperc']?>%)</div>
        <div class="orders-repeat"><?= $data['numorders_repeat'] ?> (<?=$data['numorders_detail_repeatperc']?>%)</div>
        <div class="orders-blank"><?= $data['numorders_blank'] ?> (<?=$data['numorders_detail_blankperc']?>%)</div>
        <div class="qty profitotaltooltip"  data-viewsrc="<?=$qty_tooltip?>"><?= $data['qty'] ?></div>
        <div class="revenue profitotaltooltip"  data-viewsrc="<?=$revenue_tooltip?>" ><?= $data['show_revenue'] ?></div>
        <div class="shipping profitotaltooltip" data-viewsrc="<?=$shipping_tooltip?>" ><?= $data['show_shipping'] ?></div>
        <div class="tax profitotaltooltip" data-viewsrc="<?=$tax_tooltip?>"><?= $data['show_tax'] ?></div>
        <div class="cog profitotaltooltip" data-viewsrc="<?=$cog_tooltip?>"><?= $data['show_cog'] ?></div>
        <div class="profitval  profitotaltooltip <?= $data['profit_class'] ?>" data-viewsrc="<?=$profit_tooltip?>"><?= $data['show_profit'] ?></div>
        <div class="profitperc <?= $data['profit_class'] ?>"><?= $data['profit_perc'] ?></div>
    </div>
</div>