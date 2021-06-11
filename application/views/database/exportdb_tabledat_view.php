<?php $nrow=0; ?>
<?php foreach ($items as $row) {?>
    <div class="dbitems_row <?=($nrow%2==0 ? 'grey' : '')?>">
        <div class="dbitem_number"><?=$row['item_number']?></div>
        <div class="dbitem_name"><?=$row['item_name']?></div>
        <div class="dbitem_template"><?=$row['item_template']?></div>
        <div class="dbitem_new"><?=($row['item_new']==1 ? 'Yes' : 'No')?></div>
        <div class="dbitem_vendor"><?=($row['vendor_name'])?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>