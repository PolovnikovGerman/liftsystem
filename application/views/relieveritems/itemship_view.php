<div class="relievers_shipping">
    <div class="sectionlabel">SHIPPING:</div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemparamlabel itemweigth">Weight Ea:</div>
            <div class="itemparamvalue itemweigth"><?=$item['item_weigth']?></div>
            <div class="itemparamlabel weight-measure">lbs</div>
        </div>
        <div class="itemboxarea">
            <div class="content-row">
                <div class="itemparamlabel boxnametitle">Box</div>
                <div class="itemparamlabel boxqtytitle">Qty</div>
            </div>
            <div class="itemboxdataarea">
                <?php $numpp=1;?>
                <?php foreach ($boxes as $box) {?>
                    <div class="content-row">
                        <div class="itemparamlabel boxname"><?=chr(64 + $numpp)?></div>
                        <div class="itemparamvalue boxqty"><?=$box['box_qty']?></div>
                    </div>
                    <?php $numpp++;?>
                <?php } ?>
            </div>
        </div>
        <div class="itemdimesionsarea">
            <div class="content-row">
                <div class="itemparamlabel dimensiontitle">Dimensions (Inches)</div>
            </div>
            <div class="itemdimensionsdataarea">
                <?php foreach ($boxes as $box) {?>
                    <div class="content-row">
                        <div class="itemparamvalue boxwidth"><?=$box['box_width']?></div>
                        <div class="itemparamlabel boxwidth">W</div>
                        <div class="itemparamvalue boxdepth"><?=$box['box_length']?></div>
                        <div class="itemparamlabel boxdepth">L</div>
                        <div class="itemparamvalue boxheight"><?=$box['box_height']?></div>
                        <div class="itemparamlabel boxheight">H</div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel specialeach">Special Each:</div>
            <div class="itemparamvalue specialeach"><?=$item['charge_pereach']?></div>
            <div class="itemparamlabel specialorder">Special Per Order:</div>
            <div class="itemparamvalue specialorder"><?=$item['charge_perorder']?></div>
        </div>
    </div>
</div>
