<input type="hidden" id="btitemsperpage" value="<?=$perpage?>"/>
<input type="hidden" id="btitemsorder" value="<?=$order?>"/>
<input type="hidden" id="btitemsorderdirect" value="<?=$direct?>"/>
<input type="hidden" id="btitemstotals" value="<?=$totals?>"/>
<input type="hidden" id="btitemspagenum" value="0"/>
<div class="itemlistview" data-brand="<?=$brand?>">
    <div class="pageheader">
        <div class="pagetitle">Item Center</div>
        <div class="pageheadfilter">
            <select class="itemcategoryfilter">
                <?php foreach ($categories as $category) { ?>
                    <option data-categ="<?=$category['category_id']?>" <?=$category['category_active']==1 ? '' : 'disabled="true"'?> value="<?=$category['category_id']?>" <?=$category['category_id']==$category_id ? 'selected="selected"' : ''?>>
                        <?=$category['category_code'].' - '.$category['category_name']?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="pageheadfilter">
            <input class="itemnamesearch" placeholder="Search"/>
            <div class="itemsearchbtn">
                <i class="fa fa-search" aria-hidden="true"></i>
            </div>
        </div>
        <div class="itemclearsearch">Clear</div>
    </div>
    <div class="pageheadcategories">
        <?php $numpp=0;?>
        <?php $numsep=0;?>
        <?php foreach ($categories as $category) { ?>
            <?php if ($numpp%10==0) { ?>
                <div class="content-row">
            <?php } ?>
            <div class="btcategorybtn <?=$category['category_id']==$category_id ? 'active' : ''?> <?=$category['category_active']==0 ? 'locked' : ''?> <?=$category['category_separate']==1 ? ($numsep==0 ? 'separatefirst' : 'separate') : ''?>"
                 data-category="<?=$category['category_id']?>">
                <?=$category['category_code']?> - <?=$category['category_name']?>
            </div>
            <?php $numpp++;?>
            <?php if ($category['category_separate']==1) $numsep++; ?>
            <?php if ($numpp%10==0) { ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if ($numpp%10!=0) { ?>
            </div>
        <?php } ?>
    </div>
    <div class="content-row">
        <div class="totalitems"><?=number_format($brandtotal,0)?> items</div>
    </div>
    <div class="datatablearea">
        <div class="tabledatatotals">
            <div class="tabledatatitle"><?=$category_label?></div>
            <div class="tabledatataotalvalue"><?=$totals==0 ? '' : QTYOutput($totals).' items'?></div>
        </div>
        <div class="tabledatafilter">
            <select class="itemstatusfilter">
                <option value="0">Active & Inactive</option>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>
        </div>
        <div class="tabledatafilter">
            <select class="itemvendorfilter">
                <option value="">All Suppliers</option>
                <?php foreach ($vendors as $vendor) { ?>
                    <option value="<?=$vendor['vendor_id']?>"><?=$vendor['vendor_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="tabledatafilter">
            <select class="itemmisinfofilter">
                <option value="0">Complete & Not</option>
                <option value="1">Complete</option>
                <option value="2">Not Complete</option>
            </select>
        </div>
        <div class="tabledataexecute">
            <div class="tabledataexport">
                <i class="fa fa-share-square-o" aria-hidden="true"></i>
                <span>Export Item List</span>
            </div>
            <div class="tabledatapaginator" id="btitemsPaginator"></div>
        </div>
        <div class="tabledataheader">
            <div class="numberpp" id="addnewbtitems">
                <img src="/img/masterinvent/addinvitem_bg.png" alt="Add New"/>
            </div>
            <div class="status sortable" data-sortcell="item_active">Active</div>
            <div class="edit">Edit</div>
            <div class="subcategory sortable" data-sortcell="category">Subcategory</div>
<!--            <div class="subcategory">Subcategory</div>-->
            <div class="itemnumber sortable" data-sortcell="item_number">Item # <div class="ascsort">&nbsp;</div></div>
            <div class="itemname sortable" data-sortcell="item_name">Item Name</div>
            <div class="suplier sortable" data-sortcell="vendor">Supplier</div>
            <div class="missinfo">Complete or Missing Info</div>
        </div>
        <div class="btitemnewaddarea"></div>
        <div class="btitemnewsucategarea"></div>
        <div class="" id="btitemdata">
        </div>
    </div>
</div>