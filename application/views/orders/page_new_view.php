<?=$menu_view?>
<div class="contentdata_view">
    <?php if ($showhidemenu==1) : ?>
        <div class="datarow">
            <div class="pagemenurow <?=$brandclass?>">
                <div class="linkshowmainmenu"><i class="fa fa-chevron-down"></i> Open Menu</div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($ordersview)) { ?>
        <div class="orderscontentarea" id="ordersview" style="display: none;"><?=$ordersview?></div>
    <?php } ?>
    <?php if (isset($orderlistsview)) { ?>
        <div class="orderscontentarea" id="orderlistsview" style="display: none;"><?=$orderlistsview?></div>
    <?php } ?>
    <?php if (isset($onlineordersview)) { ?>
        <div class="orderscontentarea" id="onlineordersview" style="display: none;"><?=$onlineordersview?></div>
    <?php } ?>
</div>
