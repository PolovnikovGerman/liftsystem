<input type="hidden" id="itemmonthtotal" value="<?=$totals?>"/>
<input type="hidden" id="curpageitemmonth" value="0"/>
<input type="hidden" id="itemmonthcurrentyear" value="<?=$curentyear?>"/>
<input type="hidden" id="itemmonthstartyear" value="<?=$startyear?>"/>

<div class="itemmonthsalesreportarea">
    <div class="itemmonthheadrow">
        <div class="itemmonthsort">
            <div class="labeltxt">Sort by:</div>
            <select class="sortselect" id='itemmonthsortyear'>
                <?php foreach ($sortyears as $row) { ?>
                    <option value="<?=$row?>" <?=$row==$curentyear ? 'selected="selected"' : ''?> ><?=$row?></option>
                <?php } ?>
            </select>
            <select class="sortselect" id="itemmonthsortfld">
                <option value="total" selected="selected">Total</option>
                <option value="01">Jan</option>
                <option value="02">Feb</option>
                <option value="03">Mar</option>
                <option value="04">Apr</option>
                <option value="05">May</option>
                <option value="06">Jun</option>
                <option value="07">Jul</option>
                <option value="08">Aug</option>
                <option value="09">Sep</option>
                <option value="10">Oct</option>
                <option value="11">Nov</option>
                <option value="12">Dec</option>
            </select>
        </div>
        <div class="itemmonthshowrecords">
            <select class="selectperrecord" id="itemmonthperpage">
                <?php foreach ($perpage as $row) { ?>
                    <option value="<?=$row?>" <?=$row==$currenrows ? 'selected="selected"' : ''?>><?=$row?> records / per page</option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="itemmonthheadrow">
        <div class="itemmonth_search">
            <img style="float: left; margin-right: 5px; margin-top: 5px;" src="/img/icons/magnifier.png">
            <input placeholder="Enter Item #, Item Name" value="" class="itemmonth_searchdata"/>
            <div class="itemmonth_findall">&nbsp;</div>
            <div class="itemmonth_clear">&nbsp;</div>
        </div>
        <div class="itemmonthpagination"></div>
    </div>
    <div class="itemmonth_head">
        <div class="numrec">&nbsp;</div>
        <div class="itemnumber">Item #</div>
        <div class="itemname lastcol">Item Name</div>
        <div class="year">Year</div>
        <div class="orders">Orders</div>
        <div class="qty lastcol">Qty Sold</div>
        <div class="monthdetail monthdata">Jan</div>
        <div class="monthdetail monthdata">Feb</div>
        <div class="monthdetail monthdata">Mar</div>
        <div class="monthdetail monthdata">Apr</div>
        <div class="monthdetail monthdata">May</div>
        <div class="monthdetail monthdata">Jun</div>
        <div class="monthdetail monthdata">Jul</div>
        <div class="monthdetail monthdata">Aug</div>
        <div class="monthdetail monthdata">Sep</div>
        <div class="monthdetail monthdata">Oct</div>
        <div class="monthdetail monthdata">Nov</div>
        <div class="monthdetail monthdatalast">Dec</div>
    </div>
    <div class="itemmonth_data">&nbsp;</div>
</div>
