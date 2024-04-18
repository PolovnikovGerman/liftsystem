<div class="mainheader">
    <div class="content_tabs_headers">
        <?php if ($brand=='SB') { ?>
            <div class="content_tab_header active stressballs" data-brand="SB">
                <div class="stressballs_logo active">
                    <img src="/img/page_view/stessball_logo_activ.png" alt="Stressballs">
                </div>
            </div>
            <?php if (in_array('SR', $brands)) { ?>
                <div class="content_tab_header relievers" data-brand="SR">
                    <div class="relievers_logo">
                        <img src="/img/page_view/stressreiliv_logo.png" alt="StressRelievers">
                    </div>
                </div>
            <?php } ?>
            <?php if (in_array('SG', $brands)) { ?>
            <div class="content_tab_header sigmasystem" data-brand="SG">
                <div class="sigma_logo">
                    <img src="/img/page_view/sigma_logo.png" alt="Sigma">
                </div>
            </div>
            <?php } ?>
        <?php } elseif ($brand=='SR') { ?>
            <div class="content_tab_header active relievers" data-brand="SR">
                <div class="relievers_logo active">
                    <img src="/img/page_view/stressreiliv_logo_activ.png" alt="StressRelievers">
                </div>
            </div>
            <?php if (in_array('SB', $brands)) { ?>
                <div class="content_tab_header stressballs" data-brand="SB">
                    <div class="stressballs_logo" >
                        <img src = "/img/page_view/stessball_logo.png" alt = "Stressballs" >
                    </div >
                </div >
            <?php } ?>
            <?php if (in_array('SG', $brands)) { ?>
                <div class="content_tab_header sigmasystem" data-brand="SG">
                    <div class="sigma_logo">
                        <img src="/img/page_view/sigma_logo.png" alt="Sigma">
                    </div>
                </div>
            <?php } ?>
        <?php } elseif ($brand=='SG') {?>
            <div class="content_tab_header active sigmasystem" data-brand="SG">
                <div class="sigma_logo active">
                    <img src="/img/page_view/sigma_logo_active.png" alt="Sigma">
                </div>
            </div>
            <?php if (in_array('SB', $brands)) { ?>
                <div class="content_tab_header stressballs" data-brand="SB">
                    <div class="stressballs_logo" >
                        <img src = "/img/page_view/stessball_logo.png" alt = "Stressballs" >
                    </div >
                </div >
            <?php } ?>
            <?php if (in_array('SR', $brands)) { ?>
                <div class="content_tab_header relievers" data-brand="SR">
                    <div class="relievers_logo">
                        <img src="/img/page_view/stressreiliv_logo.png" alt="StressRelievers">
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="mainsiteheader">
        <div class="datarow">
            <div class="left-box">
                <?php if ($usrrole=='masteradmin') { ?>
                    <div class="period_analitic_info"><?=$total_view?></div>
                <?php } ?>
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
                        <div class="infotext">Inventory</div>
                    </div>
                <?php } ?>
                <?php if ($adminchk) { ?>
                    <div class="infoalerts" id="admin">
                        <div class="alerticon admin" title="Admin">
                            <img src="/img/icons/cog_white.svg" class="img-responsive"/>
                        </div>
                        <!--                    <div class="alerttext">Admin</div>-->
                    </div>
                <?php } ?>
            </div>
            <?php if ($test_server==1) { ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="testsitelabel">TEST</div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="right-box">
        <div class="userinfo">
            <div class="datarow">
                <div class="signout" id="signout">[exit]</div>
                <div class="usersigninfo"><?=$user_name?></div>
            </div>
            <div class="datarow">
                <div class="dateinfo">
                    <?=date('F j, Y')?>
                </div>
            </div>
        </div>
        <?php if ($resourcechk) { ?>
            <div class="infoalerts resources" id="resources">
                <div class="alerticon" title="Resources">
                    <img src="/img/icons/book-open-white.svg" class="img-responsive"/>
                </div>
                <!--                    <div class="alerttext">Resources</div>-->
            </div>
        <?php } ?>
    </div>

    <input type="hidden" id="mainmenuactivelnk" value="<?=$activelnk?>"/>
    <input type="hidden" id="currentbrand" value="SB"/>
</div>
