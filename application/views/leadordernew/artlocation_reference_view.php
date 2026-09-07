<div class="artlocationarea <?=$artlocation['locat_ready']==1 ? 'locatready' : ''?>" data-artloc="<?=$artlocation['artwork_art_id']?>">
    <div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
    <div class="art_block1 <?=$artlocation['locat_ready']==1 ? 'text_blue' : 'text_white'?> openlocation" data-artloc="<?=$artlocation['artwork_art_id']?>" data-arttype="<?=$artlocation['art_type']?>">
        <?=$artlocation['artlabel']?>
    </div>
    <div class="art_block2">
        <?=$artlocation['redrawchk']?>
    </div>
    <div class="art_block2">
        <?=$artlocation['rushchk']?>
    </div>
    <div class="art_block3 <?=$locat_ready==1 ? 'text_blue' : 'text_white'?> <?=(empty($artlocation['logo_vectorized']) ? '' : 'viewreadyloc')?>" data-artloc="<?=$artlocation['artwork_art_id']?>">
        <?=(empty($logo_vectorized) ? '&nbsp;' : 'Open AI')?>
    </div>
    <div class="art_block4">
        <div class="redrawmsgarea <?=($artlocation['redraw_message']) ? 'active' : ''?>" data-artloc="<?=$artlocation['artwork_art_id']?>">&nbsp;</div>
        <div class="art_block2" style="padding-top: 0"><?=$artlocation['redochk']?></div>
        <?php if ($edit==1) : ?>
        <div class="icon_1 removeartlocation" data-artloc="<?=$artlocation['artwork_art_id']?>" data-artloctype="<?=$artlocation['art_type']?>">&nbsp;</div>
        <?php endif; ?>
    </div>
</div>

