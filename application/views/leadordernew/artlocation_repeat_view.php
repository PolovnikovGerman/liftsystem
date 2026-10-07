<div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
<div class="artbox_locationtitle" data-artloc="<?=$artlocation['artwork_art_id']?>" data-arttype="<?=$artlocation['art_type']?>"><?=$artlocation['artlabel']?></div>
<div class="artbox_filenameinput">
    <input type="text" readonly="readonly" value="<?=$artlocation['repeat_text']?>" data-artloc="<?=$artlocation['artwork_art_id']?>" class="artrepeat"/>
</div>
<div class="artbox_emptyspace">&nbsp;</div>
<?php if ($edit==1) : ?>
    <div class="artbox_remove" data-artloc="<?=$artlocation['artwork_art_id']?>" data-arttype="<?=$artlocation['art_type']?>">[&mdash;]</div>
<?php endif; ?>