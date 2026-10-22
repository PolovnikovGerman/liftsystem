<?php $nrow = 0; ?>
<div class="claymodelsdataarea">
    <?php foreach ($claydocs as $claydoc) : ?>
        <?php if ($nrow%5 == 0) : ?>
            <div class="claymodelsdatatable">
        <?php endif; ?>
        <div class="datarow claymodeldatarow">
            <div class="<?=$edit==0 ? 'clayapprov_view' : 'clayapprov'?>" data-clay="<?=$claydoc['artwork_clay_id']?>">
                <?php if ($claydoc['clay_approved'] == 0) : ?>
                    <img src="/img/leadorder/star.svg" alt="Star"/>
                <?php else: ?>
                    <img src="/img/leadorder/star-yellow.svg" alt="Star yellow"/>
                <?php endif; ?>
            </div>
            <div class="claymodelname" data-link="<?=$claydoc['clay_link']?>" data-title="<?=$claydoc['out_proofname']?>"><?=$claydoc['out_proofname']?></div>
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
