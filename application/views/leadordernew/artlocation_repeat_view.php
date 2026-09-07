<div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
<div class="art_block1 openlocation" data-artloc="<?=$artlocation['artwork_art_id']?>" data-arttype="<?=$artlocation['art_type']?>"><?=$artlocation['artlabel']?></div>
<div class="art_block5">
    <input type="text" readonly="readonly" value="<?=$repeat_text?>" data-artloc="<?=$artlocation['artwork_art_id']?>" class="artrepeat"/>
</div>
<div class="art_block4">
    <div class="redrawmsgarea <?=($artlocation['redraw_message']) ? 'active' : ''?>" data-artloc="<?=$artlocation['artwork_art_id']?>">&nbsp;</div>
    <div class="art_block2" style="padding-top: 0">&nbsp;</div>
    <?php if ($edit==1): ?>
    <div class="icon_1 removeartlocation" data-artloc="<?=$artlocation['artwork_art_id']?>" data-artloctype="<?=$artlocation['art_type']?>">&nbsp;</div>
    <?php endif; ?>
</div>
