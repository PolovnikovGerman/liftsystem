<div class="proofs_line2">
    <div class="proofs_line2_text1">
        <div class="proofs_star <?=$edit==1 ? 'active' : ''?>" data-proofdoc="<?=$artwork_proof_id?>" data-proofname="<?=$out_proofname?>">
            <img src="/img/leadorder/star_white.png" width="18" height="18" alt="star_white">
        </div>
        <div class="proofs_line2_bl1">
            <div class="proofs_line2_bl2 text_blue uploadproofdoc" title="<?=$source_name?>" data-proofdoc="<?=$artwork_proof_id?>"><?=$out_proofname?></div>
            <div class="proofs_line2_bl3">                
                <div class="proofs_line2_bl2_txt <?=$sended==0 ? '' : 'sendedproofdoc'?>" <?=$sended==0 ? '' : 'title="'.date('m/d/Y h:iA',$sended_time).'"'?>>
                    <?=($sended==0 ? '&nbsp;' : 'sent')?>
                </div>                
                <?php if ($edit==1) { ?>
                    <input type="checkbox" class="input_checkbox sendprofdocdata" data-proofdoc="<?=$artwork_proof_id?>" style="float: left; margin-top: 4px;"/>
                    <div class="icon_1 removeproofdoc" data-proofdoc="<?=$artwork_proof_id?>" data-proofname="<?=$out_proofname?>"  style="margin-top: 2px;">&nbsp;</div>                    
                <?php } ?>
            </div>
        </div>
    </div>
</div>	
