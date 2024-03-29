<div class="mainimage_area">
    <div class="content-row">
        <div class="imagetitle">Main:</div>
        <div class="imgsrcsize">(800x800)</div>
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
<div class="categoryimage_area">
    <div class="content-row">
        <div class="imagetitle">Category Page:</div>
        <div class="imgsrcsize">(1200x600)</div>
        <?php if (!empty($item['category_image'])) { ?>
            <div class="replaceimage categoryimage" id="replaceimagecategory"></div>
        <?php } ?>
    </div>
    <div class="content-row categoryimage_view">
        <?php if (empty($item['category_image'])) { ?>
            <div class="emptyimage categoryimage">
                <div id="uploadcategoryimage" style="float: unset"></div>
            </div>
        <?php } else { ?>
            <div class="previewimage categoryimage">
                <img src="<?=$item['category_image']?>" alt="Category Image"/>
            </div>
            <div class="removeimage categoryimage">
                <i class="fa fa-trash"></i>
            </div>
        <?php } ?>
    </div>
</div>
<div class="topbannerimage_area">
    <div class="content-row">
        <div class="imagetitle">Special Top Banner:</div>
        <div class="imgsrcsize">(1880x420)</div>
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