<div class="inventoryheadrow">
    <div class="lefttitle">Inventory Need List</div>
    <!-- #ff9c00 - Getting Low -->
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
</div>
<div class="inventrightheadrow">
    <div class="aftercontainers">% After Containers</div>
    <div class="needtomake">Need to Make</div>    
</div>
<div class="inventorytablehead">
    <div class="totalinventory">
        <div class="numpp">#</div> <!-- 20px -->
        <div class="itemnum">Item #</div> <!-- 45px -->
        <div class="itemname">Shape</div>
        <div class="itemcolor">Color</div> <!-- Shape - 166px Color 90px -->
        <div class="maxinvent">Suggested</div>
        <!-- Suggested 71px -->
        <div class="instock">In Stock</div> <!-- 50px -->
    </div>
    <div class="headonboat">
        <?=$onboathead?>
    </div>
    <div class="specinventhead">
        <div class="aftercontainers">%</div>
        <div class="needtomake">Need</div>
    </div>
    <!-- % After Containers width 34px -->
    <!-- Need to Make width 55px -->
    <div class="totcosea">
        Cost Ea <!-- <div class="costea"></div> -->
    </div>
    <div class="proofstab">
        <div class="specs">Specs</div>
        <div class="draw">Draw</div>
        <div class="pics">Pics</div>
    </div>
</div>
<div class="inventorytablebody">
    <div class="inventorytableleft"></div>
    <div class="inventoryonboatarea"></div>
    <div class="inventorytableright"></div>
</div>


<div class="boat_download">
    
        <div class="left_block">&nbsp;</div>

        <div class="onboatarea">
            <div class="after_head" style="width: <?= $width ?>px; margin-left: <?=$margin?>px">
                <?=$download_view?>
            </div>
        </div>

</div>
