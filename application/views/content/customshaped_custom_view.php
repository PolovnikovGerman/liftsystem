<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_maintitle">Main Title:</div>
        <input class="custom_maintitle" name="custom_maintitle" readonly="readonly" value="<?=ifset($data,'custom_maintitle','')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_secondarytext">Secondary Text:</div>
        <input class="custom_secondarytext" name="custom_secondarytext" readonly="readonly" value="<?=ifset($data,'custom_secondarytext','')?>"/>
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
            <div class="custom_mainimagesrc">
                <?php if (!empty(ifset($data,'custom_mainimage',''))) { ?>
                    <img src="<?=$data['custom_mainimage']?>" alt="Main Image"/>
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
            <div class="custom_homepageimagesrc">
                <?php if (!empty(ifset($data,'custom_homepageimage'))) { ?>
                    <img src="<?=$data['custom_homepageimage']?>" alt="Main Image"/>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label custom_belowimagetext">Below Image Text:</div>
        <input class="custom_belowimagetext" name="custom_belowimagetext" readonly="readonly" value="<?=ifset($data,'custom_belowimagetext')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_fivebulletpoints">5 Bullet Points:</div>
        <div class="custom_fivebulletpoints_area">
            <input class="custom_fivebulletpoints" name="custom_bulletpoint1" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint1')?>"/>
            <input class="custom_fivebulletpoints" name="custom_bulletpoint2" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint2')?>"/>
            <input class="custom_fivebulletpoints" name="custom_bulletpoint3" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint3')?>"/>
            <input class="custom_fivebulletpoints" name="custom_bulletpoint4" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint4')?>"/>
            <input class="custom_fivebulletpoints" name="custom_bulletpoint5" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint5')?>"/>
        </div>
    </div>
    <div class="content-row">
        <div class="label custom_longerbodytext">Longer Body Text:</div>
        <textarea class="custom_longerbodytext" name="custom_longerbodytext" readonly="readonly"><?=ifset($data,'custom_longerbodytext')?></textarea>
    </div>
    <div class="content-row">
        <div class="label custom_abovegallerytext">Above Gallery Text:</div>
        <input class="custom_abovegallerytext" name="custom_abovegallerytext" readonly="readonly" value="<?=ifset($data,'custom_abovegallerytext')?>">
    </div>
    <div class="content-row">
        <div class="label custom_gallerytitle">Gallery Title:</div>
        <input class="custom_gallerytitle" name="custom_gallerytitle" readonly="readonly" value="<?=ifset($data,'custom_gallerytitle')?>"/>
    </div>
</div>
<?=$gallery_view?>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_belowgallerrytext">Below Gallery Text:</div>
        <input class="custom_belowgallerrytext" readonly="readonly" name="custom_belowgallerrytext" value="<?=ifset($data,'custom_belowgallerrytext')?>"/>
    </div>
    <div class="content-row">
        <input class="custom_belowgallerrysubtext" readonly="readonly" name="custom_belowgallerrysubtext" value="<?=ifset($data,'custom_belowgallerrysubtext')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_stepprocesstitle">5 Step Process Title:</div>
        <input class="custom_stepprocesstitle" readonly="readonly" name="custom_stepprocesstitle" value="<?=ifset($data,'custom_stepprocesstitle')?>"/>
    </div>
    <div class="content-row">
        <div class="label custom_belowstepstext">Below Steps Text:</div>
        <textarea class="custom_belowstepstext" readonly="readonly" name="custom_belowstepstext"><?=ifset($data,'custom_belowstepstext')?></textarea>
    </div>
</div>
<?=$casestudy_view?>