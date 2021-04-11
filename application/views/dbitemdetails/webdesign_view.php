<div class="itemdetails-webdesign">
    <div class="content-row">
        <div class="chapterlabel leftpart">Website Design:</div>
    </div>
    <div class="content-row">
        <div class="tempalte_label">Template:</div>
        <div class="template_design">
            <?php if ($editmode==0) { ?>
                <div class="viewdesignvalue">Design 1</div>
            <?php } else { ?>
                <select class="itemlistdetailsselect" data-item="item_webteplate" <?=$editmode==0 ? 'disabled' : ''?>>
                    <option  value="design1">Design1</option>
                </select>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="tempalte_label">Template:</div>
        <div class="template-checkbox">
            <i class="fa fa-square" aria-hidden="true"></i>
        </div>
        <div class="template-checkbox-label">Top Seller</div>
        <div class="template-checkbox">
            <i class="fa fa-square-o" aria-hidden="true"></i>
        </div>
        <div class="template-checkbox-label">New</div>
    </div>

</div>
