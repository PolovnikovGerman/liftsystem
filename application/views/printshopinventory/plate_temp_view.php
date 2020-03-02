<div class="platetempupload_container">
    <div class="platetempupload_data">
        <input type="hidden" id="uploadfiles" value="0"/>
        <?php if (isset($uplsess)) { ?>
            <input type="hidden" id="uploadsession" value="<?=$uplsess?>"/>
            <input type="hidden" id="uploadprintitemtype" value="<?=$uploadtype?>"
        <?php } ?>
        <div id="orderattachlists" class="platetempuploads">
            <div class="platetempfiledat">
                <input type="hidden" id="filename" value="<?=$filename?>"/>
                <div class="platetempfilename"><?=$filename?></div>
            </div>
        </div>
        <div class="delplatefile">
            <img src="/img/fulfillment/cancel.png" alt="delete"/>
        </div>
        <div class="clear"></div>
        <div id="file-uploader"></div>
        <div class="platetempsave_data">
            <img src="/img/fulfillment/saveticket.png"/>
        </div>
    </div>
</div>