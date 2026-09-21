<div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
<div class="artbox_filenameorg <?=$artlocation['locat_ready']==1 ? 'unactive' : ''?> truncateoverflowtext" data-artloc="<?=$artlocation['artwork_art_id']?>">
    <?=$artlocation['source_title']?>
</div>
<div class="artbox_iconfile <?=$artlocation['locat_ready']==1 ? 'unactive' : ''?>">
    <img src="/img/leadorder/file-alt-grey.svg">
</div>
<div class="artbox_rush unactive">
    <input type="checkbox" class="artlockdata" <?=$artlocation['rush']==1 ? 'checked' : ''?> <?=$edit==0 ? 'disabled' : ''?>/>
    <label>RUSH</label>
</div>
<?php if ($artlocation['locat_ready'] == 1): ?>
    <div class="artbox_step tick">
        <img src="/img/leadorder/tick-blue.svg">
    </div>
    <div class="artbox_filenamevect readyfile truncateoverflowtext" data-artloc="<?=$artlocation['artwork_art_id']?>">
        <?=$artlocation['vector_title']?>
    </div>
<?php else : ?>
    <div class="artbox_step arrow">
        <img src="/img/leadorder/artbox-arrow.svg">
    </div>
    <div class="artbox_filenamevect redrawing">Redrawing...</div>
<?php endif; ?>
