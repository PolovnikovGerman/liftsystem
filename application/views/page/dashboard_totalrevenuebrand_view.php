<div class="brantotalmoneyarea">
    <div class="datarow">
        <div class="revenuelabel">Revenue</div>
        <div class="paidtotallabel">Paid</div>
        <div class="unpaidtotallabel">Unpaid</div>
    </div>
    <div class="totalrevenuesection">
        <div class="datarow">
            <div class="totalrowlabel">Stress Balls</div>
            <div class="totalrowvalue"><?=MoneyOutput($sbtotal,0)?></div>
            <div class="totalrowpercent"><?=$sbrevenueperc==0 ? '&nbsp;' : $sbrevenueperc.'%'?></div>
        </div>
        <div class="datarow">
            <div class="totalrowlabel">Stress Relievers</div>
            <div class="totalrowvalue"><?=MoneyOutput($srtotal,0)?></div>
            <div class="totalrowpercent"><?=$srrevenueperc==0 ? '&nbsp;' : $srrevenueperc.'%'?></div>
        </div>
        <div class="datarow">
            <div class="totalrowlabel">&nbsp;</div>
            <div class="totalrowvalue revenuetotal"><?=MoneyOutput($alltotal,0)?></div>
            <div class="totalrowpercent revenuetotal">&nbsp;</div>
        </div>
    </div>
    <div class="totalpaymentsection">
        <div class="totalpaymentdatasection">
            <div class="datarow">
                <div class="totalpaymentvalue"><?=MoneyOutput($sbpayment,0)?></div>
                <div class="totalpaymentperentvalue"><?=$sbpaymentperc==0 ? '&nbsp;' : $sbpaymentperc.'%'?></div>
                <div class="totalunpaidvalue"><?=MoneyOutput($sbunpaid,0)?></div>
                <div class="totalpaymentperentvalue"><?=$sbunpaidperc==0 ? '&nbsp;' : $sbunpaidperc.'%'?></div>
            </div>
            <div class="datarow">
                <div class="totalpaymentvalue"><?=MoneyOutput($srpayment,0)?></div>
                <div class="totalpaymentperentvalue"><?=$srpaymentperc==0 ? '&nbsp;' : $srpaymentperc.'%'?></div>
                <div class="totalunpaidvalue"><?=MoneyOutput($srunpaid,0)?></div>
                <div class="totalpaymentperentvalue"><?=$srunpaidperc==0 ? '&nbsp;' : $srunpaidperc.'%'?></div>
            </div>
        </div>
        <div class="datarow">
            <div class="totalpaymentvalue"><?=MoneyOutput($allpayment,0)?></div>
            <div class="totalpaymentperentvalue"><?=$allpaymentperc==0 ? '&nbsp;' : $allpaymentperc.'%'?></div>
            <div class="totalunpaidvalue"><?=MoneyOutput($allunpaid,0)?></div>
            <div class="totalpaymentperentvalue"><?=$allunpaidperc==0 ? '&nbsp;' : $allunpaidperc.'%'?></div>
        </div>
    </div>
</div>