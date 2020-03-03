<div class="inventorytablehead">
    <div class="totalinventory">
        <div class="numpp">#</div> 
        <div class="itemnum">Item #</div>
        <div class="itemname">Shape / Color</div> 
        <div class="instock">In Stock</div>
        <div class="reserved">Reserved</div>
        <div class="available">Available</div>
    </div>
    <div class="headonboat">
        <?=$onboathead?>
    </div>    
    <div class="proofstab">
        <div class="specs">Specs</div>
        <div class="platetemp">Plate Temp</div>
        <div class="prooftemp">Proof Temp</div>
        <div class="draw">Draw</div>
        <div class="pics">Pics</div>
    </div>
</div>
<div class="totalinventorysum" id="inventsalesreptotal">
    <?=$invetorytotal?>
</div>
<div class="proofstabsum">&nbsp;</div>
<!-- <div class="inventorytablebody <?=($permission == "Profit" ? '' : 'short')?>"> -->
<div class="inventorytablebody <?=($permission == "Profit" ? '' : '')?>">
    <div class="inventorytableleft"></div>
    <div class="inventoryonboatarea"></div>
    <div class="inventorytableright"></div>
</div>
