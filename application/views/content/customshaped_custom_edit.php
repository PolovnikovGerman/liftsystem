<input type="hidden" id="custom_session" value="<?=$session?>"/>
<input type="hidden" id="custom_previewurl" value="/content/prepare_customshaped_preview?version=<?=$session?>"/>
<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_maintitle">Main Title:</div>
        <input class="custom_maintitle" data-content="content" data-field="custom_maintitle" value="<?=ifset($data,'custom_maintitle')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_secondarytext">Secondary Text:</div>
        <input class="custom_secondarytext" data-content="content" data-field="custom_secondarytext" value="<?=ifset($data,'custom_secondarytext')?>"/>
    </div>
    <div class="custom_mainimagearea">
        <div class="content-row">
            <div class="label custom_mainimage">Main Image:</div>
            <div class="custom_imagesubtitle">click image to enlarge</div>
        </div>
        <div class="content-row">
            <div class="custom_imagesubtitlesize">(1100px x 460px)</div>
        </div>
        <div class="content-row">
            <div id="custom_mainimagearea">
                <?php if (empty(ifset($data,'custom_mainimage'))) { ?>
                    <div class="custom_mainimageempty"><div class="custom_mainimageupload" id="mainimageupload"></div></div>
                <?php } else { ?>
                    <div class="custom_mainimagesrc">
                        <img src="<?=$data['custom_mainimage']?>" alt="Main Image"/>
                        <div class="custom_mainimageremove">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="custom_homeimagearea">
        <div class="content-row">
            <div class="label custom_homepageimage">Homepage Collage Image:</div>
            <div class="custom_imagesubtitle">click image to enlarge</div>
        </div>
        <div class="content-row">
            <div class="custom_imagesubtitlesize">(728px x 226px)</div>
        </div>
        <div class="content-row">
            <div id="custom_homepageimagearea">
                <?php if (empty(ifset($data,'custom_homepageimage'))) { ?>
                    <div class="custom_homepageimageempty">
                        <div class="custom_mainimageupload" id="homepageimageupload"></div>
                    </div>
                <?php } else { ?>
                    <div class="custom_homepageimagesrc">
                        <img src="<?=$data['custom_homepageimage']?>" alt="Main Image"/>
                        <div class="custom_homeimageremove">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label custom_belowimagetext">Below Image Text:</div>
        <input class="custom_belowimagetext" data-content="content" data-field="custom_belowimagetext" value="<?=ifset($data,'custom_belowimagetext')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_fivebulletpoints">5 Bullet Points:</div>
        <div class="custom_fivebulletpoints_area">
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint1" value="<?=ifset($data,'custom_bulletpoint1')?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint2" value="<?=ifset($data,'custom_bulletpoint2')?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint3" value="<?=ifset($data,'custom_bulletpoint3')?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint4" value="<?=ifset($data,'custom_bulletpoint4')?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint5" value="<?=ifset($data,'custom_bulletpoint5')?>"/>
        </div>
    </div>
    <div class="content-row">
        <div class="label custom_longerbodytext">Longer Body Text:</div>
        <textarea class="custom_longerbodytext" data-content="content" data-field="custom_longerbodytext"><?=ifset($data,'custom_longerbodytext')?></textarea>
    </div>
    <div class="content-row">
        <div class="label custom_abovegallerytext">Above Gallery Text:</div>
        <input class="custom_abovegallerytext" data-content="content" data-field="custom_abovegallerytext" value="<?=ifset($data,'custom_abovegallerytext')?>">
    </div>
    <div class="content-row">
        <div class="label custom_gallerytitle">Gallery Title:</div>
        <input class="custom_gallerytitle" data-content="content" data-field="custom_gallerytitle" value="<?=ifset($data,'custom_gallerytitle')?>"/>
    </div>
</div>
<?=$gallery_view?>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_belowgallerrytext">Below Gallery Text:</div>
        <input class="custom_belowgallerrytext" data-content="content" data-field="custom_belowgallerrytext" value="<?=ifset($data,'custom_belowgallerrytext')?>"/>
    </div>
    <div class="content-row">
        <input class="custom_belowgallerrysubtext" data-content="content" data-field="custom_belowgallerrysubtext" value="<?=ifset($data,'custom_belowgallerrysubtext')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_stepprocesstitle">5 Step Process Title:</div>
        <input class="custom_stepprocesstitle" data-content="content" data-field="custom_stepprocesstitle" value="<?=ifset($data,'custom_stepprocesstitle')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_belowstepstext">Below Steps Text:</div>
        <textarea class="custom_belowstepstext" data-content="content" data-field="custom_belowstepstext"><?=ifset($data,'custom_belowstepstext')?></textarea>
    </div>
</div>
<?=$casestudy_view?>
