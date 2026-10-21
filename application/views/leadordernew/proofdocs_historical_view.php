<?php $nrow = 0; ?>
<div class="proofdocsdataarea">
    <?php foreach ($proofs as $proof) : ?>
        <?php if ($nrow%4 == 0) : ?>
            <div class="proofdocsdatatable">
        <?php endif; ?>
        <div class="datarow proofdocdatarow">
            <div class="proofdocname">
                <div class="<?=$edit==1 ? 'proof_approve' : 'proof_approve_view'?>" data-proofdoc="<?=$proof['artwork_proof_id']?>">
                    <?php if ($proof['approved']==1) : ?>
                        <img src="/img/leadorder/star-yellow.svg" alt="Star yellow"/>
                    <?php else: ?>
                        <img src="/img/leadorder/star.svg" alt="Star"/>
                    <?php endif; ?>
                </div>
                <div class="uploadproofs" data-proofdoc="<?=$proof['artwork_proof_id']?>" data-event="hover" data-css="proofdetailsballonbox"
                  data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
                  data-balloon="<?=$proof['source_name']?>" data-timer="4000" data-delay="1000">proof_<?=str_pad($proof['proof_ordnum'],2,0,STR_PAD_LEFT)?>
                </div>
                <div class="proofdocname_check">
                    <input type="checkbox" class="proofdoccheck" data-proofdoc="<?=$proof['artwork_proof_id']?>" <?=$edit==0 ? 'disabled="disabled"' : ''?>/>
                </div>
                <?php if ($edit==1) : ?>
                    <span class="artproofs_remove" data-proofdoc="<?=$proof['artwork_proof_id']?>" data-section="<?=$option['option']?>" data-proofname="<?=$proof['source_name']?>">
                            [&mdash;]
                        </span>
                <?php endif; ?>
            </div>
        </div>
        <?php $nrow++; ?>
        <?php if ($nrow%4 == 0) : ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($nrow%4 != 0) : ?>
</div>
<?php endif; ?>
</div>
