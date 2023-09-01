<div class="content_tab <?=$brand=='SB' ? 'active' : ''?> stressballs">
    <div class="stressballssubtitle">LIFT</div>
    <div class="brandcontentmenu">
        <?php foreach ($permissions as $menu) { ?>
            <?php if ($menu['item_link']=='/marketing') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active" "><img src="/img/page_view/noun-megaphone-black.svg"/></div>
                        <div class="brandmenutitle active">Marketing</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-megaphone-white.svg"/></div>
                        <div class="brandmenutitle">Marketing</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/leads') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-filter-black.svg"/></div>
                        <div class="brandmenutitle active">Leads</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-filter-white.svg"/></div>
                        <div class="brandmenutitle">Leads</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/orders') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-tick-black.svg"/></div>
                        <div class="brandmenutitle active">Orders</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-tick-white.svg"/></div>
                        <div class="brandmenutitle">Orders</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/art') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-palette-black.svg"/></div>
                        <div class="brandmenutitle active">Art</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-palette-white.svg"/></div>
                        <div class="brandmenutitle">Art</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/fulfillment') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-delivery-black.svg"/></div>
                        <div class="brandmenutitle active">Fulfillment</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-delivery-white.svg"/></div>
                        <div class="brandmenutitle">Fulfillment</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/accounting') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/icon-calculate-black.svg"/></div>
                        <div class="brandmenutitle active">Accounting</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/icon-calculate-white.svg"/></div>
                        <div class="brandmenutitle">Accounting</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/databasecenter') { ?>
                <div class="branmenudevider"></div>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-list-black.svg"/></div>
                        <div class="brandmenutitle active">Database</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-list-white.svg"/></div>
                        <div class="brandmenutitle">Database</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/database') { ?>
                <div class="branmenudevider"></div>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive" data-brand="SB" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-list-black.svg"/></div>
                        <div class="brandmenutitle active">Database</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem" data-url="<?=$menu['item_link']?>" data-brand="SB">
                        <div class="brandmenuicon" data-brand="SB" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-list-white.svg"/></div>
                        <div class="brandmenutitle">Database</div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <div class="brandsubmenu bluetrack" data-brand="SB">&nbsp;</div>
    </div>
</div>
<div class="content_tab <?=$brand=='SR' ? 'active' : ''?> relievers">
    <div class="relieverssubtitle">LIFT</div>
    <div class="brandcontentmenu">
        <?php foreach ($permissions as $menu) { ?>
            <?php if ($menu['item_link']=='/marketing') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-megaphone-black.svg"/></div>
                        <div class="brandmenutitle active">Marketing</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-megaphone-black.svg"/></div>
                        <div class="brandmenutitle">Marketing</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/leads') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-filter-black.svg"/></div>
                        <div class="brandmenutitle active">Leads</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-filter-black.svg"/></div>
                        <div class="brandmenutitle">Leads</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/orders') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-tick-black.svg"/></div>
                        <div class="brandmenutitle active">Orders</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-tick-black.svg"/></div>
                        <div class="brandmenutitle">Orders</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/art') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-palette-black.svg"/></div>
                        <div class="brandmenutitle active">Art</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-palette-black.svg"/></div>
                        <div class="brandmenutitle">Art</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/fulfillment') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-delivery-black.svg"/></div>
                        <div class="brandmenutitle active">Fulfillment</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-delivery-black.svg"/></div>
                        <div class="brandmenutitle">Fulfillment</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/accounting') { ?>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/icon-calculate-black.svg"/></div>
                        <div class="brandmenutitle active">Accounting</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/icon-calculate-black.svg"/></div>
                        <div class="brandmenutitle">Accounting</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/databasecenter') { ?>
                <div class="branmenudevider relievers"></div>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-list-black.svg"/></div>
                        <div class="brandmenutitle active">Database</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-list-black.svg"/></div>
                        <div class="brandmenutitle">Database</div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($menu['item_link']=='/database') { ?>
                <div class="branmenudevider relievers"></div>
                <?php if ($activelnk == $menu['item_link']) { ?>
                    <div class="brandmenuitemactive relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon active"><img src="/img/page_view/noun-list-black.svg"/></div>
                        <div class="brandmenutitle active">Database</div>
                    </div>
                <?php } else { ?>
                    <div class="brandmenuitem relievers" data-url="<?=$menu['item_link']?>" data-brand="SR">
                        <div class="brandmenuicon" data-brand="SR" data-item="<?=$menu['menu_item_id']?>"><img src="/img/page_view/noun-list-black.svg"/></div>
                        <div class="brandmenutitle">Database</div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <div class="brandsubmenu relievers" data-brand="SR">&nbsp;</div>
    </div>
</div>