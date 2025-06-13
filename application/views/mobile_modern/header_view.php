<div class="liftheader">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <?php if ($usrrole=='masteradmin') : ?>
                    <?=$total_view?>
                <?php endif; ?>
                <?php if ($debt_permissions) : ?>
                    <div class="infodeptreport float-left">
                        <div class="debtinfo_label">AR:</div>
                        <div class="debtinfo_value"><?=MoneyOutput($debt_total, 0)?></div>
                    </div>
                <?php endif; ?>

                <div class="btn-user float-right" data-toggle="collapse" data-target="#user-blockinfo" aria-expanded="false">
                    <img src="/img/mobile_modern/icon-user.svg">
                </div>
                <div class="collapse" id="user-blockinfo">
                    <div class="card card-body">
                        <p class="nameuser"><?=$user_name?> <span class="signout">[sign out]</span></p>
                        <p class="dateinfo"><?=date('D, F j, Y')?></p>
                    </div>
                </div>
                <div class="btn-settings float-right">
                    <img src="/img/mobile_modern/icon-settings.svg">
                </div>
                <div class="btn-other float-right" data-toggle="collapse" data-target="#other-btns" aria-expanded="false">
                    <span>+</span>
                </div>
                <div class="collapse" id="other-btns">
                    <div class="card card-body">
                        <ul>
                            <li>
                                <div class="btn-inventory"><span><img src="/img/mobile_modern/inventory-white2.svg"></span> Inventory</div>
                            </li>
                            <li>
                                <div class="btn-visitors d-flex">
                                    <div class="btn-name">Visitors:</div>
                                    <div class="btn-value ml-auto">24,479</div>
                                </div>
                            </li>
                            <li>
                                <div class="btn-leads d-flex">
                                    <div class="btn-name">Leads:</div>
                                    <div class="btn-value ml-auto">69</div>
                                </div>
                            </li>
                            <li>
                                <div class="btn-reviews d-flex">
                                    <div class="btn-name">Reviews:</div>
                                    <div class="btn-value ml-auto">202</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

