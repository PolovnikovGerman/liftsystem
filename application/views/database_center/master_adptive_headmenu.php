<main class="container-fluid">
    <div class="mastermenu_head">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-1">
                <div class="menulabel">MASTER</div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="mastermenu_items">
                    <?php foreach ($menu as $mrow) { ?>
                        <div class="col-sm-6 col-md-6 col-lg-1 col-xl-1 headmenuitem <?=$start==str_replace('#', '',$mrow['item_link']) ? 'active' : ''?>" data-lnk="<?=str_replace('#', '',$mrow['item_link'])?>">
                            <?=$mrow['item_name']?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-5">
                <div class="returndbcenter">
                    <img src="/img/database_center/return_to_mainscreen.png" class="img-responsive img-fluid" alt="'Back Main Screen"/>
                </div>
            </div>
        </div>
    </div>
</main>