<div class="dbpricelegend-left">
    <ul>
        <li><img src="/img/database/kvadrat-blue.png"/> Field header in blue is sorted field</li>
    </ul>
    <br/>
    <ul>
        <li><img src="/img/database/kvadrat-white.png"/> We Beat</li>
        <li><img src="/img/database/kvadrat-pink.png"/> We beat by <?=$mindiff?>¢ or more</li>
        <li><img src="/img/database/kvadrat-yellow.png"/> We Tie</li>
        <li><img src="/img/database/kvadrat-red.png"/> They Beat Us</li>
    </ul>
    <div class="clearfix"></div>
    <div class="searchbox">
        <input name="searchtemplate" id="dbpricetemplate" placeholder="Enter keyword or item #" type="text" value="<?=$search?>" />
        <a class="find_it" id="find_it" href="javascript:void(0);">
            Search It
        </a>
        <a class="find_it" id="clear_it" href="javascript:void(0);">
            Clear
        </a>
    </div>
</div>
<div class="dbpricelegend-right">
    <div class="pricecompare-area">&nbsp;</div>
    <div class="priceotherfiltr">
        <div class="sortupdatetimearea">&nbsp;</div>
        <div class="profit_classes">&nbsp;</div>
        <div class="dbpages_vendors">
            <select id="vendorselect" name="vendorselect" class="vendorselect">
                <option value="">Select Vendor</option>
                <?php foreach ($vendors as $row) {?>
                    <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div id="dbpricePagination" style="float: right;width: 186px;margin-left: 7px; margin-top: 5px; "></div>
    </div>
</div>
