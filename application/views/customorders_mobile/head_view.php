<div class="row">
    <div class="col-12 col-sm-12">
        <div class="legend-info">
            <div class="row">
                <div class="col-2 px-0 legend-block lbblack">0% and under</div>
                <div class="col-2 px-0 legend-block lbmaroon">1% - 9%</div>
                <div class="col-2 px-0 legend-block lbred">10% - 19%</div>
                <div class="col-2 px-0 legend-block lborange">20% - 29%</div>
                <div class="col-2 px-0 legend-block lbwhite">30% - 39%</div>
                <div class="col-2 px-0 legend-block lbgreen">40% and higher</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="row">
            <div class="col-6 sel-profitorder-filtrer">
                <select>
                    <option>Display All</option>
                    <option>Orders with Balances Only</option>
                </select>
            </div>
            <div class="col-6 sel-perpage_profitorders">
                <select>
                    <option>100 records/per page</option>
                    <option>150 records/per page</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="row leadord-search-block">
            <div class="col-7 pr-1 leadord_search">
                <input type="text" name="" placeholder="Enter order #, customer, email">
            </div>
            <div class="col-5 px-1">
                <div class="btn-searchall">Search It</div>
                <div class="btn-clear">Clear</div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <nav aria-label="" class="list-pages">
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="" tabindex="-1" aria-disabled="true">&lt;&lt;</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item" aria-current="page">
                    <a class="page-link" href="">2</a>
                </li>
                <li class="page-item"><a class="page-link" href="">3</a></li>
                <li class="page-item"><a class="page-link" href="">...</a></li>
                <li class="page-item"><a class="page-link" href="">18</a></li>
                <li class="page-item">
                    <a class="page-link" href="">&gt;&gt;</a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="col-12 profit-filterdate-area">
        <div class="row">
            <div class="col-12 col-sm-6">
                <label>Display:</label>
                <div class="checkpint">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                </div>
                <div class="excludeorderqbooklabel">Exclude Orders Found in Quickbooks</div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="" id="" value="option1" checked>
                    <div class="form-check-label" for="">
                        <select class="sel-orderyeardat">
                            <option>All time</option>
                            <option>2024</option>
                            <option>2023</option>
                        </select>
                        <select class="sel-ordermonth">
                            <option>All Months</option>
                            <option>January</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="" id="" value="option1" checked>
                    <label>Custom:</label>
                    <input type="text" name="" class="datesinpt">
                    <label class="dateslabelto">to</label>
                    <input type="text" name="" class="datesinpt">
                    <select class="sel-shiplocationdat">
                        <option>All Locations</option>
                        <option>United States</option>
                    </select>
                    <select class="sel-ordertypesdat">
                        <option>All Orders</option>
                        <option>New Only</option>
                        <option>Repeat Only</option>
                    </select>
                    <div class="btn-export">Export</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Body -->
    <div class="col-12 col-sm-12">
        <div class="table-responsive">
            <table class="table tbl-leads-custord-top table-borderless">
                <tr class="custord-toptitle">
                    <th scope="col"><div class="custord-td custord-orders">Orders</div></th>
                    <th scope="col"><div class="custord-td custord-new">New</div></th>
                    <th scope="col"><div class="custord-td custord-repeat">Repeat</div></th>
                    <th scope="col"><div class="custord-td custord-blank">Blank</div></th>
                    <th scope="col"><div class="custord-td custord-qty">QTY</div></th>
                    <th scope="col"><div class="custord-td custord-item">&nbsp;</div></th>
                    <th scope="col"><div class="custord-td custord-revenue">Revenue</div></th>
                    <th scope="col"><div class="custord-td custord-balance">Balance</div></th>
                    <th scope="col" colspan="2"><div class="custord-td custord-shipping">Shipping</div></th>
                    <th scope="col"><div class="custord-td custord-tax">Tax</div></th>
                    <th scope="col"><div class="custord-td custord-cog">COG</div></th>
                    <th scope="col"><div class="custord-td custord-profit">Profit</div></th>
                    <th scope="col"><div class="custord-td custord-percent">%</div></th>
                </tr>
                <tr class="custord-top">
                    <th><div class="custord-td custord-orders">2372</div></th>
                    <th><div class="custord-td custord-new">2K (63%)</div></th>
                    <th><div class="custord-td custord-repeat">848 (36%)</div></th>
                    <th><div class="custord-td custord-blank">22 (1%)</div></th>
                    <th><div class="custord-td custord-qty">3,327,395</div></th>
                    <th><div class="custord-td custord-item">&nbsp;</div></th>
                    <th><div class="custord-td custord-revenue">$8.55M</div></th>
                    <th><div class="custord-td custord-balance">$917.6K</div></th>
                    <th colspan="2"><div class="custord-td custord-shipping">$216.89K</div></th>
                    <th><div class="custord-td custord-tax">$5.4K</div></th>
                    <th><div class="custord-td custord-cog">$4.1M</div></th>
                    <th><div class="custord-td custord-profit custord-bggreen">$4.0M</div></th>
                    <th><div class="custord-td custord-percent custord-bggreen">47%</div></th>
                </tr>
            </table>
            <!-- Data table -->
            <table class="table tbl-leads-custord table-borderless">
                <thead>
                    <tr class="custord-title">
                        <th scope="col" colspan="2"><div class="custord-td custord-tddates">Date</div></th>
                        <th scope="col" colspan="2"><div class="custord-td custord-orders">Orders</div></th>
                        <th scope="col"><div class="custord-td custord-conf">Conf #</div></th>
                        <th scope="col"><div class="custord-td custord-customer">Customer</div></th>
                        <th scope="col"><div class="custord-td custord-qty">QTY</div></th>
                        <th scope="col"><div class="custord-td custord-item">Item</div></th>
                        <th scope="col"><div class="custord-td custord-revenue">Revenue</div></th>
                        <th scope="col"><div class="custord-td custord-balance">Balance</div></th>
                        <th scope="col" colspan="2"><div class="custord-td custord-shipping">Shipping</div></th>
                        <th scope="col"><div class="custord-td custord-shipdate">Date</div></th>
                        <th scope="col"><div class="custord-td custord-tax">Tax</div></th>
                        <th scope="col"><div class="custord-td custord-cog">COG</div></th>
                        <th scope="col"><div class="custord-td custord-profit">Profit</div></th>
                        <th scope="col"><div class="custord-td custord-percent">%</div></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><div class="custord-td custord-date">06/13/24</div></th>
                        <th><div class="custord-td custord-datepreview">&nbsp;</div></th>
                        <th><div class="custord-td custord-numorder">64655</div></th>
                        <th><div class="custord-td custord-ordertype ordertyperepeat">R</div></th>
                        <th><div class="custord-td custord-conf">VG-66735</div></th>
                        <th><div class="custord-td custord-customer">Neuroptics</div></th>
                        <th><div class="custord-td custord-qty">1000</div></th>
                        <th><div class="custord-td custord-item">Custom Colored Eyeball</div></th>
                        <th><div class="custord-td custord-revenue">$2,888.00</div></th>
                        <th><div class="custord-td custord-balance custord-bgblue">$2,888.00</div></th>
                        <th><div class="custord-td custord-shipping-calc">
                                <i class="fa fa-square-o" aria-hidden="true"></i>
                            </div></th>
                        <th><div class="custord-td custord-shipping-data">$98.00</div></th>
                        <th><div class="custord-td custord-shipdate">07/08</div></th>
                        <th><div class="custord-td custord-tax">&ndash;</div></th>
                        <th><div class="custord-td custord-cog projectcog">project</div></th>
                        <th><div class="custord-td custord-profit custord-bgdarkblue">$1,155.20</div></th>
                        <th><div class="custord-td custord-percent custord-bgdarkblue">PROJ</div></th>
                    </tr>
                    <tr class="greyline">
                        <th><div class="custord-td custord-date">06/13/24</div></th>
                        <th><div class="custord-td custord-datepreview">&nbsp;</div></th>
                        <th><div class="custord-td custord-numorder">64649</div></th>
                        <th><div class="custord-td custord-ordertype ordertyperepeat">R</div></th>
                        <th><div class="custord-td custord-conf">QG-27637</div></th>
                        <th><div class="custord-td custord-customer">USDA</div></th>
                        <th><div class="custord-td custord-qty">1850</div></th>
                        <th><div class="custord-td custord-item">Stick of Butter Stress Balls</div></th>
                        <th><div class="custord-td custord-revenue">$4,994.50</div></th>
                        <th><div class="custord-td custord-balance custord-bgblue">$4,994.50</div></th>
                        <th><div class="custord-td custord-shipping-calc">
                                <i class="fa fa-check-square-o" aria-hidden="true"></i>
                            </div></th>
                        <th><div class="custord-td custord-shipping-data">$143.00</div></th>
                        <th><div class="custord-td custord-shipdate">07/08</div></th>
                        <th><div class="custord-td custord-tax">&ndash;</div></th>
                        <th><div class="custord-td custord-cog">
                                <div class="profitorder-addlnk">
                                    <a href="" class="editcoglnk">*</a>
                                </div>
                                $2,530.00
                            </div></th>
                        <th><div class="custord-td custord-profit custord-bggreen">$2,321.50</div></th>
                        <th><div class="custord-td custord-percent custord-bggreen">46.5%</div></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>