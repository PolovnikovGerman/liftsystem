<div class="weektotalsrow small">
    <div class="parameterlabel">&nbsp;</div>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">&nbsp;</div>
    <?php } ?>
    <?php // if ($compareweek==0) { ?>
        <!-- <div class="parametervalue showhidecuryear show">[show]</div> -->
    <?php // } else { ?>
        <!-- <div class="parametervalue showhidecuryear hide">[hide]</div> -->
    <?php // } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Years</div>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue headdata years"><?= $i ?></div>
    <?php } ?>
    <div class="parametervalue headdata prognosis currentyear">Yr to Date</div>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue headdata prognosis">On Pace</div>                    
    <?php } ?>    
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Orders #</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $sales[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?= $sales[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>    
        <div class="parametervalue"><?= $sales[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Pcs Sold </div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $pcssold[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?=$pcssold[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>    
        <div class="parametervalue"><?= $pcssold[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Revenue:</div>            
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $revenue[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?= $revenue[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue"><?= $revenue[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Gross Profit:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $grossprofit[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?= $grossprofit[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue"><?= $grossprofit[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Gross Profit %</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $grossrevenue_perc[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval"><?= $grossrevenue_perc[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue"><?= $grossrevenue_perc[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow bottomborder">
    <div class="parameterlabel">Expenses:
        <span class="exponsivedata shown"><i class="fa fa-plus-square-o" aria-hidden="true"></i></span>
    </div>            
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $expenses[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?= $expenses[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue"><?= $expenses[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow expensivesrow">
    <div class="parameterlabel expensives">Operating:</div>            
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$operating_help[$j]?>"><?= $operating[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval helpexpensive" data-content="<?=$operating_help[$j]?>"><?= $operating[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$operating_help[$j]?>"><?= $operating[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Adwords:</div>            
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$advertising_help[$j]?>"><?= $advertising[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval helpexpensive" data-content="<?=$advertising_help[$j]?>"><?= $advertising[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$advertising_help[$j]?>"><?= $advertising[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">US Payroll:</div>            
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$payroll_help[$j]?>"><?= $payroll[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval helpexpensive" data-content="<?=$payroll_help[$j]?>"><?= $payroll[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$payroll_help[$j]?>"><?= $payroll[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Int Upwork:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$odesk_help[$j]?>"><?= $odesk[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval helpexpensive" data-content="<?=$odesk_help[$j]?>"><?= $odesk[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$odesk_help[$j]?>"><?= $odesk[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">W9 Work:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$profitw9_help[$j]?>"><?= $profitw9[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="<?=$profitw9_help[$j]?>"><?=$profitw9[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$profitw9_help[$j]?>"><?=$profitw9[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow bottomborder">
    <div class="parameterlabel expensives">Purchases:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="<?=$purchases_help[$j]?>"><?= $purchases[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="<?=$purchases_help[$j]?>"><?= $purchases[$j] ?></div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
    <div class="parametervalue helpexpensive" data-content="<?=$purchases_help[$j]?>"><?= $purchases[$j] ?></div>
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
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
    <div class="parametervalue netprofit"><?= $netprofit[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Net Profit %:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $revenue_perc[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?= $revenue_perc[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
    <div class="parametervalue"><?= $revenue_perc[$j] ?></div>
    <?php } ?>
</div>
<div class="weektotalsrow bottomborder">
    <div class="parameterlabel">Net/Gross %:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue"><?= $grossprofit_perc[$j] ?></div>
        <?php $j++; ?>
    <?php } ?>            
    <div class="parametervalue currentyearval"><?= $grossprofit_perc[$j] ?></div>            
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
    <div class="parametervalue"><?= $grossprofit_perc[$j] ?></div>
    <?php } ?>
</div>        
