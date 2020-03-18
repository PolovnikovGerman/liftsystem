<div class="imageuploaddata">
    <input type="hidden" id="newbannersrc" value="<?=$outstock_banner?>"/>
    <div class="viewpreloadbanner">
    <?php if ($outstock_banner!='') { ?>
        <img src="<?=$outstock_banner?>" id="outstock_bannersrc"/>
    <?php } ?>
    </div>
    <div class="manegebannerupload">
        <div id="uploadbannersrc"></div>
    </div>
    <div class="managelink">
        <div class="outstocklnklabel">URL</div>
        <div class="outstocklnkvalue">
            <input type="text" id="outstocklnk" value="<?=$outstock_link?>"/>
        </div>
    </div>
    <div class="savebannercontent">
        <img src="/img/itemdetails/save_order.png" />
    </div>
</div>