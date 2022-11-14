<div class="netprofitviewdata">
<?php $numpp=1;?>
<?php foreach ($data as $datum) { ?>
    <div class="datarow" data-profit="<?=$datum['profit_id']?>">
        <?php if (empty($datum['tax_quarter'])) { ?>
            <div class="weeknumber"><?=$datum['profit_week']?></div>
        <?php } else { ?>
            <div class="quoternumber"><?=$datum['tax_quarter']?></div>
        <?php } ?>
        <div class="netprofit-table-data <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$datum['datarowclass']?>" data-profit="<?=$datum['profit_id']?>">
            <div class="weekname editdata" data-profit="<?=$datum['profit_id']?>"><?=$datum['week']?></div>
            <div class="sales"><?=$datum['sales'] ?></div>
            <div class="revenue"><?=$datum['out_revenue']?></div>
            <div class="grossprofit <?= $datum['profit_class'] ?>" data-event="click" data-css="grossprofit_popup_message" data-bgcolor="#FFFFFF" data-bordercolor="#000"
                 data-textcolor="#000" data-position="down" data-balloon="{ajax} /netprofit/netprofit_weekdetails?id=<?=$datum['profit_id']?>">
                <?= $datum['out_profit'] ?>
            </div>
            <div class="profitperc"><?=$datum['out_profitperc']?></div>
            <div class="operating <?= $datum['operating_class'] ?>"><?=$datum['out_operating']?></div>
            <div class="ads <?=$datum['advertising_class']?>"><?=$datum['out_advertising'] ?></div>
            <div class="payroll <?= $datum['payroll_class'] ?>"><?= $datum['out_payroll'] ?></div>
            <div class="upwork <?= $datum['projects_class'] ?>"><?= $datum['out_projects'] ?></div>
            <div class="w9work <?= $datum['w9work_class'] ?>"><?=$datum['out_w9']?></div>
            <div class="discretionary <?=$datum['notesclass']?> <?= $datum['purchases_class'] ?>"><?= $datum['out_purchases'] ?></div>
            <div class="discretionarynote <?=$datum['notesclass']?>">
                <img src="/img/accounting/list.png" alt="Note"/>
            </div>
            <div class="totalcost <?= $datum['totalcost_class'] ?>"><?= $datum['out_totalcost'] ?></div>
            <div class="totalcostperc"><?=$datum['totalcostperc']?></div>
            <div class="netprofit <?= $datum['netprofit_class'] ?>"><?= $datum['out_netprofit'] ?></div>
            <div class="netprofitperc"><?=$datum['out_netprofitperc']?></div>
            <div class="invest <?= $datum['saved_class']?>"><?= $datum['out_saved'] ?></div>
            <div class="investperc"><?=$datum['out_savedperc']?></div>
            <div class="od <?= $datum['od_class']?>"><?=$datum['out_od']?></div>
            <div class="odperc"><?=$datum['out_odperc']?></div>
            <div class="retained <?= $datum['debt_class'] ?>"><?= $datum['out_debt'] ?></div>
            <div class="retainedperc"><?=$datum['out_debtperc']?></div>
            <div class="includeweek" data-profit="<?=$datum['profit_id']?>"><?=$datum['run_include']?></div>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>
</div>