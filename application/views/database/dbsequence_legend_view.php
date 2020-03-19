<div class="dbsequencelegend-left">
    <h4>Item Sequence</h4>
    <div class="clearfix"></div>
    <div class="searchbox">
        <input name="searchtemplate" id="searchdbseq" type="text" placeholder="Enter keyword or item #"/>
        <a class="find_it" id="dbseqfind_it" href="javascript:void(0);">
            Search It
        </a>
        <a class="find_it" id="dbseqclear_it" href="javascript:void(0);">
            Clear
        </a>
    </div>
</div>
<div class="dbsequencelegend-right">
    <div class="dbpage_iteminrow">
        <select id="iteminrowselect" class="iteminrowselect">
            <option value="5">5 Items Across</option>
            <option value="4">4 Items Across</option>
        </select>
    </div>
    <div class="dbpages_itemseqvendors">
        <select id="dbseqvendorselect" name="vendorselect" class="dbseqvendorselect">
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $row) { ?>
                <option value="<?=$row['vendor_id']?>" <?=$row['vendor_id']<0 ? 'disabled' : '' ?>><?=$row['vendor_name']?></option>
            <?php } ?>
        </select>
    </div>
</div>
