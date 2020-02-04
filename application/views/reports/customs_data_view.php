<div class="salestotal_title">
    <div class="year">&nbsp;</div>
    <div class="total">Total</div>
    <div class="totaldiff">Diff</div>
    <div class="map">&nbsp;</div>
    <div class="month">Jan</div>
    <div class="month">Feb</div>
    <div class="month">Mar</div>
    <div class="month">Apr</div>
    <div class="month">May</div>
    <div class="month">Jun</div>
    <div class="month">Jul</div>
    <div class="month">Aug</div>
    <div class="month">Sep</div>
    <div class="month">Oct</div>
    <div class="month">Nov</div>
    <div class="month">Dec</div>
</div>
<?php foreach ($data as $row) { ?>
    <div class="yeardata">
        <div class="yearlabelarea">
            <div class="yearlabel"><?=$row['year']?></div>
        </div>
        <div class="total <?=$row['totals']['profit_class']?>" title="Avg Profit <?=$row['totals']['avg_profit']?><br/>Avg Revenue <?=$row['totals']['avg_revenue']?>">
            <div class="row percent">
                <?=$row['totals']['profit_percent']?>
            </div>
            <div class="row profit">
                <?=($profit_type=='Profit' ? $row['totals']['profit'] : $row['totals']['profitpnts'])?>
            </div>
            <div class="row orders">
                <?=$row['totals']['numorders']?>
            </div>
            <div class="row orders">
                <?=$row['totals']['revenue']?>
            </div>
        </div>
        <div class="totaldiff <?=$row['totals']['profit_class']?>">
            <div class="row percent">
                <?=$row['totals']['profit_percent_diff']?>
            </div>
            <div class="row profit">
                <?=($profit_type=='Profit' ? $row['totals']['profit_diff'] : $row['totals']['profitpnts_diff'])?>
            </div>
            <div class="row orders customs">
                <?=$row['totals']['numorders_diff']?>
            </div>
            <div class="row orders">
                <?=$row['totals']['revenue_diff']?>
            </div>
        </div>
        <div class="map">
            <div class="row">Av %</div>
            <div class="row">Profit</div>
            <div class="row"># Ord</div>
            <div class="row">Rev</div>
        </div>
        <?php foreach ($row['months'] as $mrow) { ?>
            <div class="monthdataarea">
                <div class="month <?=($mrow['numorders']>0 ? 'salesmonthview' : '')?> <?=$mrow['profit_class']?>" data-month="<?=$mrow['month']?>" data-year="<?=$mrow['year']?>" data-saletype="<?=$newlabel?>">
                    <div class="row">
                        <div class="magnifying" data-diffurl="/reports/salesmonthdiff?month=<?=$mrow['month']?>&year=<?=$mrow['year']?>&type=<?=strtolower($goals['goal_type'])?>">
                            <img src="/img/icons/magnifier.png" alt="Popup"/>
                        </div>
                        <div class="rowpercent"><?=$mrow['profit_percent']?></div>
                    </div>
                    <div class="row profit"><?=($profit_type=='Profit' ? $mrow['profit'] : $mrow['profitpnts'])?></div>
                    <div class="row orders"><?=$mrow['numorders']?></div>
                    <div class="row orders"><?=$mrow['revenue']?></div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<div id="salestype_<?=$newlabel?>"><?=$curview?></div>
