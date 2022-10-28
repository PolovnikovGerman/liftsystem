<div class="finmenusection">
    <?php foreach ($permissions as $item) { ?>
        <?php if ($item['menu_section']=='finsection') { ?>
            <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-menulink="<?= $item['item_link'] ?>">
                <?php  if (ifset($item,'newver', 1)==0) { ?>
                    <div class="oldvesionlabel">&nbsp;</div>
                <?php } ?>
                <?= $item['item_name'] ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<div class="marketmenusection">
    <?php foreach ($permissions as $item) { ?>
        <?php if ($item['menu_section']=='marketsection') { ?>
            <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-menulink="<?= $item['item_link'] ?>">
                <?php  if (ifset($item,'newver', 1)==0) { ?>
                    <div class="oldvesionlabel">&nbsp;</div>
                <?php } ?>
                <?= $item['item_name'] ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<div class="contentmenusection">
    <?php foreach ($permissions as $item) { ?>
        <?php if ($item['menu_section']=='databasesection') { ?>
            <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-menulink="<?= $item['item_link'] ?>">
                <?php  if (ifset($item,'newver', 1)==0) { ?>
                    <div class="oldvesionlabel">&nbsp;</div>
                <?php } ?>
                <?= $item['item_name'] ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
