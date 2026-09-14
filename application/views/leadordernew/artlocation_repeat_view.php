<div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
<div class="artbox_locationtitle" data-artloc="<?=$artlocation['artwork_art_id']?>" data-arttype="<?=$artlocation['art_type']?>"><?=$artlocation['artlabel']?></div>
<div class="artbox_filenameorg">
    <input type="text" readonly="readonly" value="<?=$artlocation['repeat_text']?>" data-artloc="<?=$artlocation['artwork_art_id']?>" class="artrepeat"/>
</div>
<!--<div class="art_block4">-->
<!--    <div class="redrawmsgarea --><?php //=($artlocation['redraw_message']) ? 'active' : ''?><!--" data-artloc="--><?php //=$artlocation['artwork_art_id']?><!--">&nbsp;</div>-->
<!--    <div class="art_block2" style="padding-top: 0">&nbsp;</div>-->
<!--    --><?php //if ($edit==1): ?>
<!--    <div class="icon_1 removeartlocation" data-artloc="--><?php //=$artlocation['artwork_art_id']?><!--" data-artloctype="--><?php //=$artlocation['art_type']?><!--">&nbsp;</div>-->
<!--    --><?php //endif; ?>
<!--</div>-->
