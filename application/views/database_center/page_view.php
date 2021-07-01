<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontent_view">
            <div class="dbcenter_pagetitle">Database Center</div>
            <?php if (!empty($master)) { ?>
                <div class="dbcenter_master_title">Master Lists:</div>
                <div class="dbcenter_master_menu">
                    <div class="dbcenter_master_menuitems">
                        <?php foreach ($master as $mrow) { ?>
                            <?php if ($mrow['item_link']=='#mastercustomer') { ?>
                                <div class="dbcenter_master_item" data-lnk="mastercustomer">
                                    <div class="mastrelabel">Master</div>
                                    <div class="itemlabel">Customers</div>
                                </div>
                            <?php } elseif ($mrow['item_link']=='#mastervendors') { ?>
                                <div class="dbcenter_master_item" data-lnk="mastervendors">
                                    <div class="mastrelabel">Master</div>
                                    <div class="itemlabel">VENDORS</div>
                                </div>
                            <?php } elseif($mrow['item_link']=='#masterinventory') { ?>
                                <div class="dbcenter_master_item" data-lnk="masterinventory">
                                    <div class="mastrelabel">Master</div>
                                    <div class="itemlabel">INVENTORY</div>
                                </div>
                            <?php } elseif ($mrow['item_link']=='#mastersettings') { ?>
                                <div class="dbcenter_master_item" data-lnk="mastersettings">
                                    <div class="mastrelabel">Master</div>
                                    <div class="itemlabel">SETTINGS</div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="dbcenter_channels_title">Channel Lists:</div>
            <div class="dbcenter_channels_menu">
                <div class="dbcenter_channels_menuitems">
                    <div class="channel_menucontent">
                        <div class="channel_logo"><img src="/img/database_center/stressball_logo.png" alt="Stressball"/></div>
                        <div class="dbcenter_channel_item">Items</div>
                        <div class="dbcenter_channel_item">Customers</div>
                        <div class="dbcenter_channel_item">Pages</div>
                        <div class="dbcenter_channel_item">Settings</div>
                    </div>
                    <div class="channel_menucontent">
                        <div class="channel_logo"><img src="/img/database_center/national_sb_logo.png" alt="Nat Stressball"/></div>
                        <div class="dbcenter_channel_item">Items</div>
                        <div class="dbcenter_channel_item">Customers</div>
                        <div class="dbcenter_channel_item">Pages</div>
                        <div class="dbcenter_channel_item">Settings</div>
                    </div>
                    <div class="channel_menucontent">
                        <div class="channel_logo"><img src="/img/database_center/bluetrack_logo.png" alt="Bluetrack"/></div>
                        <div class="dbcenter_channel_item">Items</div>
                        <div class="dbcenter_channel_item">Customers</div>
                        <div class="dbcenter_channel_item">Pages</div>
                        <div class="dbcenter_channel_item">Settings</div>
                    </div>
                    <div class="channel_menucontent">
                        <div class="channel_logo"><img src="/img/database_center/bluetrack_legal_logo.png" alt="Bluetrack Legal"/></div>
                        <div class="dbcenter_channel_item">Items</div>
                        <div class="dbcenter_channel_item">Customers</div>
                        <div class="dbcenter_channel_item">Pages</div>
                        <div class="dbcenter_channel_item">Settings</div>
                    </div>
                    <div class="channel_menucontent">
                        <div class="channel_logo"><img src="/img/database_center/amazon_logo.png" alt="Amazon"/></div>
                        <div class="dbcenter_channel_item">Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

