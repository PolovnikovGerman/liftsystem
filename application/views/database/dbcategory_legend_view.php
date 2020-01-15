<div class="category_filters" style="clear: both; float:  left; width: 1001px;">
    <div class="category_legend"><img src="/img/database/kvadrat-blue.png"/>Field header in blue is sorted field</div>
</div>
<div class="category_filters" style="clear: both; float:  left; width: 1001px;">
    <div class="searchbox">
        <input name="searchtemplate" id="searchdbcategory" type="text" value="<?=$search?>" placeholder="Enter keyword or item #"/>
        <a class="find_it" id="dbcategorfind_it" href="javascript:void(0);">
            Search It
        </a>
        <a class="find_it" id="dbcategorclear_it" href="javascript:void(0);">
            Clear
        </a>
    </div>
    <div class="pagemanage">
        <div class="pagelocked" id="pagemanage">Page Locked</div>
    </div>
    <div style="float: left; margin-top: 6px; margin-left: 15px; width: 175px;" class="dbpages_vendors">
        <select style="width: 165px;" name="vendorselect" id="vendorselect">
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $row) {?>
                <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
            <?php } ?>
        </select>
    </div>
    <div id="dbcategoryPagination" style="float: left; width: 210px; margin-left: 20px; margin-top: 7px; "></div>
</div>