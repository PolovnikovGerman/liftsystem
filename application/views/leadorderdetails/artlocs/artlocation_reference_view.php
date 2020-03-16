<div class="art_line1">    
    <div class="artlocationarea <?=$locat_ready==1 ? 'locatready' : ''?>" data-artloc="<?=$artwork_art_id?>">
        <div class="art_block1 <?=$locat_ready==1 ? 'text_blue' : 'text_white'?> openlocation" data-artloc="<?=$artwork_art_id?>" data-arttype="<?=$art_type?>"><?=$artlabel?></div>
        <div class="art_block2">
            <?=$redrawchk?>
        </div>
        <div class="art_block2">
            <?=$rushchk?>            
        </div>
        <div class="art_block3 <?=$locat_ready==1 ? 'text_blue' : 'text_white'?> <?=(empty($logo_vectorized) ? '' : 'viewreadyloc')?>" data-artloc="<?=$artwork_art_id?>">
            <?=(empty($logo_vectorized) ? '&nbsp;' : 'Open AI')?>
        </div>
        <div class="art_block4">
            <div class="redrawmsgarea <?=($redraw_message ? 'active' : '')?>" data-artloc="<?=$artwork_art_id?>">&nbsp;</div>
        </div>
    </div>
</div>
