<div class="submenu_content">
    <?php foreach ($items as $item) { ?>
        <div class="content-row">
            <div class="submenuitem <?=$brand=='SR' ? 'relievers' : $brand=='SG' ? 'sigma' : 'bluetrack'?>" data-url="<?=$url?><?=str_replace('#','?start=', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        </div>
    <?php } ?>
</div>