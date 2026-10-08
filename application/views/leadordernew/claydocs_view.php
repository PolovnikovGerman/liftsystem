<?php foreach ($claydocs as $claydoc) : ?>
    <div class="claymodels_optn">
        <div class="claymodels_optn_header">
            <div class="claymodels_optn_checkbox">
                <input type="checkbox" class="" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
            </div>
            <div class="claymodels_optn_title">Opt <?=$claydoc['clay_option']?></div>
            <div class="claymodels_optn_star <?=$claydoc['approved']==1 ? 'approved' : 'unapproved'?>" data-section="<?=$claydoc['clay_option']?>">
                <?php if ($claydoc['approved']==0) : ?>
                    <img src="/img/leadorder/star.svg">
                <?php else: ?>
                    <img src="/img/leadorder/star-yellow.svg">
                <?php endif; ?>
            </div>
        </div>
        <div class="claymodels_optn_box <?=$edit==1 ? 'edit' : ''?>" id="claymodel_<?=$artwork?>_<?=$claydoc['clay_option']?>">
            <ul>
                <?php foreach ($claydoc['data'] as $claydat) : ?>
                    <li>
                        <span class="claymodelname" data-link="<?=$claydat['clay_link']?>" data-event="hover" data-css="proofdetailsballonbox"
                              data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
                              data-balloon="<?=$claydat['clay_source']?>" data-timer="4000" data-delay="1000">clay_<?=str_pad($claydat['numpp'],2,'0', STR_PAD_LEFT)?></span>
                        <?php if ($edit==1) : ?>
                        <span class="claydoc_remove" data-claydoc="<?=$claydat['artwork_clay_id']?>" data-section="<?=$claydat['clay_option']?>" data-clayname="<?=$claydat['clay_source']?>">
                            [&mdash;]
                        </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endforeach; ?>
