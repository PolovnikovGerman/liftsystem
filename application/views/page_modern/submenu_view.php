<div class="contentsubmenu">
    <?php foreach ($menu as $item) : ?>
        <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
             data-link="<?=str_replace('#','', $item['item_link'])?>">
            <?php if ($item['item_link']=='#customsbform') : ?>
                <div class="newcustomformsinfo"><?=$customforms?></div>
            <?php endif; ?>
            <?=$item['item_name']?>
        </div>
    <?php endforeach; ?>
</div>
