<?php $numpp = 1; ?>
<?php foreach ($proofs as $proof): ?>
    <div class="datarow">
        <div class="approveddocname" data-art="<?=$proof['artwork_proof_id']?>" data-link="<?=$proof['proof_name']?>" data-event="hover"
             data-css="proofdocballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000"
             data-timer="4000" data-delay="1000" data-balloon="<?=$proof['source_name']?>">
            approved_<?=str_pad($numpp,2,"0",STR_PAD_LEFT)?>
        </div>
        <div class="approveddocremove" data-art="<?=$proof['artwork_proof_id']?>">
            <i class="fa fa-times-circle"></i>
        </div>
    </div>
<?php $numpp++; ?>
<?php endforeach; ?>