<?php $nrow = 0; ?>
<div class="previewpicsdataarea">
    <?php foreach ($previews as $preview) : ?>
        <?php if ($nrow%5 == 0) : ?>
            <div class="previewpicsdatatable">
        <?php endif; ?>
        <div class="datarow previewpicdatarow">
            <div class="<?=$edit==0 ? 'previewpic_approv_view' : 'previewpic_approv'?>" data-previewpic="<?=$preview['artwork_preview_id']?>">
                <?php if ($preview['preview_approved'] == 0) : ?>
                    <img src="/img/leadorder/star.svg" alt="Star"/>
                <?php else: ?>
                    <img src="/img/leadorder/star-yellow.svg" alt="Star yellow"/>
                <?php endif; ?>
            </div>
            <div class="previewpicname" data-link="<?=$preview['preview_link']?>" data-title="<?=$preview['out_proofname']?>"><?=$preview['out_proofname']?></div>
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
