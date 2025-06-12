<div class="subitems-contant">
    <div class="container-xl">
        <div class="subitemsmenu d-flex flex-row-reverse">
                <div class="subitem-dropdown dropdown">
                    <?php if (!empty($submenu)) : ?>
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dd-subitems" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-subitems">
                        <div class="dropdown-item">PO Totals</div>
                        <div class="dropdown-item">Print Schedule</div>
                        <div class="dropdown-item active-item">Print Shop Report</div>
                        <div class="dropdown-item">Master Inventory</div>
                        <div class="dropdown-item">BT item DB</div>
                        <div class="dropdown-item">Vendors</div>
                        <div class="dropdown-item">Status</div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php if (!empty($activelink)) : ?>
            <div class="subitem-active">
                <div class="subitem-name">Print Shop Report</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="subitemsbody">
        <div class="container-xl">&nbsp;</div>
    </div>
</div>
