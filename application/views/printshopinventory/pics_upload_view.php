<div class="picsupload_container">
    <div class="picsupload_data">
        <input type="hidden" id="printshop_color_id" value="<?=$printshop_color_id?>"/>
        <input type="hidden" id="uploadfiles" value="0"/>
        <?php if (isset($uplsess)) { ?>
            <input type="hidden" id="uploadsession" value="<?=$uplsess?>"/>
        <?php } ?>
        <div class="picsupload_title">Upload Pics</div>
        <div id="orderattachlists" class="picsuploads">
            <?=$html?>
        </div>
        <div class="clear"></div>
        <div id="file-uploader"></div>
        <div class="picssave_data">
            <img src="/img/fulfillment/saveticket.png"/>
        </div>
    </div>
</div>