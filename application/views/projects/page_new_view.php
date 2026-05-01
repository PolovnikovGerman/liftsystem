<?=$menu_view?>
<div class="contentdata_view">
    <?php if ($showhidemenu==1) : ?>
        <div class="datarow">
            <div class="pagemenurow <?=$brandclass?>">
                <div class="linkshowmainmenu"><i class="fa fa-chevron-down"></i> Open Menu</div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($projectsview)) { ?>
        <div class="projcontentarea" id="projectsview" style="display: none;"><?=$projectsview?></div>
    <?php } ?>
</div>