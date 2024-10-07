<div class="relievers_shipping">
    <div class="sectionlabel">SHIPPING:</div>
    <div class="sectionbody shippingsection <?=$missinfo==0 ? '' : 'missinginfo'?>">
        <div class="content-row">
            <div class="itemparamlabel itemweigth">Weight Ea:</div>
            <div class="itemparamvalue itemweigth editmode">
                <input type="text" class="itemkeyinfoinput itemweigthinpt <?=empty($item['item_weigth']) ? 'missing_info' : ''?>" data-item="item_weigth" value="<?=$item['item_weigth']?>"/>
            </div>
            <div class="itemparamlabel weight-measure">lbs</div>
            <div class="itemparamlabel specialeach">Special Each:</div>
            <div class="itemparamvalue specialeach editmode">
                <input type="text" class="iteminfoinput chargepereach" data-item="charge_pereach" value="<?=$item['charge_pereach']?>"/>
            </div>
        </div>
        <div class="content-row">
            <div class="itemboxarea">
                <div class="content-row">
                    <div class="itemparamlabel boxnametitle">Box</div>
                    <div class="itemparamlabel boxqtytitle">Qty</div>
                </div>
            </div>
            <div class="itemdimesionsarea">
                <div class="content-row">
                    <div class="itemparamlabel dimensiontitle">Dimensions (Inches)</div>
                </div>
            </div>
        </div>
        <?php $numpp=1;?>
        <?php foreach ($boxes as $box) {?>
            <div class="content-row">
                <div class="itemboxdataarea">
                    <div class="itemparamlabel boxname"><?=chr(64 + $numpp)?></div>
                    <div class="itemparamvalue boxqty editmode">
                        <input type="text" class="itemshipbox box_qty" data-shipbox="<?=$box['item_shipping_id']?>" data-item="box_qty" value="<?=$box['box_qty']?>"/>
                    </div>
                    <?php $numpp++;?>
                </div>
                <div class="itemdimensionsdataarea">
                    <div class="itemparamvalue boxwidth editmode">
                        <input type="text" class="itemshipbox box_width" data-shipbox="<?=$box['item_shipping_id']?>" data-item="box_width" value="<?=$box['box_width']?>"/>
                    </div>
                    <div class="itemparamlabel boxwidth">W</div>
                    <div class="itemparamvalue boxdepth editmode">
                        <input type="text" class="itemshipbox box_length" data-shipbox="<?=$box['item_shipping_id']?>" data-item="box_length" value="<?=$box['box_length']?>"/>
                    </div>
                    <div class="itemparamlabel boxdepth">L</div>
                    <div class="itemparamvalue boxheight editmode">
                        <input type="text" class="itemshipbox box_height" data-shipbox="<?=$box['item_shipping_id']?>" data-item="box_height" value="<?=$box['box_height']?>"/>
                    </div>
                    <div class="itemparamlabel boxheight">H</div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
