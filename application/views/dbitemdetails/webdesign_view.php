<div class="itemdetails-webdesign">
    <div class="chapterlabel leftpart">Website Design:</div>
    <div class="content-row">
        <div class="tempalte_label">Template:</div>
        <div class="template_design">
            <?php if ($editmode==0) { ?>
                <div class="viewdesignvalue">Design 1</div>
            <?php } else { ?>
                <select class="itemlistdetailsselect webdesign" data-item="item_webteplate" <?=$editmode==0 ? 'disabled' : ''?>>
                    <option  value="design1">Design1</option>
                </select>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="tempalte_label">Template:</div>
        <div class="template-checkbox" data-item="item_sale">
            <?php if ($item['item_sale']==0) { ?>
                <i class="fa fa-square-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-square" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="template-checkbox-label">Top Seller</div>
        <div class="template-checkbox" data-item="item_new">
            <?php if ($item['item_new']==0) { ?>
                <i class="fa fa-square-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-square" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="template-checkbox-label">New</div>
    </div>

</div>
