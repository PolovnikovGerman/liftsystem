<?php $numpp = 0;?>
<?php foreach ($payments as $payment): ?>
    <div class="paymhist_tr <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="paymhist_td paymhist_date"><?=$payment['out_date']?></div>
        <div class="paymhist_td paymhist_payment"><?=$payment['out_name']?></div>
        <div class="paymhist_td paymhist_amount"><?=$payment['paysum']?></div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
