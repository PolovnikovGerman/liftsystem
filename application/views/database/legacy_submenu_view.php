<?php foreach ($menus as $menu) { ?>
    <div class="summenu_item" data-link="<?=str_replace('#', '', $menu['item_link'])?>"><?=$menu['item_name']?></div>
<?php } ?>
