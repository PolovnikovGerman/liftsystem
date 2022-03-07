<input type="hidden" id="pototalsbrand" value="<?=$brand?>">
<input type="hidden" id="pototalsinner" value="<?=$inner?>"/>
<main class="container-fluid">
    <div class="pototalsdataview">
        <div class="pageheader">
            <div class="row pt-2">
                <div class="col-7 col-lg-5">
                    <div class="pagetitle">PO TOTALS</div>
                </div>
                <div class="col-5 col-lg-5">
                    <div class="manage-purchase-methods">Manage Methods</div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xs-4 pototals-toplacehead">
                <div class="row">
                    <div class="col-4 pototals-placetitle">POs to Place</div>
                    <div class="col-4 pototals-placefiltr">
                        <i class="fa fa-square-o"></i>
                        hide internal
                    </div>
                    <div class="col-4 pototals-placeresult pr-0">
                        <div class="row">
                            <div class="col-12"><span><?=MoneyOutput($totals['total'],0)?></span> Est Value</div>
                            <div class="col-12"><span><?=MoneyOutput($totals['totalfree'],0)?></span> w/o Internal</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 poplace-tablehead">
                        <div class="poplace-timehead">Time</div>
                        <div class="poplace-orderhead">Order #</div>
                        <div class="poplace-itemhead">Item</div>
                        <div class="poplace-vendorhead">Vendor</div>
                        <div class="poplace-esttotalhead">Est PO</div>
                        <div class="poplace-actionhead">&nbsp;</div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="addnonlistedpo">Add Unlisted PO</div>
                    </div>
                </div>
                <?php if ($totaltab['toplace']['qty'] > 0) { ?>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="pototals-unsign-tolalqty"><?=QTYOutput($totaltab['toplace']['qty'])?> TO PLACE</div>
                            <div class="pototals-unsign-tolalsum"><?=MoneyOutput($totaltab['toplace']['total'],0)?></div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-12">
                            <div class="poplace-unsigntablebody"></div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($totaltab['toapprove']['qty'] > 0 ) { ?>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="pototals-approved-tolalqty"><?=QTYOutput($totaltab['toapprove']['qty'])?> TO APPROVE</div>
                            <div class="pototals-approved-tolalsum"><?=MoneyOutput($totaltab['toapprove']['total'],0)?></div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-12">
                            <div class="poplace-approvtablebody"></div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($totaltab['toproof']['qty'] > 0 ) { ?>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="pototals-proof-tolalqty"><?=QTYOutput($totaltab['toproof']['qty'])?> TO PROOF</div>
                            <div class="pototals-proof-tolalsum"><?=MoneyOutput($totaltab['toproof']['total'],0)?></div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-12">
                            <div class="poplace-prooftablebody"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- Vendor Statements: -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xs-4 pototals-toplacehead">
                <div class="row">
                    <div class="col-9 pototals-placetitle">Vendor Statements:</div>
                </div>
            </div>
            <!-- PO Reports: -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xs-4 pototals-toplacehead">
                <div class="row">
                    <div class="col-9 pototals-placetitle">PO Reports:</div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 poreport-sorting">
                        <span>Sort by:</span>
                        <select class="poreportsortselect">
                            <option value="poqty"># of POs</option>
                            <option value="pocost">Cost</option>
                            <option value="poprofitprc">Profit %</option>
                            <option value="poprofit">Profit $$</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">Compare Years:</div>
                    <div class="col-3">
                        <select class="poyearcompare yearfirst">
                            <?php foreach ($years as $year) { ?>
                                <option value="<?=$year?>" <?=($year==$year1 ? 'selected' : '')?>><?=$year?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <select class="poyearcompare yearsecond">
                            <?php foreach ($years as $year) { ?>
                                <option value="<?=$year?>" <?=($year==$year2 ? 'selected' : '')?>><?=$year?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <select class="poyearcompare yearthird">
                            <?php foreach ($years as $year) { ?>
                                <option value="<?=$year?>" <?=($year==$year3 ? 'selected' : '')?>><?=$year?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row" id="poreporttable">

                </div>
                <div class="row">
                    <div class="col-12 poreportPaginator"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalManage" tabindex="-1" role="dialog" aria-labelledby="modalManageLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalManageLabel">PO Payment Methods</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditpurchase" tabindex="-1" role="dialog" aria-labelledby="modalEditpurchaseLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEditpurchaseLabel">Enter PO Value</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
