<div class="category_filters">
    <div class="category_legend"><img src="/img/database/kvadrat-blue.png"/>Field header in blue is sorted field</div>
</div>
<div class="category_filters">
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
    <div class="dbpages_categoryvendors">
        <select name="vendorselect" id="vendordbcateg">
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $row) {?>
                <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
            <?php } ?>
        </select>
    </div>
    <div id="dbcategoryPagination" class="dbcategorypagination"></div>
</div>