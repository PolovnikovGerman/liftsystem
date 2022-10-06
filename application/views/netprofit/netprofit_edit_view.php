<input id="detailssession" type="hidden" value="<?=$session?>"/>
<div class="cell_buttonedit">
    <div class="but-accept">
        <a href="javascript:void(0);" id="netprofitdetailsave"><img src="/img/icons/accept.png"/></a>
    </div>
    <div class="but-cancel">
        <a href="javascript:void(0);" id="netprofitdetailcancel"><img src="/img/icons/cancel.png"/></a>
    </div>
</div>
<div class="cell_week2edt"><?= $week ?></div>
<div class="cell_salesedit"><?= $sales ?></div>
<div class="cell_revenue2" style="width:63px"><?= $out_revenue ?></div>
<div class="cell_gross_profit2 <?= $profit_class ?>"><?= $out_profit ?></div>
<div class="cell_gross_profitpercent2"><?= $out_revenueprc ?></div>
<div class="cell_operating2">
    <input class="inputoper netprofitdetailinpt" type="text" data-fld="profit_operating" value="<?= $profit_operating ?>"/>
</div>
<div class="cell_advertising2">
    <input class="inputadvert netprofitdetailinpt" type="text" data-fld="profit_advertising" value="<?=$profit_advertising ?>"/>
</div>
<div class="cell_payroll2">
    <input class="inputpayroll netprofitdetailinpt" type="text" data-fld="profit_payroll" value="<?= $profit_payroll ?>"/>
</div>
<div class="cell_projects2">
    <input class="inputprojects netprofitdetailinpt" type="text" data-fld="profit_projects" value="<?= $profit_projects ?>"/>
</div>
<div class="cell_w9work_edit">
    <?=$out_w9 ?>
</div>
<div class="cell_purchases_edit">
    <?= $out_purchases ?>
</div>
<div class="cell_inbut2">
    <div class="imbox_edit">
        <img src="/img/accounting/list.png" alt="box" title="Notes"/>
    </div>
</div>
<div class="cell_total_costs2"><?=$out_totalcost ?></div>            
<div class="cell_net_profit3"><?= $out_netprofit ?></div>
<div class="cell_for_debtincledit" id="editnetdetailsdebtincl"><?=$debt_include ?></div>
<div class="cell_for_saved2">
    <input class="inputsaved netprofitdetailinpt" type="text" data-fld="profit_saved" value="<?= $profit_saved ?>"/>
</div>
<div class="cell_for_owners2">
    <input class="inputowners netprofitdetailinpt" type="text" data-fld="od2" id="od2" value="<?= $od ?>"/>
</div>
<div class="cell_for_debt2"><?= $out_debt ?></div>
