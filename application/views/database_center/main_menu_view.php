<div class="dbcenter_main_menu">
    <?php foreach ($menus as $menu) { ?>
        <div class="dbcenter-main-button <?=$start==$menu['item_link'] ? 'active' : ''?>"  data-link="<?=str_replace('#','', $menu['item_link'])?>"><?=$menu['item_name']?></div>
    <?php } ?>
</div>
