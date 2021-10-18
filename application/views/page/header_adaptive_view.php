<div class="container-fluid pr-0 pl-0">
    <div class="mainheader">
        <div class="row">
            <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 pr-0">
                <div class="lm-brands-logos">
                    <div class="bluetrack_logo">
                        <img src="/img/page_view/bluetrack_logo_new.png">
                    </div>
                    <div class="lift_logo">
                        <img src="/img/page_view/lift_logo_new.png">
                    </div>
                </div>
                <?php if ($reportchk) { ?>
                    <div class="inforeports" id="reports">
                        <div class="icon">
                            <img src="/img/icons/chart-line-white.svg" class="img-responsive"/>
                        </div>
                        <div class="infotext">Reports</div>
                    </div>
                <?php } ?>
                <div class="lm-period_analitic_info desctopadminmenu">
                    <?=$total_view?>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 pl-0">
                <div class="desctopadminmenu">
                    <div class="lm-publicsearch">
                        <input type="text" class="publicsearch_template" id="publicdescsearch_template" placeholder="Find Orders">
                        <div class="publicsearch_btn" id="publicdescsearch_btn">
                            <img src="/img/page_view/search_icon_blue.png">
                        </div>
                    </div>
                </div>
                <div class="desctopadminmenu">
                    <?php if ($adminchk) { ?>
                        <div class="infoalerts" id="admin">
                            <div class="alerticon admin">
                                <img src="/img/icons/cog_white.svg" class="img-responsive"/>
                            </div>
                            <div class="alerttext">Admin</div>
                        </div>
                    <?php } ?>
                    <?php if ($resourcechk) { ?>
                        <div class="infoalerts resources" id="resources">
                            <div class="alerticon">
                                <img src="/img/icons/book-open-white.svg" class="img-responsive"/>
                            </div>
                            <div class="alerttext">Resources</div>
                        </div>
                    <?php } ?>
                </div>
                <div class="lm-userinfo">
                    <div class="datarow">
                        <div class="signout" id="signout">[sign out]</div>
                        <div class="usersigninfo"><?=$user_name?></div>
                    </div>
                    <div class="datarow">
                        <div class="dateinfo"><?=date('D, F j, Y')?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($_SERVER['SERVER_NAME']=='lifttest.stressballs.com' || $_SERVER['SERVER_NAME']=='lift.local') {  //  || $_SERVER['SERVER_NAME']=='lift.local'?>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="testsitelabel">TEST</div>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-9 pr-0">
                <div class="lm-period_analitic_info mobilemainmenu">
                    <?=$total_view?>
                </div>
            </div>
            <div class="col-3 pl-0">
                <div class="dropdown lm-menuitem-list mobilemainmenu">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="/img/page_view/icon-menu.png">
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <?php $cursection = $menu[0]['menu_section'];?>
                        <?php foreach ($menu as $menurow) { ?>
                            <?php if ($menurow['menu_section']!=$cursection) { ?>
                                <div class="dropdown-divider"></div>
                                <?php $cursection = $menurow['menu_section'];?>
                            <?php } ?>
                            <button class="dropdown-item <?=$menurow['item_link'] == $activelnk ? 'activelink' : ''?>" type="button" data-menulink="<?= $menurow['item_link'] ?>"><?=$menurow['item_name']?></button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mobilemainmenu">
                <div class="lm-publicsearch">
                    <input type="text" class="publicsearch_template" id="publicsearch_template" placeholder="Find Orders">
                    <div class="publicsearch_btn" id="publicsearch_btn">
                        <img src="/img/page_view/search_icon_blue.png">
                    </div>
                </div>
            </div>
        </div>
        <div class="row"><div class="datarow menurow"><?=$menu_view?></div></div>
        <input type="hidden" id="mainmenuactivelnk" value="<?=$activelnk?>"/>
    </div>
</div>