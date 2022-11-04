<?php $numpp=1;?>
<?php foreach ($data as $datum) { ?>
    <div class="datarow" data-profit="<?=$datum['profit_id']?>">
        <?php if (empty($datum['tax_quarter_class'])) { ?>
            <div class="weeknumber"><?=$datum['profit_week']?></div>
        <?php } else { ?>
            <div class="quoternumber"><?=$datum['tax_quarter']?></div>
        <?php } ?>
        <div class="netprofit-table-data <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$datum['datarowclass']?>">
            <div class="weekname"><?=$datum['week']?></div>
            <div class="sales">1295</div>
            <div class="revenue">$9,999,999</div>
            <div class="grossprofit">$999,999</div>
            <div class="profitperc">43%</div>
            <div class="operating">----</div>
            <div class="ads">$99,999</div>
            <div class="payroll">$999,9999</div>
            <div class="upwork">$99,999</div>
            <div class="w9work">----</div>
            <div class="discretionary">----</div>
            <div class="discretionarynote">
                <img src="/img/accounting/list.png" alt="Note"/>
            </div>
            <div class="totalcost">$999,999</div>
            <div class="totalcostperc">$43</div>
            <div class="netprofit">$999,999</div>
            <div class="netprofitperc">43%</div>
            <div class="invest">----</div>
            <div class="investperc">&nbsp;</div>
            <div class="od">----</div>
            <div class="odperc">&nbsp;</div>
            <div class="retained">----</div>
            <div class="retainedperc">&nbsp;</div>
            <div class="includeweek">
                <i class="fa fa-square-o" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>
<div class="datarow">
    <div class="quoternumber">TQ3</div>
    <div class="netprofit-table-data whitedatarow">
        <div class="weekname">Oct 10-16,2022</div>
        <div class="sales">1295</div>
        <div class="revenue">$9,999,999</div>
        <div class="grossprofit">$999,999</div>
        <div class="profitperc">43%</div>
        <div class="operating">----</div>
        <div class="ads">$99,999</div>
        <div class="payroll">$999,9999</div>
        <div class="upwork">$99,999</div>
        <div class="w9work">----</div>
        <div class="discretionary">----</div>
        <div class="discretionarynote">
            <img src="/img/accounting/list.png" alt="Note"/>
        </div>
        <div class="totalcost">$999,999</div>
        <div class="totalcostperc">$43</div>
        <div class="netprofit">$999,999</div>
        <div class="netprofitperc">43%</div>
        <div class="invest">----</div>
        <div class="investperc">&nbsp;</div>
        <div class="od">----</div>
        <div class="odperc">&nbsp;</div>
        <div class="retained">----</div>
        <div class="retainedperc">&nbsp;</div>
        <div class="includeweek">
            <i class="fa fa-square-o" aria-hidden="true"></i>
        </div>
    </div>
</div>
