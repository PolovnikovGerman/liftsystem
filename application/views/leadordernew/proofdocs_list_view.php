<?php $options = $proofs['options'];?>
<?php if ($edit==1) : ?>
    <div class="artproofs_optn">
        <div class="artproofs_addoptn">+Add<br>Option</div>
    </div>
<?php endif; ?>
<?php foreach ($options as $option) : ?>
    <div class="artproofs_optn <?=$option['aprt']==0 ? '' : 'approved'?> <?=$edit==0 ? '' : 'edit'?>" data-section="<?=$option['option']?>">
        <div class="optn_header">
            <div class="optn_checkbox">
                <input type="checkbox" class="proofdocoption" data-proofopt="<?=$option['option']?>" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
            </div>
            <div class="optn_title">Opt <?=$option['option']?></div>
            <div class="optn_star" data-section="<?=$option['option']?>">
                <?php if ($option['aprt']==0) : ?>
                    <img src="/img/leadorder/star.svg">
                <?php else: ?>
                    <img src="/img/leadorder/star-yellow.svg">
                <?php endif; ?>
            </div>
        </div>
        <div class="optn_box" id="artproof_<?=$artwork?>_<?=$option['option']?>">
            <ul>
                <?php foreach ($option['data'] as $proof) : ?>
                    <li>
                        <span class="uploadproofs" data-proofdoc="<?=$proof['artwork_proof_id']?>" data-event="hover" data-css="proofdetailsballonbox"
                        data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
                        data-balloon="<?=$proof['source_name']?>" data-timer="4000" data-delay="1000">proof_<?=str_pad($proof['proof_ordnum'],2,0,STR_PAD_LEFT)?></span>
                        <?php if ($edit==1) : ?>
                        <span class="artproofs_remove" data-proofdoc="<?=$proof['artwork_proof_id']?>" data-section="<?=$option['option']?>" data-proofname="<?=$proof['source_name']?>">
                            [&mdash;]
                        </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endforeach; ?>
