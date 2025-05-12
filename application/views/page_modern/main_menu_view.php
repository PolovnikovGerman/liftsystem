<?php foreach ($permissions as $menu) : ?>
    <?php if ($menu['item_link']=='/analytics') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/chart-line.svg" alt="Reports" class="img-responsive"/>
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/chart-line-grey.svg" alt="Reports" class="img-responsive"/>
                    <?php else : ?>
                        <img src="/img/page_view/chart-line-white.svg" alt="Reports" class="img-responsive"/>
                    <?php endif;?>
                <?php endif ?>
            </div>
            <div class="maincontentmenu_item_title">Reports</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/marketing') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/noun-megaphone-black.svg" alt="Marketing">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/noun-megaphone-grey-2.svg" alt="Marketing">
                    <?php else : ?>
                        <img src="/img/page_view/noun-megaphone-white.svg" alt="Marketing">
                    <?php endif;?>
                <?php endif ?>
            </div>
            <div class="maincontentmenu_item_title">Marketing</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/leads') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/noun-filter-black.svg" alt="Leads">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/noun-filter-grey-2.svg" alt="Leads">
                    <?php else: ?>
                        <img src="/img/page_view/noun-filter-white.svg" alt="Leads">
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Leads</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/orders') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/noun-tick-black.svg" alt="Orders">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/noun-tick-grey-2.svg" alt="Orders">
                    <?php else: ?>
                        <img src="/img/page_view/noun-tick-white.svg" alt="Orders">
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Orders</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/art') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/noun-palette-black.svg" alt="Art">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/noun-palette-grey-2.svg" alt="Art">
                    <?php else: ?>
                        <img src="/img/page_view/noun-palette-white.svg" alt="Art">
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Art</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/fulfillment') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/noun-delivery-black.svg" alt="Fulfillment">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/noun-delivery-grey-2.svg" alt="Fulfillment">
                    <?php else: ?>
                        <img src="/img/page_view/noun-delivery-white.svg" alt="Fulfillment">
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Fulfillment</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/accounting') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/icon-calculate-black.svg" alt="Accounting">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/icon-calculate-grey-2.svg" alt="Accounting">
                    <?php else: ?>
                        <img src="/img/page_view/icon-calculate-white.svg" alt="Accounting">
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Accounting</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/database') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/noun-list-black.svg" alt="Database">
                <?php else : ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/noun-list-grey-2.svg" alt="Database">
                    <?php else: ?>
                        <img src="/img/page_view/noun-list-white.svg" alt="Database">
                    <?php endif ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Database</div>
        </div>
    <?php endif; ?>
    <?php if ($menu['item_link']=='/projects') : ?>
        <div class="maincontentmenu_item <?=$brandclass?> <?=$activelnk==$menu['item_link'] ? 'active' : ''?>" data-url="<?=$menu['item_link']?>" data-brand="<?=$brand?>">
            <div class="maincontentmenu_item_icon">
                <?php if ($activelnk == $menu['item_link']) : ?>
                    <img src="/img/page_view/icon-projects-black.svg" alt="Projects">
                <?php else: ?>
                    <?php if ($brand=='SR') : ?>
                        <img src="/img/page_view/icon-projects-grey-2.svg" alt="Projects">
                    <?php else: ?>
                        <img src="/img/page_view/icon-projects-white.svg" alt="Projects">
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <div class="maincontentmenu_item_title">Projects</div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
