<div class="content-row">
    <div class="pagetitleadd">Create New Item:</div>
    <div class="canceladd"><span>Cancel</span><i class="fa fa-times"></i></div>
</div>
<div class="content-row">
    <div class="itemaddform">
        <div class="content-row">
            <div class="itemaddform-label">Category:</div>
            <div class="itemaddform-value">
                <select class="addnewitemoption" id="itemnewcategory">
                    <option value="">...</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?=$category['category_id']?>"><?=$category['category_code']?> - <?=$category['category_name']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="content-row">
            <div class="itemaddform-label">Subcategory:</div>
            <div class="itemaddform-value">
                <select class="addnewitemoption" id="itemnewsubcategory">
                    <option value=""></option>
                    <?php foreach ($subcategories as $subcategory) { ?>
                        <option value="<?=$subcategory['category_id']?>"><?=$subcategory['category_code']?> - <?=$subcategory['category_name']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="content-row">
            <div class="itemaddform-label">Item Name:</div>
            <div class="itemaddform-value">
                <input type="text" class="addnewiteminput" id="itemnewname"/>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="procedaddnewitem">Add Item</div>
    </div>
</div>