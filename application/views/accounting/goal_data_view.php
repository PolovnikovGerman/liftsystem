<div id="profitdategoal">
    <div class="year-totals">
        <div class="totalyear-titlerow">
            <?= $title ?>
            <?php if ($brand!=='ALL') { ?>
                <div class="editgoal" data-year="<?=$year?>">Edit</div>
            <?php } ?>
        </div>    
        <div class="year-totaldataarea">
            <div class="year-totaldata">
                <div class="totalyear-title">Orders:</div>
                <div class="totalyear-value"><?= $total_orders ?></div>
            </div>
            <div class="year-totaldata">
                <div class="totalyear-title">Revenue:</div>
                <div class="totalyear-value"><?= $revenue ?></div>
            </div>
            <div class="year-totaldata">
                <div class="totalyear-title">Avg Rev:</div>
                <div class="totalyear-value"><?= $avg_revenue ?></div>
            </div>
            <div class="year-totaldata">
                <div class="totalyear-title">Profit:</div>
                <div class="totalyear-value"><?= $profit ?></div>
            </div>
            <div class="year-totaldata">
                <div class="totalyear-title">Avg Profit:</div>
                <div class="totalyear-value"><?=$avg_profit ?></div>
            </div>
            <div class="year-totaldata">
                <div class="totalyear-title">Ave %:</div>
                <div class="totalyear-value <?= $profit_class ?>"><?= $avg_profit_perc ?></div>
            </div>
        </div>
    </div>
    <?php if ($growthview==1) { ?>
        <div class="year-growthtotals">
            <div class="totalyear-titlerow"><?=$growthtitle?></div>
            <div class="year-growthdataarea">
                <div class="year-growthdata">
                    <div class="numval <?=$growth_orderclass?>"><?=$growth_ordernum?></div>
                    <div class="prcval <?=$growth_orderclass?>"><?=$growth_orderprc?></div>                
                </div>
                <div class="year-growthdata">
                    <div class="numval <?=$growth_revenueclass?>"><?=$growth_revenuenum?></div>
                    <div class="prcval <?=$growth_revenueclass?>"><?=$growth_revenueprc?></div>                
                </div>
                <div class="year-growthdata">
                    <div class="numval <?=$growth_avgrevenueclass?>"><?=$growth_avgrevenuenum?></div>
                    <div class="prcval <?=$growth_avgrevenueclass?>"><?=$growth_avgrevenueprc?></div>                
                </div>
                <div class="year-growthdata">
                    <div class="numval <?=$growth_profitclass?>"><?=$growth_profitnum?></div>
                    <div class="prcval <?=$growth_profitclass?>"><?=$growth_profitprc?></div>                
                </div>
                <div class="year-growthdata">
                    <div class="numval <?=$growth_avgprofitclass?>"><?=$growth_avgprofitnum?></div>
                    <div class="prcval <?=$growth_avgprofitclass?>"><?=$growth_avgprofitprc?></div>                
                </div>
                <div class="year-growthdata">
                    <div class="numval <?=$growth_aveprcclass?>"><?=$growth_avenum?></div>
                    <div class="prcval <?=$growth_aveprcclass?>"><?=$growth_aveprc?></div>                
                </div>
            </div>
        </div>
    <?php } ?>    
</div>