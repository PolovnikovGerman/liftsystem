<input type="hidden" id="onlineordersbrand" value="<?=$brand?>"/>
<input type="hidden" id="online_totalrec" value="<?=$total_rec?>"/>
<input type="hidden" id="online_curpage" value="0"/>
<input type="hidden" id="online_perpage" value="<?=$perpage?>"/>
<input type="hidden" id="online_orderby" value="<?=$order_by?>"/>
<input type="hidden" id="online_direction" value="<?=$direction?>"/>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="row oor-form">
            <div class="col-4 pr-1">
                <div class="form-group">
                    <label>Our#, Rep</label>
                    <input type="text" class="form-control" id="online_replica">
                </div>
            </div>
            <div class="col-4 px-1">
                <div class="form-group">
                    <label>Confirmation</label>
                    <input type="text" class="form-control" id="online_confirm">
                </div>
            </div>
            <div class="col-4 pl-1">
                <div class="form-group">
                    <label>Customer</label>
                    <input type="text" class="form-control" id="online_customer">
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 oor-top">
        <div class="row">
            <div class="col-5 pr-1">
                <div class="btn-searchall" id="find_onlines">Search It</div>
                <div class="btn-clear" id="clear_it">Clear</div>
            </div>
            <div class="col-7 pl-1">
                <nav aria-label="" class="list-pages">
                    <div id="onlinePagination"></div>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12">
        <div class="table-responsive">
            <table class="table table-onlineorders table-borderless">
                <thead>
                    <tr class="oor-title">
                        <th scope="col"  colspan="3"><div class="oor-td oor-status">Status</div></th>
                        <th scope="col"><div class="oor-td oor-our">Our #</div></th>
                        <th scope="col"><div class="oor-td oor-rep">Rep</div></th>
                        <th scope="col"><div class="oor-td oor-date">Date</div></th>
                        <th scope="col"><div class="oor-td oor-confirmation">Confirmation</div></th>
                        <th scope="col"><div class="oor-td oor-name">Name</div></th>
                        <th scope="col"><div class="oor-td oor-company">Company</div></th>
                        <th scope="col"><div class="oor-td oor-item">Item</div></th>
                        <th scope="col"><div class="oor-td oor-amount">Amount</div></th>
                        <th scope="col"><div class="oor-td oor-export">Export</div></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
