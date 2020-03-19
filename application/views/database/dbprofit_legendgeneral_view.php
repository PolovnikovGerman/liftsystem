<div class="profitlegend_row">
    <ul>
        <li><img src="/img/database/kvadrat-blue.png"/> Sorted Header Field</li>
    </ul>
</div>
<input type="hidden" id="profitprefs" value=""/>
<div class="profitlegend_row" >
    <div class="searchbox">
        <input name="searchtemplate" id="searchdbprofit" type="text" value="<?$search?>" placeholder="Enter keyword or item #"/>
        <a class="find_it" id="dbprofitfind_it" href="javascript:void(0);">
            Search It
        </a>
        <a class="find_it" id="dbprofitclear_it" href="javascript:void(0);">
            Clear
        </a>
    </div>
    <div class="dbpages_vendors">
        <select id="dbprofitvendorselect" name="vendorselect" class="vendorselect">
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $row) {?>
                <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
            <?php } ?>
        </select>
    </div>
    <div id="dbprofitPagination" style="float: right;width: 186px;margin-left: 7px; margin-top: 5px; "></div>
</div>
