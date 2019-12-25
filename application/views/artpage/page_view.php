<div class="maincontent">
    <div class="maincontentmenuarea marketmenu">
        <div class="maincontentmenu">
            <?php foreach ($menu as $item) { ?>
                <div class="maincontentmenu_item" data-link="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
            <?php } ?>
        </div>
    </div>
    <div class="maincontent_view">
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
</div>
