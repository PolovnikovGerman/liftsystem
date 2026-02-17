<div class="accreceiv-content">
    <input type="hidden" id="accreceivebrand" value="<?=$brand?>">
<!--    <input type="hidden" id="accreciveownsort" value="batch_due"/>-->
<!--    <input type="hidden" id="accreciveowndir" value="asc"/>-->
<!--    <input type="hidden" id="accreciveownsort2" value="ownapprove"/>-->
    <input type="hidden" id="accreceiverefundsort" value="order_date"/>
    <input type="hidden" id="accreceiverefunddir" value="asc"/>
    <div class="accreceive-title">
        <div class="accreceiv-label">Accounts Receivable</div>
    </div>
    <div class="accreceiv-content-data">
        <div class="accreceiv-totals">
            <div class="accreceiv-content-left <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
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
                        <option value="owntype">Type</option>
                        <option value="batch_due">Date Due</option>
                        <option value="balance">Balance Owed</option>
                        <option value="artapprove">Art Approval</option>
                    </select>
                </div>
                <div class="accreceiv-export">
                    <div class="accreceiv-exportbtn">
                        <i class="fa fa-file-excel-o"></i>
                        Export
                    </div>
                </div>
                <div class="accreceiv-print">
                    <div class="accreceiv-printbtn">
                        <i class="fa fa-print"></i>
                        Print
                    </div>
                </div>
            </div>
            <div class="accreceiv-content-center <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
                <div class="accreceiv-totalrefund">
                </div>
                <div class="totalrefund-export">
                    <div class="totalrefund-exportbtn">
                        <i class="fa fa-file-excel-o"></i>
                        Export
                    </div>
                </div>
                <div class="accreceiv-content-right"></div>
            </div>
        </div>
        <div class="accreceiv-details"></div>
    </div>
</div>
