<input type="hidden" id="totalordlists" value="<?=$total?>"/>
<input type="hidden" id="leadordlistpage" value="0"/>
<input type="hidden" id="orderlistsviewbrand" value="<?=$brand?>"/>
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
                        <input type="text" class="leadordlst_searchdata" placeholder="Enter order #, customer, email">
                    </div>
                </div>
            </div>
            <div class="col-5 p-1">
                <div class="btn-searchall leadorderlst_findall">Search It</div>
                <div class="btn-clear leadorderlst_clear">Clear</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="row">
            <div class="col-7 pr-1 rec_per_page">
                <div class="form-group row">
                    <label for="inputPassword" class="col-3 p-1 col-form-label">Display:</label>
                    <div class="col-9 p-1">
                        <select id="leadordlistperpage">
                            <?php foreach ($perpage as $row) { ?>
                                <option value="<?=$row?>"><?=$row?> records/per page</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
<!--            <div class="col-5 sel-view">-->
<!--                <div class="form-group row">-->
<!--                    <div class="col-12">-->
<!--                        <select>-->
<!--                            <option>View All</option>-->
<!--                            <option>Website Only</option>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
    <div class="col-12 col-sm-12">
        <div class="row">
            <div class="col-8 offset-4">
                <nav aria-label="" class="list-pages">
                    <div class="leadord_pagination" id="leadordlist_pagination"></div>
                </nav>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12">
        <div class="table-responsive">
            <table class="table table-orderslist table-borderless">
                <thead>
                <tr class="olt-title">
                    <th scope="col">
                        <div class="olt-td olt-number">#</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-date">Date</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-order">Order#</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-customer">Customer</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-qty">Qty</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-itemnum">Item #</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-item">Item</div>
                    </th>
                    <th scope="col">
                        <div class="olt-td olt-revenue">Revenue</div>
                    </th>
                    <th scope="col"><div class="olt-td olt-null">&nbsp;</div></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
