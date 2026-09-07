<div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
<div class="artbox_filenameorg <?=$artlocation['locat_ready']==1 ? 'unactive' : ''?> truncateoverflowtext">Show Text</div>
<div class="artbox_iconfile">
    <img src="/img/leadorder/file-alt-grey.svg">
</div>
<div class="artbox_rush">
    <input type="checkbox" class="artlockdata" <?=$artlocation['rush']==1 ? 'checked' : ''?> <?=$edit==0 ? 'disabled' : ''?>/>
    <label>RUSH</label>
</div>
<?php if ($artlocation['locat_ready'] == 1): ?>
    <div class="artbox_step tick">
        <img src="/img/leadorder/tick-blue.svg">
    </div>
    <div class="artbox_filenamevect">&nbsp;</div>
<?php else : ?>
    <div class="artbox_step arrow">
        <img src="/img/leadorder/artbox-arrow.svg">
    </div>
    <div class="artbox_filenamevect redrawing">Redrawing...</div>
<?php endif; ?>
