<div class="container-fluid pr-0 pl-0">
    <div class="mainheader">
        <div class="row">
            <div class="col-6 pr-0">
                <div class="lm-brands-logos">
                    <div class="bluetrack_logo">
                        <img src="/img/page_view/bluetrack_logo_new.png">
                    </div>
                    <div class="lift_logo">
                        <img src="/img/page_view/lift_logo_new.png">
                    </div>
                </div>
            </div>
            <div class="col-6 pl-0">
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
        <?php if ($_SERVER['SERVER_NAME']=='lifttest.stressballs.com') {  //  || $_SERVER['SERVER_NAME']=='lift.local'?>
            <div class="testsitelabel">TEST</div>
        <?php } ?>
        <div class="row">
            <div class="col-9 pr-0">
                <div class="lm-period_analitic_info">
                    <?=$total_view?>
                </div>
            </div>
            <div class="col-3 pl-0">
                <div class="dropdown lm-menuitem-list mobilemainmenu">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="/img/page_view/icon-menu.png">
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button class="dropdown-item" type="button">Action</button>
                        <button class="dropdown-item" type="button">Another action</button>
                        <button class="dropdown-item" type="button">Something else here</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="lm-publicsearch">
                    <input type="text" class="publicsearch_template" id="publicsearch_template" placeholder="Find Orders">
                    <div class="publicsearch_btn">
                        <img src="/img/page_view/search_icon_blue.png">
                    </div>
                </div>
            </div>
        </div>
        <div class="datarow menurow"><?=$menu_view?></div>
        <input type="hidden" id="mainmenuactivelnk" value="<?=$activelnk?>"/>
    </div>
</div>