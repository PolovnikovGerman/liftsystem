<input type="hidden" id="custom_session" value="<?=$session?>"/>
<input type="hidden" id="custom_previewurl" value="/contents/prepare_customshaped_preview?version=<?=$session?>"/>
<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_maintitle">Main Title:</div>
        <input class="custom_maintitle" data-content="content" data-field="custom_maintitle" value="<?=$data['custom_maintitle']?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_secondarytext">Secondary Text:</div>
        <input class="custom_secondarytext" data-content="content" data-field="custom_secondarytext" value="<?=$data['custom_secondarytext']?>"/>
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
                <div class="custom_mainimagesrc">
                    <img src="<?=$data['custom_mainimage']?>" alt="Main Image"/>
                    <div class="custom_mainimageremove">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                </div>
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
                <div class="custom_homepageimagesrc">
                    <img src="<?=$data['custom_homepageimage']?>" alt="Main Image"/>
                    <div class="custom_homeimageremove">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label custom_belowimagetext">Below Image Text:</div>
        <input class="custom_belowimagetext" data-content="content" data-field="custom_belowimagetext" value="<?=$data['custom_belowimagetext']?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_fivebulletpoints">5 Bullet Points:</div>
        <div class="custom_fivebulletpoints_area">
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint1" value="<?=$data['custom_bulletpoint1']?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint2" value="<?=$data['custom_bulletpoint2']?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint3" value="<?=$data['custom_bulletpoint3']?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint4" value="<?=$data['custom_bulletpoint4']?>"/>
            <input class="custom_fivebulletpoints" data-content="content" data-field="custom_bulletpoint5" value="<?=$data['custom_bulletpoint5']?>"/>
        </div>
    </div>
    <div class="content-row">
        <div class="label custom_longerbodytext">Longer Body Text:</div>
        <textarea class="custom_longerbodytext" data-content="content" data-field="custom_longerbodytext"><?=$data['custom_longerbodytext']?></textarea>
    </div>
    <div class="content-row">
        <div class="label custom_abovegallerytext">Above Gallery Text:</div>
        <input class="custom_abovegallerytext" data-content="content" data-field="custom_abovegallerytext" value="<?=$data['custom_abovegallerytext']?>">
    </div>
    <div class="content-row">
        <div class="label custom_gallerytitle">Gallery Title:</div>
        <input class="custom_gallerytitle" data-content="content" data-field="custom_gallerytitle" value="<?=$data['custom_gallerytitle']?>"/>
    </div>
</div>
<?=$gallery_view?>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_belowgallerrytext">Below Gallery Text:</div>
        <input class="custom_belowgallerrytext" data-content="content" data-field="custom_belowgallerrytext" value="<?=$data['custom_belowgallerrytext']?>"/>
    </div>
    <div class="content-row">
        <input class="custom_belowgallerrysubtext" data-content="content" data-field="custom_belowgallerrysubtext" value="<?=$data['custom_belowgallerrysubtext']?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_stepprocesstitle">5 Step Process Title:</div>
        <input class="custom_stepprocesstitle" data-content="content" data-field="custom_stepprocesstitle" value="<?=$data['custom_stepprocesstitle']?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_belowstepstext">Below Steps Text:</div>
        <textarea class="custom_belowstepstext" data-content="content" data-field="custom_belowstepstext"><?=$data['custom_belowstepstext']?></textarea>
    </div>
</div>
<?=$casestudy_view?>
