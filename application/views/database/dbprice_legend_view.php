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
    <div class="pricecompare-area">
        <div class="pricecompare_label">Compare price with:</div>
        <div class="pricecompare_value">
            <?php foreach ($othervendor as $row) {?>
                <div class="othvendname"><?=$row['other_vendor_name']?>
                    <input type="checkbox" class="pricecomparechk" value="1" data-othvendor="<?=$row['other_vendor_id']?>" <?=$row['chk']?>/>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="priceotherfiltr">
        <div class="sortupdatetimearea">
            <select class="sortupdatetim" id="sortupdatetim">
                <option value="">Any Time</option>
                <option value="upd-desc" <?=(($order_by=='update_time' && $direction=='desc') ? 'selected="selected"' : '')?>>Update Time &#9660;</option>
                <option value="upd-asc" <?=(($order_by=='update_time' && $direction=='asc') ? 'selected="selected"' : '')?>>Update Time &#9650;</option>
            </select>
        </div>
        <div class="profit_classes">
            <select id="compareprefs" class="compareprefs" name="compareprefs">
                <option value="" <?=($priority=='' ? 'selected="selected"' : '')?>>No Priority</option>
                <option value="red" class="red" <?=($priority=='red' ? 'selected="selected"' : '')?>>They Beat Us</option>
                <option value="orange" class="orange" <?=($priority=='orange' ? 'selected="selected"' : '')?>>We Tie</option>
                <option value="pink" class="pink" <?=($priority=='pink' ? 'selected="selected"' : '')?>>We beat by 3¢ or more</option>
                <option value="white" class="white" <?=($priority=='white' ? 'selected="selected"' : '')?>>We beat</option>
            </select>
        </div>
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
