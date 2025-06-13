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
                <div class="subitem-name">Print Shop Report</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="subitemsbody">
        <div class="container-xl">&nbsp;</div>
    </div>
</div>
