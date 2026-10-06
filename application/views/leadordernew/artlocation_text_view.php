<div class="artbox_number"><?=$artlocation['art_ordnum']?>.</div>
<div class="artbox_filenametext truncateoverflowtext" data-artloc="<?=$artlocation['artwork_art_id']?>" data-event="hover" data-css="proofdetailsballonbox"
     data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
     data-balloon="<?=$artlocation['customer_text']?>" data-timer="4000" data-delay="1000"><?=$edit==1 ? 'Show Text' : 'Show Text'?></div>
<?php //=$artlocation['locat_ready']==1 ? 'unactive' : ''?>
<div class="artbox_iconfile customtext" data-artloc="<?=$artlocation['artwork_art_id']?>">
    <?php if (empty($artlocation['customer_text'])): ?>
        <img src="/img/leadorder/file-alt-grey.svg"/>
    <?php else: ?>
        <img src="/img/leadorder/file-alt-green.svg"/>
    <?php endif; ?>
</div>
<div class="artbox_rush">
    <input type="checkbox" class="artlockdata" data-artloc="<?=$artlocation['artwork_art_id']?>" <?=$artlocation['rush']==1 ? 'checked' : ''?> <?=$edit==0 ? 'disabled' : ''?>/>
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
<?php if ($edit==1) : ?>
    <div class="artbox_remove" data-artloc="<?=$artlocation['artwork_art_id']?>" data-arttype="<?=$artlocation['art_type']?>">[&mdash;]</div>
<?php endif; ?>