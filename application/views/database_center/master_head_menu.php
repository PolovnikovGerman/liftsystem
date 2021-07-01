<div class="mastermenu_head">
    <div class="menulabel">MASTER</div>
    <div class="mastermenu_items">
        <?php foreach ($menu as $mrow) { ?>
            <div class="headmenuitem <?=$start==str_replace('#', '',$mrow['item_link']) ? 'active' : ''?> "><?=$mrow['item_name']?></div>
        <?php } ?>
    </div>
    <div class="returndbcenter">
        <img src="/img/database_center/return_to_mainscreen.png" alt="'Back Main Screen"/>
    </div>
</div>