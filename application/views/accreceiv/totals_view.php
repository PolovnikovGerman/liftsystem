<div class="accreceiv-content-left <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
    <div class="accreceiv-totalown">
        <div class="accreceiv-totalown-title">Owed to Us:</div>
        <div class="accreceiv-totalown-value"><?=TotalOutput($totalown)?></div>
    </div>
    <div class="accreceiv-totalpast">
        <div class="accreceiv-totalpast-title">Past Due:</div>
        <div class="accreceiv-totalpast-value"><?=TotalOutput($pastown)?></div>
    </div>
    <div class="accreceiv-export">
        <div class="accreceiv-exportbtn">
            <i class="fa fa-file-excel-o"></i>
            Export
        </div>
    </div>
</div>
<div class="accreceiv-content-center <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
    <div class="accreceiv-totalrefund">
        <div class="accreceiv-totalrefund-title">Refunds to Customers:</div>
        <div class="accreceiv-totalrefund-value">(<?=TotalOutput(abs($totalrefund))?>)</div>
    </div>
    <div class="totalrefund-export">
        <div class="totalrefund-exportbtn">
            <i class="fa fa-file-excel-o"></i>
            Export
        </div>
    </div>
    <div class="accreceiv-content-right"></div>
</div>
