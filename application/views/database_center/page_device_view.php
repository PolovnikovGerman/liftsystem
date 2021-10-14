<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontent_view">
            <section class="databasecontentview">
                <main class="container-fluid">
                    <div class="row">
                        <div class="col-12 dbcenter_pagetitle">Database Center</div>
                    </div>
                    <?php if (!empty($master)) { ?>
                        <div class="row">
                            <div class="col-12 dbcenter_master_title">Master Lists:</div>
                        </div>
                        <div class="row">
                            <?php foreach ($master as $mrow) { ?>
                                <?php if ($mrow['item_link']=='#mastercustomer') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                        <div class="dbcenter_master_item" data-lnk="mastercustomer">
                                            <div class="mastrelabel text-center w-100">Master</div>
                                            <div class="itemlabel text-center w-100">Customers</div>
                                        </div>
                                    </div>
                                <?php } elseif ($mrow['item_link']=='#mastervendors') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                        <div class="dbcenter_master_item" data-lnk="mastervendors">
                                            <div class="mastrelabel text-center w-100">Master</div>
                                            <div class="itemlabel text-center w-100">VENDORS</div>
                                        </div>
                                    </div>
                                <?php } elseif($mrow['item_link']=='#masterinventory') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                        <div class="dbcenter_master_item" data-lnk="masterinventory">
                                            <div class="mastrelabel text-center w-100">Master</div>
                                            <div class="itemlabel text-center w-100">INVENTORY</div>
                                        </div>
                                    </div>
                                <?php } elseif ($mrow['item_link']=='#mastersettings') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                        <div class="dbcenter_master_item" data-lnk="mastersettings">
                                            <div class="mastrelabel text-center w-100">Master</div>
                                            <div class="itemlabel text-center w-100">SETTINGS</div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($channelcnt==1) { ?>
                        <div class="row mb-lg-3">
                            <div class="col-12 dbcenter_channels_title">Channel Lists:</div>
                        </div>
                        <div class="row">
                            <?php if (isset($channelsb) && count($channelsb)>0) { ?>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2">
                                    <div class="col-12 channel_logo sblogo"><img src="/img/database_center/stressballs_logo2.png" class="img-responsive img-fluid" alt="Stressball"/></div>
                                    <div class="row mt-3 mb-3">
                                        <?php foreach ($channelsb as $item) { ?>
                                            <?php if ($item['item_link']=='#sbitems') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#sbcustomers')  { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#sbpages') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#sbsettings') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($channelnsb) && count($channelnsb)>0) { ?>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2">
                                    <div class="channel_logo natsblogo"><img src="/img/database_center/national_sb_logo2.png" class="img-responsive img-fluid" alt="Nat Stressball"/></div>
                                    <div class="row mt-3 mb-3">
                                        <?php foreach ($channelnsb as $item) { ?>
                                            <?php if ($item['item_link']=='#nsbitems') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#nsbcustomers') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#nsbpages') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#nsbsettings') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="nationalsb" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($channelbt) && count($channelbt)>0) { ?>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2">
                                    <div class="channel_logo btlogo"><img src="/img/database_center/bluetrack_logo2.png" class="img-responsive img-fluid" alt="Bluetrack"/></div>
                                    <div class="row mt-3 mb-3">
                                        <?php foreach ($channelbt as $item) { ?>
                                            <?php if ($item['item_link']=='#btitems') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#btcustomers') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#btpages') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#btsettings') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="bluetrack" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($channellbt) && count($channellbt)>0) { ?>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2">
                                    <div class="channel_logo btleglogo"><img src="/img/database_center/bluetrack_legacy_logo2.png" class="img-responsive img-fluid" alt="Bluetrack Legal"/></div>
                                    <div class="row mt-3 mb-3">
                                        <?php foreach ($channellbt as $item) { ?>
                                            <?php if ($item['item_link']=='#btlegitems') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#btlegcustomers') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#btlegpages') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                                </div>
                                            <?php } elseif ($item['item_link']=='#btlegsettings') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="btlegacy" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($channelamz) && count($channelamz)>0) { ?>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2">
                                    <div class="channel_logo amazlogo"><img src="/img/database_center/amazon_logo2.png" class="img-responsive img-fluid" alt="Amazon"/></div>
                                    <div class="row mt-3 mb-3">
                                        <?php foreach ($channelamz as $item) { ?>
                                            <?php if ($item['item_link']=='#amazitems') { ?>
                                                <div class="col-12">
                                                    <div class="dbcenter_channel_item" data-brand="amazon" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </main>
            </section>
        </div>
    </div>
</div>