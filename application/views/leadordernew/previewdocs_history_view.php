<?php $nrow = 0; ?>
<div class="previewpicsdataarea">
    <?php foreach ($previews as $preview) : ?>
        <?php if ($nrow%6 == 0) : ?>
            <div class="previewpicsdatatable">
        <?php endif; ?>
        <div class="datarow previewpicdatarow">
            <div class="previewpicname" data-link="<?=$preview['preview_link']?>"><?=$preview['out_proofname']?></div>
        </div>
        <?php $nrow++; ?>
        <?php if ($nrow%6 == 0) : ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($nrow%6 != 0) : ?>
</div>
<?php endif; ?>
</div>
