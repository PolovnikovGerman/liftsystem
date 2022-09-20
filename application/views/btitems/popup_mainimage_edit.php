<div class="mainimage_area">
    <div class="content-row">
        <div class="imagetitle">Main:</div>
        <?php if (!empty($item['main_image'])) { ?>
            <div class="replaceimage mainimage" id="replaceimagemain"></div>
        <?php } ?>
    </div>
    <div class="content-row mainimage_view">
        <?php if (empty($item['main_image'])) { ?>
            <div class="emptyimage mainimage">
                <div id="uploadmainimage" style="float: unset;"></div>
            </div>
        <?php } else { ?>
            <div class="previewimage mainimage">
                <img src="<?=$item['main_image']?>" alt="Main Image"/>
            </div>
            <div class="removeimage mainimage">
                <i class="fa fa-trash"></i>
            </div>
        <?php } ?>
    </div>
    <div class="content-row">
        <input class="itemimagecaption mainimage" placeholder="Enter Caption..."/>
    </div>
</div>
<div class="topbannerimage_area">
    <div class="content-row">
        <div class="imagetitle">Special Top Banner:</div>
        <?php if (!empty($item['top_banner'])) { ?>
            <div class="replaceimage topbannerimage" id="replaceimagetopbanner"></div>
        <?php } ?>
    </div>
    <div class="content-row topbannerimage_view">
        <?php if (empty($item['top_banner'])) { ?>
            <div class="emptyimage topbannerimage">
                <div id="uploadtopbannerimage" style="float: unset"></div>
            </div>
        <?php } else { ?>
            <div class="previewimage topbannerimage">
                <img src="<?=$item['top_banner']?>" alt="Top Banner Image"/>
            </div>
            <div class="removeimage topbannerimage">
                <i class="fa fa-trash"></i>
            </div>
        <?php } ?>
    </div>
</div>