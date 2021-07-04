<div class="channelmenu_head">
    <div class="menulabel <?=$brand?>">&nbsp;</div>
    <div class="channelmenu_items">
        <?php foreach ($menu as $mrow) { ?>
            <div class="headmenuitem <?=$start==str_replace('#', '',$mrow['item_link']) ? 'active' : ''?>" data-lnk="<?=str_replace('#', '',$mrow['item_link'])?>"><?=$mrow['item_name']?></div>
        <?php } ?>
    </div>
    <div class="returndbcenter">
        <img src="/img/database_center/return_to_mainscreen.png" alt="'Back Main Screen"/>
    </div>
</div>