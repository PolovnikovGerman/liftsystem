<div class="content-row">
    <div class="pagetitleadd">Add New Subcategory:</div>
    <div class="cancelsubcateg"><span>Cancel</span><i class="fa fa-times"></i></div>
</div>
<div class="content-row">
    <div class="subcategaddform">
        <div class="content-row">
            <div class="subcategaddform-label">Category:</div>
            <div class="subcategaddform-value">
                <span><?=$category['category_code']?> - <?=$category['category_name']?></span>
            </div>
        </div>
        <div class="content-row">
            <div class="subcategaddform-label">Subcategory Code:</div>
            <div class="subcategaddform-value">
                <input type="text" class="addnewsubcategoryinput code" id="newsubcategcode"/>
            </div>
        </div>
        <div class="content-row">
            <div class="subcategaddform-label">Subcategory Name:</div>
            <div class="subcategaddform-value">
                <input type="text" class="addnewsubcategoryinput name" id="newsubcategname"/>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="procedaddnewsubcateg">Add Subcategory</div>
    </div>
</div>
