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
            <?php if ($reportchk) { ?>
                <div class="inforeports <?=($reportsold==0 ? 'oldver' : '')?>" id="reports">
                    <?php  if ($reportsold==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <div class="icon">
                        <img src="/img/icons/chart-line-white.svg" class="img-responsive"/>
                    </div>
                    <div class="infotext">Reports</div>
                </div>
            <?php } ?>
            <div class="period_analitic_info"><?=$total_view?></div>
            <div class="publicsearch">
                <input type="text" class="publicsearch_template" id="publicsearch_template" placeholder="Find Orders"/>
                <div class="publicsearch_btn">
                    <img src="/img/page_view/search_icon_blue.png"/>
                </div>
            </div>
        </div>
        <?php if ($_SERVER['SERVER_NAME']=='lifttest.stressballs.com') {  //  || $_SERVER['SERVER_NAME']=='lift.local'?>
            <div class="testsitelabel">TEST</div>
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
                <div class="infoalerts admininfo <?=$adminold==0 ? 'oldver' : ''?>" id="admin">
                    <?php  if ($adminold==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <div class="alerticon admin ">
                        <img src="/img/icons/cog_white.svg" class="img-responsive"/>
                    </div>
                    <div class="alerttext">Admin</div>
                </div>
            <?php } ?>
            <?php if ($resourcechk) { ?>
                <div class="infoalerts resources <?=$resourceold==0 ? 'oldver' : ''?>" id="resources">
                    <?php  if ($resourceold==0) { ?>
                        <div class="oldvesionlabel">&nbsp;</div>
                    <?php } ?>
                    <div class="alerticon">
                        <img src="/img/icons/book-open-white.svg" class="img-responsive"/>
                    </div>
                    <div class="alerttext">Resources</div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="datarow menurow"><?=$menu_view?></div>
    <input type="hidden" id="mainmenuactivelnk" value="<?=$activelnk?>"/>
</div>
