<div class="printshoparea">
    <div class="printshopcontent">
        <input type="hidden" id="orderreptotals" value="<?= $totals ?>"/>
        <input type="hidden" id="orderrepperpage" value="250"/>
        <input type="hidden" id="orderrepcurpage" value="0"/>
        <input type="hidden" id="report_year" value=""/>

        <div class="orderreportheadrow">
            <div class="search_orderreports">
                <img src="/img/icons/magnifier.png">
                <input placeholder="Enter order #, customer, item" value="" class="reportorder_searchdata">
                <div class="orderreport_findall">&nbsp;</div>
                <div class="orderreport_clear">&nbsp;</div>
            </div>
            <div class="orederreportsaddcosts">
                <!-- <div class="editsign"><i class="fa fa-pencil" aria-hidden="true"></i></div> -->
                <div class="labeltxt">Repaid / Saved:</div>
                <div class="costval">
                    <input type="text" class="orderreportsaved" value="<?= $repaid_cost ?>"/>
                </div>        
                <!-- <div class="editsign"><i class="fa fa-pencil" aria-hidden="true"></i></div> -->
                <div class="labeltxt">Orange Plate:</div>
                <div class="costval">
                    <input type="text" class="orderreportplatecost" data-fldname="orangeplate_price" value="<?= $orangeplate_price ?>"/>
                </div>
                <!-- <div class="editsign"><i class="fa fa-pencil" aria-hidden="true"></i></div> -->
                <div class="labeltxt">Blue Plate:</div>
                <div class="costval">
                    <input type="text" class="orderreportplatecost" data-fldname="blueplate_price" value="<?= $blueplate_price ?>"/>
                </div>        
            </div>
            <div class="orderreport_legend">
                <div class="signlegend systeminpt"><i class="fa fa-square" aria-hidden="true"></i></div>
                <div class="labeltxt">System Entered</div>
                <div class="signlegend manualinpt"><i class="fa fa-square" aria-hidden="true"></i></div>
                <div class="labeltxt">Manually Entered</div>
            </div>            
        </div>
        <div class="orderreportheadrow">
            <div class="orderreport_filter">
                <div class="labeltxt active" data-year="">All / </div>
                <?php foreach ($report_years as $row) {?>
                    <div class="labeltxt" data-year="<?=$row['year_amount']?>"> <?=$row['year_amount']?> / </div>
                <?php } ?>
            </div>
            <div class="orderreport_export">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Report
            </div>
            <div class="orderreport_pagination"></div>
        </div>
        <div class="orderreporttablehead">
            <div class="bgntable">&nbsp;</div>
            <div class="orderdate greycel">Date</div>
            <div class="ordernum blackcel">Order</div>
            <div class="customer blackcel">Customer</div>
            <div class="itemname blackcel">Shape</div>
            <div class="itemcolor blackcel">Color</div>    
            <div class="shipped greycel">Shipped</div>
            <div class="kepted greycel">We Kept</div>
            <div class="misprints greycel">Misprnt</div>
            <div class="misprintproc greycel">&percnt;</div>
            <div class="totalqty blackcel">Total QTY</div>
            <div class="costea greycel">Cost EA</div>
            <div class="addlcost greycel">Add&apos;l Extra</div>
            <div class="totalea greycel">Total EA</div>
            <div class="totaladdlcost greycel">Total Extra</div>
            <div class="itemscost blackcel">Items Cost</div>
            <div class="oranplate greycel">Oran Plate</div>
            <div class="blueplate greycel">Blue Plate</div>
            <div class="totalplate greycel">Total Plate</div>
            <div class="platecost blackcel">Plate Cost</div>
            <div class="totalcost blackcel">Total Cost</div>
            <div class="misprintcost greycel">Misprint Cost</div>
            <div class="endtable">&nbsp;</div>
        </div>
        <div id="orderreportsummaryarea" class="summaryrow"><?= $summary ?></div>
        <div class="orderreporttablebody">    
            <div id="orderreportdataarea">&nbsp;</div>
        </div>
        <div id="neworderprofitview" class="neworderprofitview"></div>    
    </div>
</div>
<input type="hidden" id="printshopreportbrand" value="<?=$brand?>"/>
<div id="printshopreportbrandmenu">
    <?=$top_menu?>
</div>
