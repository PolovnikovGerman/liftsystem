<div class="relievers_shipping">
    <div class="sectionlabel">SHIPPING:</div>
    <div class="sectionbody <?=$missinfo==0 ? '' : 'missinginfo'?>">
        <div class="content-row">
            <div class="itemparamlabel itemweigth">Weight Each:</div>
            <div class="itemparamvalue itemweigth <?=empty($item['item_weigth']) ? 'missing_info' : ''?>"><?=$item['item_weigth']?></div>
            <div class="itemparamlabel weight-measure">lbs</div>
            <div class="itemparamlabel specialeach">Extra $ Each:</div>
            <div class="itemparamvalue specialeach"><?=number_format($item['charge_pereach'],2)?></div>
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
                    <div class="itemparamvalue boxqty"><?=$box['box_qty']?></div>
                </div>
                <div class="itemdimensionsdataarea">
                    <div class="itemparamvalue boxwidth"><?=$box['box_width']?></div>
                    <div class="itemparamlabel boxwidth">W</div>
                    <div class="itemparamvalue boxdepth"><?=$box['box_length']?></div>
                    <div class="itemparamlabel boxdepth">L</div>
                    <div class="itemparamvalue boxheight"><?=$box['box_height']?></div>
                    <div class="itemparamlabel boxheight">H</div>
                </div>
            </div>
            <?php $numpp++;?>
        <?php } ?>
    </div>
</div>
