<div class="mainheader">
    <div class="datarow">
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
            <select class="publicsearch_type">
                <option value="Orders">Orders</option>
                <option value="Customers">Customers</option>
                <option value="Items">Items</option>
            </select>
            <input type="text" class="publicsearch_template" id="publicsearch_template" placeholder="Find Orders"/>
            <div class="publicsearch_btn">
                <img src="/img/page_view/search_icon_blue.png"/>
            </div>
        </div>
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
        <?php if ($alertchk) { ?>
            <div class="infoalerts" id="infoalerts">
                <div class="alerticon">
                    <img src="/img/icons/bell_white.svg"/>
                </div>
                <div class="alerttext">Alerts</div>
            </div>
        <?php } ?>
        <?php if ($adminchk) { ?>
            <div class="infoalerts" id="admin">
                <div class="alerticon admin">
                    <img src="/img/icons/cog_white.svg"/>
                </div>
                <div class="alerttext">Admin</div>
            </div>
        <?php } ?>
    </div>
    <div class="datarow menurow"><?=$menu_view?></div>
</div>
