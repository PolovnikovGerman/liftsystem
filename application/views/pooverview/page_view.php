<input type="hidden" id="pototalsbrand" value="<?=$brand?>"/>
<input type="hidden" id="pohistoryyearview" value="<?=$curyear?>"/>
<input type="hidden" id="pohistoryslider" value="0"/>
<input type="hidden" id="sliderright" value="0"/>
<input type="hidden" id="domesticpoyear" value="1"/>
<input type="hidden" id="custompoyear" value="1"/>
<div class="pooverdataview">
    <div class="datarow">
        <div class="pooverviewtitle">PO Overview</div>
        <div class="pohistoryviewlink">History of Placed POs <span><i class="fa fa-caret-right" aria-hidden="true"></i></span></div>
    </div>
    <div class="datarow">
        <div class="pooverleftpart">
            <div class="pooverviewsubtitle">Domestic / Other List to Place:</div>
            <div class="poplacehideoldorders">
                <span class="chkpodomestic"><i class="fa fa-check-square-o"></i></span>
                hide > 52 weeks
            </div>
        </div>
        <div class="pooverrightpart">
            <div class="pooverviewsubtitle">Custom Shaped Orders to Place:</div>
            <div class="poplacehideoldorders">
                <span class="chkpocustom"><i class="fa fa-check-square-o"></i></span>
                hide > 52 weeks
            </div>
        </div>
    </div>
    <div class="pooverleftpart">
        <div class="datarow">
            <div class="pooverviewdomestictabletitle">
                <div class="rush">Rush</div>
                <div class="approved">Approved</div>
                <div class="vendor">Vendor</div>
                <div class="ordernum">Order #</div>
                <div class="itemname">Item</div>
                <div class="itemqty">Qty</div>
                <div class="remainqty">Remaining</div>
            </div>
        </div>
        <div class="datarow">
            <div class="pooverviewdomestictablearea">&nbsp;</div>
        </div>
    </div>
    <div class="pooverrightpart">
        <div class="datarow">
            <div class="pooverviewcustomtabletitle">
                <div class="arrivedays"># Days</div>
                <div class="eventdate">Event</div>
                <div class="arrivedate">Arrival</div>
                <div class="approved">Proof</div>
                <div class="ordernum">Order #</div>
                <div class="customer">Customer</div>
                <div class="itemname">Item</div>
                <div class="itemqty">Qty</div>
                <div class="remainqty">Remaining</div>
            </div>
        </div>
        <div class="datarow">
            <div class="pooverviewcustomtablearea">&nbsp;</div>
        </div>
    </div>
</div>
<div class="pohistorydataview">
    <div class="datarow">
        <div class="pooverviewlink"><span><i class="fa fa-caret-left" aria-hidden="true"></i></span> PO Overview </div>
    </div>
    <div class="datarow">
        <div class="pohistorytitle">History of POs Sent</div>
    </div>
    <div class="datarow">
        <div class="pohistcalendartable">
            <div class="pohcalendartitletbl">
                <div class="listyears">
                    <?php foreach ($years as $year): ?>
                        <div class="yearbox <?=$year['year']==$curyear ? 'active' : ''?>" data-year="<?=$year['year']?>"><?=$year['year']?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="pohcald-tblbody">&nbsp;</div>
        </div>
        <div class="pohistinfdaytable"></div>
    </div>
    <div class="povendorallyears">
        <div class="povendor-arrowleft">
            <i class="fa fa-caret-left" aria-hidden="true"></i>
        </div>
        <div class="pohistory-slider-area" style="max-width: 1260px; height: 460px; overflow-x: hidden">&nbsp;</div>
        <div class="povendor-arrowright">
            <i class="fa fa-caret-right" aria-hidden="true"></i>
        </div>
    </div>
</div>