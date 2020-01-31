<div class="vectorupload_container">
    <div class="vectorupload_data">
        <input type="hidden" id="newartid" value="<?=$artwork_id?>"/>
        <?php if (isset($title)) { ?>
            <div class="vectorupload_title"><?=$title?></div>
        <?php } ?>
        <div class="vectoruploads">
            <textarea class="artworkusertext"><?=$usrtxt?></textarea>
        </div>
        <div class="vectorsave_data">
            <img src="/img/artpage/saveticket.png"/>
        </div>
    </div>
</div>