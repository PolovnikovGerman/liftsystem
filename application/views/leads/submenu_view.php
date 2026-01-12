<div class="contentsubmenu">
    <?php // Leads ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#leadsview') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php // Quotes ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#leadquotes') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php // Proof Requests ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#proofrequestsview') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="contentsubmenu_devider">&nbsp;</div>
    <?php // SB Custom Forms ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#customsbform') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <div class="newcustomformsinfo"><?=$customforms?></div>
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php // Web Questions ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#questionsview') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <div class="newwebquestioninfo"><?=$webquestions?></div>
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php // Web Quotes ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#onlinequotesview') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <div class="newwebquotesinfo"><?=$webquotes?></div>
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php // Checkout Attempts ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#checkoutattemptsview') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="contentsubmenu_devider">&nbsp;</div>
    <?php // Items List ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#itemslistview') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php // Custom orders ?>
    <?php foreach ($menu as $item) : ?>
        <?php if ($item['item_link']=='#customorders') : ?>
            <div class="contentsubmenu_item <?=$brandclass?> <?=$start==str_replace('#','', $item['item_link']) ? 'active' : ''?>"
                 data-link="<?=str_replace('#','', $item['item_link'])?>">
                <?=$item['item_name']?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>