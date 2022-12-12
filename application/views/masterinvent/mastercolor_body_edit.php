<input type="hidden" id="invsessioin" value="<?=$session?>"/>
<div class="inventorycolor_body_content">
    <div class="datarow">
        <div class="statusradiobtn edit" data-status="1">
            <?php if ($color['color_status']==1 && $color['notreorder']==0) { ?>
                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-circle-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="statuslabel">
            Active
        </div>
        <div class="statusradiobtn edit" data-status="0">
            <?php if ($color['color_status']==1 && $color['notreorder']==0) { ?>
                <i class="fa fa-circle-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="statuslabel">
            Inactive / Do Not Reorder <span>(after sold out)</span>
        </div>
    </div>
    <div class="inventorycolor_body_left">
        <div class="datarow">
            <div class="colorparamlabel">Color Name:</div>
            <div class="colordatainput">
                <input type="text" class="colornameinpt invcolor" data-item="color" value="<?=$color['color']?>" placeholder="Color Name"/>
            </div>
        </div>
        <div class="datarow">
            <div class="colorparamlabel">Pantone/s:</div>
            <div class="colordatainput">
                <textarea class="colorpantonesinpt invcolor" data-item="pantones"><?=$color['pantones']?></textarea>
            </div>
        </div>
        <div class="datarow">
            <div class="colorparamlabel">Max Amnt:</div>
            <div class="colordatainput colormaxvalue">
                <input type="text" class="colormaxvalueinpt invcolor" data-item="suggeststock" value="<?=$color['suggeststock']?>" placeholder="Max Amnt"/>
            </div>
        </div>
        <div class="datarow">
            <div class="colorparamlabel mfgprice">Mfg Cost (Made in USA by BLUETRACK):</div>
            <div class="colorparamvalue mfgprice"><?=empty($price) ? '&nbsp;' : MoneyOutput($price,3)?></div>
        </div>
        <div class="colordatadevide">&nbsp;</div>
        <div class="datarow">
            <div class="colorvendorslabel">Possible Vendors (Import or Domestic):</div>
            <div class="colorvendorspricelabel">Price Ea:</div>
        </div>
        <div class="colorvendorsdata">
            <?php foreach ($vendors as $vendor) { ?>
                <div class="datarow">
                    <div class="colorvendorinpt">
                        <select class="colorvendornameinpt" data-list="<?=$vendor['invcolor_vendor_id']?>">
                            <option value="" <?=empty($vendor['vendor_id']) ? 'seleted="selected"' : ''?>>Vendor...</option>
                            <?php foreach ($vendorlists as $vendorlist) { ?>
                                <option value="<?=$vendorlist['vendor_id']?>" <?=$vendor['vendor_id']==$vendorlist['vendor_id'] ? 'seleted="selected"' : ''?>><?=$vendorlist['vendor_name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="colorvendorinpt">
                        <input type="text" class="colorvendorpriceinpt" value="<?=$vendor['price']?>" data-list="<?=$vendor['invcolor_vendor_id']?>" placeholder="Price"/>
                    </div>
                    <div class="colorvendorunit"><?=$color['color_unit']?></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="inventorycolor_body_right">
        <div class="colorimagelabel">Image</div>
        <div class="colorimagedata">
            <?php if (empty($color['color_image'])) { ?>
                <div class="colorimagevalue">
                    <div id="pic-uploader"></div>
                </div>
            <?php } else { ?>
                <div class="colorimagevalue">
                    <img src="<?=$color['color_image']?>" alt="Color image" />
                </div>
                <div id="picnew-uploader"></div>
            <?php } ?>
        </div>
    </div>
</div>