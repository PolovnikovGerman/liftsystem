<div class="container-fluid brown-header">
    <div class="">
        <div class="row">
            <div class="col-7 pr-0 header-left">
                <?=$total_view?>
            </div>
            <div class="col-5 pl-0 header-right">
                <div class="headerbuttons">
                    <?php if ($reportchk) : ?>
                        <div class="btn-reports">
                            <img src="/img/page_mobile/icon-reports.svg">
                        </div>
                    <?php endif; ?>
                    <?php if ($inventorychk): ?>
                    <div class="btn-inventory">
                        <img src="/img/page_mobile/icon-inventory.svg">
                    </div>
                    <?php endif; ?>
                    <?php if ($adminchk): ?>
                    <div class="btn-settings">
                        <img src="/img/page_mobile/icon-settings.svg">
                    </div>
                    <?php endif; ?>
                    <div class="btn-user" data-toggle="collapse" data-target="#user-blockinfo" aria-expanded="false">
                        <img src="/img/page_mobile/icon-user.svg">
                    </div>
                    <div class="collapse" id="user-blockinfo">
                        <div class="card card-body">
                            <p class="nameuser"><?=$user_name?><span class="signout">[sign out]</span></p>
                            <p class="dateinfo"><?=date('F j, Y')?></p>
                        </div>
                    </div>
                </div>
                <div class="search-block">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Find Orders" aria-label="Find Orders" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                    <span class="input-group-text">
                      <img src="/img/page_mobile/icon-magnifying-glass.svg">
                    </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="nav-tabs-sites">
                <ul>
                    <?php if ($brand=='SB') : ?>
                        <li class="tab-stressballs active">
                            <img src="/img/page_mobile/logo-stressballs.svg">
                        </li>
                        <?php if (in_array('SR', $brands)) : ?>
                            <li class="tab-stressrelievers">
                                <img src="/img/page_mobile/logo-stressrelievers.svg">
                            </li>
                        <?php endif;?>
                        <?php if (in_array('SG', $brands)) : ?>
                            <li class="tab-sigma">
                                <img src="/img/page_mobile/logo-sigma.png">
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($brand=='SR') : ?>
                        <li class="tab-stressrelievers active">
                            <img src="/img/page_mobile/logo-stressrelievers.svg">
                        </li>
                        <?php if (in_array('SB', $brands)) : ?>
                            <li class="tab-stressballs">
                                <img src="/img/page_mobile/logo-stressballs.svg">
                            </li>
                        <?php endif;?>
                        <?php if (in_array('SG', $brands)) : ?>
                            <li class="tab-sigma">
                                <img src="/img/page_mobile/logo-sigma.png">
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($brand=='SG') : ?>
                        <li class="tab-sigma active">
                            <img src="/img/page_mobile/logo-sigma.png">
                        </li>
                        <?php if (in_array('SB', $brands)) : ?>
                            <li class="tab-stressballs">
                                <img src="/img/page_mobile/logo-stressballs.svg">
                            </li>
                        <?php endif;?>
                        <?php if (in_array('SR', $brands)) : ?>
                            <li class="tab-stressrelievers active">
                                <img src="/img/page_mobile/logo-stressrelievers.svg">
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>
</div>
