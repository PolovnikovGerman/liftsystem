<div class="weektotalsrow small">
    <div class="parameterlabel">&nbsp;</div>
    <?php $j=0;?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue <?=($projects[$j]==0 ? '' : 'incomplete')?>"><?=($projects[$j]==0 ? '' : 'Incomplete')?></div>
        <?php $j++;?>
    <?php } ?>
    <div class="parametervalue prognosis">Yr to Date</div>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue prognosis" style="cursor: pointer" data-event="hover" data-css="onpace_popup_message" data-bgcolor="#FFFFFF" data-bordercolor="#000"
             data-textcolor="#000" data-position="up" data-balloon="{ajax} /netprofit/onpace_message?brand=<?=$brand?>">
            On Pace
        </div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Years</div>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue headdata years"><?= $i ?></div>
    <?php } ?>
    <div class="parametervalue headdata prognosis currentyear <?=$compareweek==0 ? '' : 'narrow'?>"><?=$i?></div>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Orders #</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?=$sales[$j]?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval"><?= $sales[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue"><?= $sales[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Pcs Sold </div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue pcssold"><?= $pcssold[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval pcssold"><?= $pcssold[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue pcssold"><?= $pcssold[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Revenue:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue netprofit"><?= $revenue[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval netprofit"><?= $revenue[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue netprofit"><?= $revenue[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Gross Profit:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue netprofit"><?= $grossprofit[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval netprofit"><?= $grossprofit[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue netprofit"><?= $grossprofit[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Gross Profit %</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue grossprofitperc"><?= $grossrevenue_perc[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval grossprofitperc"><?= $grossrevenue_perc[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue grossprofitperc"><?= $grossrevenue_perc[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow bottomborder">
    <div class="parameterlabel">Expenses:
        <span class="exponsivedata shown"><i class="fa fa-plus-square-o" aria-hidden="true"></i></span>
    </div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue <?=$expensiveclass[$j]?>"><?= $expenses[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval <?=$expensiveclass[$j]?>"><?= $expenses[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue <?=$expensiveclass[$j]?>"><?= $expenses[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow expensivesrow">
    <div class="parameterlabel expensives">Operating:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content=""><?=$operating[$j]?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content=""><?= $operating[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $operating[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Ads:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $advertising[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content=""><?= $advertising[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $advertising[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Payroll:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $payroll[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content=""><?= $payroll[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $payroll[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Upwork:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $odesk[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content=""><?= $odesk[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $odesk[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">W9 Work:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $profitw9[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content=""><?= $profitw9[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $profitw9[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow bottomborder">
    <div class="parameterlabel expensives">Discretionary:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $purchases[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content=""><?= $purchases[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue helpexpensive" data-content=""><?= $purchases[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel netprofit">Net Profit:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue netprofit"><?= $netprofit[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval netprofit"><?= $netprofit[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue netprofit"><?= $netprofit[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Net Profit %:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue grossprofitperc"><?= $revenue_perc[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval grossprofitperc"><?= $revenue_perc[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue grossprofitperc"><?= $revenue_perc[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow bottomborder">
    <div class="parameterlabel">Net/Gross %:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue grossprofitperc"><?= $grossprofit_perc[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval grossprofitperc"><?= $grossprofit_perc[$j] ?></div>
    <?php if ($compareweek==0) { ?>
        <?php $j++; ?>
        <div class="parametervalue grossprofitperc"><?= $grossprofit_perc[$j] ?></div>
    <?php } ?>
</div>
