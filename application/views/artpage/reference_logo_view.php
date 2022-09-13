<div class="reflogoupload_container">
    <div class="reflogoupload_data">
        <input type="hidden" id="newartid" value="<?=$artwork_id?>"/>
        <?php foreach ($attachs as $attach) { ?>
            <div class="referencelogo_row" style="width: 100%">
                <div class="refattachcheck">
                    <input type="checkbox" class="attachcurlogo" data-logoid="<?=$attach['email_attachment_id']?>" />
                </div>
                <div class="refattachlogoname"><?=$attach['email_attachment_name']?></div>
                <div class="refattachview" data-link="<?=$attach['email_attachment_filename']?>">
                    <i class="fa fa-search"></i>
                </div>
            </div>
        <?php } ?>
        <div class="clear"></div>
        <div class="reflogouploadsave_data">
            <img src="/img/artpage/saveticket.png"/>
        </div>
    </div>
</div>