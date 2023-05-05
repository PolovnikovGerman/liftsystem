<div class="maincontent">
    <div class="maincontentmenuarea databasemenu">
        <div class="maincontent_view">
            <section class="databasecontentview">
                <main class="container-fluid">
                    <div class="row">
                        <div class="col-12 dbcenter_pagetitle">Database Center</div>
                    </div>
                    <?php if (!empty($master)) { ?>
                        <div class="row mb-3">
                            <div class="col-12 dbcenter_master_title">Master Lists:</div>
                        </div>
                        <div class="row masteritemsarea">
                            <?php foreach ($master as $mrow) { ?>
                                <?php if ($mrow['item_link']=='#mastervendors') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-4">
                                        <div class="dbcenter_master_item" data-lnk="mastervendors">
                                            <div class="mastrelabel text-center w-100">Master</div>
                                            <div class="itemlabel text-center w-100">VENDORS</div>
                                        </div>
                                    </div>
                                <?php } elseif($mrow['item_link']=='#masterinventory') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-4">
                                        <div class="dbcenter_master_item" data-lnk="masterinventory">
                                            <div class="mastrelabel text-center w-100">Master</div>
                                            <div class="itemlabel text-center w-100">INVENTORY</div>
                                        </div>
                                    </div>
                                <?php } elseif ($mrow['item_link']=='#mastersettings') { ?>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-4">
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
                        <div class="row masteritemsarea">
                            <div class="col-6 col-sm-6 col-md-6 col-lg-4">
                                <div class="channel_logo">
                                    <img src="/img/database_center/stressballs_bluetrack_logo.png" class="img-responsive img-fluid" alt="Stressball"/>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Items</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Customers</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item wcontent" data-brand="stressballs" data-start="">
                                            <div class="channellabel text-center w-100">Bluetrack</div>
                                            <div class="channelsublabel text-center w-100">Web Pages</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">
                                            <div class="channellabel text-center w-100">Stressballs</div>
                                            <div class="channelsublabel text-center w-100">Web Pages</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Settings</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6 col-md-6 col-lg-4">
                                <div class="channel_logo">
                                    <img src="/img/database_center/stressrelievers_logo.png" class="img-responsive img-fluid" alt="Stressball"/>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Items</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Customers</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">
                                            <div class="channellabel text-center w-100">StressRelievers</div>
                                            <div class="channelsublabel text-center w-100">Web Pages</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Settings</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6 col-md-6 col-lg-4">
                                <div class="channel_logo">
                                    <img src="/img/database_center/amazon_logo_new.png" class="img-responsive img-fluid" alt="Stressball"/>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-12">
                                        <div class="dbcenter_channel_item" data-brand="stressballs" data-start="">Items</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </main>
            </section>
        </div>
    </div>
</div>