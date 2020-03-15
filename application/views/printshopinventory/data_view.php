<?php foreach ($inventory as $row) { ?>
    <?= $row['item'] ?>
    <?php foreach ($row['colors'] as $color) { ?>
        <?= $color ?>
    <?php } ?>
<?php } ?>
