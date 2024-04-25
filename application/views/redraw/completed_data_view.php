<?php $nrow=0;?>
<?php foreach ($data as $row) { ?>
    <div class="datarow completed_data_row <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
        <div class="completed_numpp_data"><?=$row['num_pp']?></div>
        <div class="rushdata">
            <?=$row['rush']==0 ? '&nbsp;' : '<i class="fa fa-star"></i>'?>
        </div>
        <div class="completed_proofnum_data"><?=$row['proof_num']?></div>
        <div class="completed_ordernum_data"><?=$row['order_num']?></div>
        <?php if ($row['imagesourceclass']=='imagereadysourceview') { ?>
            <div class="completed_srcfile_data sourcedat" data-logoid="<?=$row['artwork_logo_id']?>" data-event="hover" data-timer = 5000 data-css="weekbrandtotals"
                 data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF" data-position="right" data-balloon="<img src='<?=$row['redrawedsource']?>' alt='Preview' style='width:250px; height: auto'/>">
                <?=$row['srcfile']?>
            </div>
        <?php } else { ?>
            <div class="completed_srcfile_data sourcedat" data-logoid="<?=$row['artwork_logo_id']?>"><?=$row['srcfile']?></div>
        <?php } ?>
        <div class="completed_srcfile_data vectordat" data-logoid="<?=$row['artwork_logo_id']?>"><?=$row['vectorfile']?></div>
        <div class="exectime_data"><?=$row['exec_date']?></div>
        <div class="spendtime_data"><?=$row['spend_days']?></div>
        <div class="spendtime_data"><?=$row['spend_hours']?></div>
        <div class="spendtime_data"><?=$row['spend_mins']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
