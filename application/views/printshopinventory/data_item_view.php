<?php $numpp = 0 ?>
<div class="inventorydatarow <?= $numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow' ?> <?= $row['type'] == 'item' ? 'itemdata' : '' ?>" data-item="<?= $row['printshop_item_id'] ?>" data-color="<?=$row['printshop_color_id']?>">
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
        <div class="coloritemname" data-color="<?=$row['printshop_color_id']?>"><?= $row['item_name'] ?></div>
    <?php } ?>
    <div class="itempercent <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? 'border_b' : '') ?>" <?=$row['percenturl']?>><?= $row['percent'] ?></div>
    <div class="instock <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? 'border_b' : '') ?>"><?= QTYOutput($row['instock']) ?></div>
    <div class="reserved <?= ($row['type'] == 'item' ? 'border_b' : '') ?>"><?= $row['reserved'] ?></div>
    <div class="available <?=$row['stockclass']?> <?= ($row['type'] == 'item' ? 'border_b' : '') ?>"><?= ($row['availabled'] == 0) ? '&nbsp;' : QTYOutput($row['availabled']) ?></div>
    <div class="devider <?= ($row['type'] == 'item' ? 'border_b' : '') ?> left_arrow">&nbsp;</div>
    <div class="onboatarea">
        <div class="after_head" style="width: <?= $width_bottom ?>px; margin-right: <?= $margin_bottom ?>px;">
            <?php /*if(empty($boats)) { */?><!--
                <div class="on_route total_item">&nbsp;</div>
                <div class="devider <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">&nbsp;</div>
                <div class="add_boat" id="total_onboat_item<?/*= $row['printshop_item_id'] */?>" data-item="<?/*= $row['printshop_item_id'] */?>"></div>
            --><?php /*} else { */?>
                <?php foreach ($boats as $key => $boat) { ?>
                    <?php if($boat_status[$key]['status'] == 1) { ?>
                        <div class="on_route total_item arrived_data" data-onboat="<?= $key ?>"><?= ($boat == '') ? '&nbsp;' : QTYOutput($boat) ?></div>
                        <div class="edit_onboatcol" data-editonboat="<?= $key ?>" style="text-align: center" id="total_onboat_item<?= $row['printshop_item_id'] ?>" data-item="<?= $row['printshop_item_id'] ?>"><?= ($boat == '') ? '&nbsp;' : QTYOutput($boat) ?></div>
                        <div class="devider <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">&nbsp;</div>
                    <?php } else { ?>
                        <div class="on_route total_item" data-onboat="<?= $key ?>"><?= ($boat == '') ? '&nbsp;' : QTYOutput($boat) ?></div>
                        <div class="edit_onboatcol" data-editonboat="<?= $key ?>" style="text-align: center" id="total_onboat_item<?= $row['printshop_item_id'] ?>" data-item="<?= $row['printshop_item_id'] ?>"><?= ($boat == '') ? '&nbsp;' : QTYOutput($boat) ?></div>
                        <div class="devider <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">&nbsp;</div>
                    <?php } ?>
                <?php } ?>
                <div class="add_boat" id="total_onboat_item<?= $row['printshop_item_id'] ?>" data-item="<?= $row['printshop_item_id'] ?>"></div>
            <?php /*} */?>
        </div>
    </div>
    <div class="devider <?= ($row['type'] == 'item' ? 'border_b' : '') ?> right_arrow">&nbsp;</div>
    <?php if($permission == "Profit") { ?>
        <div class="costea <?= ($row['type'] == 'item' ? 'border_b' : '') ?>"><?= $row['price'] ?></div>
        <div class="totalea <?= ($row['type'] == 'item' ? 'border_b' : '') ?>"><?= $row['total'] ?></div>
        <div class="devider <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">&nbsp;</div>
    <?php } else { ?>
        <div class="costea" style="display: none;">&nbsp;</div>
        <div class="totalea" style="display: none;">&nbsp;</div>
    <?php } ?>
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
            <div class="platetempdata" data-color="<?=$row['printshop_item_id']?>">
                <i class="fa fa-file-text-o <?= $row['platetemp'] ?>" aria-hidden="true"></i>
            </div>
        <?php } else { ?>
            &nbsp;
        <?php } ?>
    </div>
    <div class="proof_temp <?= ($row['type'] == 'item' ? 'border_b' : '') ?>">
        <?php if ($row['type']=='item') { ?>
            <div class="prooftempdata" data-color="<?=$row['printshop_item_id']?>">
                <i class="fa fa-file-text-o <?= $row['prooftemp'] ?>" aria-hidden="true"></i>
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
    <!--<div class="colororder <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['color_order'] */?></div>
        <div class="colordesript <?/*=($row['type'] == 'item' ? 'border_b' : '')*/?>"><?/*=$row['color_descript']*/?></div>-->
</div>
<?php $numpp++; ?>