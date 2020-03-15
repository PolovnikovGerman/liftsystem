<div class="art_line1">
    <div class="art_line1_backgr4">&nbsp;</div>
    <div class="art_line1_backgr5 art_line1_text">
        <div class="art_block1 text_white" data-artloc="<?=$artwork_art_id?>" data-arttype="<?=$art_type?>"><?=$artlabel?></div>
        <div class="art_block2">
            <input type="checkbox" class="input_checkbox" style="float: left;" <?=$edit==1 ? '' : 'disabled="disabled"'?>/>
            <input type="checkbox" class="input_checkbox" style="float: left;" <?=$edit==1 ? '' : 'disabled="disabled"'?>/>
        </div>
        <div class="art_block3 text_white">&nbsp;</div>
        <div class="art_block4">
            <div class="icon_file">&nbsp;</div>
            <?php if ($edit==1) { ?>
            <input type="checkbox" name="name3" value="c1" class="input_checkbox" style="float: left;">
            <div class="icon_1 removeartlocation" data-artloc="<?=$artwork_art_id?>" data-artloctype="<?=$art_type?>">&nbsp;</div>
            <?php } ?>
        </div>
    </div>
    <div class="art_line1_backgr6">&nbsp;</div>
</div>
