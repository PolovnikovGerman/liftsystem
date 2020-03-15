<?php $numpp=0;?>
<?php foreach ($data as $row) { ?>
    <div class="inventorydatarow <?=($numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow') ?>" data-color="<?=$row['printshop_color_id']?>">
        <div class="numpp">
            <?= $row['numpp'] ?>
        </div>
        <div class="itemnum">
            <?= $row['item_num'] ?>
        </div>        
        <div class="itemname"><?= $row['item_name'] ?></div>
        <div class="coloritemname" data-color="<?=$row['printshop_color_id']?>"><?= $row['color'] ?></div>
        <div class="maxinvent">
            <?=QTYOutput($row['suggeststock'])?>
        </div>
        <div class="instock"><?=QTYOutput($row['instock'])?></div>
    </div>
    <?php $numpp++;?>
<?php } ?>
