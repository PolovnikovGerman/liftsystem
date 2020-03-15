<div class="ordertotaldetailsarea">
    <div class="ordertotaldetailrow">
        <div class="ordertotallabel">Orders : <?=$total?></div>
        <div class='ordertotalperiod'><?=$label?></div>
    </div>
    <div class="ordertotaldetailrow">
        <div class="ordertotal_legend">
            <img src="/img/leads/legend_proj.png"/>
            <div class="ordertotal_legendlabel">PROJ</div>
        </div>
        <div class="ordertotal_legend">
            <img src="/img/leads/legend_lose.png"/>
            <div class="ordertotal_legendlabel">Lose $$</div>
        </div>
        <div class="ordertotal_legend">
            <img src="/img/leads/legend_verybad.png"/>
            <div class="ordertotal_legendlabel">Very Bad</div>
        </div>
        <div class="ordertotal_legend bad">
            <img src="/img/leads/legend_bad.png"/>
            <div class="ordertotal_legendlabel bad">Bad</div>
        </div>
        <div class="ordertotal_legend belowavg">
            <img src="/img/leads/legend_bellowavg.png"/>
            <div class="ordertotal_legendlabel belowavg">Below Avg</div>
        </div>
        <div class="ordertotal_legend">
            <img src="/img/leads/legend_target.png"/>
            <div class="ordertotal_legendlabel">Target</div>
        </div>
        <div class="ordertotal_legend">
            <img src="/img/leads/legend_great.png"/>
            <div class="ordertotal_legendlabel">Great</div>
        </div>
    </div>

    <div class="ordertotalhead">
        <div class="ordernum">Order #</div>
        <div class="orderdate">Date</div>
        <div class="customer">Customer</div>
        <div class="itemname">Item</div>
        <div class="revenue">Rev</div>
        <div class="points">Pts</div>
    </div>
    <div class="ordertotaldataarea">
        <?php $numpp=0;?>
        <?php foreach ($orders as $row) { ?>
            <div class="ordertotaldatarow <?=($numpp%2==0 ? 'greydatarow' : 'whitedatarow')?>">
                <div class="ordernum" data-order="<?=$row['order_id']?>"><?=$row['order_num']?></div>
                <div class="orderdate"><?=$row['order_date']?></div>
                <div class="customer"><?=$row['customer_name']?></div>
                <div class="itemname <?=($row['custom_order']==1 ? 'customorder' : '')?>"><?=$row['out_item']?></div>
                <div class="revenue"><?=$row['revenue']?></div>
                <div class="points <?=$row['profit_class']?>"><?=$row['points_val']?></div>
            </div>
            <?php $numpp++;?>
        <?php } ?>
    </div>
</div>