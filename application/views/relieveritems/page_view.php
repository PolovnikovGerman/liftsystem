<div class="reliversitemdataview">
    <div class="pageheader">
        <div class="pagetitle">Item Center</div>
        <div class="pageheadfilter">
            <select class="itemcategoryfilter">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?=$category['category_id']?>"><?=$category['category_name']?></option>
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
        <?php foreach ($categories as $category) { ?>
            <div class="relivecategorybtn" data-category="<?=$category['category_id']?>">
                <?=$category['category_code']?> - <?=$category['category_name']?>
            </div>
        <?php } ?>
    </div>
    <div class="datatablearea">
        <div class="tabledatatotals">
            <div class="tabledatatitle">i - Stock Shapes</div>
            <div class="tabledatataotalvalue">89 items</div>
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
            </select>
        </div>
        <div class="tabledatafilter">
            <select class="itemvendorfilter">
                <option value="">Complete & Not</option>
                <option value="1">Complete</option>
                <option value="2">Not Complete</option>
            </select>
        </div>
        <div class="tabledataexecute">
            <div class="tabledataexport">
                <i class="fa fa-share-square-o" aria-hidden="true"></i>
                <span>Export Item List</span>
            </div>
            <div class="tabledatapaginator"></div>
        </div>
        <div class="tabledataheader">
            <div class="numberpp">
                <img src="/img/masterinvent/addinvitem_bg.png" alt="Add New"/>
            </div>
            <div class="status">Active</div>
            <div class="edit">Edit</div>
            <div class="itemnumber">Item #</div>
            <div class="itemname">Item Name</div>
            <div class="missinfo">Complete or Missing Info</div>
        </div>
        <div class="tabledataarea"></div>
    </div>
</div>