<div class="option_title">Options:</div>
<div class="option_name"><?=$option?></div>
<div class="options_data">
    <div class="colorsdat-info">
        <?php foreach ($colors as $row) { ?>
            <div class="colorrow" title="<?= $row['item_color'] ?>">
                <?= $row['item_color'] ?>
            </div>
        <?php } ?>
    </div>
</div>