<div class="itemdetails-metadata">
    <div class="content-row">
        <div class="chapterlabel leftpart">Meta Data:</div>
    </div>
    <div class="content-row">
        <div class="metadata-label">URL:</div>
    </div>
    <div class="content-row">
        <div class="metadata-value">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$item['item_url']?></div>
            <?php } else { ?>
                <input type="text" class="itemlistdetailsinpt metadata" data-item="item_url" value="<?=$item['item_url']?>"/>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="metadata-label">Meta Title:</div>
    </div>
    <div class="content-row">
        <div class="metadata-value">
            <?php if ($editmode==0) { ?>
                <div class="viewparam"><?=$item['item_meta_title']?></div>
            <?php } else { ?>
                <input type="text" class="itemlistdetailsinpt metadata" data-item="item_meta_title" value="<?=$item['item_meta_title']?>"/>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="metadata-label">Meta Keywords:</div>
    </div>
    <div class="content-row">
        <div class="metadata-value">
            <?php if ($editmode==0) { ?>
                <div class="viewparam-multirow"><?=$item['item_metakeywords']?></div>
            <?php } else { ?>
                <textarea class="itemlistdetailsinpt metadata" data-item="item_metakeywords"><?=$item['item_metakeywords']?></textarea>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="metadata-label">Meta Description:</div>
    </div>
    <div class="content-row">
        <div class="metadata-value">
            <?php if ($editmode==0) { ?>
                <div class="viewparam-multirow"><?=$item['item_metadescription']?></div>
            <?php } else { ?>
                <textarea class="itemlistdetailsinpt metadata" data-item="item_metadescription"><?=$item['item_metadescription']?></textarea>
            <?php } ?>
        </div>
    </div>
</div>