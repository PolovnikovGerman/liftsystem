<input type="hidden" class="itemsperpage" data-brand="<?=$brand?>" value="<?=$perpage?>"/>
<input type="hidden" class="itemsorder" data-brand="<?=$brand?>" value="<?=$order?>"/>
<input type="hidden" class="itemsorderdirect" data-brand="<?=$brand?>" value="<?=$direct?>"/>
<input type="hidden" class="itemstotals" data-brand="<?=$brand?>" value="<?=$totals?>"/>
<input type="hidden" class="itemspagenum" data-brand="<?=$brand?>" value="0"/>
<div class="itemlistview" data-brand="<?=$brand?>">
    <div class="content-row">
        <div class="itemlisttitle">Item Database</div>
        <div class="itemlistsubtitle"><?=$brand=='SB' ? '(Stressballs.com)' : '(Bluetrack.com)' ?></div>
        <div class="itemlistsearchinpt">
            <input class="search_input" type="text" data-brand="<?=$brand?>" placeholder="Enter keyword or item #"/>
            <div class="searchlist-btn" data-brand="<?=$brand?>"><img src="/img/database/search_items_btn.png"></div>
            <div class="clearsearchlist-btn" data-brand="<?=$brand?>"><img src="/img/database/clear_searchitems_btn.png"></div>
        </div>
        <div class="itemlistfilterarea">
            <div class="content-row">
                <div class="itemlistfilter-label">Display:</div>
                <div class="itemlistfilter-input">
                    <select class="vendorfilter" data-brand="<?=$brand?>">
                        <option value="">All Vendors</option>
                        <?php foreach ($vendors as $vendor) { ?>
                            <option value="<?=$vendor['vendor_id']?>"><?=$vendor['vendor_name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemlistfilter-label">Display: </div>
                <div class="itemlistfilter-input">
                    <select class="itemlistatusfilter" data-brand="<?=$brand?>">
                        <option value="0">Active &amp; Inactive</option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                </div>
                <div class="categorymanagebtn locked" data-brand="<?=$brand?>">Categories Locked</div> <!-- Unlocked -->
            </div>
            <div class="content-row">
                <div class="itemlistpagination" data-brand="<?=$brand?>"></div>
                <div class="itemslisttotalsview"><?=QTYOutput($totals)?> Records</div>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="itemlist-tablehead" data-brand="<?=$brand?>">
            <div class="listaction"><img src="/img/database/add_itemlist_btn.png"></div>
            <div class="listnumrow">&nbsp;</div>
            <div class="liststatus">Active</div>
            <div class="listitemnumber sortable active">Item # <div class="ascsort">&nbsp;</div> </div>
            <div class="listitemname sortable">Item Name</div>
            <div class="listvendor">Supplier</div>
            <div class="listcategoryname">Category 1</div>
            <div class="listcategoryname">Category 2</div>
            <div class="listcategoryname">Category 3</div>
            <div class="listmissinginfo">Missing</div>
        </div>
    </div>
    <div class="content-row">
        <div class="itemlist-tablebody" data-brand="<?=$brand?>"></div>
    </div>
</div>
