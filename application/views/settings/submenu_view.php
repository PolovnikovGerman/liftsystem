<?php foreach ($menus as $menu) { ?>
    <div class="submenu_item" data-link="<?=str_replace('#', '', $menu['item_link'])?>" data-brand="<?=$brand?>"><?=$menu['item_name']?></div>
<?php } ?>
