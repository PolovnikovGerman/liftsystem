<div class="contant-popup">
    <div class="prpopup-contant">
        <div class="prpopup-colorsarea">
            <div class="prpopup-listcolors">
                <?php foreach ($colors as $color): ?>
                    <div class="prp-colorarea">
                        <div class="prp-colorinpt">
                            <input type="radio" name="prpcolorarea" id="color<?=substr($color['code'], 1)?>" value="<?=$color['code']?>"/>
                        </div>
                        <div class="prp-colorbox <?=$color['class']?>"><?=str_replace('(', '<br>(', $color['name'])?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="prpopup-footer">
            <div class="prpopupcolors-btn">
                <div class="prpopup-bluebtn">Select Color</div>
            </div>
        </div>
    </div>
</div>
