<div class="numpp">&nbsp;</div>
<div class="itemnum">
    <div class="inventorymanage save">
        <i class="fa fa-check-circle"></i>
    </div>
    <div class="inventorymanage cancel">
        <i class="fa fa-times-circle"></i>
    </div>
</div>
<div class="coloritemname">
    <input class="invitemcolordata colorname" data-item="color" value="<?= $color ?>"/>
</div>
<div class="itempercent">
    <input class="invitemcolordata suggstock" data-item="suggeststock" value="<?= $suggeststock ?>"/>
</div>
<div class="instock">&nbsp;</div>
<div class="reserved">&nbsp;</div>
<div class="available">&nbsp;</div>
<div class="devider">&nbsp;</div>
<div class="onroute">
    <input class="invitemcolordata stockval" data-item="onroutestock" value="<?= $onroutestock ?>"/>        
</div>
<div class="costea">
    <input class="invitemcolordata priceea" data-item="price" value="<?= $price ?>"/>
</div>
<div class="totalea">&nbsp;</div>
<div class="colororder">
    <select class="colororderselect">
        <?php for ($i=1; $i<=$numcolors ; $i++) { ?>
        <option value="<?=$i?>" <?=($i==$color_order ? 'selected="selected"' : '')?>><?=$i?></option>
        <?php } ?>
    </select>
</div>
<div class="colordesript">
    <input class="invitemcolordata colordescript" data-item="color_descript" value="<?= $color_descript ?>"/>
</div>

