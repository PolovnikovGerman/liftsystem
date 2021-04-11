<div class="itemdetails-intersearchdata">
    <div class="content-row">
        <div class="chapterlabel leftpart">Internal Search &amp; Browsing:</div>
    </div>
    <div class="content-row">
        <div class="intersearch-label">Internal Keywords:</div>
    </div>
    <div class="content-row">
        <div class="intersearch-value">
            <?php if ($editmode==0) { ?>
                <div class="viewparam-multirow"><?=$item['item_keywords']?></div>
            <?php } else { ?>
                <textarea class="itemlistdetailsinpt" data-item="item_keywords"><?=$item['item_keywords']?></textarea>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="intersearch-label">Similar Items:</div>
    </div>
    <div class="content-row">
        <div class="intersearch-value">
        <?php foreach ($similar as $row) { ?>
            <?php if ($editmode==0) { ?>
                <?php if (!empty($row['item_similar_similar'])) { ?>
                    <div class="simulardataview"><?=$row['item_number']?></div>
                <?php } ?>
            <?php } else { ?>
                <select class="simulardataselect" data-item="<?=$row['item_similar_id']?>">
                    <option value="">Select</option>
                    <?php foreach ($items as $itemrow) { ?>
                        <option value="<?=$itemrow['item_id']?>"><?=$itemrow['item_name']?></option>
                    <?php } ?>
                </select>
            <?php } ?>
        <?php } ?>
        </div>
    </div>
</div>