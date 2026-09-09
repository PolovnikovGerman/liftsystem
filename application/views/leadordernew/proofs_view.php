<?php $proofhead = $proofs['head'];?>
<?php $options = $proofs['options'];?>
<div class="artproofs_header">
    <div class="artproofheader_title">Proofs:</div>
    <div class="artproofheader_apprvl <?=$proofhead['class']?>">
        <?php if (empty($proofhead['apprtime'])) : ?>
            <span><?=$proofhead['status']?></span>
        <?php else : ?>
            <span class="artproofapprvl_icon"><img src="/img/leadorder/tick-black.svg"></span>
            <span><?=$proofhead['status']?></span>
            <div class="artproofapprvl_days"><?=$proofhead['apprtime']?></div>
        <?php endif; ?>
    </div>
    <div class="artproofheader_open"><span>Open</span><span><img src="/img/leadorder/icon-link.svg"></span></div>
    <div class="artproofheader_send">Send<span><i class="fa fa-envelope-o" aria-hidden="true"></i></span></div>
</div>
<div class="artproofs_body">
    <?php if ($edit==1) : ?>
        <div class="artproofs_optn">
            <div class="artproofs_addoptn">+Add<br>Option</div>
        </div>
    <?php endif; ?>
    <?php foreach ($options as $option) : ?>
        <div class="artproofs_optn <?=$option['aprt']==0 ? '' : 'approved'?>">
            <div class="optn_header">
                <div class="optn_checkbox">
                    <input type="checkbox" class="" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
                </div>
                <div class="optn_title">Opt <?=$option['option']?></div>
                <div class="optn_star">
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
                    <li>proof_<?=str_pad($proof['proof_ordnum'],2,0,STR_PAD_LEFT)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
</div>