<?=$menu_view?>
<div class="contentdata_view">
    <div class="datarow">
        <div class="pagemenurow <?=$brandclass?>" style="display: <?=$showhidemenu==1 ? 'block' : 'none'?>">
            <div class="linkshowmainmenu"><i class="fa fa-chevron-down"></i> Open Menu</div>
        </div>
    </div>
    <?php if (isset($projectsview)) { ?>
        <div class="projcontentarea" id="projectsview" style="display: none;"><?=$projectsview?></div>
    <?php } ?>
</div>