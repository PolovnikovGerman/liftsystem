<?php $nrow=0;?>
<?php foreach ($email_dat as $row) {?>
    <div class="proof_tabrow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?> <?=$row['rowclass']?>" id="profrow<?=$row['email_id']?>">
        <div class="proof_ordnum_dat"><?=$row['ordnum']?></div>
        <div class="proof_deldata" data-proofid="<?=$row['email_id']?>">
            <?=$row['action_icon']?>
        </div>
        <div class="proof_parsedata" <?=$row['emailparsed_title']?>><?=$row['emailparsed']?></div>
        <div class="proof_leadnum_dat <?=$row['assigned']?>" data-leadid="<?=$row['leadid']?>"  data-proofid="<?=$row['email_id']?>"><?=$row['lead_number']?></div>
        <div class="proof_includ_dat" data-proofid="<?=$row['email_id']?>"><?=$row['inclicon']?></div>
        <div class="proof_brand_dat" data-proofid="<?=$row['email_id']?>"><?=$row['proof_num']?></div>
        <div class="proof_date_dat"><?=$row['email_date']?></div>
        <div class="proof_customer_dat">
            <div class="prfart_customer-maildat"><?=$row['email']?></div>
            <div class="prfart_customer-data"><?=$row['email_sender']?></div>
        </div>
        <div class="proof_emptydiv">&nbsp;</div>
        <div class="proof_item_dat"><?=$row['email_item_name']?></div>
        <div class="proof_qty_dat"><?=$row['email_qty']?></div>
        <div class="artdataarea" data-proofid="<?=$row['email_id']?>">
            <div class="proof_art artdata <?=$row['art_class']?>  <?=$row['art_title']?>" data-messageview="<?=$row['lastmsg']?>">
                <?=$row['art_cell']?>
            </div>
            <div class="proof_redrawn artdata <?=$row['redrawn_class']?> <?=$row['redrawn_title']?>" data-messageview="<?=$row['lastmsg']?>">
                <?=$row['redrawn_cell']?>
            </div>
            <div class="proof_vector artdata <?=$row['vectorized_class']?> <?=$row['vectorized_title']?>" data-messageview="<?=$row['lastmsg']?>">
                <?=$row['vectorized_cell']?>
            </div>
            <div class="proof_proofed artdata <?=$row['proofed_class']?> <?=$row['proofed_title']?>" data-messageview="<?=$row['lastmsg']?>">
                <?=$row['proofed_cell']?>
            </div>
            <div class="proof_approve artdata <?=$row['approved_class']?> <?=$row['approved_title']?>" data-messageview="<?=$row['lastmsg']?>">
                <?=$row['approved_cell']?>
            </div>
        </div>
        <div class="proof_note_dat" data-proofid="<?=$row['email_id']?>" <?=($row['note_title']=='' ? '' : 'data-content="'.$row['note_title'].'"')?>><?=$row['proof_note']?></div>
        <div class="proof_ordernum_dat <?=$row['orderedit']?>" data-proofid="<?=$row['email_id']?>"><?=$row['proof_order']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>