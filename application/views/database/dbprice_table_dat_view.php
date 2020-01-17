<?php if (count($item_dat)==0) { ?>
    <div class="whitedatarow">
        <div class="emptypricedata">No records</div>
    </div>
<?php } else { ?>
    <?php $n_row=$offset+1;?>
    <?php foreach ($item_dat as $row) {?>
        <div class="<?=($n_row%2==0 ? 'greydatarow' : 'whitedatarow')?> pricetable-row">
            <div class="pricedatacell numlist">
                <?=$n_row?>
            </div>
            <div class="pricedatacell editcoll" data-item="<?=$row['item_id']?>">
                <i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i>
            </div>
            <div class="pricedatacell itemnumber"><?=$row['item_number']?></div>
            <div class="pricedatacell overflowtext itemtitle <?=$row['itemnameclass']?>" data-content="<?=$row['item_name']?>"><?=$row['item_name']?></div>
            <div class="pricedatacell competitor <?=($row['vendor_id']=='0' ? 'vendor_empty' : ($row['vendor_id']==-1 ? 'vendor_multi' : ''))?>">
                <?=$row['vendor_name']?>
            </div>
            <div class="pricedatacell  <?=$row['price_25_class']?> <?=$row['item_profitclass_25']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_25']=='' ? '' : '<p>Profit $'.$row['item_profit_25'].'</p><p>Profit % '.$row['item_profitperc_25'].'</p> ')?>">
                 <?=($row['item_price_25']==''? 'n/a' : $row['item_price_25'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_75_class']?> <?=$row['item_profitclass_75']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_75']=='' ? '' : '<p>Profit $'.$row['item_profit_75'].'</p><p>Profit % '.$row['item_profitperc_75'].'</p> ')?>">
                <?=($row['item_price_75']==''? 'n/a' : $row['item_price_75'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_150_class']?> <?=$row['item_profitclass_150']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_150']=='' ? '' : '<p>Profit $'.$row['item_profit_150'].'</p><p>Profit % '.$row['item_profitperc_150'].'</p> ')?>">
                <?=($row['item_price_150']==''? 'n/a' : $row['item_price_150'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_250_class']?> <?=$row['item_profitclass_250']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_250']=='' ? '' : '<p>Profit $'.$row['item_profit_250'].'</p><p>Profit % '.$row['item_profitperc_250'].'</p> ')?>">
                <?=($row['item_price_250']==''? 'n/a' : $row['item_price_250'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_500_class']?> <?=$row['item_profitclass_500']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_500']=='' ? '' : '<p>Profit $'.$row['item_profit_500'].'</p><p>Profit % '.$row['item_profitperc_500'].'</p> ')?>">
                <?=($row['item_price_500']==''? 'n/a' : $row['item_price_500'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_1000_class']?> <?=$row['item_profitclass_1000']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_1000']=='' ? '' : '<p>Profit $'.$row['item_profit_1000'].'</p><p>Profit % '.$row['item_profitperc_1000'].'</p> ')?>">
                <?=($row['item_price_1000']==''? 'n/a' : $row['item_price_1000'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_2500_class']?> <?=$row['item_profitclass_2500']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_2500']=='' ? '' : '<p>Profit $'.$row['item_profit_2500'].'</p><p>Profit % '.$row['item_profitperc_2500'].'</p> ')?>">
                <?=($row['item_price_2500']==''? 'n/a' : $row['item_price_2500'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_5000_class']?> <?=$row['item_profitclass_5000']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_5000']=='' ? '' : '<p>Profit $'.$row['item_profit_5000'].'</p><p>Profit % '.$row['item_profitperc_5000'].'</p> ')?>">
                <?=($row['item_price_5000']==''? 'n/a' : $row['item_price_5000'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_10000_class']?> <?=$row['item_profitclass_10000']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_10000']=='' ? '' : '<p>Profit $'.$row['item_profit_10000'].'</p><p>Profit % '.$row['item_profitperc_10000'].'</p> ')?>">
                <?=($row['item_price_10000']==''? 'n/a' : $row['item_price_10000'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_20000_class']?> <?=$row['item_profitclass_20000']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_20000']=='' ? '' : '<p>Profit $'.$row['item_profit_20000'].'</p><p>Profit % '.$row['item_profitperc_20000'].'</p> ')?>">
                <?=($row['item_price_20000']==''? 'n/a' : $row['item_price_20000'])?>
            </div>
            <div class="pricedatacell  <?=$row['price_setup_class']?> <?=$row['item_profitclass_setup']?>prof pricevalcell"
                 data-content="<?=($row['item_profitclass_setup']=='' ? '' : '<p>Profit $'.$row['item_profit_setup'].'</p><p>Profit % '.$row['item_profitperc_setup'].'</p> ')?>">
                <?=($row['item_price_setup']==''? 'n/a' : $row['item_price_setup'])?>
            </div>
            <div class="pricedatacell priceseparator">&nbsp;</div>
            <div class="pricedatacell <?=$row['update_class']?> updatetime"><?=$row['update']?></div>

        </div>

        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>