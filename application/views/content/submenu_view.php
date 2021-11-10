<?php foreach ($menus as $menu) { ?>
    <div class="row">
        <div class="col-12">
            <div class="submenu_item" data-link="<?=str_replace('#', '', $menu['item_link'])?>" data-brand="<?=$brand?>"><?=$menu['item_name']?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="submenu_manage" data-link="<?=str_replace('#', '', $menu['item_link'])?>" data-brand="<?=$brand?>">
                <div class="submenu_label">View Mode</div>
                <div class="row">
                    <div class="col-12">
                        <div class="buttons">
                            <div class="edit_button" data-link="<?=str_replace(['#',strtolower($brand)], '', $menu['item_link'])?>" data-brand="<?=$brand?>">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
