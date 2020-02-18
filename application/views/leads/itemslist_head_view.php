<input type="hidden" id="totalleaditems" value="<?=$total?>"/>
<input type="hidden" id="leaditempage" value="<?=$cur_page?>"/>
<input type="hidden" id="leaditemperpage" value="150"/>
<div class="leaditem_headarea">
    <div class="leaditem_headrow">
        <div class="leaditem_search">
            <img src="/img/magnifier.png"/>
            <input class="leaditem_searchdata" value="" placeholder="Enter Item#, Item Name"/>
            <div class="leaditem_find">&nbsp;</div>
            <div class="leaditem_clear">&nbsp;</div>
        </div>
        <div class="leaditem_emptyspace" style="width: 15%">&nbsp;</div>
        <div class="leaditem_filters">
            <select class="leaditem_filterselect leaditemsvendor">
                <option value="">All Vendors</option>
                <?php foreach ($vendors as $vrow) { ?>
                    <option value="<?=$vrow['vendor_id']?>"><?=$vrow['vendor_name']?></option>
                <?php } ?>
            </select>
            <select class="leaditem_filterselect leaditemspriority">
                <option value="">No Priority</option>
                <option class="blackpriority" value="black">Blacks as Priority</option>
                <option class="maroonpriority" value="maroon">Maroon as Priority</option>
                <option class="redpriority" value="red">Reds as Priority</option>
                <option class="orangepriority" value="orange">Oranges as Priority</option>
                <option class="whitepriority" value="white">Whites as Priority</option>
                <option class="greenpriority" value="green">Greens as Priority</option>
            </select>
        </div>
    </div>
    <!-- Legend -->
    <div class="leaditem_headrow">
        <div class="leaditem_legend long">
            <img src="/img/leads/legend_lose.png"/>
            <!-- <div class="leaditem_legendlabel long">Lose $$ (0% or less)</div> -->
            <div class="leaditem_legendlabel long">Lose $$</div>
        </div>
        <div class="leaditem_legend long">
            <img src="/img/leads/legend_verybad.png"/>
            <!-- <div class="leaditem_legendlabel long">Very Bad (less than 1%)</div> -->
            <div class="leaditem_legendlabel long">Very Bad</div>
        </div>
        <div class="leaditem_legend">
            <img src="/img/leads/legend_bad.png"/>
            <!-- <div class="leaditem_legendlabel">Bad (1% - 1.5%)</div> -->
            <div class="leaditem_legendlabel">Bad</div>
        </div>
        <div class="leaditem_legend long">
            <img src="/img/leads/legend_bellowavg.png"/>
            <!-- <div class="leaditem_legendlabel long">Below Avg (1.6% - 2.3%)</div> -->
            <div class="leaditem_legendlabel long">Below Avg</div>
        </div>
        <div class="leaditem_legend long">
            <img src="/img/leads/legend_target.png"/>
            <!--<div class="leaditem_legendlabel long">Target (2.4% - 3.1%)</div> -->
            <div class="leaditem_legendlabel long">Target</div>
        </div>
        <div class="leaditem_legend">
            <img src="/img/leads/legend_great.png"/>
            <!-- <div class="leaditem_legendlabel">Great (over 3.2%)</div> -->
            <div class="leaditem_legendlabel">Great</div>
        </div>
        <div class="leaditem_pagination">&nbsp;</div>
    </div>
    <!-- Item Table Head -->
    <div class="leaditem_datahead">
        <div class="itemnumber">Item #</div>
        <div class="itemname">Item Name</div>
        <div class="vendor">Vendor</div>
        <div class="vendorzip">Zip</div>
        <?php foreach ($prices as $row) { ?>
            <div class="vendorprice"><?=$row?></div>
        <?php } ?>
        <div class="vendorsetup">Setup</div>
    </div>
    <div class="leaditems_dataarea">&nbsp;</div>
</div>
