<div class="page_container">
    <input type="hidden" value="<?= $brand ?>" id="calendarsviewbrand"/>
    <div class="left_maincontent" id="calendarsviewbrandmenu">
        <?= $left_menu ?>
    </div>
    <div class="right_maincontent">
        <input type="hidden" id="totalcalend" value="<?= $total ?>"/>
        <input type="hidden" id="perpagecalend" value="<?= $perpage ?>"/>
        <input type="hidden" id="ordercalend" value="<?= $orderby ?>"/>
        <input type="hidden" id="direccalend" value="<?= $direct ?>"/>
        <div class="calendarscontent">
            <div class="calendartable-head">
                <div class="calendar-actions">
                    <div class="newcalend">
                        <a href="javascript:void(0);" class="addcalend">add calendar</a>
                    </div>
                </div>
                <div class="calendar_name">Calendar:</div>
                <div class="calendar_status">Status:</div>
            </div>
            <div id="calendinfo" class="calendartable-data"></div>
        </div>

    </div>
</div>


