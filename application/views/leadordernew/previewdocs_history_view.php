<?php $nrow = 0; ?>
<div class="previewpicsdataarea">
    <?php foreach ($previews as $preview) : ?>
        <?php if ($nrow%5 == 0) : ?>
            <div class="previewpicsdatatable">
        <?php endif; ?>
        <div class="datarow previewpicdatarow">
            <div class="<?=$edit==0 ? 'previewpic_approv_view' : 'previewpic_approv'?> <?=$preview['preview_approved']==1 ? 'approved' : ''?>" data-previewpic="<?=$preview['artwork_preview_id']?>">
                <?php if ($preview['preview_approved'] == 0) : ?>
                    <img src="/img/leadorder/star.svg" alt="Star"/>
                <?php else: ?>
                    <img src="/img/leadorder/star-yellow.svg" alt="Star yellow"/>
                <?php endif; ?>
            </div>
            <div class="previewpicname" data-link="<?=$preview['preview_link']?>" data-previewdoc="<?=$preview['artwork_preview_id']?>" data-event="hover" data-css="proofdetailsballonbox"
                 data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
                 data-balloon="<?=$preview['preview_source']?>" data-timer="4000" data-delay="1000">preview_<?=str_pad($preview['numpp'],2, '0', STR_PAD_LEFT)?></div>
            <?php if ($edit==1) : ?>
                <span class="previewremove" data-previewdoc="<?=$preview['artwork_preview_id']?>" data-previewname="<?=$preview['preview_source']?>">[&mdash;]</span>
            <?php endif; ?>
        </div>
        <?php $nrow++; ?>
        <?php if ($nrow%5 == 0) : ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($nrow%5 != 0) : ?>
</div>
<?php endif; ?>
</div>
