<?php $numpp=0;?>
<div class="optimages-slide-list">
    <?php foreach ($colors as $color) { ?>
        <?php if ($numpp%8==0) { ?>
            <div class="optimages-slide-wrap">
        <?php } ?>
        <div class="optimages-slide-item">
            <?php if ($image==1) { ?>
                <?php if (empty($color['item_color_image'])) { ?>
                    <div class="replaseoptionitems">&nbsp;</div>
                <?php } else { ?>
                    <div class="replaseoptionitems" id="reploptimg<?=$color['item_color_id']?>"></div>
                <?php } ?>
            <?php } else { ?>
                <div class="replaseoptionitems">&nbsp;</div>
            <?php } ?>
            <div class="img-addimagebox" data-image="<?=$color['item_color_id']?>">
                <?php if (empty($color['item_color_image'])) { ?>
                    <?php if ($image==1) { ?>
                        <div class="addoptionimageslider" id="addoptionimageslider<?=$color['item_color_id']?>"></div>
                    <?php } else { ?>
                        &nbsp;
                    <?php } ?>
                <?php } else { ?>
                    <img src="<?=$color['item_color_image']?>">
                <?php } ?>
                <div class="<?=empty($color['item_color_image']) ? 'removeimage' : 'removeimagefull'?> <?=$image==0 ? 'sideright' : '' ?> optimage" data-image="<?=$color['item_color_id']?>">
                    <i class="fa fa-trash"></i>
                </div>
            </div>
            <div class="content-row">
                <div class="imageorder">
                    <select class="optimageorderinpt" data-image="<?=$color['item_color_id']?>">
                        <?php for ($i=1; $i<=$cntimages; $i++) { ?>
                            <option value="<?=$i?>" <?=$i==$color['item_color_order'] ? 'selected="selected"' : ''?>><?=$i?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="imagecaption">
                    <?php if (empty($inventory)) { ?>
                        <input class="itemimagecaption optimage" data-image="<?=$color['item_color_id']?>" value="<?=$color['item_color']?>" placeholder="Enter Caption..."/>
                    <?php } else { ?>
                        <select class="itemimagecaptionselect optimage" data-image="<?=$color['item_color_id']?>">
                            <option value="" <?=$color['item_color']=='' ? 'selected="selected"' : ''?>></option>
                            <option value="<?=$color['item_color_source']?>" <?=$color['item_color']==$color['item_color_source'] ? 'selected="selected"' : ''?>><?=$color['item_color_source']?></option>
                        </select>
                    <?php } ?>
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
<div class="cas-arrows slideaddimg-prev" id="prevcolorimageslider">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"/></svg>
</div>
<div class="cas-arrows slideaddimg-next" id="nextcolorimageslider">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
</div>

