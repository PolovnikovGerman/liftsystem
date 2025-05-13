<?=$menu_view?>
<div class="contentdata_view">
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
