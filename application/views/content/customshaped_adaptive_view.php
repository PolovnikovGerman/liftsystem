<div class="row">
    <div class="col-12">
        <div class="contentinfotitle">
            <div class="displaycontent show">
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="title">Content</div>
        </div>
    </div>
</div>
<div class="customcontent-area">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <div class="label custom_maintitle">Main Title:</div>
            <input class="custom_maintitle" name="custom_maintitle" readonly="readonly" value="<?=ifset($data,'custom_maintitle','')?>"/>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <div class="label custom_secondarytext">Sub Title:</div>
            <input class="custom_secondarytext" name="custom_secondarytext" readonly="readonly" value="<?=ifset($data,'custom_secondarytext','')?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-7 col-xl-7">
            <div class="custom_mainimagearea">
                <div class="row">
                    <div class="col-6">
                        <div class="label custom_mainimage">Main Image:</div>
                    </div>
                    <div class="col-6">
                        <div class="custom_imagesubtitle">(1140px х 461px)</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="custom_imagesubtitlesize">click to enlarge</div>
                    </div>
                    <div class="col-12">
                        <div class="custom_mainimagesrc">
                            <?php if (!empty(ifset($data,'custom_mainimage',''))) { ?>
                                <img src="<?=$data['custom_mainimage']?>" alt="Main Image"/>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-5 col-xl-5">
            <div class="custom_homeimagearea">
                <div class="row">
                    <div class="col-6">
                        <div class="label custom_homepageimage">Image 2:</div>
                    </div>
                    <div class="col-6">
                        <div class="custom_imagesubtitle">(500px x 516px)</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="custom_imagesubtitlesize">click to enlarge</div>
                    </div>
                    <div class="col-12">
                        <div class="custom_homepageimagesrc">
                            <?php if (!empty(ifset($data,'custom_homepageimage'))) { ?>
                                <img src="<?=$data['custom_homepageimage']?>" alt="Main Image"/>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">&nbsp;</div>
    </div>
</div>