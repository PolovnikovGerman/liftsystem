<div class="inventorycolor_body_content">
    <div class="datarow">
        <div class="statusradiobtn">
            <i class="fa fa-check-circle-o" aria-hidden="true"></i>
        </div>
        <div class="statuslabel">
            <?=$color['color_status']==1 && $color['notreorder']==0 ? 'Active' : 'Inactive / Do Not Reorder <span>(after sold out)</span>'?>
        </div>
    </div>
    <div class="inventorycolor_body_left">
        <div class="datarow">
            <div class="colorparamlabel">Color Name:</div>
            <div class="colorparamvalue colorname"><?=$color['color']?></div>
        </div>
        <div class="datarow">
            <div class="colorparamlabel">Pantone/s:</div>
            <div class="colorparamvalue pantones"><?=$color['pantones']?></div>
        </div>
        <div class="datarow">
            <div class="colorparamlabel">Max Amnt:</div>
            <div class="colorparamvalue colormaxvalue"><?=empty($color['suggeststock']) ? '&nbsp;' : QTYOutput($color['suggeststock'])?></div>
        </div>
        <div class="datarow">
            <div class="colorparamlabel">Mfg Cost:</div>
            <div class="colorparamvalue mfgprice"><?=empty($price) ? '&nbsp;' : MoneyOutput($price,3)?></div>
        </div>
        <div class="colordatadevide">&nbsp;</div>
        <div class="datarow">
            <div class="colorvendorslabel">Possible Vendors:</div>
        </div>
        <div class="colorvendorsdata">
            <?php foreach ($vendors as $vendor) { ?>
                <div class="datarow">
                    <div class="colorvendorname"><?=$vendor['vendor_name']?></div>
                    <div class="colorvendorprice"><?=(empty($vendor['price']) || $vendor['price']==0) ? '&nbsp;' : MoneyOutput($vendor['price'],3) ?></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="inventorycolor_body_right">
        <div class="colorimagelabel">Image</div>
        <div class="coloriamgevalue">
            <?php if (empty($color['color_image'])) { ?>
                &nbsp;
            <?php } else { ?>
                <img src="<?=$color['color_image']?>" alt="Color image" />
            <?php } ?>
        </div>
    </div>
</div>