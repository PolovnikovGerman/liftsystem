<input type="hidden" id="itemsalestotal" value="<?= $itmtotals ?>"/>
<input type="hidden" id="curpageitemsale" value="0"/>
<input type="hidden" id="itemsalecurrentyear" value="<?= $curentyear ?>"/>
<input type="hidden" id="itemsaleprevyear" value="<?= $prevyear ?>"/>
<div class="itemsalesreportarea">

    <div class="itemsalesheadrow">
        <div class="sortitemsalesarea">
            <div class="labeltxt">Sort by:</div>
            <div class="selectsortarea">
                <input type="radio" class="listchkbox" name="selectsort" value="curyearqty" <?= $sort == 'curyearqty' ? 'checked="checked"' : '' ?> />
                <div class="labeltxt year"><?= $curentyear ?> Qty</div>
                <input type="radio" class="listchkbox" name="selectsort" value="prvyearqty" <?= $sort == 'prvyearqty' ? 'checked="checked"' : '' ?>/>
                <div class="labeltxt year"><?= $prevyear ?> Qty</div>
                <input type="radio" class="listchkbox" name="selectsort" value="cursaved" <?= $sort == 'cursaved' ? 'checked="checked"' : '' ?> />
                <div class="labeltxt">Savings</div>
            </div>
        </div>
        <div class="vendorchoicearea">
            <div class="labeltxt">View:</div>
            <div class="selectsortarea">
                <input type="radio" class="listchkbox" name="selectvendor" value="" <?= $vendor == '' ? 'checked="checked"' : '' ?>/>
                <div class="labelallvend">All Vendors</div>
                <?php foreach ($vendors as $row) { ?>
                    <input type="radio" class="listchkbox" name="selectvendor" value="<?= $row ?>" <?= $vendor == $row ? 'checked="checked"' : '' ?>/>
                    <div class="labeltxt"><?= $row ?></div>
                <?php } ?>
                <input type="radio" class="listchkbox" name="selectvendor" value="other" <?= $vendor == 'other' ? 'checked="checked"' : '' ?>/>
                <div class="labeltxt">Other</div>
            </div>
        </div>
    </div>
    <div class="itemsalesheadrow">
        <div class="vendorcost">
            <div class="labeltxt">Vendor Cost</div>
            <div class="optionselect">
                <select class="vendorcostcalcselect">
                    <option value="high" <?= ($vendor_cost == 'high' ? 'selected="selected"' : '') ?> >Highest</option>
                    <option value="low" <?= ($vendor_cost == 'low' ? 'selected="selected"' : '') ?> >Lowest</option>
                    <option value="avg" <?= ($vendor_cost == 'avg' ? 'selected="selected"' : '') ?> >From PO</option>
                </select>
            </div>
        </div>
        <?= $selectyearshow ?>
        <div class="itemsalesshowrecords">
            <select class="perpage" id="itemsalesperpage">
                <?php foreach ($perpage as $row) { ?>
                    <option value="<?= $row ?>" <?= ($row == $currenrows ? 'selected="selected"' : '') ?>><?= $row ?> records/per page</option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="itemsalesheadrow">
        <div class="itemsales_search">
            <img style="float: left; margin-right: 5px; margin-top: 5px;" src="/img/icons/magnifier.png">
            <input placeholder="Enter Item #, Item Name" value="<?= $search ?>" class="itemsales_searchdata"/>
            <div class="itemsales_findall">&nbsp;</div>
            <div class="itemsales_clear">&nbsp;</div>
        </div>
        <div class="itemsalescaclarea">
            <div class="calclabel">Calculate for:</div>
            <input type="radio" class="calcyear" name="calcyear" value="<?= $curentyear ?>" checked="checked"/>
            <div class="labeltxt"><?= $curentyear ?></div>
            <input type="radio" class="calcyear" name="calcyear" value="<?= $prevyear ?>"/>
            <div class="labeltxt"><?= $prevyear ?></div>
        </div>
        <div class="itemsalesaddcost">
            <div class="addlcostlabel">Addl Cost:</div>
            <input type="text" class="addlcostinpt" value="<?= $addcost ?>"/>
            <div class="saveaddcost">&nbsp;</div>
        </div>
        <div class="itemsalespagination"></div>
    </div>
    <div class="itemsalesreporthead">
        <div class="edit lastcol">
            <input type="checkbox" class="itemsoldalldatachk" />
        </div>
        <div class="itemnumber">Item #</div>
        <div class="itemname lastcol">Item Name</div>
        <div class="repqty">‘<?= substr($curentyear, 2) ?> Qty</div>
        <div class="ordqty lastcol">#</div>
        <div class="repqty">‘<?= substr($prevyear, 2) ?> Qty</div>
        <div class="ordqty lastcol">#</div>
        <div class="qtychange">Change</div>
        <div class="ordqty lastcol">#</div>
        <div class="reprevenue">‘<?= substr($curentyear, 2) ?> Rev</div>
        <div class="reprevenue lastcol">‘<?= substr($prevyear, 2) ?> Rev</div>
        <div class="vendorname">Vendor</div>
        <div class="cost">Cost</div>
        <div class="curcog">Cur COG</div>
        <div class="curprofit">Cur Profit</div>
        <div class="curprofitperc lastcol">%</div>
        <div class="imptcost">Impt Cost</div>
        <div class="imptcog">Impt COG</div>
        <div class="imptprofit">Impt Profit</div>
        <div class="imptprofitproc lastcol">%</div>
        <div class="savings">Savings</div>
    </div>
    <div class="itemsalesdatarow totaldataview" data-item='totals' <?= ($totals == 1 ? 'style="display:block;"' : '') ?> >
        <?= ($totals == 1 ? $totalview : '') ?>
    </div>
    <div class="itemsalesreportdata"></div>
</div>
<input type="hidden" id="itemsalesreportbrand" value="<?=$brand?>"/>
<?=$top_menu?>
