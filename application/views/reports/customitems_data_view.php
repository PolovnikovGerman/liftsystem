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
    <div class="yeardata" data-year="<?=$row['year']?>">
        <div class="yearlabelarea">
            <div class="yearlabel"><?=$row['year']?></div>
            <div class="rowshowhide showdata" data-year="<?=$row['year']?>">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </div>
        </div>
        <div class="total <?=$row['totals']['profit_class']?>" title="Avg Profit <?=$row['totals']['avg_profit']?><br/>Avg Revenue <?=$row['totals']['avg_revenue']?>">
            <div class="row percent">
                <?=$row['totals']['profit_percent']?>
            </div>
            <div class="row profit">
                <?=($profit_type=='Profit' ? $row['totals']['profit'] : $row['totals']['profitpnts'])?>
            </div>
            <div class="row orders customs">
                <?=$row['totals']['numorders']?>
            </div>
            <div class="row orders datayear" data-year="<?=$row['year']?>">
                <?=$row['totals']['rob']?>
            </div>
            <div class="row orders datayear" data-year="<?=$row['year']?>">
                <?=$row['totals']['sage']?>
            </div>
            <div class="row orders datayear" data-year="<?=$row['year']?>">
                <?=$row['totals']['sean']?>
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
            <div class="row orders datayear">
                <?=$row['totals']['rob_diff']?>
            </div>
            <div class="row orders datayear">
                <?=$row['totals']['sage_diff']?>
            </div>
            <div class="row orders datayear">
                <?=$row['totals']['sean_diff']?>
            </div>
            <div class="row orders">
                <?=$row['totals']['revenue_diff']?>
            </div>
        </div>
        <div class="map">
            <div class="row">Av %</div>
            <div class="row">Profit</div>
            <div class="row"># Ord</div>
            <div class="row datayear">Robert</div>
            <div class="row datayear">Sage</div>
            <div class="row datayear">Sean</div>
            <div class="row">Rev</div>
        </div>
        <?php foreach ($row['months'] as $mrow) { ?>
            <div class="monthdataarea">
                <div class="month <?=($mrow['numorders']>0 ? 'salesmonthview' : '')?> <?=$mrow['profit_class']?>" data-month="<?=$mrow['month']?>" data-year="<?=$mrow['year']?>" data-saletype="<?=$newlabel?>">
                    <div class="row">
                        <div class="magnifying" data-diffurl="/analytics/salesmonthdiff?month=<?=$mrow['month']?>&year=<?=$mrow['year']?>&type=customs">
                            <img src="/img/icons/magnifier.png" alt="Popup"/>
                        </div>
                        <div class="rowpercent"><?=$mrow['profit_percent']?></div>
                    </div>
                    <div class="row profit"><?=($profit_type=='Profit' ? $mrow['profit'] : $mrow['profitpnts'])?></div>
                    <div class="row orders customs" style="font-weight: bold"><?=$mrow['numorders']?></div>
                    <div class="row orders datayear"><?=$mrow['rob']?></div>
                    <div class="row orders datayear"><?=$mrow['sage']?></div>
                    <div class="row orders datayear"><?=$mrow['sean']?></div>
                    <div class="row orders"><?=$mrow['revenue']?></div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<div id="salestype_<?=$newlabel?>"><?=$curview?></div>
