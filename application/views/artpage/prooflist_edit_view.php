<?php foreach ($proofs as $row) {?>
    <?php /* if ($row['approved']==0) { */ ?>
        <div class="artpopup_proofrow">
            <div class="artpopup_proofstar <?=$row['approve_class']?>" data-artworkid="<?=$artwork_id?>" data-proofid="<?=$row['artwork_proof_id']?>"><?=($row['out_approved']=='' ? '&nbsp;' : $row['out_approved'])?></div>
            <div class="artpopup_proofname" data-proofid="<?=$row['artwork_proof_id']?>" data-artworkid="<?=$artwork_id?>" title="<?=$row['source_name']?>"><?=$row['out_proofname']?></div>
            <div class="artpopup_proofsend" <?=($row['sended']==1 ? 'title="'.date('m/d/y h:iA',$row['sended_time']).'"' : '')?>><?=($row['sended']==1 ? 'sent' : '')?></div>
            <div class="artpopup_artproofcheck">
                <input type="checkbox" class="artproofdatasend" value="1" data-proofid="<?=$row['artwork_proof_id']?>" data-artworkid="<?=$artwork_id?>"/>
            </div>
            <?=$row['dellink']?>
        </div>
    <?php  /* } */ ?>
<?php } ?>
