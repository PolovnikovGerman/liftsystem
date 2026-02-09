<input type="hidden" id="printcaledyear" value="<?= $yearprint ?>"/>
<input type="hidden" id="calendarprintdate" value="0"/>
<input type="hidden" id="calendweekbgn" value="0"/>
<input type="hidden" id="calendweekend" value="0"/>
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
        <div class="calendar-week">Slider CALENDAR WEEK</div>
        <div class="calendar-full">
            <div class="calnd-daysweek">
                <div class="dayweek">Monday</div>
                <div class="dayweek">Tuesday</div>
                <div class="dayweek">Wednesday</div>
                <div class="dayweek">Thursday</div>
                <div class="dayweek">Friday</div>
                <div class="dayweek">Saturday</div>
                <div class="dayweek">Sunday</div>
            </div>
            <div class="clndrfull-body">
                <div id="clndrfull-body"></div>
                <div class="calnd-arrowsbar">
                    <div class="calnd-arrow calnd-down">
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </div>
                    <div class="calnd-arrow calnd-up">
                        <i class="fa fa-caret-up" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div class="clndrfull-weeklytotal">
                <div class="weeklytotal-title">Weekly Total</div>
                <div class="weeklytotal-body"></div>
            </div>
        </div>
        <div class="statistics-block"></div>
        <div class="simpltodayblock">Simplified TODAY Table</div>
        <div class="todayblock">TODAY Full Table</div>
    </div>
    <div class="prnshd-right">
        <div class="psright-topbar">
            <div class="datarow">
                <div class="ps_keymaps">
                    <div class="keymaps-box white">&nbsp;</div>
                    <div class="keymaps-label">Prtd Amnt = Shpd Amnt</div>
                    <div class="keymaps-box orange">&nbsp;</div>
                    <div class="keymaps-label">Prtd Amnt > Shpd Amnt</div>
                    <div class="keymaps-box lightpink">&nbsp;</div>
                    <div class="keymaps-label">Prtd Amnt < Shpd Amnt</div>
                    <div class="keymaps-box pink">&nbsp;</div>
                    <div class="keymaps-label">Actn Needed</div>
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
                <div class="ro_stillprint">
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
                <div class="ro_lateorders">
                    <div class="ro_infotitle">Late Orders:</div>
                    <div class="lateorders_box">
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
                </div>
            </div>
            <div class="reschdl-linebody">&nbsp;</div>
        </div>
    </div>
</div>
