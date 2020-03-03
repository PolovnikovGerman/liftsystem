<?php $numpp = 0 ?>
<?php foreach ($data as $row) { ?>
    <div class="inventorydatarow <?= $numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow' ?> <?= $row['type'] == 'item' ? 'itemdata' : '' ?>" data-item="<?= $row['printshop_item_id'] ?>" data-color="<?=$row['printshop_color_id']?>">
        <div class="specs <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?php if ($row['type']=='item') { ?>
                &nbsp;
            <?php } else { ?>
                <div class="specsdata <?=$row['specsclass']?>" <?=$row['specsurl']?> data-color="<?=$row['printshop_color_id']?>">
                    <i class="fa fa-file-text-o <?=$row['specsclass']?>" aria-hidden="true"></i>
                </div>
            <?php } ?>
        </div>
        <div class="plate_temp <?= ($row['type'] == 'item' ? 'border_b' : '') ?>" name="platetemp">
            <?php if ($row['type']=='item') { ?>
                <div class="platetempdata <?=(empty($row['plate_temp']) ? '' : 'full')?>" data-color="<?=$row['printshop_item_id']?>">
                    <i class="fa fa-file-text-o <?=$row['platetemp'] ?>" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                &nbsp;
            <?php } ?>
        </div>
        <div class="proof_temp <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?php if ($row['type']=='item') { ?>
                <div class="prooftempdata <?=(empty($row['proof_temp']) ? '' : 'full')?>" data-color="<?=$row['printshop_item_id']?>">
                    <i class="fa fa-file-text-o <?=$row['prooftemp']?>" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                &nbsp;
            <?php } ?>
        </div>
        <div class="draw <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?php if ($row['type']=='item') { ?>
                <div class="itemlabel <?=(empty($row['item_label']) ? '' : 'full')?>" data-color="<?=$row['printshop_item_id']?>">
                    <i class="fa fa-file-text-o <?=$row['itemlabel']?>" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                &nbsp;
            <?php } ?>
        </div>        
        <div class="pics <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
            <?php if ($row['type']=='item') { ?>
                &nbsp;
            <?php } else { ?>
                <div class="picsdata" data-color="<?=$row['printshop_color_id']?>">
                    <i class="fa fa-file-text-o <?= $row['picsclass'] ?>" aria-hidden="true"></i>
                </div>
            <?php } ?>
        </div>        
    </div>
    <?php if($row['type']=='color') $numpp++;?>
<?php } ?>