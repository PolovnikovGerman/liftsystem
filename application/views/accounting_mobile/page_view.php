<div class="subitems-contant">
    <div class="container-xl">
        <div class="subitemsmenu d-flex flex-row-reverse">
            <div class="subitem-dropdown dropdown">
                <?php if (!empty($submenu)) : ?>
                    <?=$submenu?>
                <?php endif; ?>
            </div>
            <?php if (!empty($activelink)) : ?>
                <div class="subitem-active">
                    <div class="subitem-name"><?=$active_label?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="subitemsbody">
        <div class="container-xl">
            <?php foreach ($content_views as $content_view) :?><?=$content_view?><?php endforeach; ?>
        </div>
    </div>
</div>
