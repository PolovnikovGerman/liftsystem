<?php $nrow = 0; ?>
<?php foreach ($proofs as $proof) : ?>
    <div class="proofreqbox-row <?=$nrow%2==0 ? 'whitedatarow' : 'greydatarow'; ?>">
        <div class="proofreqbox-date"><?=date('m/d/y', strtotime($proof['email_date']))?> -</div>
        <div class="proofreqbox-number" data-proof="<?=$proof['email_id']?>">pr<?=$proof['proof_num']?></div>
        <div class="proofreqbox-info truncateoverflowtext">- <?=$proof['email_item_name']?></div>
        <div class="proofreqbox-arrows">
            <?php if ($proof['art_stage']==1) : ?>
                <div class="proofreqbox-arrowbox active">
                    <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
                </div>
            <?php else : ?>
                <div class="proofreqbox-arrowbox">&nbsp;</div>
            <?php endif; ?>
            <?php if ($proof['redrawn_stage']==1) : ?>
                <div class="proofreqbox-arrowbox active">
                    <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
                </div>
            <?php else : ?>
                <div class="proofreqbox-arrowbox">&nbsp;</div>
            <?php endif; ?>
            <?php if ($proof['vectorized_stage']==1) : ?>
                <div class="proofreqbox-arrowbox active">
                    <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
                </div>
            <?php else : ?>
                <div class="proofreqbox-arrowbox">&nbsp;</div>
            <?php endif; ?>
            <?php if ($proof['proofed_stage']==1) : ?>
                <div class="proofreqbox-arrowbox active">
                    <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
                </div>
            <?php else : ?>
                <div class="proofreqbox-arrowbox">&nbsp;</div>
            <?php endif; ?>
            <?php if ($proof['approved_stage']==1) : ?>
                <div class="proofreqbox-arrowbox active">
                    <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
                </div>
            <?php else : ?>
                <div class="proofreqbox-arrowbox">&nbsp;</div>
            <?php endif; ?>
        </div>
    </div>
    <?php $nrow++; ?>
<?php endforeach; ?>
