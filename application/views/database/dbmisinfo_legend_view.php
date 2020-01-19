<div class="misinfo_legend">
    <img src="/img/database/kvadrat-blue.png"/>
    Field header in blue is sorted field
</div>
<div class="searchbox">
    <input name="searchtemplate" id="searchmisinfo" type="text" value="<?=$search?>" placeholder="Enter keyword or item #"/>
    <a class="find_it" id="misinfofind_it" href="javascript:void(0);">
        Search It
    </a>
    <a class="find_it" id="misinfoclear_it" href="javascript:void(0);">
        Clear
    </a>
</div>
<div class="dbpages_misinfovendors">
    <select name="vendorselect" id="vendorselectmisinfo">
        <option value="">Select Vendor</option>
        <?php foreach ($vendors as $row) {?>
            <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
        <?php } ?>
    </select>
</div>
<div id="misinfoPagination" class="misinfopagination"></div>
