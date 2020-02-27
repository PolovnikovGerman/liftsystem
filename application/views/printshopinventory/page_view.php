<div class="printshopinventor">
    <input type="hidden" id="inventorytotals" value="<?= $totals ?>"/>
    <input type="hidden" id="showonlinemaxvalue" value="0"/>
    <div class="maxinventsum">
        <div class="datarow">
            <div class="labeltxt">Current Inventory Value:</div>
            <div class="valuedata" id="curinvtotal">&nbsp;</div>            
        </div>
        <div class="datarow">
            <div class="labeltxt">Max Inventory Value:</div>
            <div class="valuedata"><?=MoneyOutput($maxsum)?></div>            
        </div>        
    </div>
    <div class="head_title">
        <div class="on_boat">Container <span class="add_onboat">[+]</span></div>
        <div class="history_low">
            <div class="critical_low">
                <div class="color_critical_low"></div>
                <div class="text_critical_low">Severe (25% & Under)</div>
            </div>
            <div class="getting_low">
                <div class="color_getting_low"></div>
                <div class="text_getting_low">Low (50% & Under)</div>
            </div>
        </div>
        <div class="exportdataarea">
            <div class="printshopexporttoexcel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Inventory</div>
        </div>        
    </div>
    <div id="pageviewarea">
        <?=$fullview?>
    </div>
    <div id="stockdataarea" style="display: none; width: 366px; height: 346px;"></div>
    <div id="onboatarea" style="display: none; width: 366px; height: 346px;"></div>
</div>
<input type="hidden" id="printshopinventbrand" value="<?=$brand?>"/>
<div id="printshopinventbrandmenu">
    <?=$top_menu?>
</div>
