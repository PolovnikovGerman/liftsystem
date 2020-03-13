<div class="art_line1">    
    <div class="artlocationarea <?=$locat_ready==1 ? 'locatready' : ''?>" data-artloc="<?=$artwork_art_id?>">
        <div class="art_block1 <?=$locat_ready==1 ? 'text_blue' : 'text_white'?> openlocation" data-artloc="<?=$artwork_art_id?>" data-arttype="<?=$art_type?>"><?=$artlabel?></div>
        <div class="art_block5">
            <input type="text" readonly="readonly" value="<?=$repeat_text?>" data-artloc="<?=$artwork_art_id?>" class="artrepeat"/>
        </div>
        <div class="art_block4">
            <div class="redrawmsgarea <?=($redraw_message ? 'active' : '')?>" data-artloc="<?=$artwork_art_id?>">&nbsp;</div>
        </div>
    </div>
</div>
