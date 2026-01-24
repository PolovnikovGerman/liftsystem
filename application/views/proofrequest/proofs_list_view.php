<?php $numpp=1; ?>
<?php foreach ($proofs as $proof) : ?>
    <div class="datarow">
        <div class="approveflag proofdocsdata <?=empty($proof['approved']) ? '' : 'approved'?>" data-art="<?=$proof['artwork_proof_id']?>">
            <?php if(empty($proof['approved'])): ?>
                <i class="fa fa-star-o" aria-hidden="true"></i>
            <?php else : ?>
                <i class="fa fa-star" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
        <div class="proofdocname" data-art="<?=$proof['artwork_proof_id']?>" data-link="<?=$proof['proof_name']?>" data-event="hover"
             data-css="proofdocballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000"
             data-timer="4000" data-delay="1000" data-balloon="<?=$proof['source_name']?>">
            proof_<?=str_pad($numpp,2,"0",STR_PAD_LEFT)?>
        </div>
        <div class="proofdocsend"><?=empty($proof['sended_time']) ? '&nbsp;' : date('m/d/y', $proof['sended_time'])?></div>
        <div class="proofdoccheck">
            <input type="checkbox" class="proofdocsinpt" data-art="<?=$proof['artwork_proof_id']?>"/>
        </div>
        <div class="proofdocremove" data-art="<?=$proof['artwork_proof_id']?>">
            <i class="fa fa-times-circle"></i>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
