<?php foreach ($proofs as $row) {?>
    <?php if ($row['approved']==1) {?>
        <div class="artpopup_proofrow">
            <div class="artpopup_approvedname" data-artworkid="<?=$artwork_id?>" data-proofid="<?=$row['artwork_proof_id']?>" title="<?=$row['source_name']?>"><?=$row['out_apprname']?></div>
            <div class="artpopup_artredcirkle delapproved" data-artworkid="<?=$artwork_id?>" data-proofid="<?=$row['artwork_proof_id']?>">&nbsp;</div>
        </div>
    <?php } ?>
<?php } ?>