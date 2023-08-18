<div class="mainimage_area">
    <div class="content-row">
        <div class="imagetitle">Main:</div>
    </div>
    <div class="content-row mainimage_view">
        <div class="previewimage mainimage">
            <?php if (!empty($item['main_image'])) { ?>
                <img src="<?=$item['main_image']?>" alt="Main Image"/>
            <?php } ?>
        </div>
    </div>
</div>
<div class="categoryimage_area">
    <div class="content-row">
        <div class="imagetitle">Category Page:</div>
    </div>
    <div class="content-row categoryimage_view">
        <div class="previewimage categoryimage">
            <?php if (!empty($item['category_image'])) { ?>
                <img src="<?=$item['category_image']?>" alt="Category Image"/>
            <?php } ?>
        </div>
    </div>
</div>
<div class="topbannerimage_area">
    <div class="content-row">
        <div class="imagetitle">Special Top Banner:</div>
    </div>
    <div class="content-row topbannerimage_view">
        <div class="previewimage topbannerimage">
            <?php if (!empty($item['top_banner'])) { ?>
                <img src="<?=$item['top_banner']?>" alt="Top Banner Image"/>
            <?php } ?>
        </div>
    </div>
</div>