<?=$menu_view?>
<div class="contentdata_view">
    <?php if ($showhidemenu==1) : ?>
        <div class="datarow">
            <div class="pagemenurow <?=$brandclass?>">
                <div class="linkshowmainmenu"><i class="fa fa-chevron-down"></i> Open Menu</div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($taskview)) { ?>
        <div class="artcontentarea" id="taskview" style="display: none;"><?=$taskview?></div>
    <?php } ?>
    <?php if (isset($orderlist)) { ?>
        <div class="artcontentarea" id="orderlist" style="display: none;"><?=$orderlist?></div>
    <?php } ?>
    <?php if (isset($requestlist)) { ?>
        <div class="artcontentarea" id="requestlist" style="display: none;"><?=$requestlist?></div>
    <?php } ?>
</div>
