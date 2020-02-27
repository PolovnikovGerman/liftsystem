<?php $numpp=0;?>
<?php foreach ($data as $row) { ?>
    <div class="inventorydatarow <?= $row['type'] == 'item' ? 'itemdata' : ($numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow') ?>" data-item="<?= $row['printshop_item_id'] ?>" data-color="<?=$row['printshop_color_id']?>">
        <div class="numpp">
            <?php if ($row['type']=='item') { ?>
                <div class="edititem" data-item="<?= $row['printshop_item_id'] ?>">
                    <i class="fa fa-pencil"></i>
                </div>
            <?php } else { ?>
                <?= $row['numpp'] ?>
            <?php } ?>
        </div>
        <div class="itemnum <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?php if ($row['type']=='item') { ?>
                <?= $row['item_num'] ?>
            <?php } else { ?>
                <div class="editcolor" data-color="<?=$row['printshop_color_id']?>" data-item="<?= $row['printshop_item_id'] ?>">
                    <i class="fa fa-pencil"></i>
                </div>
            <?php } ?>
        </div>
        <?php if ($row['type'] == 'item') { ?>
            <div class="itemname"><?= $row['item_name'] ?></div>
            <div class="additemcolor" data-item="<?= $row['printshop_item_id'] ?>">&nbsp;</div>
        <?php } else { ?>
            <div class="donotreorder <?=$row['notreorder']==1 ? 'filled' : ''?>"><?=$row['notreorder']==1 ? 'Do Not Reorder' : '&nbsp;'?></div>
            <div class="coloritemname" data-color="<?=$row['printshop_color_id']?>"><?= $row['item_name'] ?></div>
        <?php } ?>
        <div class="itempercent <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? 'border_b' : '') ?>" <?=$row['percenturl']?>>
            <?= $row['percent'] ?>
        </div>
        <div class="maxinvent">
            <?=$row['max']?>
        </div>
        <div class="instock <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?=$row['instock']?>
        </div>
        <div class="reserved <?=($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?= $row['reserved'] ?>
        </div>
        <div class="available <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? '' : '') ?>">
            <?=$row['availabled']?>
        </div>
    </div>
    <?php if($row['type']=='color') $numpp++;?>
<?php } ?>
