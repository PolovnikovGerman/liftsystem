<div class="updatehistorydetailsarea">
    <div class="title">Changes <?=date('D - M d, Y g:i a',$head['created_time'])?> by <?=$head['user_name']?></div>
    <div class="detailstablehead">
        <div class="parameterlabel">Parameter</div>
        <div class="parametervalue">Old Value</div>
        <div class="parametervalue">New Value</div>
    </div>
    <div class="detailstablebody">
        <?php $nrow=0;?>
        <?php foreach ($details as $row) { ?>
        <div class="detailstablerow <?=($nrow%2==0 ? 'grey' : 'white')?>">
            <div class="parameterlabel border_r"><?=$row['parameter_name']?></div>
            <div class="parametervalue border_r"><?=$row['parameter_oldvalue']?></div>
            <div class="parametervalue"><?=$row['parameter_newvalue']?></div>            
        </div>        
        <?php $nrow++?>
        <?php } ?>
    </div>
</div>