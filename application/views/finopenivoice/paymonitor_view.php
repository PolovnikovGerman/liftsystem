<div class="paymonitor-content">
    <input type="hidden" id="orderbytab4" value="<?=$order?>"/>
    <input type="hidden" id="directiontab4" value="<?=$direc?>"/>
    <input type="hidden" id="totaltab4" value="<?=$total?>"/>
    <input type="hidden" id="curpagetab4" value="<?=$curpage;?>"/>
    <div class="monitor-head">
        <div class="monitor-head-totals">
            <div class="total_notinvoiced">Total Not Invoiced <?=$total_inv?></div>
            <div class="total_notpaid">Partial Paid Orders <?=$total_paid?></div>
            <div class="total_notinvoiced_qty">Qty Not Invoiced <?=$qty_inv?></div>
            <div class="total_notpaid_qty">Qty Partial Paid <?=$qty_paid?></div>
        </div>
        <div class="openinvoiceperpage"><?=$perpage_view?></div>
        <div class="showpaid">
            <select id="addpayfilter" name="addpayfilter">
                <option value="4"  <?=($paid==4 ? 'selected="selected"' : '')?>>Only ready to inv</option>
                <option value="1" <?=($paid==1 ? 'selected="selected"' : '')?>>Only not invoiced</option>
                <option value="2" <?=($paid==2 ? 'selected="selected"' : '')?>>Only not paid</option>
                <option value="3" <?=($paid==3 ? 'selected="selected"' : '')?>>All orders</option>
            </select>
        </div>
    </div>
    <div class="monitor-head inner">
        <div class="adminsearchform">
            <?=$searchform?>
        </div>
        <div class="accounting_pagination" id="paymentmonitor_pagination"></div>
    </div>
    <div class="paymonitor-table">
        <div class="paymonitor-title">
            <div class="paymonitor-approved">&nbsp;</div>
            <div class="paymonitor-orderdate">Date</div>
            <div class="paymonitor-numorder paymonitorsort <?=($order=='order_num' ? 'paymsortactive' : '')?>" id="monitorsort_order">
                <div class="sortcalclnk">
                    <?php if ($order=='order_num') {?>
                        <?php if ($direc=='desc') {?>
                            <img alt="Sort" src="/img/icons/sort_down.png"/>
                        <?php } else { ?>
                            <img alt="Sort" src="/img/icons/sort_up.png"/>
                        <?php } ?>
                    <?php } else { ?>                        &nbsp;
                    <?php } ?>
                </div>
                Order #
            </div>
            <div class="paymonitor-customer">Customer</div>
            <div class="paymonitor_ccfee">&nbsp;</div>
            <div class="paymonitor-revenue paymonitorsort <?=($order=='revenue' ? 'paymsortactive' : '')?>" id="monitorsort_revenue">
                <div class="sortcalclnk">
                    <?php if ($order=='revenue') {?>
                        <?php if ($direc=='desc') {?>
                            <img alt="Sort" src="/img/sort_down.png"/>
                        <?php } else { ?>
                            <img alt="Sort" src="/img/sort_up.png"/>
                        <?php } ?>
                    <?php } else { ?>
                        &nbsp;
                    <?php } ?>
                </div>
                Revenue
            </div>

            <div class="paymonitor-inv">INV</div>
            <div class="paymonitor-revenue">Not Invoiced</div>
            <div class="paymonitor-revenue">Invoiced</div>
            <div class="paymonitor-pay">Pay</div>
            <div class="paymonitor-revenue">Not Paid</div>
            <div class="paymonitor-code">Code</div>
        </div>
        <div class="content-paymonutortable" id="tableinfotab4">
        </div>
    </div>
    <!--
    <div id="editpayment" style="display:none;clear: both; float: left; width:220px; height: 220px">
        <div class="editcogform" style="float: left; width: 210px; height: 109px; background-color: #FFFFFF;">

        </div>
    </div>
    -->
</div>
<input type="hidden" id="paymentmonitorbrand" value="<?=$brand?>">
<div id="paymentmonitorbrandmenu">
    <?=$top_menu?>
</div>

