<div class="col-12 col-sm-12">
    <div class="leadclosed_label">% Leads Closed:</div>
    <div class="closedtotal_area">
        <div class="row">
            <div class="col-1 px-0">
                <div class="cta-arrow closeprevview <?=($prev==1 ? 'active' : '')?>">
                    <i class="fa fa-caret-left" aria-hidden="true"></i>
                </div>
            </div>
            <div class="col-10 px-0">
                <ul>
                    <?php foreach ($data as $row) : ?>
                        <li><?=$row['label']?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-1 px-0">
                <div class="cta-arrow closenextview <?=($next==1 ? 'active' : '')?>">
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="closedtotalshowfeature">Show Future Weeks</div>
</div>
