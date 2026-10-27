<?php $nrow = 0; ?>
<div class="claymodelsdataarea">
    <?php foreach ($claydocs as $claydoc) : ?>
        <?php if ($nrow%5 == 0) : ?>
            <div class="claymodelsdatatable">
        <?php endif; ?>
        <div class="datarow claymodeldatarow">
            <div class="<?=$edit==0 ? 'clayapprov_view' : 'clayapprov'?> <?=$claydoc['clay_approved']==1 ? 'approved' : ''?>" data-clay="<?=$claydoc['artwork_clay_id']?>">
                <?php if ($claydoc['clay_approved'] == 0) : ?>
                    <img src="/img/leadorder/star.svg" alt="Star"/>
                <?php else: ?>
                    <img src="/img/leadorder/star-yellow.svg" alt="Star yellow"/>
                <?php endif; ?>
            </div>
            <div class="claymodelname" data-link="<?=$claydoc['clay_link']?>" data-title="<?=$claydoc['out_proofname']?>"
                 data-event="hover" data-css="proofdetailsballonbox"
                 data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
                 data-balloon="<?=$claydoc['clay_source']?>" data-timer="4000" data-delay="1000">clay_<?=str_pad($claydoc['numpp'],2, '0', STR_PAD_LEFT)?></div>
            </div>
            <?php if ($edit==1) : ?>
                <div class="claydocremove" data-claydoc="<?=$claydoc['artwork_clay_id']?>" data-clayname="<?=$claydoc['clay_source']?>">[&mdash;]</div>
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
