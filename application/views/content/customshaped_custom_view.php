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
        <div class="label custom_secondarytext">Secondary Text:</div>
        <input class="custom_secondarytext" name="custom_secondarytext" readonly="readonly" value="<?=ifset($data,'custom_secondarytext','')?>"/>
    </div>
    <div class="custom_mainimagearea">
        <div class="content-row">
            <div class="label custom_mainimage">Main Image:</div>
            <div class="custom_imagesubtitle">(1140px х 461px)</div>
        </div>
        <div class="content-row">
            <div class="custom_imagesubtitlesize">click to enlarge</div>
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
            <div class="custom_imagesubtitle">(500px x 516px)</div>
        </div>
        <div class="content-row">
            <div class="custom_imagesubtitlesize">click to enlarge</div>
            <div class="custom_homepageimagesrc">
                <?php if (!empty(ifset($data,'custom_homepageimage'))) { ?>
                    <img src="<?=$data['custom_homepageimage']?>" alt="Main Image"/>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="custompage_content_left">
        <div class="content-row">
            <div class="label custom_belowimagetext">Text Below Image:</div>
            <input class="custom_belowimagetext" name="custom_belowimagetext" readonly="readonly" value="<?=ifset($data,'custom_belowimagetext')?>"/>
        </div>
        <div class="content-row">
            <div class="label custom_fivebulletpoints">Bullet Points:</div>
            <div class="custom_fivebulletpoints_area">
                <input class="custom_fivebulletpoints" name="custom_bulletpoint1" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint1')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint3" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint3')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint5" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint5')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint7" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint7')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint9" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint9')?>"/>
            </div>
            <div class="custom_fivebulletpoints_area">
                <input class="custom_fivebulletpoints" name="custom_bulletpoint2" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint2')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint4" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint4')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint6" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint6')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint8" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint8')?>"/>
                <input class="custom_fivebulletpoints" name="custom_bulletpoint10" readonly="readonly" value="<?=ifset($data,'custom_bulletpoint10')?>"/>
            </div>
        </div>
    </div>
    <div class="custompage_content_right">
        <div class="content-row">
            <div class="label custom_longerbodytext">Longer Body Text:</div>
        </div>
        <div class="content-row">
            <textarea class="custom_longerbodytext" name="custom_longerbodytext" readonly="readonly"><?=ifset($data,'custom_longerbodytext')?></textarea>
        </div>
    </div>
</div>
<div class="galleryinfotitle">
    <div class="displaygallery show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">EXAMPLE TYPES:</div>
</div>
<div class="customcontent-area">
    <div class="content-row">
        <div class="label custom_commongallerytitle">Example Types Title:</div>
        <input class="custom_commongallerytitle" name="custom_gallerytitle" readonly="readonly" value="<?=ifset($data,'custom_gallerytitle')?>"/>
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
</div>
<div class="galleryitemsinfotitle">
    <div class="displaygalleryitems show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">STRESSBALLS GALLERY:</div>
</div>
<div class="custom_galleryitems_area">
    <div class="content-row">
        <div class="custom_imagesubtitle">click image to enlarge (900px x 900px)</div>
    </div>
    <?=$galleryitems_view?>
</div>
<?=$casestudy_view?>