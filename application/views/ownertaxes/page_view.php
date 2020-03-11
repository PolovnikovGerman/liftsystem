<input type="hidden" id="calcsessionid" value="<?= $session_id ?>"/>
<input type="hidden" id="taxownincome" value="1"/>
<input type="hidden" id="taxownexpence" value="1"/>
<div class="ownertaxcontent">
    <div class="ownertaxtotalsarea">
        <div class="ownertaxtotals">
            <div class="datarow">
                <div class="label">Projected <?= $year ?> Company Profit:</div>
                <div class="value ownertxprofit"><?= MoneyOutput($netprofit, 0) ?></div>
            </div>
            <div class="datarow">
                <div class="label"><?= $profitkf ?>% Ownership:</div>
                <div class="value ownertxvalue"><?= MoneyOutput($ownership, 0) ?></div>                
            </div>
        </div>
        <div class="baseperiodselectarea">
            <div class="periodtypeselect">
                <div class="label">Projected Income</div>
                <div class="periodtypeselectarea">
                    <div class="datarow">
                        <div class="inputplace switchon" data-pace="income" data-proj="1">
                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>                        
                        </div>
                        <div class="inputlabel">Current Pace</div>
                    </div>
                    <div class="datarow">
                        <div class="inputplace" data-pace="income" data-proj="2">
                            <i class="fa fa-circle-o" aria-hidden="true"></i>
                        </div>
                        <div class="inputlabel">Last Year&apos;s Pace</div>
                    </div>                
                </div>
            </div>
            <div class="periodtypeselect">
                <div class="label">Projected Expenses</div>
                <div class="periodtypeselectarea">
                    <div class="datarow">
                        <div class="inputplace switchon" data-pace="expenses" data-proj="1">
                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>                        
                        </div>
                        <div class="inputlabel">Current Pace</div>
                    </div>
                    <div class="datarow">
                        <div class="inputplace" data-pace="expenses" data-proj="2">
                            <i class="fa fa-circle-o" aria-hidden="true"></i>
                        </div>
                        <div class="inputlabel">Last Year&apos;s Pace</div>
                    </div>                
                </div>
            </div>
        </div>
    </div>
    <div class="ownertaxcontentcalcarea">
        <!-- Single Filing Calculator -->
        <?= $singlecalc ?>
        <div class="singlecalcmanage">
            <div class="turnoffsign switchoff">
                <i class="fa fa-square-o" aria-hidden="true"></i>
                <!-- <i class="fa fa-check-square-o" aria-hidden="true"></i> -->
            </div>
            <div class="label">Exclude OD</div>
        </div>
        <!-- Joint Filing Calculator -->
        <?= $jointcalc ?>        
    </div>
    <div class="ownertaxessave">
        <img src="/img/saveticket.png" alt="Save"/>
    </div>
</div>