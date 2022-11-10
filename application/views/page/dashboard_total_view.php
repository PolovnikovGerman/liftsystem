<div class="period_name" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF"
     data-position="down" data-balloon="{ajax} /welcome/weektotalorders">This Week</div>
<div id="showtotalthisweek">
    <div class="param_value_label">ORDERS:</div>
    <div class="param_value" id="totalsales"><?=$data['sales']?></div>
</div>
<div>
    <div class="allbrandstotalweek" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF"
         data-position="down" data-balloon="{ajax} /welcome/weektotals">All Brands</div>
    <div class="param_value money" id="totalrevenue"><?=MoneyOutput($data['revenue'],0)?></div>
</div>
