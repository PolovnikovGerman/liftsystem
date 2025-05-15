<div class="mainheader">
    <div class="content_tabs_headers">
        <?php if ($brand=='SB') : ?>
            <div class="content_tab_header active stressballs" data-brand="SB">
                <div class="stressballs_logo active">
                    <img src="/img/page_view/sb-newlogo.svg" alt="Stressballs">
                </div>
            </div>
        <?php elseif ($brand=='SR') : ?>
            <div class="content_tab_header active relievers" data-brand="SR">
                <div class="relievers_logo active">
                    <img src="/img/page_view/sr-newlogo.svg" alt="StressRelievers">
                </div>
            </div>
        <?php else: ?>
            <div class="content_tab_header active sigmasystem" data-brand="SG">
                <div class="sigma_logo active">
                    <img src="/img/page_view/sigma-logo-white.svg" alt="Sigma">
                </div>
            </div>
        <?php endif; ?>
        <?php if (in_array('SB', $brands) && $brand!=='SB') : ?>
            <div class="content_tab_header stressballs" data-brand="SB">
                <div class="stressballs_logo">
                    <img src="/img/page_view/sb-newlogo.svg" alt="Stressballs">
                </div>
            </div>
        <?php endif; ?>
        <?php if (in_array('SR', $brands) && $brand!=='SR') : ?>
            <div class="content_tab_header relievers" data-brand="SR">
                <div class="relievers_logo">
                    <img src="/img/page_view/sr-newlogo.svg" alt="StressRelievers">
                </div>
            </div>
        <?php endif; ?>
        <?php if (in_array('SG', $brands) && $brand!=='SG') : ?>
            <div class="content_tab_header sigmasystem" data-brand="SG">
                <div class="sigma_logo">
                    <img src="/img/page_view/sigma-logo-black.svg" alt="Sigma">
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="mainsiteheader">
        <?php if ($usrrole=='masteradmin') : ?>
            <?=$total_view?>
        <?php endif; ?>
        <?php if ($debtpermiss) : ?>
        <div class="infodeptreport" id="debttotalview" data-event="click" data-css="weekbrandtotals" data-bgcolor="#000000"
             data-bordercolor="#adadad" data-textcolor="#FFFFFF" data-position="down" data-balloon="{ajax} /welcome/viewbalance">
            <div class="debtinfo_label">AR:</div>
            <div class="debtinfo_value"><?=MoneyOutput($debttotal,0)?></div>
        </div>
        <?php endif; ?>
        <?php if ($inventorychk) : ?>
        <div class="inforeports" id="inventory">
            <div class="icon">
                <img src="/img/icons/inventory-white2.svg" class="img-responsive" alt="Inventory"/>
            </div>
            <div class="infotext">Inventory</div>
        </div>
        <?php endif; ?>
        <?php if ($reportchk) : ?>
            <div class="inforeports" id="reports">
                <div class="icon" title="Reports">
                    <img src="/img/icons/chart-line-white.svg" class="img-responsive"/>
                </div>
                <div class="infotext" style="">Reports</div>
                <!-- padding-right: 4px; -->
            </div>
        <?php endif; ?>
        <?php if ($adminchk) : ?>
        <div class="infoalerts" title="Admin">
            <div class="alerticon admin" id="admin">
                <img src="/img/icons/cog_white.svg" alt="Admin" class="img-responsive"/>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="right-box">
        <div class="userinfo">
            <div class="signout" id="signout">[exit]</div>
            <div class="usersigninfo"><?=$user_name?></div>
        </div>
    </div>
</div>

