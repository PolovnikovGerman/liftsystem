<input type="hidden" id="whitelisttotal" value="<?=$total?>"/>
<input type="hidden" id="whitelistperpage" value="<?=$perpage?>"/>
<input type="hidden" id="whitelistorder" value="<?=$orderby?>"/>
<input type="hidden" id="whitelistdirect" value="<?=$direct?>"/>
<div class="whitelistdataview_left">
    <div class="whitelist_header">
        <div class="newsender">
            add sender
        </div>
        <div class="sender">Sender</div>
        <div class="user_parsered">User</div>
    </div>
    <div class="whitelist_data"></div>
</div>
<div class="whitelistdataview_right">
    <div class="whitelist_search">
        <input class="wldate" name="wldate" id="wldate" readonly/>
        <input class="search_input" name="wlsearch" id="wlsearch" value=""/>
        <div class="findwparse" data-typeid="find_wl">Search</div>
        <div class="findwparse" data-typeid="clear_wl">Clear</div>
    </div>
    <div class="whitelistpagination" id="whitelistpagination"></div>
    <div class="whitelist_parselog_head">
        <div class="wlparse_date">Date</div>
        <div class="wlparse_email">Sender</div>
        <div class="wlparse_subject">Subject</div>
        <div class="wlparse_result">Result</div>
    </div>
    <div class="whitelist_parselog_data"></div>
</div>
