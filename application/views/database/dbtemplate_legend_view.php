<div class="templatelegend_row">
    <ul>
        <li>
            <img src="/img/database/kvadrat-blue.png"/> Field header in blue is sorted field
        </li>
    </ul>
</div>
<div class="templatelegend_row">
    <div class="searchbox">
        <input name="searchtemplate" id="searchdbtemplat" type="text" value="<?=$search?>" placeholder="Enter keyword or item #"/>
        <a class="find_it" id="dbtemplatfind_it" href="javascript:void(0);">
            Search It
        </a>
        <a class="find_it" id="dbtemplatclear_it" href="javascript:void(0);">
            Clear
        </a>
    </div>
    <div class="dbtemplatepages_vendors">
        <select  name="vendorselect" id="dbtemplatvendorselect" class="dbtemplvendorselect">
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $row) {?>
                <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
            <?php } ?>
        </select>
    </div>
    <div id="dbtemplatPagination" class="dbtemplatpagination"></div>
</div>
