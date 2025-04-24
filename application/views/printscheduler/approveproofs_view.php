<div class="print_approved_proofsdata">
    <?php if (count($proofs)==0) : ?>
        <span>No approved proofs</span>
    <?php else : ?>
        <?php foreach ($proofs as $proof) : ?>
            <div class="datarow">
                <div class="printproofs_starapproved">
                    <img src="/img/leadorder/star_yellow.png" alt="star_yellow">
                </div>
                <div class="print_proofs_name uploadproofdoc" data-proofdoc="<?=$proof['src']?>" style="">
                    <?=$proof['out_apprname']?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
