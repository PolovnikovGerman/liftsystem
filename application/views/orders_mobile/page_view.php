<div class="bodypage <?=$brand=='SR' ? 'stressrelievers' : ($brand=='SG' ? 'sigma' : 'stressballs')?>">
    <div class="container-fluid">
        <div class="row justify-content-between topblock">
            <div class="col-12 topline">
                <div class="tb-menu">
                    <div class="dropdown" id="tb-menu-dropdown">
                        <button class="btn btn-secondary" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="/img/page_mobile/icon-menu.svg">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2"><?=$left_menu?></div>
                    </div>
                </div>
                <h4>Orders:</h4>
                <div class="tb-tabs text-right">
                    <ul class="tb-nav-tabs">
                        <?php foreach ($menu as $menurow) : ?>
                        <?php if ($menurow['item_link'] == '#ordersview') : ?>
                            <li class="whitetab" id="ordersviewtab">
                                <?php if ($menurow['newver']==0) : ?>
                                    <div class="oldvesionlabel">&nbsp;</div>
                                <?php endif; ?>
                                <?=$menurow['item_name']?>
                            </li>
                        <?php endif; ?>
                        <?php if ($menurow['item_link'] == '#orderlistsview') : ?>
                            <li class="whitetab" id="orderlistsviewtab">
                                <?php if ($menurow['newver']==0) : ?>
                                    <div class="oldvesionlabel">&nbsp;</div>
                                <?php endif; ?>
                                <?=$menurow['item_name']?>
                            </li>
                        <?php endif; ?>
                        <?php if ($menurow['item_link'] == '#onlineordersview') : ?>
                            <li class="whitetab" id="onlineordersviewtab">
                                <?php if ($menurow['newver']==0) : ?>
                                    <div class="oldvesionlabel">&nbsp;</div>
                                <?php endif; ?>
                                <?=$menurow['item_name']?>
                            </li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <li class="greytab">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>
                            <div class="dropdown-menu dropdown-menu-right ordercontentmenu">
                                <?php foreach ($menu as $menurow) : ?>
                                <?php if ($menurow['item_link'] == '#ordersview') : ?>
                                    <a class="dropdown-item" data-link="ordersview" href="javascript:void(0)"><?=$menurow['item_name']?></a>
                                <?php endif; ?>
                                <?php if ($menurow['item_link'] == '#orderlistsview') : ?>
                                    <a class="dropdown-item" data-link="orderlistsview" href="javascript:void(0)"><?=$menurow['item_name']?></a>
                                <?php endif; ?>
                                <?php if ($menurow['item_link'] == '#onlineordersview') : ?>
                                    <a class="dropdown-item" data-link="onlineordersview" href="javascript:void(0)"><?=$menurow['item_name']?></a>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="white-body">
            <?php foreach ($menu as $menurow) : ?>
                <?php if ($menurow['item_link'] == '#ordersview') : ?>
                <div id="ordersview" class="orderscontentarea">
                    <?=$ordersview?>
                </div>
                <?php endif; ?>
                <?php if ($menurow['item_link'] == '#onlineordersview') : ?>
                <div id="onlineordersview" class="orderscontentarea">
                    <?=$onlineordersview?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
