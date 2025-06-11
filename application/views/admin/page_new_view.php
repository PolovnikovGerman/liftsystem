<?=$menu_view?>
<div class="contentdata_view">
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
</div>
