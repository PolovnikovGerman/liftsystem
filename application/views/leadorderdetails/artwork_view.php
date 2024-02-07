<div class="block_7_artwork">
    <div class="block_7_artwork_line">
        <div class="block_7_header">Artwork</div>
        <div class="block_7_artwork1">
            <div class="block_7_artwork2">
                <input type="checkbox" class="input_checkbox chkboxleadorddata" <?=($artwork_blank==0 ? '' : 'checked="checked"')?> 
                <?=($edit==0 ? 'disabled="disabled"' : 'data-entity="artwork" data-field="artwork_blank"')?>/> blank
            </div>
            <div class="block_7_artwork2">
                <input type="checkbox" class="input_checkbox chkboxleadorddata" <?=$artwork_rush==0 ? '' : 'checked="checked"'?>
                 <?=($edit==0 ? 'disabled="disabled"' : 'data-entity="artwork" data-field="artwork_rush"')?> > rush
            </div>
        </div>
    </div>
    <div class="block_7_artwork_line">
        <div class="block_7_artwork2 text_style_2">
            Instructions:
        </div>
        <div class="block_7_artwork5 text_style_2">
            <div class="block_7_artwork3 text_style_2">
                Templates:
            </div>
            <div class="block_7_artwork4 text_blue">
                <div class="icon_file empty_template" data-url="<?=$empty_url?>" data-title="<?=$empty_title?>" style="margin: 0 3px;">&nbsp;</div> Master
            </div>
            <div class="block_7_artwork4 text_blue">
                <div class="icon_file artpopup_templview" style="margin: 0 3px;">&nbsp;</div> Item
            </div>
        </div>
    </div>
    <div class="block_7_artwork_line">
        <textarea class="input_border_gray block_7_textarea inputleadorddatas" <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="artwork" data-field="customer_instruct"')?>><?=$customer_instruct?></textarea>
    </div>
    <?php if ($weborder==1) { ?>
        <div class="block_7_artwork_line">
            <div class="artcolorlabel text_style_2">Colors:</div>
            <div class="artcolorvalues text_style_2"><?=($artcolors)?></div>
        </div>
        <div class="block_7_artwork_line">
            <div class="artfontlabel text_style_2">Font:</div>
            <div class="artfontvalues text_style_2"><?=($artfont)?></div>
        </div>        
    <?php } ?>
</div>
<div class="block_7_art">
    <div class="block_7_header">Art:</div>
    <div class="block_7_art1 input_border_gray">
        <!-- Art Locations -->
        <div id="artlocationsarea"><?=$locat_view?></div>        
        <!-- Art Location Add -->
        <?php if ($edit==1) { ?>
        <div class="art_line1" id="newartbuttonareaview" <?=$artwork_blank==1 ? 'style="display: none;"' : ''?>>
            <div class="button_newart">
                <div class="button_newart_text">+ New Art</div>
                <select class="art_select input_border_gray" id="arttypechoice">
                    <option value="Logo">Logo</option>
                    <option value="Text">Text</option>
                    <option value="Repeat">Repeat</option>
                    <option value="Reference">Reference</option>
                </select>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<div class="block_7_proofs">
    <div class="block_7_header">Proofs:</div>
    <div class="block_7_proofs_text">Approved = Yellow</div>
    <div class="block_7_proofs1 input_border_gray">
        <div class="block_7_proofs1_scrool">
            <div class="proofs_line1">
                <div class="proofs_line1_text1"><?=$artstage_txt?></div>
                <div class="proofs_line1_text2"><?=$artstage_time?></div>
            </div>
            <div id="profdocsshowarea" class="proofs_content_area" style="width: <?=$profdocwidth?>px;">
                <?=$proofdoc_view?>
            </div>            
        </div>
        <?php if ($edit==1) { ?>
            <div class="button_addproof">
                <div class="button_addproof_text" id="uploadproofdoc" data-artwork="<?=$artwork_id?>">add</div>
            </div>            
            <div class="button_proofemail">
                <div class="button_email_text" data-artwork="<?=$artwork_id?>">email</div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="artclaypreviewarea" <?=$extendview=='0' ? 'style="display: none"' : ''?>>
    <div class="datarow">
        <div class="areatitle <?=$claycnt==0 ? 'view' : ''?>">Clay Models:</div>
        <?php if ($edit==0) { ?>
            <?php if ($claycnt > 0) { ?>
                <div class="openclaymodelsview">[open all]</div>
            <?php } ?>
        <?php } else { ?>
            <div class="areachek">
                <input type="checkbox" class="input_checkbox chkboxleadorddata" <?=$art_clay==1 ? 'checked="checked"' : ''?> id="art_claychk"
                    data-entity="order" data-field="art_clay"/>
                require clay
            </div>
        <?php } ?>
    </div>
    <div class="datarow">
        <div class="claypreviewtable <?=$claycnt==0 ? 'view' : ''?>">
            <div id="claymodshowarea" class="claymodshowarea" style="width: <?=$claydocswidth?>px;">
                <?=$claydoc_view?>
            </div>
        </div>
    </div>
    <div id="clayaddrow" class="addclaypreviewdoc">
        <?php if ($edit==1 && $art_clay==1) { ?>
            <div id="addclay">&nbsp;</div>
        <?php } ?>
    </div>
</div>
<div class="artpreviewpreviewarea" <?=$extendview=='0' ? 'style="display: none"' : ''?>>
    <div class="datarow">
        <div class="areatitle <?=($previewcnt==0) ? 'view' : ''?>">Preview Pictures:</div>
        <?php if ($edit==0) { ?>
            <?php if ($previewcnt > 0) { ?>
                <div class="openpreviewsview">[open all]</div>
            <?php } ?>
        <?php } else { ?>
            <div class="areachek">
                <input type="checkbox" class="input_checkbox chkboxleadorddata" <?=$art_preview==1 ? 'checked="checked"' : ''?> id="art_previewchk"
                       data-entity="order" data-field="art_preview"/>
                require preview
            </div>
        <?php } ?>
    </div>
    <div class="datarow">
        <div class="previewpreviewtable <?=$previewcnt==0 ? 'view' : ''?>">
            <div id="previewpicshowarea" class="previewpicshowarea" style="width: <?=$previewswidth?>px;">
                <?=$previewdoc_view?>
            </div>
        </div>
    </div>
    <div id="previewaddrow" class="addpreviewpreviewdoc">
        <?php if ($edit==1 && $art_preview==1) { ?>
            <div id="addpreview">&nbsp;</div>
        <?php } ?>
    </div>
</div>