<div class="relievers_customisation">
    <div class="sectionlabel">CUSTOMIZATION:</div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemvendorfilebtn editmode">
                <div class="content-row">Vector AI</div>
                <div class="content-row vectorfilemanage">
                    <?php if (empty($item['item_vector_img'])) { ?>
                        <div id="addvectorfile"></div>
                    <?php } else { ?>
                        <div class="vendorfile_view" data-link="<?=$item['item_vector_img']?>">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="vendorfile_delete">
                            <i class="fa fa-trash"></i>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="custommethodarea">
                <div class="content-row">
                    <div class="itemparamlabel custommethod">Method:</div>
                </div>
                <div class="content-row">
                    <div class="itemparamvalue custommethod editmode">
                        <select class="custommethodselect">
                            <option value=""></option>
                            <option value="Imprinting" <?=$item['imprint_method']=='Imprinting' ? 'selected="selected"' : ''?>>Imprinting</option>
                            <option value="Full Color" <?=$item['imprint_method']=='Full Color' ? 'selected="selected"' : ''?>>Full Color</option>
                            <option value="Laser Engraved" <?=$item['imprint_method']=='Laser Engraved' ? 'selected="selected"' : ''?>>Laser Engraved</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="customprinrcolorsarea">
                <div class="content-row">
                    <div class="itemparamlabel printcolors">Print Colors:</div>
                </div>
                <div class="content-row">
                    <div class="itemparamvalue printcolors editmode">
                        <select class="customprintcolors">
                            <option value=""></option>
                            <option value="bluetrack" <?=$item['imprint_color']=='bluetrack' ? 'selected="selected"' : ''?>>bluetrack</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="printlocationstable">
            <div class="content-row">
                <div class="addprintlocation"><i class="fa fa-plus"></i></div>
                <div class="locationnametitle editmode">Location Name:</div>
                <div class="locationplacetitle">Print Size:</div>
                <div class="locationviewtitle">View:</div>
            </div>
        </div>
        <div class="printlocationsdata">
            <?=$locations?>
        </div>
    </div>
</div>