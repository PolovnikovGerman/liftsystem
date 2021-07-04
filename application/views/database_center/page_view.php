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
            <?php if ($channelcnt==1) { ?>
                <div class="dbcenter_channels_title">Channel Lists:</div>
                <div class="dbcenter_channels_menu">
                    <div class="dbcenter_channels_menuitems">
                        <?php if (isset($channelsb) && !empty($channelsb)) { ?>
                            <div class="channel_menucontent">
                                <div class="channel_logo sblogo"><img src="/img/database_center/stressballs_logo.png" alt="Stressball"/></div>
                                <?php foreach ($channelsb as $item) { ?>
                                    <?php if ($item['item_link']=='#sbitems') { ?>
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                    <?php } elseif ($item['item_link']=='#sbcustomers')  { ?>
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                    <?php } elseif ($item['item_link']=='#sbpages') { ?>
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                    <?php } elseif ($item['item_link']=='#sbsettings') { ?>
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (isset($channelnsb) && !empty($channelnsb)) { ?>
                            <div class="channel_menucontent">
                                <div class="channel_logo natsblogo"><img src="/img/database_center/national_sb_logo.png" alt="Nat Stressball"/></div>
                                <?php foreach ($channelnsb as $item) { ?>
                                    <?php if ($item['item_link']=='#nsbitems') { ?>
                                        <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                    <?php } elseif ($item['item_link']=='#nsbcustomers') { ?>
                                        <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                    <?php } elseif ($item['item_link']=='#nsbpages') { ?>
                                        <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                    <?php } elseif ($item['item_link']=='#nsbsettings') { ?>
                                        <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (isset($channelbt) && !empty($channelbt)) { ?>
                            <div class="channel_menucontent">
                                <div class="channel_logo btlogo"><img src="/img/database_center/bluetrack_logo.png" alt="Bluetrack"/></div>
                                <?php foreach ($channelbt as $item) { ?>
                                    <?php if ($item['item_link']=='#btitems') { ?>
                                        <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                    <?php } elseif ($item['item_link']=='#btcustomers') { ?>
                                        <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                    <?php } elseif ($item['item_link']=='#btpages') { ?>
                                        <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                    <?php } elseif ($item['item_link']=='#btsettings') { ?>
                                        <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (isset($channellbt) && !empty($channellbt)) { ?>
                            <div class="channel_menucontent">
                                <div class="channel_logo btleglogo"><img src="/img/database_center/bluetrack_legacy_logo.png" alt="Bluetrack Legal"/></div>
                                <?php foreach ($channellbt as $item) { ?>
                                    <?php if ($item['item_link']=='#btlegitems') { ?>
                                        <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                    <?php } elseif ($item['item_link']=='#btlegcustomers') { ?>
                                        <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                    <?php } elseif ($item['item_link']=='#btlegpages') { ?>
                                        <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                    <?php } elseif ($item['item_link']=='#btlegsettings') { ?>
                                        <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (isset($channelamz) && !empty($channelamz)) { ?>
                            <div class="channel_menucontent">
                                <div class="channel_logo amazlogo"><img src="/img/database_center/amazon_logo.png" alt="Amazon"/></div>
                                <?php foreach ($channelamz as $item) { ?>
                                    <?php if ($item['item_link']=='#amazitems') { ?>
                                        <div class="dbcenter_channel_item" data-brand="amazon" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

