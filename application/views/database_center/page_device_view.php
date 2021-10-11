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
                        <div class="row">
                            <div class="col-12 dbcenter_channels_title">Channel Lists:</div>
                        </div>
                        <div class="row">
                            <?php if (isset($channelsb) && !empty($channelsb)) { ?>
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                    <div class="col-12 channel_logo sblogo"><img src="/img/database_center/stressballs_logo.png" class="img-responsive img-fluid" alt="Stressball"/></div>
                                    <div class="row mt-3">
                                        <?php foreach ($channelsb as $item) { ?>
                                            <?php if ($item['item_link']=='#sbitems') { ?>
                                                <div class="col-12 dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Items</div>
                                            <?php } elseif ($item['item_link']=='#sbcustomers')  { ?>
                                                <div class="col-12 dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Customers</div>
                                            <?php } elseif ($item['item_link']=='#sbpages') { ?>
                                                <div class="col-12 dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Pages</div>
                                            <?php } elseif ($item['item_link']=='#sbsettings') { ?>
                                                <div class="col-12 dbcenter_channel_item" data-brand="stressballs" data-start="<?=str_replace('#', '', $item['item_link'])?>">Settings</div>
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