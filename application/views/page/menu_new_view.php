<div class="row">
    <div class="finmenusection">
        <?php foreach ($permissions as $item) { ?>
            <?php if ($item['menu_section']=='finsection') { ?>
                <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?>" data-menulink="<?= $item['item_link'] ?>"><?= $item['item_name'] ?></div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="marketmenusection">
        <?php foreach ($permissions as $item) { ?>
            <?php if ($item['menu_section']=='marketsection') { ?>
                <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?>" data-menulink="<?= $item['item_link'] ?>"><?= $item['item_name'] ?></div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="contentmenusection">
        <?php foreach ($permissions as $item) { ?>
            <?php if ($item['menu_section']=='databasesection') { ?>
                <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?>" data-menulink="<?= $item['item_link'] ?>"><?= $item['item_name'] ?></div>
            <?php } ?>
        <?php } ?>
    </div>
</div>