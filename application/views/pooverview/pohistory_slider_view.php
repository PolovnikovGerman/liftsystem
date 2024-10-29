<div class="povendor-body" style="position: relative; width: <?=$slider_width?>px; margin-right: <?=$slider_margin?>px; margin-left: 0px;">
    <?php foreach ($years as $year): ?>
    <div class="povendorbox">
        <h4 class="datarow"><?=$year['year']?> POs by Vendor</h4>
        <div class="povendor-table">
            <div class="povendortbl-tr povendortitletbl">
                <div class="povendortbl-td vendor"> Vendor</div>
                <div class="povendortbl-td totalsumprice">Price $</div>
                <div class="povendortbl-td totalsumpo"># POs</div>
            </div>
            <div class="povendortbl-tr povendortitletbl-grey">
                <div class="povendortbl-td total"> TOTAL:</div>
                <div class="povendortbl-td totalsumprice"><?=MoneyOutput($year['sumamnt'],0)?></div>
                <div class="povendortbl-td totalsumpo"><?=$year['cnt']?></div>
            </div>
            <div class="povendortbl-body">
                <?php $numpp = 1;?>
                <?php foreach ($year['vendors'] as $vendor): ?>
                    <div class="povendortbl-tr <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
                        <div class="povendortbl-td number"><?=$numpp?></div>
                        <div class="povendortbl-td vendorname"><?=$vendor['vendor_name']?></div>
                        <div class="povendortbl-td totalprice"><?=MoneyOutput($vendor['sumamnt'],0)?></div>
                        <div class="povendortbl-td totalpriceprec"><?=$vendor['proc_total']?>%</div>
                        <div class="povendortbl-td totalpo"><?=$vendor['cnt']?></div>
                        <div class="povendortbl-td totalpoprec"><?=$vendor['proc_cnt']?>%</div>
                    </div>
                    <?php $numpp++;?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>