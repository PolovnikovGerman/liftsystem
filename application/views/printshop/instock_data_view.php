<?php $numpp=0;?>
<?php foreach ($data as $row) {?>
<div class="stokdatarow <?=($numpp%2==0 ? 'white' : 'grey')?>" data-stock="<?=$row['printshop_instock_id']?>">    
    <?php if ($row['instok_type']=='S') { ?>
            <div class="editstock" data-stock="<?=$row['printshop_instock_id']?>"><i class="fa fa-pencil"></i></div>
        <?php } else { ?>
            <div class="viewstock">&nbsp;</div>            
        <?php } ?>            
    <div class="stockdate"><?=$row['outstockdate']?></div>
    <div class="stockdescr <?=$row['order_class']?>" data-order="<?=$row['order_id']?>"><?=$row['instock_descrip']?></div>
    <div class="stockamnt <?=$row['amntclass']?>"><?=$row['outamnt']?></div>
    <div class="stockbalance <?=$row['balanceclass']?>"><?=$row['balance']?></div>    
</div>
<?php $numpp++;?>
<?php } ?>
<?php if ($numpp<14) { ?>
    <?php for($i=$numpp; $i<14; $i++) { ?>
    <div class="stokdatarow <?=($i%2==0 ? 'white' : 'grey')?>">
        <div class="editstock">&nbsp;</div>
        <div class="stockdate">&nbsp;</div>
        <div class="stockdescr">&nbsp;</div>
        <div class="stockamnt">&nbsp;</div>
        <div class="stockbalance">&nbsp;</div>    
    
    </div>    
    <?php } ?>
<?php } ?>