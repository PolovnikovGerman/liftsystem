<div class="inventoryheadrow">
    <div class="additem">&nbsp;</div>
    <div class="showonlinemaxvalue">[show max]</div>
    <div class="head_text">
        <div class="total_inventory">Total Inventory</div>
    </div>
    <!-- #ff9c00 - Getting Low -->
    <?php if($permission == "Profit") { ?>
        <div class="additcost">
            <div class="labeltxt">Add’l:</div>
            <div class="value">
                <input type="text" class="addlcostinpt" id="invaddvcost" value="$<?= $addcost ?>"/>
            </div>
        </div>
    <?php } ?>
</div>
<div class="inventorytablehead">
    <div class="totalinventory">
        <div class="numpp">#</div> <!-- 20px -->
        <div class="itemnum">Item #</div> <!-- 45px -->
        <div class="itemname">Shape / Color</div> <!-- Shape - 166px Color 90px -->
        <div class="itempercent">&percnt;</div>
        <div class="maxinvent">Max</div>
        <!-- Suggested 71px -->
        <div class="instock">In Stock</div> <!-- 50px -->
        <div class="reserved">Reserved</div>
        <div class="available">Available</div>
    </div>
    <div class="headonboat">
        <?=$onboathead?>
    </div>    
    <!-- % After Containers width 34px -->
    <!-- Need to Make width 55px -->
    <?php if($permission == "Profit") { ?>
        <div class="totcosea">
            <div class="costea">Cost Ea</div>
            <div class="totalea">Total Ea:</div>
        </div>
    <?php } ?>
    <div class="proofstab">
        <div class="specs">Specs</div>
        <div class="platetemp">Plate Temp</div>
        <div class="prooftemp">Proof Temp</div>
        <div class="draw">Draw</div>
        <div class="pics">Pics</div>
    </div>
</div>
<div class="totalinventorysum" id="inventtotal">
    <?=$invetorytotal?>
</div>
<?php if($permission == "Profit") { ?>
    <div class="totcoseasum">&nbsp;</div>
<?php } ?>
<?php if ($permission=="Profit") { ?>
    <div class="proofstabsum">&nbsp;</div>
<?php } else { ?>
    <div class="proofstabsum" style="margin-left: 282px;">&nbsp;</div>
<?php } ?>
<div class="inventorytablebody <?=($permission == "Profit" ? '' : 'short')?>">
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

