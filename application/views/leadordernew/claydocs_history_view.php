<?php $nrow = 0; ?>
<div class="claymodelsdataarea">
    <?php foreach ($claydocs as $claydoc) : ?>
        <?php if ($nrow%6 == 0) : ?>
            <div class="claymodelsdatatable">
        <?php endif; ?>
        <div class="datarow claymodeldatarow">
            <div class="claymodelname" data-link="<?=$claydoc['clay_link']?>"><?=$claydoc['out_proofname']?></div>
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
