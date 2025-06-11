<?=$menu_view?>
<div class="contentdata_view">
    <?php if (isset($searchestimeview)) { ?>
        <div class="marketingcontentarea" id="searchestimeview" style="display: none;"><?= $searchestimeview ?></div>
    <?php } ?>
    <?php if (isset($searcheswordview)) { ?>
        <div class="marketingcontentarea" id="searcheswordview" style="display: none"><?=$searcheswordview?></div>
    <?php } ?>
    <?php if (isset($searchesipadrview)) { ?>
        <div class="marketingcontentarea" id="searchesipadrview" style="display: none"><?=$searchesipadrview?></div>
    <?php } ?>
    <?php if (isset($signupview)) { ?>
        <div class="marketingcontentarea" id="signupview" style="display: none"><?=$signupview?></div>
    <?php } ?>
    <?php if (isset($couponsview)) { ?>
        <div class="marketingcontentarea" id="couponsview" style="display: none"><?=$couponsview?></div>
    <?php } ?>
    <?php if (isset($searchesview)) { ?>
        <div class="marketingcontentarea" id="searchesview" style="display: none"><?=$searchesview?></div>
    <?php } ?>
</div>
