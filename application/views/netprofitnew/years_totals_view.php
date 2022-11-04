<div class="weektotalsrow small">
    <div class="parameterlabel">&nbsp;</div>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue prognosis">&nbsp;</div>
    <?php } ?>
    <div class="parametervalue prognosis">Yr to Date</div>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue prognosis">On Pace</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Years</div>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue headdata years"><?= $i ?></div>
    <?php } ?>
    <div class="parametervalue headdata prognosis currentyear"><?=$i?></div>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Orders #</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">1000</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">1000</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">1001</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Pcs Sold </div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">10,000</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">10,000</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">10,000</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Revenue:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">$9,999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">$9,999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">$9,999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Gross Profit:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">$9,999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">$9,999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">$9,999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Gross Profit %</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">43%</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">43%</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">43%</div>
    <?php } ?>
</div>
<div class="weektotalsrow bottomborder">
    <div class="parameterlabel">Expenses:
        <span class="exponsivedata shown"><i class="fa fa-plus-square-o" aria-hidden="true"></i></span>
    </div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow expensivesrow">
    <div class="parameterlabel expensives">Operating:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Adwords:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Payroll:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">Upwork:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow">
    <div class="parameterlabel expensives">W9 Work:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow  expensivesrow bottomborder">
    <div class="parameterlabel expensives">Purchases:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval helpexpensive" data-content="">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue helpexpensive" data-content="">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel netprofit">Net Profit:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue netprofit">$999,999</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval netprofit">$999,999</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue netprofit">$999,999</div>
    <?php } ?>
</div>
<div class="weektotalsrow">
    <div class="parameterlabel">Net Profit %:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">43%</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">43%</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">43%</div>
    <?php } ?>
</div>
<div class="weektotalsrow bottomborder">
    <div class="parameterlabel">Net/Gross %:</div>
    <?php $j = 0; ?>
    <?php for ($i = $start_year; $i < $end_year; $i++) { ?>
        <div class="parametervalue">43%</div>
        <?php $j++; ?>
    <?php } ?>
    <div class="parametervalue currentyearval">43%</div>
    <?php $j++; ?>
    <?php if ($compareweek==0) { ?>
        <div class="parametervalue">43%</div>
    <?php } ?>
</div>
