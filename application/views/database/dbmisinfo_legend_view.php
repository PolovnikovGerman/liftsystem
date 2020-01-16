<div class="misinfo_legend">
    <img src="/img/database/kvadrat-blue.png"/>
    Field header in blue is sorted field
</div>
<div class="searchbox">
    <input name="searchtemplate" id="searchtemplate" type="text" value="<?= ($search == '' ? 'Enter keyword or item #' : $search) ?>" onfocus="if(this.value=='Enter keyword or item #') this.value='';" onblur="if(this.value=='') this.value='Enter keyword or item #';" />
    <a class="find_it" id="find_it" href="javascript:void(0);">
        Search It
    </a>
    <a class="find_it" id="clear_it" href="javascript:void(0);">
        Clear
    </a>
</div>
<div style="float: left; margin-top: 6px; margin-left: 15px; width: 175px;" class="dbpages_vendors">
    <select style="width: 165px;" name="vendorselect" id="vendorselect">
        <option value="">Select Vendor</option>
        <?php foreach ($vendors as $row) {?>
            <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
        <?php } ?>
    </select>
</div>
<div id="misinfoPagination" style="float: left; width: 210px; margin-left: 188px; margin-top: 7px; "></div>
