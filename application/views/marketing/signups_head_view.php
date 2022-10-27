<input type='hidden' id='totalsign' value="<?= $total_rec ?>"/>
<input type="hidden" id='ordersign' value="<?= $order_by ?>"/>
<input type="hidden" id="direcsign" value="<?= $direction ?>"/>
<input type="hidden" id="cursign" value="<?= $cur_page ?>"/>
<input type="hidden" id="perpagesign" value="<?=$perpage?>"/>
<input type="hidden" id="signupemailbrand" value="<?=$brand?>"/>
<div class="signup_content">
    <div class="signup_header">
        <div>
            <input type="text" class="signupdate" id="beginsignup" autocomplete="off"/>
            <input type="text" class="signupdate" id="endsignup" autocomplete="off"/>
            <button class="btn btn-default" id="exportgignup">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export
            </button>
        </div>
        <div class="signup_paginator" id="signupPagination"></div>
    </div>
    <div class="signup_data_container">
        <div class="left-table" id="tabinfo_left"></div>
        <div class="right-table" id="tabinfo_right"></div>
    </div>
</div>
