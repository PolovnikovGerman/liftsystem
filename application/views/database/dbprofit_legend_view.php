<div class="profitlegend_row">
    <ul>
        <li><img src="/img/database/kvadrat-blue.png"/> Sorted Header Field</li>
    </ul>
</div>
<div class="profitlegend_row">
    <ul class="profitlegend">
        <li><img src="/img/database/kvadrat-black.png"/> Is 0% and under</li>
        <li><img src="/img/database/kvadrat-maroon.png"/> 1% - 9% </li>
        <li><img src="/img/database/kvadrat-red.png"/>  10% - 19% </li>
        <li><img src="/img/database/kvadrat-orange.png"/> 20% - 25% </li>
        <li><img src="/img/database/kvadrat-white.png"/> 26% - 39%</li>
        <li><img src="/img/database/kvadrat-green.png"/> 40% and higher</li>
    </ul>
    <div class="profit_classes" style="float: left; margin-top: 6px; padding-left: 1px; width: 154px;">
        <select id="dbprofitprofitprefs" name="profitprefs" style="width: 150px">
            <option value="" <?=($priority=='' ? 'selected="selected"' : '')?>>No Priority</option>
            <option value="black" class="black" <?=($priority=='black' ? 'selected="selected"' : '')?>>Blacks as Priority</option>
            <option value="maroon" class="maroon" <?=($priority=='maroon' ? 'selected="selected"' : '')?>>Maroon as Priority</option>
            <option value="red" class="red" <?=($priority=='red' ? 'selected="selected"' : '')?>>Reds as Priority</option>
            <option value="orange" class="orange" <?=($priority=='orange' ? 'selected="selected"' : '')?>>Oranges as Priority</option>
            <option value="white" class="white" <?=($priority=='white' ? 'selected="selected"' : '')?>>Whites as Priority</option>
            <option value="green" class="green" <?=($priority=='green' ? 'selected="selected"' : '')?>>Greens as Priority</option>
        </select>
    </div>
</div>
<div class="profitlegend_row" >
    <div class="searchbox">
        <input name="searchtemplate" id="searchdbprofit" type="text" value="<?=$search?>" placeholder="Enter keyword or item #"/>
        <a class="find_it" id="dbprofitfind_it" href="javascript:void(0);">
            Search It
        </a>
        <a class="find_it" id="dbprofitclear_it" href="javascript:void(0);">
            Clear
        </a>
    </div>
    <div class="dbpages_vendors">
        <select  name="vendorselect" id="dbprofitvendorselect" class="vendorselect">
            <option value="">Select Vendor</option>
            <?php foreach ($vendors as $row) {?>
                <option value="<?=$row['vendor_id']?>" <?=($row['vendor_id']==$vendor ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
            <?php } ?>
        </select>
    </div>
    <div id="dbprofitPagination" style="float: left; width: 210px; margin-left: 188px; margin-top: 7px; "></div>
</div>
