<input type='hidden' id='totalproof' value="<?=$total_rec?>"/>
<input type="hidden" id='orderproof' value="<?=$order_by?>"/>
<input type="hidden" id="direcproof" value="<?=$direction?>"/>
<input type="hidden" id="curpageproof" value="<?=$cur_page?>"/>
<input type="hidden" id="perpageproof" value="<?=$perpage?>"/>
<input type="hidden" id="hideartproof" value="<?=(isset($hideart) ? $hideart : '')?>"/>
<div class="proof_content">
    <div class="questions_header">
        <div class="proof_select_type">
            <div class="proof_selecttype_label">Display:</div>
            <select id="proof_status" class="proof_status_select">
                <option value="1" <?=($assign==1 ? 'selected="selected"' : '')?>>Not assigned lead</option>
                <option value="" <?=($assign==1 ? '' : 'selected="selected"')?>>All Proofs</option>
            </select>
            <select id="proofbrand" class="proof_brandselect">
                <option value="" selected>Display ALL websites</option>
                <option value="BT">Display bluetrack.com only</option>
                <option value="SB">Display stressball.com only</option>
            </select>
            <input type="text" id="proofsearch" class="proofsearch search_input" placeholder="Req #, customer,company, email.."/>
            <div class="leadsearch_actions">
                <a class="find_it" id="find_proof" href="javascript:void(0);">Search It</a>
                <a class="find_it" id="clear_proof" href="javascript:void(0);">Clear</a>
            </div>
        </div>
        <div class="proof_pagination">
            <div id="proofpagination"></div>
        </div>
    </div>
    <div class="proof_title">
        <div class="proof_ordnum">#</div>
        <div class="proof_del">
            <select id="hidedelproofs" class="proofhideproof">
                <option value="0" selected="selected">No Void</option>
                <option value="1">All Proofs</option>
            </select>
            <!--<input type="checkbox" id="hidedelproofs" value="1" /> -->
        </div>
        <div class="proof_titlecentr proof_leadnum">Lead #</div>
        <div class="proof_titlecentr proof_brand">Req #</div>
        <div class="proof_titlecentr proof_date">Date</div>
        <div class="proof_titlecentr proof_customer">Customer</div>
        <div class="proof_titlecentr proof_item">Item</div>
        <div class="proof_titlecentr proof_qty">QTY</div>
        <div class="artdataarea">
            <div class="proof_titlecentr proof_art">Art</div>
            <div class="proof_titlecentr proof_redrawn">Redr</div>
            <div class="proof_titlecentr proof_vector">Vec</div>
            <div class="proof_titlecentr proof_proofed">Pro</div>
            <div class="proof_titlecentr proof_approve">Apr</div>
        </div>
        <div class="proof_titlecentr proof_note">&nbsp;</div>
    </div>
    <div class="proof_tabledat"></div>
    <div class="quest_tablefoot">
        <div class="quest_tablefoot_left">&nbsp;</div>
        <div class="quest_tablefoot_center">&nbsp;</div>
        <div class="quest_tablefoot_right">&nbsp;</div>
    </div>
    <div id="proof_dialog" style="display: none; width: 895px; height: 443px;"></div>
</div>
