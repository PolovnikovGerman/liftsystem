<input type="hidden" id="orderreptotals" value="<?= $totals ?>"/>
<input type="hidden" id="orderrepperpage" value="250"/>
<input type="hidden" id="orderrepcurpage" value="0"/>
<div class="orderreportheadrow">
    <div class="search_orderreports">
        <img src="/img/magnifier.png">
        <input placeholder="Enter order #, customer" value="" class="reportorder_searchdata">
        <div class="orderreport_findall">&nbsp;</div>
        <div class="orderreport_clear">&nbsp;</div>
    </div>
    <div class="orederreportsaddcosts">
        <div class="editsign"><i class="fa fa-pencil" aria-hidden="true"></i></div>
        <div class="label">Repaid / Saved:</div>
        <div class="costval">
            <input type="text" class="orderreportsaved" value="<?=$repaid_cost?>"/>
        </div>        
        <div class="editsign"><i class="fa fa-pencil" aria-hidden="true"></i></div>
        <div class="label">Orange Plate:</div>
        <div class="costval">
            <input type="text" class="orderreportplatecost" data-fldname="orangeplate_price" value="<?=$orangeplate_price?>"/>
        </div>
        <div class="editsign"><i class="fa fa-pencil" aria-hidden="true"></i></div>
        <div class="label">Blue Plate:</div>
        <div class="costval">
            <input type="text" class="orderreportplatecost" data-fldname="blueplate_price" value="<?=$blueplate_price?>"/>
        </div>        
    </div>
    <div class="orderreport_legend">
        <div class="signlegend systeminpt"><i class="fa fa-square" aria-hidden="true"></i></div>
        <div class="label">System Entered</div>
        <div class="signlegend manualinpt"><i class="fa fa-square" aria-hidden="true"></i></div>
        <div class="label">Manually Entered</div>
    </div>
    <div class="orderreport_pagination"></div>
</div>
<div class="orderreporttablehead">
    <div class="bgntable">&nbsp;</div>
    <div class="orderdate greycel">Date</div>
    <div class="ordernum blackcel">Order#</div>
    <div class="customer blackcel">Customer</div>
    <div class="itemname blackcel">Shape</div>
    <div class="itemcolor blackcel">Color</div>    
    <div class="shipped greycel">Shipped</div>
    <div class="kepted greycel">We Kept</div>
    <div class="misprints greycel">Misprints</div>
    <div class="misprintproc greycel">&percnt;</div>
    <div class="totalqty blackcel">Total QTY</div>
    <div class="costea greycel">Cost EA</div>
    <div class="addlcost greycel">Add&apos;l Extra</div>
    <div class="totalea greycel">Total EA</div>
    <div class="itemscost blackcel">Items Cost</div>
    <div class="oranplate greycel">Oran Plate</div>
    <div class="blueplate greycel">Blue Plate</div>
    <div class="totalplate greycel">Total Plate</div>
    <div class="platecost blackcel">Plate Cost</div>
    <div class="totalcost blackcel">Total Cost</div>
    <div class="misprintcost greycel">Misprint Cost</div>
    <div class="endtable">&nbsp;</div>
</div>
<div id="orderreportsummaryarea" class="summaryrow"><?=$summary?></div>
<div class="orderreporttablebody">    
    <div id="orderreportdataarea">&nbsp;</div>
</div>
<div id="neworderprofitview" class="neworderprofitview">HELLO</div>