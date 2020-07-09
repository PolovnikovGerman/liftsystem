<div class="row">
    <ul class="nav nav-tabs" id="mainmenutabs">
        <?php foreach ($permissions as $item) { ?>
            <?php if (!empty($item['item_link']) && $item['item_link'] == $activelnk) { ?>
                <li class="activemenubutton <?= $item['menu_section'] ?>"
                    data-menulink="<?= $item['item_link'] ?>"><?= $item['item_name'] ?></li>
            <?php } else { ?>
                <li class="mainmenubutton <?= $item['menu_section'] ?> <?=$item['submenus']>0 ? 'hasdropdownmenu' : 'singlemenuitem'?>"
                    data-menulink="<?= $item['item_link'] ?>" data-menuid="<?=$item['menu_item_id']?>"><?= $item['item_name'] ?></li>
                <?php if ($item['submenus']>0) { ?>
                    <div class="dropdown-menu" id="dropdown-<?=$item['menu_item_id']?>">
                        <?php foreach ($item['submenu'] as $subitem) { ?>
                            <li class="menubutton <?=$item['menu_section']?>" data-menulink="<?=$subitem['item_link']?>"><?=$subitem['item_name'] ?></li>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <li id="lastTab" class="mainmenulasttab">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                More <span class="caret"></span>
            </a>
            <div class="dropdown-menu" id="collapsed"></div>
        </li>
    </ul>
</div>