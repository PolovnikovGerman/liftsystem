<input type="hidden" id="totalleadorders" value="<?=$total?>"/>
<input type="hidden" id="leadorderperpage" value="<?=$cur_page?>"/>
<input type="hidden" id="leadorderactivate" value="<?=$activesearch?>"/>
<input type="hidden" id="ordersviewbrand" value="<?=$brand?>"/>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="row">
            <div class="col-7 pr-1 leadord_search">
                <div class="row">
                    <div class="col-2 pr-0">
                        <div class="ic-magnifier">
                            <img src="/img/page_mobile/magnifier.png">
                        </div>
                    </div>
                    <div class="col-9 p-1">
                        <input type="text" class="leadord_searchdata" placeholder="Enter order #, customer, email">
                    </div>
                </div>
            </div>
            <div class="col-5 p-1">
                <div class="btn-searchall leadorder_findall">Search It</div>
                <div class="btn-clear leadorder_clear">Clear</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="row">
            <div class="col-7 pr-1 rec_per_page">
                <div class="form-group row">
                    <label for="inputPassword" class="col-3 p-1 col-form-label">Display:</label>
                    <div class="col-9 p-1">
                        <select id="leadorderperpage">
                            <option value="250" <?=$default_perpage == 250 ? 'selected' : ''?>>250 records/per page</option>
                            <option value="500" <?=$default_perpage == 500 ? 'selected' : ''?>>500 records/per page</option>
                            <option value="1000" <?=$default_perpage == 1000 ? 'selected' : ''?>>1000 records/per page</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-5 sel-view">
                <div class="form-group row">
                    <div class="col-12">
                        <select>
                            <option>View All</option>
                            <option>Website Only</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12">
        <div class="row">
            <div class="col-4">
                <div class="btn-addnew">add new</div>
            </div>
            <div class="col-8">
                <nav aria-label="" class="list-pages">
                    <div class="leadorder_pagination"></div>
                </nav>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12">
        <div class="table-responsive">
            <table class="table table-orders table-borderless">
                <thead>
                <tr class="tbl-title">
                    <th scope="col">
                        <div class="tbl-td td-order">Order#</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-customer">Customer</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-qty">Qty</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-item">Item</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-color">Color</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-conf">Conf #</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-revenue">Revenue</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-balance">Balance</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-salesrep">Sales Rep</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-class">Class</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-artstatus">Art Status</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-points">Points</div>
                    </th>
                    <th scope="col">
                        <div class="tbl-td td-fulfilled">Fulfilled</div>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

