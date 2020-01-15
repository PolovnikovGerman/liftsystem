<?php if (count($item_dat)==0) { ?>
    <div style="clear: both;float: left;text-align: center;color: #000000;font-size: 14px;font-weight: bold">
        No records
    </div>
<?php } else { ?>
    <?php $n_row=$offset+1;?>
    <?php foreach ($item_dat as $row) {?>
        <div class="<?=($n_row%2==0 ? 'grey-stroka' : 'white-stroka')?> table-row" style="clear:both;float: left;width: 973px;height: 20px;" >
            <div class="bor-1 text-2 cell-center" style="float: left;width: 30px;height: 20px;">
                <?=$n_row?>
            </div>
            <div class="bor-1 cell-center" style="float: left; width:25px;height: 20px;">
                <a id="<?=$row['item_id']?>" href="javascript:void(0);" onclick="item_edit(this);">
                    <img src="/img/pen.png" class="edit_item"/>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center" style="float:left;width:60px;height: 20px;">
                <?=$row['item_number']?>
            </div>
            <div class="bor-1 text-2 cell-left overflowtext" style="float: left; width:169px;height:20px;padding-left: 5px;margin-right: 7px;">
                <a href=javascript:void(0)" class="itemtitle <?=$row['itemnameclass']?>" title="<?=$row['item_name']?>"><?=$row['item_name']?></a>
            </div>
            <div class="bor-1 text-2 cell-center <?=($row['vendor_id']=='0' ? 'vendor_empty' : ($row['vendor_id']==-1 ? 'vendor_multi' : ''))?>" style="float:left;width:84px;height: 20px;">
                <?=$row['vendor_name']?>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_25_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=$row['item_profitclass_25']?>prof" title="<?=($row['item_profitclass_25']=='' ? '' : '<p>Profit $'.$row['item_profit_25'].'</p><p>Profit % '.$row['item_profitperc_25'].'</p> ')?>">
                    <?=($row['item_price_25']==''? 'n/a' : $row['item_price_25'])?>
                </a>
            </div>
            <!--
        <div class="bor-1 text-2 cell-center <?=$row['price_50_class']?>" style="float:left;width:47px;height: 20px;">
            <a href="javascript:void(0);" class="<?=$row['item_profitclass_50']?>prof" title="<?=($row['item_profitclass_50']=='' ? '' : '<p>Profit $'.$row['item_profit_50'].'</p><p>Profit % '.$row['item_profitperc_50'].'</p> ')?>">
                <?=($row['item_price_50']==''? 'n/a' : $row['item_price_50'])?>
            </a>
        </div>
        -->
            <div class="bor-1 text-2 cell-center <?=$row['price_75_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=$row['item_profitclass_75']?>prof" title="<?=($row['item_profitclass_75']=='' ? '' : '<p>Profit $'.$row['item_profit_75'].'</p><p>Profit % '.$row['item_profitperc_75'].'</p> ')?>">
                    <?=($row['item_price_75']==''? 'n/a' : $row['item_price_75'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_150_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_150']=='' ? 'empty' : $row['item_profitclass_150'])?>prof" title="<?=($row['item_profitclass_150']=='' ? '' : '<p>Profit $'.$row['item_profit_150'].'</p><p>Profit % '.$row['item_profitperc_150'].'</p> ')?>">
                    <?=($row['item_price_150']==''? 'n/a' : $row['item_price_150'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_250_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_250']=='' ? 'empty' : $row['item_profitclass_250'])?>prof" title="<?=($row['item_profitclass_250']=='' ? '' : '<p>Profit $'.$row['item_profit_250'].'</p><p>Profit % '.$row['item_profitperc_250'].'</p> ')?>">
                    <?=($row['item_price_250']==''? 'n/a' : $row['item_price_250'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_500_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_500']=='' ? 'empty' : $row['item_profitclass_500'])?>prof" title="<?=($row['item_profitclass_500']=='' ? '' : '<p>Profit $'.$row['item_profit_500'].'</p><p>Profit % '.$row['item_profitperc_500'].'</p> ')?>">
                    <?=($row['item_price_500']==''? 'n/a' : $row['item_price_500'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_1000_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_1000']=='' ? 'empty' : $row['item_profitclass_1000'])?>prof" title="<?=($row['item_profitclass_1000']=='' ? '' : '<p>Profit $'.$row['item_profit_1000'].'</p><p>Profit % '.$row['item_profitperc_1000'].'</p> ')?>">
                    <?=($row['item_price_1000']==''? 'n/a' : $row['item_price_1000'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_2500_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_2500']=='' ? 'empty' : $row['item_profitclass_2500'])?>prof" title="<?=($row['item_profitclass_2500']=='' ? '' : '<p>Profit $'.$row['item_profit_2500'].'</p><p>Profit % '.$row['item_profitperc_2500'].'</p> ')?>">
                    <?=($row['item_price_2500']==''? 'n/a' : $row['item_price_2500'])?>
                </a>
            </div>
            <!--
        <div class="bor-1 text-2 cell-center <?php //echo $row['price_3000_class']?>" style="float:left;width:47px;height: 20px;">
            <a href="javascript:void(0);" class="<?php //echo ($row['item_profitclass_3000']=='' ? 'empty' : $row['item_profitclass_3000'])?>prof" title="<?php //echo ($row['item_profitclass_3000']=='' ? '' : '<p>Profit $'.$row['item_profit_3000'].'</p><p>Profit % '.$row['item_profitperc_3000'].'</p> ')?>">
                <?php // echo ($row['item_price_3000']==''? 'n/a' : $row['item_price_3000'])?>
            </a>
        </div>
        -->
            <div class="bor-1 text-2 cell-center <?=$row['price_5000_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_5000']=='' ? 'empty' : $row['item_profitclass_5000'])?>prof" title="<?=($row['item_profitclass_5000']=='' ? '' : '<p>Profit $'.$row['item_profit_5000'].'</p><p>Profit % '.$row['item_profitperc_5000'].'</p> ')?>">
                    <?=($row['item_price_5000']==''? 'n/a' : $row['item_price_5000'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_10000_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_10000']=='' ? 'empty' : $row['item_profitclass_10000'])?>prof" title="<?=($row['item_profitclass_10000']=='' ? '' : '<p>Profit $'.$row['item_profit_10000'].'</p><p>Profit % '.$row['item_profitperc_10000'].'</p> ')?>">
                    <?=($row['item_price_10000']==''? 'n/a' : $row['item_price_10000'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_20000_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_20000']=='' ? 'empty' : $row['item_profitclass_20000'])?>prof" title="<?=($row['item_profitclass_20000']=='' ? '' : '<p>Profit $'.$row['item_profit_20000'].'</p><p>Profit % '.$row['item_profitperc_20000'].'</p> ')?>">
                    <?=($row['item_price_20000']=='' ? 'n/a' : $row['item_price_20000'])?>
                </a>
            </div>
            <div class="bor-1 text-2 cell-center <?=$row['price_setup_class']?>" style="float:left;width:47px;height: 20px;">
                <a href="javascript:void(0);" class="<?=($row['item_profitclass_setup']=='' ? 'empty' : $row['item_profitclass_setup'])?>prof" title="<?=($row['item_profitclass_setup']=='' ? '' : '<p>Profit $'.$row['item_profit_setup'].'</p><p>Profit % '.$row['item_profitperc_setup'].'</p> ')?>">
                    <?=($row['item_price_setup']==='' ? 'n/a' : $row['item_price_setup'])?>
                </a>
            </div>
            <div class="grey" style="float:left;width:10px;height: 23px;">&nbsp;</div>
            <div class="text-2 bor-2 cell-center <?=$row['update_class']?> last_col" style="float:left;height: 20px;"><?=$row['update']?></div>
        </div>
        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>
