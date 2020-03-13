<?php $nrow=0;?>
<?php $rowh=count($years)*21;?>
<?php foreach ($data as $row) { ?>
    <div class="itemmonth_data_row <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
        <div class="itemdetails">
            <div class="numrec" style="height: <?=$rowh?>px;"><?=$row['numrec']?></div>
            <div class="itemnumber venditemshowprice" data-item='<?=$row['item_id']?>' style="height: <?=$rowh?>px;"><?=$row['item_number']?></div>
            <div class="itemname lastcol" style="height: <?=$rowh?>px;">
                <div class="itemnameview"><?=$row['item_name']?></div>
                <div class="vendornameview"><span class="vendorname">Vendor</span> <?=$row['vendor_name']?></div>
            </div>
        </div>
        <div class="itemsoldresult">
            <?php foreach ($row['sales'] as $salerow) {?>
                <div class="itemsoldresultrow <?=($salerow['year']==$start_year ? 'lastyear' : '')?>">
                    <div class="year"><?=$salerow['year']?></div>
                    <div class="orders"><?=($salerow['orders']==0 ? '&mdash;' : $salerow['orders'])?></div>
                    <div class="qty lastcol"><?=($salerow['qty']==0 ? '&mdash;' : number_format($salerow['qty'], 0, '.', ','))?></div>
                    <div class="monthdata <?=$salerow['Jan']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="01" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Jan']==0 ? '&mdash;' : $salerow['Jan'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Feb']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="02" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Feb']==0 ? '&mdash;' : $salerow['Feb'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Mar']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="03" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Mar']==0 ? '&mdash;' : $salerow['Mar'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Apr']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="04" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Apr']==0 ? '&mdash;' : $salerow['Apr'])?>
                    </div>
                    <div class="monthdata <?=$salerow['May']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="05" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['May']==0 ? '&mdash;' : $salerow['May'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Jun']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="06" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Jun']==0 ? '&mdash;' : $salerow['Jun'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Jul']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="07" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Jul']==0 ? '&mdash;' : $salerow['Jul'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Aug']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="08" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Aug']==0 ? '&mdash;' : $salerow['Aug'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Sep']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="09" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Sep']==0 ? '&mdash;' : $salerow['Sep'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Oct']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="10" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Oct']==0 ? '&mdash;' : $salerow['Oct'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Nov']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="11" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Nov']==0 ? '&mdash;' : $salerow['Nov'])?>
                    </div>
                    <div class="monthdata <?=$salerow['Dec']==0 ? '' : 'datacell'?>" data-item="<?=$row['item_id']?>" data-month="12" data-year='<?=$salerow['year']?>'>
                        <?=($salerow['Dec']==0 ? '&mdash;' : $salerow['Dec'])?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>
