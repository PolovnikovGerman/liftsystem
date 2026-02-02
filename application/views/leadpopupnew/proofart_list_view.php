<?php $nrow = 0; ?>
<?php foreach ($proofs as $proof) : ?>
    <div class="proofreqbox-row <?=$nrow%2==0 ? 'whitedatarow' : 'greydatarow'; ?>">
        <div class="proofreqbox-date"><?=date('m/d/y', strtotime($proof['email_date']))?> -</div>
        <div class="proofreqbox-number">pr<?=$proof['proof_num']?></div>
        <div class="proofreqbox-info truncateoverflowtext">- <?=$proof['email_item_name']?></div>
        <div class="proofreqbox-arrows">
            <div class="proofreqbox-arrowbox active">
                <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
            </div>
            <div class="proofreqbox-arrowbox active">
                <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
            </div>
            <div class="proofreqbox-arrowbox active">
                <img src="/img/artpage/proofreq-arrow.svg" alt="proofreq-arrow">
            </div>
            <div class="proofreqbox-arrowbox">&nbsp;</div>
            <div class="proofreqbox-arrowbox">&nbsp;</div>
        </div>
    </div>
    <?php $nrow++; ?>
<?php endforeach; ?>
