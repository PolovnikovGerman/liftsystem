<?php foreach ($permissions as $item) { ?>
    <?php if ($item['item_link']==$activelnk) { ?>
        <div class="activemenubutton <?=$item['menu_section']?>" data-menulink="<?=$item['item_link']?>"><?=$item['item_name']?></div>
    <?php } else { ?>
        <div class="menubutton <?=$item['menu_section']?>" data-menulink="<?=$item['item_link']?>"><?=$item['item_name']?></div>
    <?php } ?>

<?php } ?>