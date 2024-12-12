<div class="visitordetailarea">
    <div class="datarow">
        <div class="detail_parameter_name visitors">VISITORS</div>
        <div class="detailstitle visitors weekdata">For Week</div>
        <div class="detailstitle visitors daydata">Mon</div>
        <div class="detailstitle visitors daydata">Tue</div>
        <div class="detailstitle visitors daydata">Wed</div>
        <div class="detailstitle visitors daydata">Thu</div>
        <div class="detailstitle visitors daydata">Fri</div>
        <div class="detailstitle visitors daydata weekenddat">Sat</div>
        <div class="detailstitle visitors daydata weekenddat">Sun</div>
    </div>
    <div class="detailsleftpart">
        <div class="datarow">
            <div class="detail_brand_name visitors">Stress Balls</div>
            <div class="detailsdata weekdatavalue"><?=QTYOutput($btvisittotal)?></div>
        </div>
        <div class="datarow">
            <div class="detail_brand_name visitors">Stress Relievers</div>
            <div class="detailsdata weekdatavalue"><?=QTYOutput($srvisittotal)?></div>
        </div>
        <div class="datarow">
            <div class="detail_brand_name visitors total">Total</div>
            <div class="detailsdata totalweek"><?=QTYOutput($visittotal)?></div>
        </div>
    </div>
    <div class="detailsrightpart">
        <div class="weekdataarea">
            <div class="datarow">
                <div class="dayvalue"><?=QTYOutput($btvisits['1'])?></div>
                <div class="dayvalue"><?=QTYOutput($btvisits['2'])?></div>
                <div class="dayvalue"><?=QTYOutput($btvisits['3'])?></div>
                <div class="dayvalue"><?=QTYOutput($btvisits['4'])?></div>
                <div class="dayvalue"><?=QTYOutput($btvisits['5'])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($btvisits['6'])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($btvisits['0'])?></div>
            </div>
            <div class="datarow">
                <div class="dayvalue"><?=QTYOutput($srvisits[1])?></div>
                <div class="dayvalue"><?=QTYOutput($srvisits[2])?></div>
                <div class="dayvalue"><?=QTYOutput($srvisits[3])?></div>
                <div class="dayvalue"><?=QTYOutput($srvisits[4])?></div>
                <div class="dayvalue"><?=QTYOutput($srvisits[5])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($srvisits[6])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($srvisits[0])?></div>
            </div>
        </div>
        <div class="dayvalue total"><?=QTYOutput($visitors[1])?></div>
        <div class="dayvalue total"><?=QTYOutput($visitors[2])?></div>
        <div class="dayvalue total"><?=QTYOutput($visitors[3])?></div>
        <div class="dayvalue total"><?=QTYOutput($visitors[4])?></div>
        <div class="dayvalue total"><?=QTYOutput($visitors[5])?></div>
        <div class="dayvalue total weekenddat"><?=QTYOutput($visitors[6])?></div>
        <div class="dayvalue total weekenddat"><?=QTYOutput($visitors[0])?></div>
    </div>
    <!-- Leads -->
    <div class="datarow leaddetailsarea">
        <div class="detail_parameter_name leads">LEADS</div>
        <div class="detailstitle leads weekdata">For Week</div>
        <div class="detailstitle leads daydata">Mon</div>
        <div class="detailstitle leads daydata">Tue</div>
        <div class="detailstitle leads daydata">Wed</div>
        <div class="detailstitle leads daydata">Thu</div>
        <div class="detailstitle leads daydata">Fri</div>
        <div class="detailstitle leads daydata weekenddat">Sat</div>
        <div class="detailstitle leads daydata weekenddat">Sun</div>
    </div>
    <div class="detailsleftpart">
        <div class="datarow">
            <div class="detail_brand_name leads">Stress Balls</div>
            <div class="detailsdata weekdatavalue"><?=QTYOutput($btleadtotal)?></div>
        </div>
        <div class="datarow">
            <div class="detail_brand_name leads">Stress Relievers</div>
            <div class="detailsdata weekdatavalue"><?=QTYOutput($srleadtotal)?></div>
        </div>
        <div class="datarow">
            <div class="detail_brand_name leads total">Total</div>
            <div class="detailsdata totalweek"><?=QTYOutput($leadtotal)?></div>
        </div>
    </div>
    <div class="detailsrightpart">
        <div class="weekdataarea">
            <div class="datarow">
                <div class="dayvalue"><?=QTYOutput($btleads[1])?></div>
                <div class="dayvalue"><?=QTYOutput($btleads[2])?></div>
                <div class="dayvalue"><?=QTYOutput($btleads[3])?></div>
                <div class="dayvalue"><?=QTYOutput($btleads[4])?></div>
                <div class="dayvalue"><?=QTYOutput($btleads[5])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($btleads[6])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($btleads[0])?></div>
            </div>
            <div class="datarow">
                <div class="dayvalue"><?=QTYOutput($srleads[1])?></div>
                <div class="dayvalue"><?=QTYOutput($srleads[2])?></div>
                <div class="dayvalue"><?=QTYOutput($srleads[3])?></div>
                <div class="dayvalue"><?=QTYOutput($srleads[4])?></div>
                <div class="dayvalue"><?=QTYOutput($srleads[5])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($srleads[6])?></div>
                <div class="dayvalue weekenddat"><?=QTYOutput($srleads[0])?></div>
            </div>
        </div>
        <div class="dayvalue total"><?=QTYOutput($leads[1])?></div>
        <div class="dayvalue total"><?=QTYOutput($leads[2])?></div>
        <div class="dayvalue total"><?=QTYOutput($leads[3])?></div>
        <div class="dayvalue total"><?=QTYOutput($leads[4])?></div>
        <div class="dayvalue total"><?=QTYOutput($leads[5])?></div>
        <div class="dayvalue total weekenddat"><?=QTYOutput($leads[6])?></div>
        <div class="dayvalue total weekenddat"><?=QTYOutput($leads[0])?></div>
    </div>
</div>