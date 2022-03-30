<div class="finmenusection">
    <?php foreach ($permissions as $item) { ?>
        <?php if ($item['menu_section']=='finsection') { ?>
            <?php if ($item['item_name']=='Finance') { ?>
                <div class="menuitem finance <?=($activelnk=='/accounting' || $activelnk=='/finances') ? 'activelink' : ''?>" data-menulink="/accounting">
                    <span class="bnt-side">&nbsp;</span>
                    <span class="nameitem">Finance</span>
                    <span class="triangle-top">&nbsp;</span>
                </div>
            <?php } elseif ($item['item_name']=='Finance NEW') { ?>
                <div class="menuitem financenew <?=($activelnk=='/accounting' || $activelnk=='/finances') ? 'activelink' : ''?>" data-menulink="/finances">
                    <span class="triangle-bootom">&nbsp;</span>
                    <span class="nameitem">Finance</span>
                    <span class="bnt-side">&nbsp;</span>
                </div>
            <?php } else { ?>
                <div class="menuitem <?=$item['item_link'] == $activelnk ? 'activelink' : ''?> <?=ifset($item,'newver', 1)==0 ? 'oldver' :  ''?>" data-menulink="<?= $item['item_link'] ?>">
                    <?php  if (ifset($item,'newver', 1)==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <?= $item['item_name'] ?>
                </div>
            <?php } ?>
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
