<?php foreach ($data as $row) { ?>
    <div class="leaditem_datarow <?=($row['numpp']%2==0 ? 'greydatarow' : 'whitedatarow')?>">
        <div class="numpp"><?=$row['numpp']?></div>
        <div class="itemnumber"><?=$row['item_number']?></div>
        <div class="itemname"><?=$row['item_name']?></div>
        <div class="vendor"><?=$row['vendor_name']?></div>
        <div class="vendorzip"><?=$row['vendor_zipcode']?></div>
        <!-- prices zone -->
        <div class="vendorpricesarea">
            <div class="vendorpricerow">
                <?php foreach ($prices as $prow) { ?>
                    <div class="vendoritemsum <?=$row['profit_'.$prow.'_class']?>"><?=$row['price_'.$prow]?></div>
                <?php } ?>
                <div class="vendoritemsum <?=$row['profit_setup_class']?>"><?=$row['price_setup']?></div>
            </div>
            <div class="vendorpricerow">
                <?php foreach ($prices as $prow) { ?>
                    <div class="vendoritemsum <?=$row['profit_'.$prow.'_class']?>"><?=$row['profit_'.$prow.'_sum']?></div>
                <?php } ?>
                <div class="vendoritemsum <?=$row['profit_setup_class']?>"><?=$row['profit_setup_sum']?></div>
            </div>
        </div>
    </div>
<?php } ?>