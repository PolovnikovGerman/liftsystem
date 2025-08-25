<input type="hidden" id="printcaledyear" value="<?= $yearprint ?>"/>
<div class="printschedular_body">
    <div id="printcalendarfullview">
        <div class="pschedular-calendar">
            <div class="datarow">
                <div class="psc-header">
                    <div class="pscheader-title">Click a date to open the print schedule for that day:</div>
                    <div class="pscheader-years">
                        <ul>
                            <?php foreach ($years as $year): ?>
                                <li>
                                    <div class="pscheader-yearbox <?= $year['yearprint'] == $yearprint ? 'active' : '' ?>"
                                         data-yearprint="<?= $year['yearprint'] ?>"><?= $year['yearprint'] ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="datarow">
                <div class="pschedular-calendartable">
                    <div class="psctable-tr weekdays">
                        <div class="psctable-td">Monday</div>
                        <div class="psctable-td">Tuesday</div>
                        <div class="psctable-td">Wednesday</div>
                        <div class="psctable-td">Thursday</div>
                        <div class="psctable-td">Friday</div>
                        <div class="psctable-td">Saturday</div>
                        <div class="psctable-td">Sunday</div>
                        <div class="psctable-td weeklytotal">Weekly Total</div>
                    </div>
                    <div class="psctable-body" id="psctable-body"></div>
                </div>
            </div>
        </div>
        <div class="pschedular-rightside">
            <div class="datarow">
                <div class="btn-reschedular">
                    <div class="btnreschedular-txt">Reschedule Orders</div>
                    <div class="btnreschedular-btn"><i class="fa fa-caret-down" aria-hidden="true"></i></div>
                </div>
            </div>
            <div class="datarow">
                <div class="lateorders">
                    <div class="lateorders-name">Late Orders</div>
                    <div class="lateorders-box"></div>
                </div>
            </div>
            <div class="datarow">
                <div class="statisticsblok">
                    <div class="statisticsblok-name">Statistics:</div>
                    <div class="statistics-boxes"></div>
                    <div class="statisticsblok-checkbox">
                        <input type="checkbox" name="">
                        <label>Hide orders where % Fulfilled ≠ % Shipped</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="printcalendardetailsview" style="display: none">
        <!-- week calendar -->
        <div class="pscalendar">
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
        <!-- end week -->
    </div>
</div>
