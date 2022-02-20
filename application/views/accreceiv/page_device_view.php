<input type="hidden" id="accreceivebrand" value="<?=$brand?>">
<input type="hidden" id="accreciveownsort" value="batch_due"/>
<input type="hidden" id="accreciveowndir" value="desc"/>
<input type="hidden" id="accreceiverefundsort" value="order_date"/>
<input type="hidden" id="accreceiverefunddir" value="desc"/>
<main class="container-fluid">
    <div class="accreceivedataview">
        <div class="pageheader">
            <div class="row pt-2">
                <div class="col-12 col-sm-5 col-md-5 col-lg-4 col-xl-5">
                    <div class="pagetitle">Accounts Receivable</div>
                </div>
                <div class="col-12 col-sm-7 col-md-7 col-lg-8 col-xl-7">
                    <div class="row mb-3">
                        <div class="col-12 accreceiv-period">
                            <span>Display: </span>
                            <select class="accreceiv-period-select">
                                <option value="3">Last 3 Years</option>
                                <option value="5">Last 5 Years</option>
                                <option value="-1">All Years</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3 accrecive-totalsarea mobileviewonly"></div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xs-8 accreceiv-content-data">
                <div class="row accreceiv-content-left">
                    <div class="col-12 accreceiv-details-totals"></div>
                    <div class="col-12 accreceiv-details"></div>
                </div>
                <div class="accreceiv-content-center">
                    <div class="accreceiv-details-totals"></div>
                    <div class="accreceiv-details"></div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xs-4 accreceiv-content-right desktopviewonly"></div>
        </div>
    </div>
</main>