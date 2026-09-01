<?=$menu_view?>
<div class="contentdata_view">
    <div class="datarow">
        <div class="pagemenurow <?=$brandclass?>" style="display: <?=$showhidemenu==1 ? 'block' : 'none'?>">
            <div class="linkshowmainmenu"><i class="fa fa-chevron-down"></i> Open Menu</div>
        </div>
    </div>
    <?php if (isset($usersview)) { ?>
        <div class="admincontentarea" id="usersview" style="display: none;"><?=$usersview?></div>
    <?php } ?>
    <?php if (isset($parseremailsview)) { ?>
        <div class="admincontentarea" id="parseremailsview" style="display: none;"><?=$parseremailsview?></div>
    <?php } ?>
    <?php if (isset($artalertsview)) { ?>
        <div class="admincontentarea" id="artalertsview" style="display: none;"><?=$artalertsview?></div>
    <?php } ?>
    <?php if (isset($calendarsview)) { ?>
        <div class="admincontentarea" id="calendarsview" style="display: none;"><?=$calendarsview?></div>
    <?php } ?>
    <?php if (isset($customprices)) : ?>
        <div class="admincontentarea" id="customprices" style="display: none;"><?=$customprices?></div>
    <?php endif;?>
</div>
