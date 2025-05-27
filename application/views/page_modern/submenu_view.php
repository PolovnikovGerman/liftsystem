<div class="contentsubmenu">
    <?php foreach ($menu as $item) : ?>
        <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>" data-link="<?=str_replace('#','', $item['item_link'])?>">
            <?=$item['item_name']?>
        </div>
    <?php endforeach; ?>
</div>
