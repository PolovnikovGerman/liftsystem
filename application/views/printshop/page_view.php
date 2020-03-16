<div class="printshoparea">
    <input type="hidden" id="prinshopactivesubmenu" value="<?=$active?>"/>
    <div class="printshopsubmenu">
        <div class="button shchedule" data-submenu="schedule">Schedule</div>
        <div class="button" data-submenu="inventory">Inventory</div>
        <div class="button productreport" data-submenu="productreport">Production Report</div>
        <div class="button" data-submenu="orderreport">Order Report</div>
        <div class="button supplycount" data-submenu="supplycount">Supply Count</div>        
    </div>
    <div class="printshopcontent"><?=$subpage?></div>
</div>