<div class="pototalsdataview">
    <input type="hidden" id="pototalsbrand" value="<?=$brand?>">
    <input type="hidden" id="pototalsinner" value="<?=$inner?>"/>
    <input type="hidden" id="poreporttotals" value="<?=$poreporttotals?>"/>
    <input type="hidden" id="poreportperpage" value="<?=$poreportperpage?>"/>
    <input type="hidden" id="poreportcurpage" value="0"/>

    <div class="pageheader">
        <div class="pagetitle">PO TOTALS</div>
        <div class="manage-purchase-methods">Manage Methods</div>
    </div>
    <div class="pototals-toplacehead">
        <div class="datarow">
            <div class="pototals-placetitle">POs to Place</div>
            <div class="pototals-placefiltr">
                <i class="fa fa-square-o"></i>
                hide internal
            </div>
            <div class="pototals-placeresult">
                <div class="pototals-placeresult-data"><span><?=MoneyOutput($totals['total'],0)?></span> Est Value</div>
                <div class="pototals-placeresult-data"><span><?=MoneyOutput($totals['totalfree'],0)?></span> w/o Internal</div>
            </div>
        </div>
        <div class="datarow">
            <div class="poplace-tablehead">
                <div class="poplace-timehead">Time</div>
                <div class="poplace-orderhead">Order #</div>
                <div class="poplace-itemhead">Item</div>
                <div class="poplace-vendorhead">Vendor</div>
                <div class="poplace-esttotalhead">Est PO</div>
                <div class="poplace-actionhead">&nbsp;</div>
            </div>
        </div>
        <div class="datarow">
            <div class="addnonlistedpo">Add Unlisted PO</div>
        </div>

        <?php if ($totaltab['toplace']['qty'] > 0) { ?>
            <div class="datarow pototalsrow">
                <div class="pototals-unsign-tolalqty"><?=QTYOutput($totaltab['toplace']['qty'])?> TO PLACE</div>
                <div class="pototals-unsign-tolalsum"><?=MoneyOutput($totaltab['toplace']['total'],0)?></div>
            </div>
            <div class="datarow">
                <div class="poplace-unsigntablebody"></div>
            </div>
        <?php } ?>
        <?php if ($totaltab['toapprove']['qty'] > 0 ) { ?>
            <div class="datarow pototalsrow">
                <div class="pototals-approved-tolalqty"><?=QTYOutput($totaltab['toapprove']['qty'])?> TO APPROVE</div>
                <div class="pototals-approved-tolalsum"><?=MoneyOutput($totaltab['toapprove']['total'],0)?></div>
            </div>
            <div class="datarow">
                <div class="poplace-approvtablebody"></div>
            </div>
        <?php } ?>
        <?php if ($totaltab['toproof']['qty'] > 0 ) { ?>
            <div class="datarow pototalsrow">
                <div class="pototals-proof-tolalqty"><?=QTYOutput($totaltab['toproof']['qty'])?> TO PROOF</div>
                <div class="pototals-proof-tolalsum"><?=MoneyOutput($totaltab['toproof']['total'],0)?></div>
            </div>
            <div class="datarow">
                <div class="poplace-prooftablebody"></div>
            </div>
        <?php } ?>
    </div>
    <!-- PO Reports: -->
    <div class="pototals-poreports">
        <div class="datarow">
            <div class="pototals-placetitle">PO Reports:</div>
        </div>
        <div class="datarow">
            <div class="poreport-sorting">
                Sort by:
            </div>
            <div class="poreport-sortingdata">
                <select class="poreportsortselect">
                    <option value="poqty"># of POs</option>
                    <option value="pocost">Cost</option>
                    <option value="poprofitprc">Profit %</option>
                    <option value="poprofit">Profit $$</option>
                </select>
            </div>
        </div>
        <div class="datarow">
            <div class="poreporttablehead">Compare Years:</div>
            <div class="poreportyeardata">
                <select class="poyearcompare yearfirst">
                    <?php foreach ($years as $year) { ?>
                        <option value="<?=$year?>" <?=($year==$year1 ? 'selected' : '')?>><?=$year?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="poreportyeardata">
                <select class="poyearcompare yearsecond">
                    <?php foreach ($years as $year) { ?>
                        <option value="<?=$year?>" <?=($year==$year2 ? 'selected' : '')?>><?=$year?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="poreportyeardata">
                <select class="poyearcompare yearthird">
                    <?php foreach ($years as $year) { ?>
                        <option value="<?=$year?>" <?=($year==$year3 ? 'selected' : '')?>><?=$year?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="datarow">
            <div class="poreporttablebody" id="poreporttable"></div>
        </div>
        <div class="datarow poreportPaginator">

        </div>
    </div>
    <!-- Vendor Statements: -->
    <div class="datarow">
        <div class="pototals-toplacehead">
            <div class="datarow">
                <div class="pototals-placetitle">Vendor Statements:</div>
            </div>
        </div>
    </div>
</div>
