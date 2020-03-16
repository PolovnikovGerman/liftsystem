<?php foreach ($proofs as $row) { ?>
    <div class="lead_onlineproofrow">
        <div class="lead_quotedate"><?=$row['email_date']?></div>
        <div class="lead_quoteseparat">-</div>
        <div class="lead_quoteprofnum" data-leadid="<?=$row['email_id']?>">pr<?=$row['proof_num']?></div>
        <div class="lead_quoteseparat">-</div>
        <div class="lead_quoteqty"><?=$row['email_qty']?></div>
        <div class="lead_proofitem"><?=$row['email_item_name']?></div>
        <div class="lead_proofartdata <?=$row['art_class']?>"><?=$row['art_cell']?></div>
        <div class="lead_proofartdata <?=$row['redrawn_class']?>"><?=$row['redrawn_cell']?></div>
        <div class="lead_proofartdata <?=$row['vectorized_class']?>"><?=$row['vectorized_cell']?></div>
        <div class="lead_proofartdata <?=$row['proofed_class']?>"><?=$row['proofed_cell']?></div>
        <div class="lead_proofartdata <?=$row['approved_class']?>"><?=$row['approved_cell']?></div>
        <div class="lead_profapprove <?=$row['apoofclass']?>" data-leadid="<?=$row['email_id']?>">
            <?=$row['leadproofcell']?>
        </div>
    </div>
<?php } ?>