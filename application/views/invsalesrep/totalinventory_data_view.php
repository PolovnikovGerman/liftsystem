<?php $numpp=0;?>
<?php foreach ($data as $row) { ?>
    <div class="inventorydatarow <?= $row['type'] == 'item' ? 'itemdata' : ($numpp % 2 == 0 ? 'itemcolordata whitedatarow' : 'itemcolordata greydatarow') ?>" data-item="<?= $row['printshop_item_id'] ?>" data-color="<?=$row['printshop_color_id']?>">
        <div class="numpp">
            <?php if ($row['type']=='item') { ?>
            &nbsp;
            <?php } else { ?>
                <?= $row['numpp'] ?>
            <?php } ?>
        </div>
        <div class="itemnum <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?php if ($row['type']=='item') { ?>
                <?= $row['item_num'] ?>
            <?php } ?>
        </div>
        <?php if ($row['type'] == 'item') { ?>
            <div class="itemname"><?= $row['item_name'] ?></div>
        <?php } else { ?>
            <div class="donotreorder <?=$row['notreorder']==1 ? 'filled' : ''?>"><?=$row['notreorder']==1 ? 'Do Not Reorder' : '&nbsp;'?></div>
            <div class="coloritemname" data-color="<?=$row['printshop_color_id']?>"><?= $row['item_name'] ?></div>
        <?php } ?>
        <div class="instock <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?=$row['instock']?>
        </div>
        <div class="reserved <?=($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?= $row['reserved'] ?>
        </div>
        <div class="available <?=$row['stockclass']?>">
            <?=$row['availabled']?>
        </div>
    </div>
    <?php if($row['type']=='color') $numpp++;?>
<?php } ?>
