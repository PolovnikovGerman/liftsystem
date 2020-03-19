<?php if (count($item_dat) == 0) { ?>
    <div class="whitedatarow">
        <div class="emptymissinfodata">No records</div>
    </div>
<?php } else { ?>
    <?php $n_row = $offset + 1; ?>
    <?php foreach ($item_dat as $row) { ?>
        <div class="<?=($n_row % 2 == 0 ? 'greydatarow' : 'whitedatarow') ?> missinfodatarow">
            <div class="misinfocell numinlist"><?= $n_row ?></div>
            <div class="misinfocell editcoll" data-item="<?=$row['item_id']?>"><i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i></div>
            <div class="misinfocell itemnum"><?= $row['item_number'] ?></div>
            <div class="misinfocell overflowtext itemtitle" data-content="<?=$row['item_name']?>"><?= $row['item_name'] ?></div>
            <div class="misinfocell missingdata">
                <?php $ind_miss = 0; ?>
                <?php foreach ($row['missings'] as $miss) { ?>
                    <div class="misinfo_dat"><?= $miss['type'] ?></div>
                    <?php $ind_miss++; ?>
                    <?php if ($ind_miss == 10) { ?>
                        <?php $less = count($row['missings']);
                        $less = $less - $ind_miss ?>
                        <div>+ <?= $less; ?> more</div>
                        <?php break; ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>
