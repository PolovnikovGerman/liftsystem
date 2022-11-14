<input id="detailssession" type="hidden" value="<?=$session?>"/>
<div class="cell_buttonedit">
    <div class="but-accept">
        <img class="img-responsive" src="/img/icons/accept.png"/></a>
    </div>
    <div class="but-cancel">
        <img class="img-responsive" src="/img/icons/cancel.png"/></a>
    </div>
</div>
<div class="cell_week2edt"><?= $week ?></div>
<div class="sales"><?=$sales?></div>
<div class="revenue"><?=$out_revenue?></div>
<div class="grossprofit <?=$profit_class ?>"><?= $out_profit ?></div>
<div class="profitperc"><?=$out_profitperc?></div>
<div class="operating">
    <input class="netprofitedit" data-fld="operating" value="<?=$operating?>"/>
</div>
<div class="ads btneditnetprofit"><?=$out_advertising ?></div>
<div class="payroll">
    <input class="netprofitedit" data-fld="payroll" value="<?=$payroll?>"/>
</div>
<div class="upwork btneditnetprofit"><?=$out_projects ?></div>
<div class="w9work btneditnetprofit"><?=$out_w9?></div>
<div class="discretionary  btneditnetprofit"><?=$out_purchases ?></div>
<div class="discretionarynote">
    <img src="/img/accounting/list.png" alt="Note"/>
</div>
<div class="totalcost <?= $totalcost_class ?>"><?= $out_totalcost ?></div>
<div class="totalcostperc"></div>
<div class="netprofit <?= $netprofit_class ?>"><?= $out_netprofit ?></div>
<div class="netprofitperc"><?=$out_netprofitperc?></div>
<div class="invest">
    <input class="netprofitedit" data-fld="saved" value="<?=$saved?>"/>
</div>
<div class="investperc"><?=$out_savedperc?></div>
<div class="od">
    <input class="netprofitedit" data-fld="od2" value="<?=$od2?>"/>
</div>
<div class="odperc"><?=$out_odperc?></div>
<div class="retained"><?=$out_debt?></div>
<div class="retainedperc"><?=$out_debtperc?></div>
<div class="includeweek_edit"><?=$run_include?></div>
