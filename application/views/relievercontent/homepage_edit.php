<input type="hidden" id="homepage_session" value="<?=$session?>"/>
<input type="hidden" id="homepage_previewurl" value="/content/prepare_contactus_preview?version=<?=$session?>"/>
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
            <div id="homepage_slider1">
                <?php if (empty(ifset($data,'slider_image_1'))) { ?>
                    <div class="slider_emptyimage">
                        <div class="slider_imageupload" id="sliderupload1"></div>
                    </div>
                <?php } else { ?>
                    <div class="homepage_imagesrc">
                        <img src="<?=$data['slider_image_1']?>" alt="Slider Image 1"/>
                    </div>
                    <div class="slider_imageremove" data-slider="1">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div id="homepage_slider2">
                <?php if (empty(ifset($data,'slider_image_2'))) { ?>
                    <div class="slider_emptyimage">
                        <div class="slider_imageupload" id="sliderupload2"></div>
                    </div>
                <?php } else { ?>
                    <div class="homepage_imagesrc">
                        <img src="<?=$data['slider_image_2']?>" alt="Slider Image 2"/>
                    </div>
                    <div class="slider_imageremove" data-slider="2">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div id="homepage_slider3">
                <?php if (empty(ifset($data,'slider_image_3'))) { ?>
                    <div class="slider_emptyimage">
                        <div class="slider_imageupload" id="sliderupload3"></div>
                    </div>
                <?php } else { ?>
                    <div class="homepage_imagesrc">
                        <img src="<?=$data['slider_image_3']?>" alt="Slider Image 3"/>
                    </div>
                    <div class="slider_imageremove" data-slider="3">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="homepage_imagesrc_area">
            <div id="homepage_slider4">
                <?php if (empty(ifset($data,'slider_image_4'))) { ?>
                    <div class="slider_emptyimage">
                        <div class="slider_imageupload" id="sliderupload4"></div>
                    </div>
                <?php } else { ?>
                    <div class="homepage_imagesrc">
                        <img src="<?=$data['slider_image_4']?>" alt="Slider Image 4"/>
                    </div>
                    <div class="slider_imageremove" data-slider="4">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label home_underslidertext">Text Under Slider:</div>
        <input class="home_underslidertext" data-content="content" data-field="text_under_slider" value="<?=ifset($data,'text_under_slider')?>"/>
        <div class="label home_articletitletext">Article Title:</div>
        <input class="home_articletitletext" data-content="content" data-field="article_title" value="<?=ifset($data,'article_title')?>"/>
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
        <input class="feature_prodtitle" data-content="content" data-field="featured_products_title" value="<?=ifset($data,'featured_products_title')?>"/>
    </div>
</div>
