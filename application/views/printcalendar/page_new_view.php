<input type="hidden" id="printcaledyear" value="<?= $yearprint ?>"/>
<input type="hidden" id="calendarprintdate" value="0"/>
<input type="hidden" id="calendweekbgn" value="0"/>
<input type="hidden" id="calendweekend" value="0"/>
<input type="hidden" id="calendarlateneedaction" value="1"/>
<input type="hidden" id="calendarlatenotapproved" value="1"/>
<div class="printcalendarcontent">
    <div class="prnshd-left">
        <div class="psleft-topbar">
            <div class="psleft-txt">Click a date to open the print schedule for that day:</div>
            <div class="psleft-years">
                <?php foreach ($years as $year): ?>
                    <div class="psleft-years-box <?= $year['yearprint'] == $yearprint ? 'active' : '' ?>" data-yearprint="<?= $year['yearprint'] ?>">
                        <?= $year['yearprint'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="calendar-week">
            <div class="pscalendar-arrows">
                <div class="pscalendar-arrowsleft">
                    <i class="fa fa-caret-left" aria-hidden="true"></i>
                </div>
                <div class="pscalendar-arrowsright">
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                </div>
            </div>
            <div class="pscalendar-days">
                <div class="pscalendar-td">Monday</div>
                <div class="pscalendar-td">Tuesday</div>
                <div class="pscalendar-td">Wednesday</div>
                <div class="pscalendar-td">Thursday</div>
                <div class="pscalendar-td">Friday</div>
                <div class="pscalendar-td">Saturday</div>
                <div class="pscalendar-td">Sunday</div>
            </div>
            <div class="pscalendar-week"></div>
        </div>
        <div class="calendar-full">
            <div class="calnd-daysweek">
                <div class="dayweek">Monday</div>
                <div class="dayweek">Tuesday</div>
                <div class="dayweek">Wednesday</div>
                <div class="dayweek">Thursday</div>
                <div class="dayweek">Friday</div>
                <div class="dayweek">Saturday</div>
                <div class="dayweek">Sunday</div>
                <div class="dayweek weeklytotal">Weekly Total</div>
            </div>
            <div class="clndrfull-body" id="clndrfull-body">
            </div>
        </div>
        <div class="statistics-block"></div>
        <div class="simpltodayblock">
            <div id="regularview-short" style="width: 100%"></div>
            <div id="historyview-short" style="width: 100%"></div>
        </div>
        <div class="todayblock">
            <div id="regularview-full" style="width: 100%"></div>
            <div id="historyview-full" style="width: 100%"></div>
        </div>
    </div>
    <div class="prnshd-right">
        <div class="psright-topbar">
            <div class="datarow">
                <div class="ps_keymaps">
                    <div class="keymaps-box white">&nbsp;</div>
                    <div class="keymaps-label">Print Qty = Ship Qty</div>
                    <div class="keymaps-box orange">&nbsp;</div>
                    <div class="keymaps-label">Print Qty  > Ship Qty</div>
                    <div class="keymaps-box lightpink">&nbsp;</div>
                    <div class="keymaps-label">Print Qty  < Ship Qty</div>
                    <div class="keymaps-box pink">&nbsp;</div>
                    <div class="keymaps-label">Need Action</div>
                </div>
            </div>
            <div class="datarow">
                <div class="reschedulartabs">
                    <div class="reschdl-tab active" data-sortfld="print_date">By Print Date</div>
                    <div class="reschdl-tab" data-sortfld="item_id">By Item</div>
                </div>
                <div class="reshedlordr-btn">
                    <div class="reshedlordr-btntxt">Reschedule Orders</div>
                    <div class="btnreschedular-btn"><i class="fa fa-caret-down" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
        <div class="reshedlordr">
            <div class="reschdl-body">&nbsp;</div>
            <div class="reschdl-infobody">
                <div class="datarow">
                    <div class="ro_stillprint-totals">
                        <div class="ro_infotitle">Still to Print:</div>
                        <div class="stillprint_box">
                            <div class="sprbox-orders">
                                <div class="sprbox-title">Orders:</div>
                                <div class="sprbox-result" data-fld="stilorders">&nbsp;</div>
                            </div>
                            <div class="sprbox-items">
                                <div class="sprbox-title">Items:</div>
                                <div class="sprbox-result" data-fld="stilitems">&nbsp;</div>
                            </div>
                            <div class="sprbox-total">
                                <div class="sprbox-title">Total Prints:</div>
                                <div class="sprbox-result" data-fld="stilprints">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="datarow">
                    <div class="ro_stillprint">
                        <div class="ro_infotitle">On Time:</div>
                        <div class="stillprint_box">
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-title">Orders:</div>
                                    <div class="sprbox-result" data-fld="ontimeorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-title">Items:</div>
                                    <div class="sprbox-result" data-fld="ontimeitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-title">Total Prints:</div>
                                    <div class="sprbox-result" data-fld="ontimeprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-result proc" data-fld="ontimeprcorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-result proc" data-fld="ontimeprcitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-result proc" data-fld="ontimeprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ro_lateorders">
                        <div class="ro_infotitle">Late Orders:</div>
                        <div class="lateorders_box">
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-title">Orders:</div>
                                    <div class="lateordbox-result" data-fld="lateorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-title">Items:</div>
                                    <div class="lateordbox-result" data-fld="lateitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-title">Total Prints:</div>
                                    <div class="lateordbox-result" data-fld="lateprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-result proc" data-fld="lateprcorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-result proc" data-fld="lateprcitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-result proc" data-fld="lateprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="datarow">
                    <div class="ro_stillprint">
                        <div class="ro_infotitle">Print Qty  &lt; Ship Qty</div>
                        <div class="stillprint_box">
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-title">Orders:</div>
                                    <div class="sprbox-result" data-fld="ontimecriticorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-title">Items:</div>
                                    <div class="sprbox-result" data-fld="ontimecriticitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-title">Total Prints:</div>
                                    <div class="sprbox-result" data-fld="ontimecriticprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-result" data-fld="ontimecriticprcorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-result" data-fld="ontimecriticprcitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-result" data-fld="ontimecriticprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ro_lateorders">
                        <div class="ro_infotitle">Print Qty  &lt; Ship Qty</div>
                        <div class="lateorders_box">
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-title">Orders:</div>
                                    <div class="lateordbox-result" data-fld="latecriticorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-title">Items:</div>
                                    <div class="lateordbox-result" data-fld="latecriticitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-title">Total Prints:</div>
                                    <div class="lateordbox-result" data-fld="latecriticprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-result" data-fld="latecriticprcorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-result" data-fld="latecriticprcitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-result" data-fld="latecriticprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="datarow">
                    <div class="ro_stillprint">
                        <div class="ro_infotitle">Not Approved</div>
                        <div class="stillprint_box">
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-title">Orders:</div>
                                    <div class="sprbox-result" data-fld="ontimeapprorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-title">Items:</div>
                                    <div class="sprbox-result" data-fld="ontimeappritems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-title">Total Prints:</div>
                                    <div class="sprbox-result" data-fld="ontimeapprprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-result" data-fld="ontimeapprprcorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-result" data-fld="ontimeapprprcitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-result" data-fld="ontimeapprprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ro_lateorders">
                        <div class="ro_infotitle">Not Approved</div>
                        <div class="lateorders_box">
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-title">Orders:</div>
                                    <div class="lateordbox-result" data-fld="lateapprorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-title">Items:</div>
                                    <div class="lateordbox-result" data-fld="lateappritems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-title">Total Prints:</div>
                                    <div class="lateordbox-result" data-fld="lateapprprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-result" data-fld="lateapprprcorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-result" data-fld="lateapprprcitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-result" data-fld="lateapprprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="datarow">
                    <div class="ro_stillprint">
                        <div class="ro_infotitle">Approved & Print Qty = Ship Qty</div>
                        <div class="stillprint_box">
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-title">Orders:</div>
                                    <div class="sprbox-result" data-fld="ontimenormorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-title">Items:</div>
                                    <div class="sprbox-result" data-fld="ontimenormitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-title">Total Prints:</div>
                                    <div class="sprbox-result" data-fld="ontimenormprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-result" data-fld="ontimenormprcorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-result" data-fld="ontimenormprcitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-result" data-fld="ontimenormprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ro_lateorders">
                        <div class="ro_infotitle">Approved & Print Qty = Ship Qty</div>
                        <div class="lateorders_box">
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-title">Orders:</div>
                                    <div class="lateordbox-result" data-fld="latenormorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-title">Items:</div>
                                    <div class="lateordbox-result" data-fld="latenormitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-title">Total Prints:</div>
                                    <div class="lateordbox-result" data-fld="latenormprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-result" data-fld="latenormprcorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-result" data-fld="latenormprcitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-result" data-fld="latenormprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="datarow">
                    <div class="ro_stillprint">
                        <div class="ro_infotitle">Approved & Print Qty &gt; Ship Qty</div>
                        <div class="stillprint_box">
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-title">Orders:</div>
                                    <div class="sprbox-result" data-fld="ontimenormorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-title">Items:</div>
                                    <div class="sprbox-result" data-fld="ontimenormitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-title">Total Prints:</div>
                                    <div class="sprbox-result" data-fld="ontimenormprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="sprbox-orders">
                                    <div class="sprbox-result" data-fld="ontimenormprcorders">&nbsp;</div>
                                </div>
                                <div class="sprbox-items">
                                    <div class="sprbox-result" data-fld="ontimenormprcitems">&nbsp;</div>
                                </div>
                                <div class="sprbox-total">
                                    <div class="sprbox-result" data-fld="ontimenormprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ro_lateorders">
                        <div class="ro_infotitle">Approved & Print Qty &gt; Ship Qty</div>
                        <div class="lateorders_box">
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-title">Orders:</div>
                                    <div class="lateordbox-result" data-fld="latenormorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-title">Items:</div>
                                    <div class="lateordbox-result" data-fld="latenormitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-title">Total Prints:</div>
                                    <div class="lateordbox-result" data-fld="latenormprints">&nbsp;</div>
                                </div>
                            </div>
                            <div class="datarow">
                                <div class="lateordbox-orders">
                                    <div class="lateordbox-result" data-fld="latenormprcorders">&nbsp;</div>
                                </div>
                                <div class="lateordbox-items">
                                    <div class="lateordbox-result" data-fld="latenormprcitems">&nbsp;</div>
                                </div>
                                <div class="lateordbox-total">
                                    <div class="lateordbox-result" data-fld="latenormprcprints">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="reschdl-linebody">&nbsp;</div>
        </div>
    </div>
</div>
