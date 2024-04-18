<div class="period_name">ALL BRANDS THIS WEEK</div>
<div class="period_results">
    <div class="param_value" id="totalsales" data-event="click" data-css="weekbrandtotals" data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF"
         data-position="down" data-balloon="{ajax} /welcome/weektotalorders"><?=$data['sales']?></div>
    <div class="param_value_label"> Orders @ </div>
    <div class="param_value money" id="totalrevenue" data-event="click" data-css="weekbrandtotals" data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF"
         data-position="down" data-balloon="{ajax} /welcome/weektotals"><?=MoneyOutput($data['revenue'],0)?></div>
</div>
