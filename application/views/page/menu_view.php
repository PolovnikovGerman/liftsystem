<div class="row">
    <ul class="navbar-nav mainmenu" id="mainmenutabs">
        <?php foreach ($permissions as $item) { ?>
            <?php if (!empty($item['item_link']) && $item['item_link'] == $activelnk) { ?>
                <li class="activelink">
                    <a class="nav-link activelink <?= $item['menu_section'] ?>" data-menulink="<?= $item['item_link'] ?>"
                       href="javascript:void(0);">
                        <?= $item['item_name'] ?>
                    </a>
                </li>
            <?php } else { ?>
                <?php if ($item['submenus']>0) { ?>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?=$item['menu_section']?>" href="#" id="navbarDropdown-<?=$item['menu_item_id']?>"
                       role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$item['item_name']?></a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown-<?=$item['menu_item_id']?>">
                        <?php foreach ($item['submenu'] as $subitem) { ?>
                            <a class="dropdown-item <?= $item['menu_section'] ?>"
                               href="<?=$item['item_link']?>/?start=<?=str_replace('#','', $subitem['item_link'])?>"><?= $subitem['item_name'] ?></a>
                        <?php } ?>
                    </div>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                    <a class="nav-link <?= $item['menu_section'] ?>" data-menulink="<?= $item['item_link'] ?>"
                       href="<?= $item['item_link'] ?>">
                        <?= $item['item_name'] ?>
                    </a>
                    </li>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <li id="lastTab" class="nav-item mainmenulasttab">
            <a class="test" href="#" id="morebtn" >
                More <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" id="collapsed"></ul>
        </li>
    </ul>
</div>