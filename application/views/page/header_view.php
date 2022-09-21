<div class="mainheader">
    <div class="datarow">
        <div class="left-box">
            <div class="brands-logos">
                <div class="bluetrack_logo">
                    <img src="/img/page_view/bluetrack_logo.png"/>
                </div>
                <div class="stressballs_logo">
                    <img src="/img/page_view/stressballs_logo.png"/>
                </div>
                <div class="lift_logo">
                    <img src="/img/page_view/lift_logo.png"/>
                </div>
            </div>
            <div class="period_analitic_info"><?=$total_view?></div>
            <div class="publicsearch">
                <input type="text" class="publicsearch_template" id="publicsearch_template" placeholder="Find Orders"/>
                <div class="publicsearch_btn">
                    <img src="/img/page_view/search_icon_blue.png"/>
                </div>
            </div>
            <?php if ($reportchk) { ?>
                <div class="inforeports" id="reports">
                    <div class="icon" title="Reports">
                        <img src="/img/icons/chart-line-white.svg" class="img-responsive"/>
                    </div>
                    <div class="infotext">Reports</div>
                </div>
            <?php } ?>
            <?php if ($inventorychk) { ?>
                <div class="inforeports" id="inventory">
                    <div class="icon" title="Inventory">
                        <img src="/img/icons/inventory-white2.svg" class="img-responsive"/>
                    </div>
                    <!-- <div class="infotext">Reports</div> -->
                </div>
            <?php } ?>
        </div>
        <?php if ($_SERVER['SERVER_NAME']=='lifttest.stressballs.com' || $_SERVER['SERVER_NAME']=='lift.local') {  //  || $_SERVER['SERVER_NAME']=='lift.local'?>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="testsitelabel">TEST</div>
                </div>
            </div>
        <?php } ?>
        <div class="right-box">
            <div class="userinfo">
                <div class="datarow">
                    <div class="signout" id="signout">[sign out]</div>
                    <div class="usersigninfo"><?=$user_name?></div>
                </div>
                <div class="datarow">
                    <div class="dateinfo">
                        <?=date('D, F j, Y')?>
                    </div>
                </div>
            </div>
            <?php if ($adminchk) { ?>
                <div class="infoalerts" id="admin">
                    <div class="alerticon admin" title="Admin">
                        <img src="/img/icons/cog_white.svg" class="img-responsive"/>
                    </div>
<!--                    <div class="alerttext">Admin</div>-->
                </div>
            <?php } ?>
            <?php if ($resourcechk) { ?>
                <div class="infoalerts resources" id="resources">
                    <div class="alerticon" title="Resources">
                        <img src="/img/icons/book-open-white.svg" class="img-responsive"/>
                    </div>
<!--                    <div class="alerttext">Resources</div>-->
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="datarow menurow"><div class="row"><?=$menu_view?></div></div>
    <input type="hidden" id="mainmenuactivelnk" value="<?=$activelnk?>"/>
</div>
