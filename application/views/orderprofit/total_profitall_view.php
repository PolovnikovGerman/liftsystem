<div class="orders-profitalltotal-row">
    <div class="profitorder_total">Totals of Search:</div>
    <div class="profitorder_totaldata">
        <div class="orders profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$order_tooltip?>"><?= $data['numorders'] ?></div>
        <div class="orders-new"><?= $data['numorders_new'] ?> (<?=$data['numorders_detail_newperc']?>%)</div>
        <div class="orders-repeat"><?= $data['numorders_repeat'] ?> (<?=$data['numorders_detail_repeatperc']?>%)</div>
        <div class="orders-blank"><?= $data['numorders_blank'] ?> (<?=$data['numorders_detail_blankperc']?>%)</div>
        <div class="qty profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$qty_tooltip?>"><?= $data['qty'] ?></div>
        <div class="revenue profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$revenue_tooltip?>"><?= $data['show_revenue'] ?></div>
        <div class="balance profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$balance_tooltip?>" ><?= $data['show_balance'] ?></div>
        <div class="shipping profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$shipping_tooltip?>" ><?= $data['show_shipping'] ?></div>
        <div class="tax profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$tax_tooltip?>" ><?= $data['show_tax'] ?></div>
        <div class="cog profitotaltooltip" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$cog_tooltip?>" ><?= $data['show_cog'] ?></div>
        <div class="profitval  profitotaltooltip <?= $data['profit_class'] ?>" data-bgcolor="#fff" data-event="hover" data-bordercolor="#000" data-textcolor="#000"
             data-balloon="{ajax} <?=$profit_tooltip?>" ><?= $data['show_profit'] ?></div>
        <div class="profitperc <?= $data['profit_class'] ?>"><?= $data['profit_perc'] ?></div>
    </div>
</div>