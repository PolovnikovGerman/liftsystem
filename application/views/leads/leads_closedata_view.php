<div class="leadclosedreportlabel">
    <div class="closeleadreplabel"><?=$owner_name?><br/> Lead Report</div>
    <div class="closeleadreplabel"><?=$owner_name?><br/> Order Report</div>
    <div class="closeleadreplabel">Company<br/> Order Report</div>
</div>
<div class="leadclosedreportdata">
    <div class="weekdatarowtotal">
        <div class="weekday" data-bgcolor="#a7a7a7" data-css="yeartodate_tooltip" data-event="click" data-textcolor="#000" data-bordercolor="#000" data-balloon="{ajax} /leads/leadsclosed_yeartotals?brand=<?=$brand?>">
            Year to Date
        </div>
        <div class="newleads"><?=$totals['newleads']?></div>
        <div class="workleads"><?=$totals['wrkleads']?></div>
        <div class="outcalls"><?=$totals['outcalls']?></div>
        <div class="ordersnum"><?=$totals['orders']?></div>
        <div class="ordersrevenue"><?=$totals['revenue']?></div>
        <div class="ordersprofit"><?=$totals['profit']?></div>
        <div class="curpoints"><?=$totals['points']?></div>
        <div class="goalpoints"><?=$totals['goals']?></div>
        <div class="procgoals <?=$totals['goalperc_class']?>"><?=$totals['goalperc']?></div>
    </div>
    <div class="leadclosedreporsubtitle weekday">Week<br/>Day</div>
    <div class="leadclosedreporsubtitle leadrep">
        <div class="newleads"># New Leads</div>
        <div class="workleads"># Wrk Leads</div>
        <div class="outcalls"># Out Calls</div>
    </div>
    <div class="leadclosedreporsubtitle orders">
        <div class="ordersnum"># Orders</div>
        <div class="ordersrevenue">$<br/> Orders</div>
        <div class="ordersprofit">#<br/>Pts</div>
    </div>
    <div class="leadclosedreporsubtitle goals">
        <div class="curpoints">Current Points</div>
        <div class="goalpoints">GOAL Points</div>
        <div class="procgoals">% Goal</div>
    </div>
</div>
<div class="leadclosedreportdatas">
    <div class="leadclosedreportdataweeks">
        <?php $numpp=0;?>
        <?php foreach ($weeks as $row) { ?>
            <div class="weekdatarow <?=($numpp%2==0 ? 'white' :  'grey')?>" data-week="<?=$row['week']?>" data-start="<?=$row['bgn']?>" data-end="<?=$row['end']?>">
                <div class="weekday <?=$row['weekclass']?>"><?=$row['label']?></div>
                <div class="newleads" id="newlead<?=$row['bgn']?>"
                <?=$row['newleadsurl']=='' ? '' : 'data-bgcolor="#a7a7a7" data-css="weekuserleads_tooltip" data-event="click" data-bordercolor="#000" data-balloon="{ajax} '.$row['newleadsurl'].'"'?>><?=$row['newleads']?></div>
                <div class="workleads" id="wrklead<?=$row['bgn']?>"
                <?=$row['wrkleadsurl']=='' ? '' : 'data-bgcolor="#a7a7a7" data-css="weekuserleads_tooltip" data-event="click" data-bordercolor="#000" data-balloon="{ajax} '.$row['wrkleadsurl'].'"' ?>><?=$row['wrkleads']?></div>
                <div class="outcalls"><?=$row['outcalls']?></div>
                <div class="ordersnum" id="orders<?=$row['bgn']?>"
                <?=$row['ordersurl']=='' ? '' : 'data-bgcolor="#a7a7a7" data-css="weekuserorder_tooltip" data-event="click" data-bordercolor="#000" data-balloon="{ajax} '.$row['ordersurl'].'"' ?>><?=$row['orders']?></div>
                <div class="ordersrevenue" id="ordrevenue<?=$row['bgn']?>"
                <?=$row['ordersurl']=='' ? '' : 'data-bgcolor="#a7a7a7" data-css="weekuserorder_tooltip" data-event="click" data-bordercolor="#000" data-balloon="{ajax} '.$row['ordersurl'].'"' ?>><?=$row['revenue']?></div>
                <div class="ordersprofit" id="ordprofit<?=$row['bgn']?>"
                <?=$row['ordersurl']=='' ? '' : 'data-bgcolor="#a7a7a7" data-css="weekuserorder_tooltip" data-event="click" data-bordercolor="#000" data-balloon="{ajax} '.$row['ordersurl'].'"' ?>><?=$row['profit']?></div>
                <div class="curpoints" id="points<?=$row['bgn']?>"
                <?=$row['cmporderurl']=='' ? '' : 'data-bgcolor="#a7a7a7" data-css="curpoints_tooltip" data-event="click" data-bordercolor="#000" data-balloon="{ajax} '.$row['cmporderurl'].'"' ?>><?=$row['points']?></div>
                <div class="goalpoints"><?=$row['goals']?></div>
                <div class="procgoals <?=$row['goalperc_class']?>"><?=$row['goalperc']?></div>
            </div>
            <?php if ($row['curweek']==1) { ?>
                <div class="weekdatadetails" data-week="<?=$row['week']?>" style="display: block">
                    <?php $nrow=0;?>
                    <?php foreach ($curweek as $wrow) {?>
                        <div class="weekdetailsrow <?=($nrow%2==0 ? 'grey' : 'white')?>">
                            <div class="weekday"><?=$wrow['label']?></div>
                            <div class="newleads"><?=$wrow['newleads']?></div>
                            <div class="workleads"><?=$wrow['wrkleads']?></div>
                            <div class="outcalls"><?=$wrow['outcalls']?></div>
                            <div class="ordersnum"><?=$wrow['orders']?></div>
                            <div class="ordersrevenue"><?=$wrow['revenue']?></div>
                            <div class="ordersprofit"><?=$wrow['profit']?></div>
                        </div>
                        <?php $nrow++;?>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="weekdatadetails" data-week="<?=$row['week']?>">&nbsp</div>
            <?php } ?>
            <?php $numpp++;?>
        <?php } ?>
    </div>
</div>