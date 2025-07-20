<?php $nrow = 0; ?>
<?php foreach ($reserved as $reserv) : ?>
    <div class="inventoryreseved_table_row <?= $nrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
        <div class="shipdate <?=$reserv['shipdateclass']?>"><?=$reserv['shipdate']?></div>
        <div class="ordernumber" data-order="<?=$reserv['order_id']?>"><?=$reserv['order']?></div>
        <div class="customername"><?=$reserv['customer_name']?></div>
        <div class="amntval"><?=QTYOutput($reserv['reserved'])?></div>
        <div class="forecastbal"><?=QTYOutput($reserv['forecastbal'])?></div>
        <div class="artapprov <?=$reserv['approvedclass']?>"><?=$reserv['approved']?></div>
        <div class="fullfiledperc <?=$reserv['fullfillclass']?>"><?=$reserv['fullfill']?></div>
        <div class="shippedperc <?=$reserv['shipclass']?>"><?=$reserv['ship']?></div>
    </div>
    <?php $nrow++; ?>
<?php endforeach; ?>
