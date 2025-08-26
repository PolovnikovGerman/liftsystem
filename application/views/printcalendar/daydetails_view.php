<div class="maingrey-header">
    <div class="maingrey-title">TODAY - <?=$title?></div>
    <div class="maingrey-infobox">
        <div class="maingreyinfo-prints"><span><?=$prints==0 ? '-' : QTYOutput($prints)?></span> prints</div>
        <div class="maingreyinfo-items"><span><?=$items==0 ? '-' : QTYOutput($items)?></span> items</div>
        <div class="maingreyinfo-orders"><span><?=$orders==0 ? '-' : QTYOutput($orders)?></span> orders</div>
    </div>
    <div class="maingrey-close">
        <i class="fa fa-times" aria-hidden="true"></i>
    </div>
</div>
<div class="warning-section">
    <div class="warning-title"><span>WARNING:</span> For these orders, more items shipped (% Shipped) than were printed (% Fulfilled). This indicates a problem that must be resolved before they can be worked on.</div>
    <div class="warning-table">
        <div class="warntabl-tr warntabl-header">
            <div class="warntabl-apprblock">
                <div class="warntabl-td warntabl-prcful">%Ful</div>
                <div class="warntabl-td warntabl-prcship">%Ship</div>
                <div class="warntabl-td warntabl-approval">Approval</div>
            </div>
            <div class="warntabl-mainblock">
                <div class="warntabl-td warntabl-brand">&nbsp;</div>
                <div class="warntabl-td warntabl-rush">&nbsp;</div>
                <div class="warntabl-td warntabl-order">Order#</div>
                <div class="warntabl-td warntabl-items">#Items</div>
                <div class="warntabl-td warntabl-imp">Imp</div>
                <div class="warntabl-td warntabl-prints">#Prints</div>
                <div class="warntabl-td warntabl-itmcolor">Item Color/s</div>
                <div class="warntabl-td warntabl-description">Item / Description</div>
                <div class="warntabl-td warntabl-inkcolor">Ink Color/s</div>
            </div>
            <div class="warntabl-fulfblock">
                <div class="warntabl-td warntabl-done">Done</div>
                <div class="warntabl-td warntabl-flfremain">Remain</div>
                <div class="warntabl-td warntabl-flfdate">Date</div>
                <div class="warntabl-td warntabl-flfprint">Printed</div>
                <div class="warntabl-td warntabl-flfkept">Kept</div>
                <div class="warntabl-td warntabl-flfmisprt">Misprt</div>
                <div class="warntabl-td warntabl-flftotal">Total</div>
                <div class="warntabl-td warntabl-flfplates">Plates</div>
            </div>
            <div class="warntabl-shipblock">
                <div class="warntabl-td warntabl-sent">Sent</div>
                <div class="warntabl-td warntabl-shipremain">Remain</div>
                <div class="warntabl-td warntabl-qty">Qty</div>
                <div class="warntabl-td warntabl-shipdate">Date</div>
                <div class="warntabl-td warntabl-method">Method</div>
                <div class="warntabl-td warntabl-tracking">Tracking#s</div>
            </div>
        </div>
        <div class="warntabl-tr">
            <div class="warntabl-apprblock">
                <div class="warntabl-td warntabl-prcful pink">34%</div>
                <div class="warntabl-td warntabl-prcship pink">50%</div>
                <div class="warntabl-td warntabl-approval notapprv">Not Approved</div>
            </div>
            <div class="warntabl-mainblock">
                <div class="warntabl-td warntabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="warntabl-td warntabl-rush redrush">RUSH</div>
                <div class="warntabl-td warntabl-order">64742</div>
                <div class="warntabl-td warntabl-items">1000</div>
                <div class="warntabl-td warntabl-imp">1</div>
                <div class="warntabl-td warntabl-prints">1000</div>
                <div class="warntabl-td warntabl-itmcolor">Yellow</div>
                <div class="warntabl-td warntabl-description">i050 - Droplet Stress Balls</div>
                <div class="warntabl-td warntabl-inkcolor">2 - White</div>
            </div>
            <div class="warntabl-fulfblock">
                <div class="warntabl-td warntabl-done">1000</div>
                <div class="warntabl-td warntabl-flfremain">500</div>
                <div class="warntabl-td warntabl-flfdate">07/14</div>
                <div class="warntabl-td warntabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-flftotal">99999</div>
                <div class="warntabl-td warntabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="warntabl-shipblock">
                <div class="warntabl-td warntabl-sent">99999</div>
                <div class="warntabl-td warntabl-shipremain">100</div>
                <div class="warntabl-td warntabl-qty">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="warntabl-td warntabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
        <div class="warntabl-tr">
            <div class="warntabl-apprblock">
                <div class="warntabl-td warntabl-prcful pink">27%</div>
                <div class="warntabl-td warntabl-prcship pink">45%</div>
                <div class="warntabl-td warntabl-approval">Approved <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span></div>
            </div>
            <div class="warntabl-mainblock">
                <div class="warntabl-td warntabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="warntabl-td warntabl-rush">&nbsp;</div>
                <div class="warntabl-td warntabl-order">64742</div>
                <div class="warntabl-td warntabl-items">1000</div>
                <div class="warntabl-td warntabl-imp">1</div>
                <div class="warntabl-td warntabl-prints">1000</div>
                <div class="warntabl-td warntabl-itmcolor">Yellow</div>
                <div class="warntabl-td warntabl-description">i050 - Droplet Stress Balls</div>
                <div class="warntabl-td warntabl-inkcolor">1 - Black</div>
            </div>
            <div class="warntabl-fulfblock">
                <div class="warntabl-td warntabl-done">1000</div>
                <div class="warntabl-td warntabl-flfremain">500</div>
                <div class="warntabl-td warntabl-flfdate">07/14</div>
                <div class="warntabl-td warntabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-flftotal">99999</div>
                <div class="warntabl-td warntabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="warntabl-shipblock">
                <div class="warntabl-td warntabl-sent">99999</div>
                <div class="warntabl-td warntabl-shipremain">100</div>
                <div class="warntabl-td warntabl-qty">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="warntabl-td warntabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="warntabl-td warntabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="regular-section">
    <div class="regular-table">
        <div class="regltabl-tr regltabl-header">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful">%Ful</div>
                <div class="regltabl-td regltabl-prcship">%Ship</div>
                <div class="regltabl-td regltabl-approval">Approval</div>
            </div>
            <div class="regltabl-td regltabl-userprinter">&nbsp;</div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">&nbsp;</div>
                <div class="regltabl-td regltabl-rush">&nbsp;</div>
                <div class="regltabl-td regltabl-order">Order#</div>
                <div class="regltabl-td regltabl-items">#Items</div>
                <div class="regltabl-td regltabl-imp">Imp</div>
                <div class="regltabl-td regltabl-prints">#Prints</div>
                <div class="regltabl-td regltabl-itmcolor">Item Color/s</div>
                <div class="regltabl-td regltabl-description">Item / Description</div>
                <div class="regltabl-td regltabl-inkcolor">Ink Color/s</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">Prepared</div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">Done</div>
                <div class="regltabl-td regltabl-flfremain">Remain</div>
                <div class="regltabl-td regltabl-flfprint">Printed</div>
                <div class="regltabl-td regltabl-flfkept">Kept</div>
                <div class="regltabl-td regltabl-flfmisprt">Misprt</div>
                <div class="regltabl-td regltabl-flftotal">Total</div>
                <div class="regltabl-td regltabl-flfplates">Plates</div>
            </div>
            <div class="regltabl-shipblock">
                <div class="regltabl-td regltabl-sent">Sent</div>
                <div class="regltabl-td regltabl-shipremain">Remain</div>
                <div class="regltabl-td regltabl-qty">Qty</div>
                <div class="regltabl-td regltabl-shipdate">Date</div>
                <div class="regltabl-td regltabl-method">Method</div>
                <div class="regltabl-td regltabl-tracking">Tracking#s</div>
            </div>
        </div>
        <div class="regltabl-tr printerline">
            <div class="regltabl-printername">Unassigned</div>
            <div class="regltabl-printerinfo"><span>7000</span> prints - <span>4000</span> items - <span>3</span> orders</div>
        </div>
        <div class="regltabl-tr">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful peach">45%</div>
                <div class="regltabl-td regltabl-prcship peach">25%</div>
                <div class="regltabl-td regltabl-approval">Approved <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span></div>
            </div>
            <div class="regltabl-td regltabl-userprinter">
                <div class="userprinter">
                    <img src="img/user-printer.svg">
                </div>
            </div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="regltabl-td regltabl-rush">&nbsp;</div>
                <div class="regltabl-td regltabl-order">64742</div>
                <div class="regltabl-td regltabl-items">1000</div>
                <div class="regltabl-td regltabl-imp">1</div>
                <div class="regltabl-td regltabl-prints">1000</div>
                <div class="regltabl-td regltabl-itmcolor">Yellow</div>
                <div class="regltabl-td regltabl-description">i001 - Round Stress Balls</div>
                <div class="regltabl-td regltabl-inkcolor">5 - Multiple</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">
                    <div class="regltabl-prepstock">Stock</div>
                    <div class="regltabl-prepplate">Plate</div>
                    <div class="regltabl-prepink grey">Ink</div>
                </div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">1000</div>
                <div class="regltabl-td regltabl-flfremain">500</div>
                <div class="regltabl-td regltabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flftotal">99999</div>
                <div class="regltabl-td regltabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="regltabl-shipblock">
                <div class="regltabl-td regltabl-sent">99999</div>
                <div class="regltabl-td regltabl-shipremain">100</div>
                <div class="regltabl-td regltabl-qty">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="regltabl-td regltabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
        <div class="regltabl-tr">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful">50%</div>
                <div class="regltabl-td regltabl-prcship">50%</div>
                <div class="regltabl-td regltabl-approval notapprv"> Not Approved</div>
            </div>
            <div class="regltabl-td regltabl-userprinter">
                <div class="userprinter">
                    <img src="img/user-printer.svg">
                </div>
            </div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="regltabl-td regltabl-rush redrush">RUSH</div>
                <div class="regltabl-td regltabl-order">64742</div>
                <div class="regltabl-td regltabl-items">1000</div>
                <div class="regltabl-td regltabl-imp">1</div>
                <div class="regltabl-td regltabl-prints">1000</div>
                <div class="regltabl-td regltabl-itmcolor">Yellow</div>
                <div class="regltabl-td regltabl-description">i001 - Round Stress Balls</div>
                <div class="regltabl-td regltabl-inkcolor">3 - Red</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">
                    <div class="regltabl-prepstock">Stock</div>
                    <div class="regltabl-prepplate">Plate</div>
                    <div class="regltabl-prepink">Ink</div>
                </div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">2500</div>
                <div class="regltabl-td regltabl-flfremain">750</div>
                <div class="regltabl-td regltabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flftotal">99999</div>
                <div class="regltabl-td regltabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="regltabl-shipblock closedblock">
                <div class="regltabl-td regltabl-sent">99999</div>
                <div class="regltabl-td regltabl-shipremain">100</div>
                <div class="regltabl-td regltabl-qty">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="regltabl-td regltabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
        <div class="regltabl-tr">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful">38%</div>
                <div class="regltabl-td regltabl-prcship">38%</div>
                <div class="regltabl-td regltabl-approval notapprv"> Not Approved</div>
            </div>
            <div class="regltabl-td regltabl-userprinter">
                <div class="userprinter">
                    <img src="img/user-printer.svg">
                </div>
            </div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="regltabl-td regltabl-rush">&nbsp;</div>
                <div class="regltabl-td regltabl-order">64742</div>
                <div class="regltabl-td regltabl-items">1000</div>
                <div class="regltabl-td regltabl-imp">1</div>
                <div class="regltabl-td regltabl-prints">1000</div>
                <div class="regltabl-td regltabl-itmcolor">Green</div>
                <div class="regltabl-td regltabl-description">i050 - Droplet Stress Balls</div>
                <div class="regltabl-td regltabl-inkcolor">4 - PMS 304 Pink</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">
                    <div class="regltabl-prepstock">Stock</div>
                    <div class="regltabl-prepplate grey">Plate</div>
                    <div class="regltabl-prepink grey">Ink</div>
                </div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">2500</div>
                <div class="regltabl-td regltabl-flfremain">750</div>
                <div class="regltabl-td regltabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flftotal">99999</div>
                <div class="regltabl-td regltabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="regltabl-shipblock closedblock">
                <div class="regltabl-td regltabl-sent">99999</div>
                <div class="regltabl-td regltabl-shipremain">100</div>
                <div class="regltabl-td regltabl-qty">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="regltabl-td regltabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
        <div class="regltabl-tr printerline">
            <div class="regltabl-printername">Junior</div>
            <div class="regltabl-printerinfo"><span>1000</span> prints - <span>1000</span> items - <span>1</span> orders</div>
        </div>
        <div class="regltabl-tr">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful">50%</div>
                <div class="regltabl-td regltabl-prcship">50%</div>
                <div class="regltabl-td regltabl-approval notapprv"> Not Approved</div>
            </div>
            <div class="regltabl-td regltabl-userprinter">
                <div class="userprinter">
                    <img src="img/user-printer.svg">
                </div>
            </div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="regltabl-td regltabl-rush redrush">RUSH</div>
                <div class="regltabl-td regltabl-order">64742</div>
                <div class="regltabl-td regltabl-items">1000</div>
                <div class="regltabl-td regltabl-imp">1</div>
                <div class="regltabl-td regltabl-prints">1000</div>
                <div class="regltabl-td regltabl-itmcolor">Yellow</div>
                <div class="regltabl-td regltabl-description">i001 - Round Stress Balls</div>
                <div class="regltabl-td regltabl-inkcolor">3 - Red</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">
                    <div class="regltabl-prepstock">Stock</div>
                    <div class="regltabl-prepplate">Plate</div>
                    <div class="regltabl-prepink">Ink</div>
                </div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">2500</div>
                <div class="regltabl-td regltabl-flfremain">750</div>
                <div class="regltabl-td regltabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flftotal">99999</div>
                <div class="regltabl-td regltabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="regltabl-shipblock closedblock">
                <div class="regltabl-td regltabl-sent">99999</div>
                <div class="regltabl-td regltabl-shipremain">100</div>
                <div class="regltabl-td regltabl-qty">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="regltabl-td regltabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
        <div class="regltabl-tr printerline">
            <div class="regltabl-printername">Lorena</div>
            <div class="regltabl-printerinfo"><span>2000</span> prints - <span>2500</span> items - <span>2</span> orders</div>
        </div>
        <div class="regltabl-tr">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful peach">45%</div>
                <div class="regltabl-td regltabl-prcship peach">25%</div>
                <div class="regltabl-td regltabl-approval">Approved <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span></div>
            </div>
            <div class="regltabl-td regltabl-userprinter">
                <div class="userprinter">
                    <img src="img/user-printer.svg">
                </div>
            </div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="regltabl-td regltabl-rush">&nbsp;</div>
                <div class="regltabl-td regltabl-order">64742</div>
                <div class="regltabl-td regltabl-items">1000</div>
                <div class="regltabl-td regltabl-imp">1</div>
                <div class="regltabl-td regltabl-prints">1000</div>
                <div class="regltabl-td regltabl-itmcolor">Yellow</div>
                <div class="regltabl-td regltabl-description">i001 - Round Stress Balls</div>
                <div class="regltabl-td regltabl-inkcolor">5 - Multiple</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">
                    <div class="regltabl-prepstock">Stock</div>
                    <div class="regltabl-prepplate">Plate</div>
                    <div class="regltabl-prepink grey">Ink</div>
                </div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">1000</div>
                <div class="regltabl-td regltabl-flfremain">500</div>
                <div class="regltabl-td regltabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flftotal">99999</div>
                <div class="regltabl-td regltabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="regltabl-shipblock">
                <div class="regltabl-td regltabl-sent">99999</div>
                <div class="regltabl-td regltabl-shipremain">100</div>
                <div class="regltabl-td regltabl-qty">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="regltabl-td regltabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
        <div class="regltabl-tr">
            <div class="regltabl-apprblock">
                <div class="regltabl-td regltabl-prcful">50%</div>
                <div class="regltabl-td regltabl-prcship">50%</div>
                <div class="regltabl-td regltabl-approval notapprv"> Not Approved</div>
            </div>
            <div class="regltabl-td regltabl-userprinter">
                <div class="userprinter">
                    <img src="img/user-printer.svg">
                </div>
            </div>
            <div class="regltabl-mainblock">
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move"><img src="img/move-blue.svg"></div>
                </div>
                <div class="regltabl-td regltabl-rush redrush">RUSH</div>
                <div class="regltabl-td regltabl-order">64742</div>
                <div class="regltabl-td regltabl-items">1000</div>
                <div class="regltabl-td regltabl-imp">1</div>
                <div class="regltabl-td regltabl-prints">1000</div>
                <div class="regltabl-td regltabl-itmcolor">Yellow</div>
                <div class="regltabl-td regltabl-description">i001 - Round Stress Balls</div>
                <div class="regltabl-td regltabl-inkcolor">3 - Red</div>
            </div>
            <div class="regltabl-prepblock">
                <div class="regltabl-td regltabl-prepared">
                    <div class="regltabl-prepstock">Stock</div>
                    <div class="regltabl-prepplate">Plate</div>
                    <div class="regltabl-prepink">Ink</div>
                </div>
            </div>
            <div class="regltabl-fulfblock">
                <div class="regltabl-td regltabl-done">2500</div>
                <div class="regltabl-td regltabl-flfremain">750</div>
                <div class="regltabl-td regltabl-flfprint">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-flftotal">99999</div>
                <div class="regltabl-td regltabl-flfplates">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
            <div class="regltabl-shipblock closedblock">
                <div class="regltabl-td regltabl-sent">99999</div>
                <div class="regltabl-td regltabl-shipremain">100</div>
                <div class="regltabl-td regltabl-qty">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-shipdate">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-method">
                    <select>
                        <option></option>
                        <option>FedEx</option>
                        <option>UPS</option>
                    </select>
                </div>
                <div class="regltabl-td regltabl-tracking">
                    <input type="text" name="">
                </div>
                <div class="regltabl-td regltabl-save">
                    <div class="btnsave">Save</div>
                </div>
            </div>
        </div>
    </div>
</div>
