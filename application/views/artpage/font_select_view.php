<div class="contant-popup">
    <div class="prpopup-contant">
        <input type="hidden" id="fontselectfor"/>
        <div class="prpopup-fontstop">
            <div class="prp-fonttext">
                <label>Enter the name of the font you would like:</label>
                <input type="text" name="">
            </div>
            <div class="prp-fonttext">Or select one of the following popular fonts from our library:</div>
        </div>
        <div class="prpopup-fontsarea" id="prpopup-fontsarea">
            <h5>Verry Popular Fonts:</h5>
            <div class="tbl-fontslist verrypopular">
                <!-- Popular Show -->
                <?php $numpp = 0;?>
                <?php foreach($fonts_popular as $font) :?>
                    <?php if ($numpp==0): ?>
                        <div class="tblfontslist-tr">
                    <?php endif; ?>
                    <div class="tblfontslist-td td-fontinpt">
                        <input type="radio" name="prpfontarea" value="<?=$font['font_name']?>"/>
                    </div>
                    <div class="tblfontslist-td td-fontbox">
                        <div class="fontbox"><img src="<?=$font['font_example']?>" alt="<?=$font['font_name']?>" title="<?=$font['font_name']?>"></div>
                    </div>
                    <?php $numpp++;?>
                    <?php if ($numpp==3) : ?>
                        </div>
                        <?php $numpp = 0;?>
                    <?php endif; ?>
                <?php endforeach;?>
                <?php if ($numpp > 0) : ?>
                    </div>
                <?php endif; ?>
            </div>
            <h5>Other Popular Fonts:</h5>
            <div class="tbl-fontslist otherpopular">
                <!-- Other fonts -->
                <?php $numpp = 0;?>
                <?php foreach($fonts_other as $font) :?>
                    <?php if ($numpp==0): ?>
                        <div class="tblfontslist-tr">
                    <?php endif; ?>
                    <div class="tblfontslist-td td-fontinpt">
                        <input type="radio" name="prpfontarea" value="<?=$font['font_name']?>"/>
                    </div>
                    <div class="tblfontslist-td td-fontbox">
                        <div class="fontbox"><img src="<?=$font['font_example']?>" alt="<?=$font['font_name']?>" title="<?=$font['font_name']?>"></div>
                    </div>
                    <?php $numpp++;?>
                    <?php if ($numpp==2) : ?>
                        </div>
                        <?php $numpp = 0;?>
                    <?php endif; ?>
                <?php endforeach;?>
                <?php if ($numpp > 0) : ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="prpopup-footer">
            <div class="prpopupcolors-btn">
                <div class="prpopup-bluebtn">Select Font</div>
            </div>
        </div>
    </div>
</div>