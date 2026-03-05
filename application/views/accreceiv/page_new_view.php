<div class="accreceiv-content">
    <input type="hidden" id="accreceivebrand" value="<?=$brand?>">
    <input type="hidden" id="accreceiverefundsort" value="order_date"/>
    <input type="hidden" id="accreceiverefunddir" value="asc"/>
    <div class="accreceiv-content-data">
        <div class="accreceiv-content-left <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
            <div class="datarow">
                <div class="accreceive-title">
                    <div class="accreceiv-label">Accounts Receivable</div>
                </div>
            </div>
            <div class="datarow">
                <div class="accreceiv-totalown"></div>
                <div class="accreceiv-totalpast"></div>
                <div class="accreceiv-period">
                    <span>Display: </span>
                    <select class="accreceiv-period-select">
                        <option value="3">Last 3 Years</option>
                        <option value="5">Last 5 Years</option>
                        <option value="-1">All Years</option>
                    </select>
                </div>
                <div class="accreceiv-period">
                    <span>Sort: </span>
                    <select class="accreceiv-sort-select">
                        <option value="batch_due">Date Due</option>
                        <option value="owntype">Type</option>
                        <option value="balance">Balance Owed</option>
                        <option value="artapprove">Art Approval</option>
                    </select>
                </div>
                <div class="accreceiv-export">
                    <div class="accreceiv-exportbtn"><i class="fa fa-file-excel-o"></i> Export</div>
                </div>
                <div class="accreceiv-print">
                    <div class="accreceiv-printbtn"><i class="fa fa-print"></i> Print</div>
                </div>
            </div>
            <div class="datarow">
                <div class="accreceiv-totalapproved"></div>
            </div>
            <div class="datarow">
                <!-- place for edit note -->
                <!-- approved table head -->
                <div class="approvedowntablehead">
                    <div class="accreceiv-owndetails-headnum">#</div>
                    <div class="accreceiv-owndetails-headapproval ownsort" data-sort="ownapprove">Approval</div>
                    <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
                    <div class="accreceiv-owndetails-headdays"># Days</div>
                    <div class="accreceiv-owndetails-headdue ownsort" data-sort="batch_due">Due </div>
                    <div class="accreceiv-owndetails-headtype ownsort " data-sort="owntype">Type <span></span></div>
                    <div class="accreceiv-owndetails-headbalance ownsort" data-sort="balance">Balance</div>
                    <?php if ($brand=='ALL') : ?>
                        <div class="accreceiv-owndetails-headbrand ownsort" data-sort="brand"><span></span></div>
                    <?php endif; ?>
                    <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order</div>
                    <div class="accreceiv-owndetails-headconfirm ownsort" data-sort="order_num">conf #</div>
                    <?php if ($brand=='SR') : ?>
                        <div class="accreceiv-owndetails-headponumber ownsort" data-sort="customer_ponum">Cust PO#</div>
                    <?php endif; ?>
                    <div class="accreceiv-owndetails-headcustomer ownsort" data-sort="customer_name">Customer <span></span></div>
                    <div class="accreceiv-owndetails-headstatus ownsort" data-sort="debt_status">Status</div>
                </div>
            </div>
            <div class="datarow">
                <div class="approvedowntablebody" id="approvedowntablebody"></div>
            </div>
            <div class="datarow">
                <div class="accreceiv-totalnotapproved"></div>
            </div>
            <div class="datarow">
                <div class="accreceiv-owndetails-bodystatusedit">&nbsp;</div>
                <div class="notapprovedowntablehead">
                    <div class="accreceiv-owndetails-headnum">#</div>
                    <div class="accreceiv-owndetails-headapproval ownsort" data-sort="ownapprove">Approval</div>
                    <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
                    <div class="accreceiv-owndetails-headdays"># Days</div>
                    <div class="accreceiv-owndetails-headdue ownsort" data-sort="batch_due">Due </div>
                    <div class="accreceiv-owndetails-headtype ownsort " data-sort="owntype">Type <span></span></div>
                    <div class="accreceiv-owndetails-headbalance ownsort" data-sort="balance">Balance</div>
                    <?php if ($brand=='ALL') : ?>
                        <div class="accreceiv-owndetails-headbrand ownsort" data-sort="brand"><span></span></div>
                    <?php endif; ?>
                    <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order</div>
                    <div class="accreceiv-owndetails-headconfirm ownsort" data-sort="order_num">conf #</div>
                    <?php if ($brand=='SR') : ?>
                        <div class="accreceiv-owndetails-headponumber ownsort" data-sort="customer_ponum">Cust PO#</div>
                    <?php endif; ?>
                    <div class="accreceiv-owndetails-headcustomer ownsort" data-sort="customer_name">Customer <span></span></div>
                    <div class="accreceiv-owndetails-headstatus ownsort" data-sort="debt_status">Status</div>
                </div>
            </div>
            <div class="datarow">
                <div class="datarow">
                    <div class="notapprovedowntablebody" id="notapprovedowntablebody"></div>
                </div>
            </div>
        </div>
        <div class="accreceiv-content-center <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
            <div class="datarow">
                <div class="accreceiv-content-right"></div>
            </div>
            <div class="datarow">
                <div class="accreceiv-totalrefund"></div>
                <div class="totalrefund-export">
                    <div class="totalrefund-exportbtn"><i class="fa fa-file-excel-o"></i> Export</div>
                </div>
            </div>
            <div class="datarow">
                <div class="accreceiv-refunddetails-head">
                    <div class="accreceiv-refunddetails-headnum">#</div>
                    <div class="accreceiv-refunddetails-headorderdate refundsort <?=$refundsort=='order_date' ? 'activesort'.$brand : ''?>" data-sort="order_date">Order Date</div>
                    <div class="accreceiv-refunddetails-headbalance refundsort <?=$refundsort=='balance' ? 'activesortbt' : ''?>" data-sort="balance">Refund</div>
                    <div class="accreceiv-refunddetails-headorder refundsort <?=$refundsort=='order_num' ? 'activesortbt' : ''?>" data-sort="order_num">Order</div>
                    <div class="accreceiv-refunddetails-headcustomer refundsort <?=$refundsort=='customer_name' ? 'activesortbt' : ''?>" data-sort="customer_name">Customer</div>
                </div>
            </div>
            <div class="datarow">
                <div class="accreceiv-refunddetails-body" id="accreceiv-refunddetails-body"></div>
            </div>
        </div>
    </div>
</div>