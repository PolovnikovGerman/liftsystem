<div class="row">
    <ul class="nav nav-tabs" id="mainmenutabs">
        <?php foreach ($permissions as $item) { ?>
            <?php if (!empty($item['item_link']) && $item['item_link'] == $activelnk) { ?>
                <li class="activemenubutton <?= $item['menu_section'] ?>"
                    data-menulink="<?= $item['item_link'] ?>"><?= $item['item_name'] ?></li>
            <?php } else { ?>
                <li class="menubutton <?= $item['menu_section'] ?>"
                    data-menulink="<?= $item['item_link'] ?>"><?= $item['item_name'] ?></li>
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