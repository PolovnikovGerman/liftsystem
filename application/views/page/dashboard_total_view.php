<div class="period_name"
     data-event="click" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
     data-position="left" data-balloon="{ajax} /welcome/weektotalorders">This Week</div>
<div id="showtotalthisweek">
    <div class="param_value_label">ORDERS:</div>
    <div class="param_value" id="totalsales"><?=$data['sales']?></div>
</div>
<div>
    <div class="allbrandstotalweek" data-viewsrc="/welcome/weektotals">All Brands</div>
    <div class="param_value money" id="totalrevenue"><?=MoneyOutput($data['revenue'],0)?></div>
</div>
