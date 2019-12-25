<?php foreach ($permissions as $item) { ?>
    <div class="menubutton <?=$item['menu_section']?>" data-menulink="<?=$item['item_link']?>"><?=$item['item_name']?></div>
<?php } ?>