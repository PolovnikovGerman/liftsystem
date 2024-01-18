<?php $numpp=0;?>
<div class="addimages-slide-list">
    <?php foreach ($images as $image) { ?>
        <?php if ($numpp%8==0) { ?>
    <div class="addimages-slide-wrap">
        <?php } ?>
        <div class="addimages-slide-item">
            <div class="imgsrcsize">(800x800)</div>
            <?php if (empty($image['item_img_name'])) { ?>
                <div class="replaseadditems">&nbsp;</div>
            <?php } else { ?>
                <div class="replaseadditems" id="replimg<?=$image['item_img_id']?>"></div>
            <?php } ?>
            <div class="img-addimagebox">
                <?php if (empty($image['item_img_name'])) { ?>
                    <div class="addimageslider" id="addimageslider<?=$image['item_img_id']?>"></div>
                <?php } else { ?>
                    <img src="<?=$image['item_img_name']?>">
                    <div class="removeimage addimage" data-image="<?=$image['item_img_id']?>">
                        <i class="fa fa-trash"></i>
                    </div>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="imageorder">
                    <select class="imageorderinpt" data-image="<?=$image['item_img_id']?>">
                        <?php for ($i=1; $i<=$cntimages; $i++) { ?>
                            <option value="<?=$i?>" <?=$i==$image['item_img_order'] ? 'selected="selected"' : ''?>><?=$i?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="imagecaption">
                    <input class="itemimagecaption addimage" data-image="<?=$image['item_img_id']?>" value="<?=$image['item_img_label']?>" placeholder="Enter Caption..."/>
                </div>
            </div>
        </div>
        <?php $numpp++;?>
        <?php if ($numpp%8==0) { ?>
            </div>
        <?php } ?>
    <?php } ?>
<?php if ($numpp%8!=0) { ?>
    </div>
<?php } ?>
</div>
<div class="cas-arrows slideaddimg-prev" id="prevaddimageslider">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"/></svg>
</div>
<div class="cas-arrows slideaddimg-next" id="nextaddimageslider">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
</div>
