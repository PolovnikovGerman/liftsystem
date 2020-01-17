<input type="hidden" id="service_session" value="<?=$session?>"/>
<input type="hidden" id="service_previewurl" value="/content/prepare_service_preview?version=<?=$session?>"/>
<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="extraservicecontent-area">
    <div class="content-row">
        <div class="label service_maintitle">Main Title:</div>
        <input class="service_maintitle" data-content="content" data-field="service_maintitle" value="<?=$data['service_maintitle']?>"/>
    </div>
    <div class="content-row">
        <div class="label service_secondarytext">Secondary Text:</div>
        <input class="service_secondarytext" data-content="content" data-field="service_secondarytext" value="<?=$data['service_secondarytext']?>"/>
    </div>
    <div class="service_mainimagearea">
        <div class="content-row">
            <div class="label service_mainimage">Main Image:</div>
            <div class="service_imagesubtitle">click image to enlarge</div>
        </div>
        <div class="content-row">
            <div class="service_imagesubtitlesize">(1052px x 337px)</div>
        </div>
        <div class="content-row">
            <div id="service_mainimagearea">
                <?php if ($data['service_mainimage']) { ?>
                    <div class="service_mainimagesrc">
                        <img src="<?=$data['service_mainimage']?>" alt="Main Image"/>
                    </div>
                    <div class="remove_service_mainimage">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                <?php } else { ?>
                    <div class="service_mainimagesrcempty">
                        <div class="uploadservicemainimage" id="uploadservicemainimage"/>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label service_belowimagetext">Below Image Text:</div>
        <textarea class="service_belowimagetext" data-content="content" data-field="service_belowimagetext"><?=$data['service_belowimagetext']?></textarea>
    </div>
    <div class="content-row">
        <div class="label serviceslabel">SERVICES</div>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 1 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title1" value="<?=$data['service_title1']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 1 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_1" data-service="1">
            <?php if ($data['service_image1']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image1']?>" alt="Service 1"/>
                </div>
                <div class="remove_service_image" data-service="1">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image1" data-service="1" id="uploadserviceimage1"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text1"><?=$data['service_text1']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 2 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title2" value="<?=$data['service_title2']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 2 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_2" data-service="2">
            <?php if ($data['service_image2']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image2']?>" alt="Service 2"/>
                </div>
                <div class="remove_service_image" data-service="2">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image2" data-service="2" id="uploadserviceimage2"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text2"><?=$data['service_text2']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 3 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title3" value="<?=$data['service_title3']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 3 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_3" data-service="3">
            <?php if ($data['service_image3']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image3']?>" alt="Service 3"/>
                </div>
                <div class="remove_service_image" data-service="3">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image3" data-service="3" id="uploadserviceimage3"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text3"><?=$data['service_text3']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 4 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title4" value="<?=$data['service_title4']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 4 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_4" data-service="4">
            <?php if ($data['service_image4']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image4']?>" alt="Service 4"/>
                </div>
                <div class="remove_service_image" data-service="4">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image4" data-service="4" id="uploadserviceimage4"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text4"><?=$data['service_text4']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 5 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title5" value="<?=$data['service_title5']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 5 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_5" data-service="5">
            <?php if ($data['service_image5']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image5']?>" alt="Service 5"/>
                </div>
                <div class="remove_service_image" data-service="5">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image5" data-service="5" id="uploadserviceimage5"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text5"><?=$data['service_text5']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 6 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title6" value="<?=$data['service_title6']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 6 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_6" data-service="6">
            <?php if ($data['service_image6']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image6']?>" alt="Service 6"/>
                </div>
                <div class="remove_service_image" data-service="6">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image6" data-service="6" id="uploadserviceimage6"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text6"><?=$data['service_text6']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 7 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title7" value="<?=$data['service_title7']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 7 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_7" data-service="7">
            <?php if ($data['service_image7']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image7']?>" alt="Service 7"/>
                </div>
                <div class="remove_service_image" data-service="7">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image7" data-service="7" id="uploadserviceimage7"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text7"><?=$data['service_text7']?></textarea>
    </div>
    <div class="content-row">
        <div class="label service_title">Service 8 Title: </div>
        <input class="service_title" data-content="content" data-field="service_title8" value="<?=$data['service_title8']?>"/>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace">
            <div class="service_imagesize">(512px x 272px)</div>
            <div class="service_imageenlarge">click image to enlarge</div>
        </div>
        <div class="label service_text">Service 8 Text:</div>
    </div>
    <div class="content-row">
        <div class="service_serviceimageplace" data-place="service_8" data-service="8">
            <?php if ($data['service_image8']) { ?>
                <div class="service_imagesrc">
                    <img src="<?=$data['service_image8']?>" alt="Service 8"/>
                </div>
                <div class="remove_service_image" data-service="8">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="service_imagesrcempty">
                    <div class="uploadserviceimage" data-image="service_image8" data-service="8" id="uploadserviceimage8"></div>
                </div>
            <?php }?>
        </div>
        <textarea class="service_text" data-content="content" data-field="service_text8"><?=$data['service_text8']?></textarea>
    </div>
</div>