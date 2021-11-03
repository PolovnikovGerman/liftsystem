<main class="container-fluid">
    <div class="mastermenu_head">
        <div class="row">
            <div class="col-12 col-sm-3 col-md-3 col-lg-2 col-xl-2 menulabel">MASTER</div>
            <div class="col-sm-3 col-md-3 col-lg-2 col-xl-4 smallmobile">
                <div class="returndbcenter">
                    <img src="/img/database_center/return_to_mainscreen.png" class="img-responsive img-fluid" alt="'Back Main Screen"/>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-8 col-xl-6">
                <div class="row">
                    <?php foreach ($menu as $mrow) { ?>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                            <div class="headmenuitem <?=$start==str_replace('#', '',$mrow['item_link']) ? 'active' : ''?>" data-lnk="<?=str_replace('#', '',$mrow['item_link'])?>">
                                <?=$mrow['item_name']?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-2 col-xl-4 bigdeviceview">
                <div class="returndbcenter">
                    <img src="/img/database_center/return_to_mainscreen.png" class="img-responsive img-fluid" alt="'Back Main Screen"/>
                </div>
            </div>
        </div>
    </div>
</main>