<!-- Init values -->
<input type="hidden" id="totalquest" value="<?=$total_rec?>"/>
<input type="hidden" id="perpagequest" value="<?=$perpage?>"/>
<input type="hidden" id="curpagequest" value="<?=$cur_page?>"/>
<input type="hidden" id="sortquest" value="<?=$order_by?>"/>
<input type="hidden" id="sortdirquest" value="<?=$direction?>"/>
<input type="hidden" id="questionsviewbrand" value="<?=$brand?>"/>
<div class="questions_content">
    <div class="questions_header">
        <div class="questions_selecttype_label">Display:</div>
        <select id="questions_status" class="questions_status_select">
            <option value="1" selected>Not assigned</option>
            <option value="">All Quotes</option>
        </select>
        <input type="text" id="questionsearch" class="questionsearch search_input" placeholder="Customer,company, email.."/>

        <a class="find_questbnt" id="find_quest" href="javascript:void(0);">Search It</a>
        <a class="find_questbnt" id="clear_quest" href="javascript:void(0);">Clear</a>
        <div class="questions_pagination" id="questpagination"></div>
    </div>
    <!-- Table head -->
    <div class="questions_tabtitle">
        <div class="quest_numrec">#</div>
        <div class="quest_websys">
            <select class="questhideincl" id="questhideincl">
                <option value="1" selected="selected">Not Hidden</option>
                <option value="">All</option>
            </select>
        </div>
        <div class="quest_status">Status</div>
        <div class="quest_date">Date</div>
        <div class="quest_customname">Name</div>
        <div class="quest_custommail">Email</div>
        <div class="quest_customphone">Phone</div>
        <div class="quest_type">Type</div>
        <div class="quest_text">Message</div>
        <div class="quest_webpage">Webpage</div>
    </div>
    <div class="question_tabledat"></div>
</div>
