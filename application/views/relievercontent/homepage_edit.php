<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="homepagecontent-area">
    <div class="content-row">
        <div class="label sliderimages">Slider Images:</div>
        <div class="homepage_imagesubtitle">(1245px х 442px)</div>
    </div>
    <div class="content-row sliderimagesarea">
        <div class="homepage_imagesubtitlesize">click to enlarge</div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'slider_image_1'))) { ?>
                    <img src="<?=$data['slider_image_1']?>" alt="Slider Image 1"/>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'slider_image_2'))) { ?>
                    <img src="<?=$data['slider_image_2']?>" alt="Slider Image 2"/>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'slider_image_3'))) { ?>
                    <img src="<?=$data['slider_image_3']?>" alt="Slider Image 3"/>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'slider_image_4'))) { ?>
                    <img src="<?=$data['slider_image_4']?>" alt="Slider Image 4"/>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label home_underslidertext">Text Under Slider:</div>
        <input class="home_underslidertext" name="custom_belowimagetext" readonly="readonly" value="<?=ifset($data,'text_under_slider')?>"/>
        <div class="label home_articletitletext">Article Title:</div>
        <input class="home_articletitletext" name="custom_belowimagetext" readonly="readonly" value="<?=ifset($data,'article_title')?>"/>
    </div>
    <div class="content-row">
        <div class="label linkimages">Link Images:</div>
        <div class="homepage_imagesubtitle">(1244px х 365px)</div>
    </div>
    <div class="content-row">
        <div class="homepage_imagesrc_area">
            <div class="homepage_liknimage">Customize a STOCK SHAPE</div>
            <div class="homepage_imagesubtitlesize">click to enlarge</div>
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'customize_shape_image'))) { ?>
                    <img src="<?=$data['customize_shape_image']?>" alt="Customize a Stock Shape"/>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_liknimage">Make Your Own CUSTOM SHAPE</div>
            <div class="homepage_imagesubtitlesize">click to enlarge</div>
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'makeyourown_shape_image'))) { ?>
                    <img src="<?=$data['makeyourown_shape_image']?>" alt="Make your own Shape"/>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_liknimage">Explore Other HEALTH ITEMS</div>
            <div class="homepage_imagesubtitlesize">click to enlarge</div>
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'explore_healthitems_image'))) { ?>
                    <img src="<?=$data['explore_healthitems_image']?>" alt="Explore Health Items"/>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div class="homepage_liknimage">Explore Other HEALTH ITEMS</div>
            <div class="homepage_imagesubtitlesize">click to enlarge</div>
            <div class="homepage_imagesrc">
                <?php if (!empty(ifset($data,'custom_packaging_image'))) { ?>
                    <img src="<?=$data['custom_packaging_image']?>" alt="Custom Packaging"/>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label feature_prodtitle">Featured Product Title:</div>
        <input class="feature_prodtitle" name="featured_products_title" readonly="readonly" value="<?=ifset($data,'featured_products_title')?>"/>
    </div>
</div>
