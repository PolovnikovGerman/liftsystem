<div class="yeardata" data-year="<?=$data['year']?>">
    <div class="yearlabelarea">
        <div class="yearlabel"><?=$data['year']?></div>
        <div class="rowshowhide showdata" data-year="<?=$data['year']?>">
            <i class="fa fa-plus-circle" aria-hidden="true"></i>
        </div>
    </div>
    <div class="total <?=$data['totals']['profit_class']?>" title="Avg Profit <?=($profit_type=='Profit' ? $data['totals']['avg_profit'] : $data['totals']['avg_profitpts'])?><br/>Avg Revenue <?=$data['totals']['avg_revenue']?>">
        <div class="row percent">
            <?=$data['totals']['profit_percent']?>
        </div>
        <div class="row profit">
            <?=($profit_type=='Profit' ? $data['totals']['profit'] : $data['totals']['profitpts']) ?>
        </div>
        <div class="row orders customs">
            <?=$data['totals']['numorders']?>
        </div>
        <div class="row orders datayear">
            <?=$data['totals']['rob']?>
        </div>
        <div class="row orders datayear">
            <?=$data['totals']['sage']?>
        </div>
        <div class="row orders datayear">
            <?=$data['totals']['sean']?>
        </div>
        <div class="row orders">
            <?=$data['totals']['revenue']?>
        </div>
    </div>
    <div class="totaldiff <?=$data['totals']['profit_class']?>">
        <div class="row percent">
            <?=$data['totals']['profit_percent_diff']?>
        </div>
        <div class="row profit">
            <?=($profit_type=='Profit' ? $data['totals']['profit_diff'] : $data['totals']['profitpnts_diff'])?>
        </div>
        <div class="row orders customs">
            <?=$data['totals']['numorders_diff']?>
        </div>
        <div class="row orders datayear">
            <?=$data['totals']['rob_diff']?>
        </div>
        <div class="row orders datayear">
            <?=$data['totals']['sage_diff']?>
        </div>
        <div class="row orders datayear">
            <?=$data['totals']['sean_diff']?>
        </div>
        <div class="row orders">
            <?=$data['totals']['revenue_diff']?>
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
    <?php foreach ($data['months'] as $mrow) { ?>
        <div class="monthdataarea">
            <div class="month <?=($mrow['numorders']>0 ? 'salesmonthview' : '')?> <?=$mrow['profit_class']?>" data-month="<?=$mrow['month']?>" data-year="<?=$mrow['year']?>" data-saletype="<?=strtolower($goals['goal_type'])?>">
                <div class="row">
                    <div class="magnifying" data-diffurl="/reports/salesmonthdiff?month=<?=$mrow['month']?>&year=<?=$mrow['year']?>&type=customs">
                        <img src="/img/icons/magnifier.png" alt="Popup"/>
                    </div>
                    <div class="rowpercent"><?=$mrow['profit_percent']?></div>
                </div>
                <div class="row profit"><?=($profit_type=='Profit' ? $mrow['profit'] : $mrow['profitpts'])?></div>
                <div class="row orders customs" style="font-weight: bold;"><?=$mrow['numorders']?></div>
                <div class="row orders datayear"><?=$mrow['rob']?></div>
                <div class="row orders datayear"><?=$mrow['sage']?></div>
                <div class="row orders datayear"><?=$mrow['sean']?></div>
                <div class="row orders"><?=$mrow['revenue']?></div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="differences_area" data-type="<?=$type?>">
    <?=$differences?>
</div>
<div class="salestype_goals">
    <div class="pacehittitle">
        <div class="delimvertical">&nbsp;</div>
        <div class="delimhorisont">&nbsp;</div>
        <div class="label">On Pace to Hit:</div>
    </div>
    <div class="pacehitdata <?=$pacehits['profit_class']?>" data-year="<?=$data['year']?>">
        <div class="row percent"><?=$pacehits['profit_percent']?></div>
        <div class="row profit"><?=($profit_type=='Profit' ? $pacehits['profit'] : $pacehits['profitpts'])?></div>
        <div class="row orders customs"><?=$pacehits['numorders']?></div>
        <div class="row orders datayear"><?=$pacehits['rob']?></div>
        <div class="row orders datayear"><?=$pacehits['sage']?></div>
        <div class="row orders datayear"><?=$pacehits['sean']?></div>
        <div class="row orders"><?=$pacehits['revenue']?></div>
    </div>
    <div class="pacehitdiffdata <?=$pacehits['profit_class']?>"  data-year="<?=$data['year']?>">
        <div class="row percent"><?=$pacehits['profit_percent_diff']?></div>
        <div class="row profit"><?=($profit_type=='Profit' ? $pacehits['profit_diff'] : $pacehits['profitpnts_diff'])?></div>
        <div class="row orders customs"><?=$pacehits['numorders_diff']?></div>
        <div class="row orders datayear"><?=$pacehits['rob_diff']?></div>
        <div class="row orders datayear"><?=$pacehits['sage_diff']?></div>
        <div class="row orders datayear"><?=$pacehits['sean_diff']?></div>
        <div class="row orders"><?=$pacehits['revenue_diff']?></div>
    </div>
    <div class="salesgoaltitle">
        <div class="label">Goal<br/> for Year:</div>
    </div>
    <div class="salesgoaldataarea">
        <div class="salesgoaldata <?=$goals['profit_class']?>">
            <div class="row percent"><?=$goals['profit_percent']?></div>
            <div class="row profit"><?=($profit_type=='Profit' ? $goals['profit'] : $goals['profitpts'])?></div>
            <div class="row orders"><?=$goals['numorders']?></div>
            <div class="row orders"><?=$goals['revenue']?></div>
        </div>
        <div class="salesgoaledit" data-goal="<?=$goals['goal_order_id']?>" data-goaltype="<?=  strtolower($goals['goal_type'])?>">Edit</div>
    </div>
    <div class="salesavgtitle">
        <div class="label">Need to Avg:</div>
    </div>
    <div class="salesavgarea" style="width: 197px">
        <div class="head"><?=$data['days']?> Days Left (<?=$elapsed?> Days Past)</div>
        <div class="salesavgdata">
            <div class="row profit"><?=($profit_type=='Profit' ? $avg['profit'] : $avg['profitpts'])?></div>
            <div class="row orders"><?=$avg['numorders']?></div>
            <div class="row orders"><?=$avg['revenue']?></div>
        </div>
        <div class="avglegend">
            <div class="row">profit / day</div>
            <div class="row">orders / day</div>
            <div class="row">revenue / day</div>
        </div>
    </div>
</div>