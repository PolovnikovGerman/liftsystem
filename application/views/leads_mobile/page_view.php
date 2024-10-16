<div class="bodypage <?=$brand=='SR' ? 'stressrelievers' : ($brand=='SG' ? 'sigma' : '')?>">
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
                <h4>Leads:</h4>
                <div class="tb-tabs text-right">
                    <ul class="tb-nav-tabs">
                        <?php foreach ($menu as $menurow) : ?>
                        <?php if ($menurow['item_link'] == '#leadsview') : ?>
                            <li class="whitetab" id="leadsviewtab">
                                <?php if ($menurow['newver']==0) : ?>
                                    <div class="oldvesionlabel">&nbsp;</div>
                                <?php endif; ?>
                                <?=$menurow['item_name']?>
                            </li>
                        <?php endif; ?>
                        <?php if ($menurow['item_link'] != '#itemslistview') : ?>
                                <li class="whitetab" id="itemslistviewtab">
                                    <?php if ($menurow['newver']==0) : ?>
                                        <div class="oldvesionlabel">&nbsp;</div>
                                    <?php endif; ?>
                                    <?=$menurow['item_name']?>
                                </li>
                        <?php endif; ?>
                        <?php endforeach;?>
                        <li class="greytab">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>
                            <div class="dropdown-menu dropdown-menu-right leadscontentmenu">
                                <?php foreach ($menu as $menurow) : ?>
                                    <?php if ($menurow['item_link'] == '#leadsview') : ?>
                                        <a class="dropdown-item" data-link="leadsview" href="javascript:void(0)"><?=$menurow['item_name']?></a>
                                    <?php endif; ?>
                                    <?php if ($menurow['item_link'] == '#itemslistview') : ?>
                                        <a class="dropdown-item" data-link="itemslistview" href="javascript:void(0)"><?=$menurow['item_name']?></a>
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
                <?php if ($menurow['item_link'] == '#leadsview') : ?>
                    <div id="leadsview" class="leadscontentarea">
                        <?=$leadsview?>
                    </div>
                <?php endif; ?>
                <?php if ($menurow['item_link'] == '#itemslistview') : ?>
                    <div id="itemslistview" class="leadscontentarea">
                        <?=$itemslistview?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>