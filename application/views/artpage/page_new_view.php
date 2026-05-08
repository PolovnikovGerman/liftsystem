<?=$menu_view?>
<div class="contentdata_view">
    <div class="datarow">
        <div class="pagemenurow <?=$brandclass?>" style="display: <?=$showhidemenu==1 ? 'block' : 'none'?>">
            <div class="linkshowmainmenu"><i class="fa fa-chevron-down"></i> Open Menu</div>
        </div>
    </div>
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
