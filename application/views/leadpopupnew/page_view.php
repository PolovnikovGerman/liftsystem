<div class="contant-popup">
    <input type="hidden" id="leadeditid" value="<?=$leadsession?>"/>
    <div class="section-customer"><?=$customer_view?></div>
    <div class="section-content">
        <div class="leadblock">
<!--            <div class="btnclose"><i class="fa fa-times" aria-hidden="true"></i></div>-->
            <div class="leadblock-header">
                <div class="leadnumber">Lead: <span>#L<?=$lead['lead_number']?></span></div>
                <div class="leadtopstatus">
                    <label>Status:</label>
                    <select id="lead_type" class="leadmainedit" data-fld="lead_type">
                        <option value="1" <?=$lead['lead_type']==1 ? 'selected="selected"' : ''?>><span class="leadtopstatus-star"><i class="fa fa-star" aria-hidden="true"></i></span> Priority</option>
                        <option value="2" <?=$lead['lead_type']==2 ? 'selected="selected"' : ''?>>Open</option>
                        <option value="4" <?=$lead['lead_type']==4 ? 'selected="selected"' : ''?>>Closed</option>
                        <option value="3" <?=$lead['lead_type']==3 ? 'selected="selected"' : ''?>>Dead</option>
                    </select>
                </div>
                <div class="leadtopreps">
                    <div class="leadtopreps-txt">Reps:</div>
                    <?=$replica_view ?>
                </div>
            </div>
            <div class="datarow">
                <div class="leadblockleft">
                    <div class="lead-itemdescr">
                        <div class="lead-itemdescrtitle">Item & Description:</div>
                        <div class="lead-item">
                            <select id="lead_item" class="leadpopupitem" data-fld="lead_item_id">
                                <option value=""></option>
                                <?php foreach ($items as $item): ?>
                                <option value="<?=$item['item_id']?>" <?=$item['item_id']==$lead['lead_item_id'] ? 'selected="selected"' : ''?>>
                                    <?=$item['itemnumber']?> &mdash; <?=$item['itemname']?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="lead-descr <?=$lead['lead_item_id']==$this->config->item('custom_id') ? 'active' : ''?>">
                            <textarea class="leadmainedit" data-fld="other_item_name"><?=$lead['other_item_name']?></textarea>
                        </div>
                    </div>
                    <div class="lead-statushistory">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#custom-status-history" aria-controls="custom-status-history" role="tab" data-toggle="tab">Status & History:</a></li>
                            <li role="presentation"><a href="#custom-interest-history" aria-controls="custom-interest-history" role="tab" data-toggle="tab">Interest History</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="custom-status-history">
                                <div class="lead-status">
                                    <div class="lead-statusbox">
                                        <textarea class="leadmainedit" data-fld="newhistorymsg"></textarea>
                                    </div>
                                </div>
                                <div class="lead-history">
                                    <div class="lead-historybox">
                                        <div class="list-leadhistory" id="list-leadhistory"><?=$history_view?></div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="custom-interest-history">
                                <div class="list-quests"><?=$tasks_view?></div>
                            </div>
                        </div>
                    </div>
                    <div class="lead-attachments">
                        <div class="lead-attachmentstitle">Attachments:</div>
                        <div class="btn-attach">+ add attachment</div>
                        <div class="list-attachfiles"><?=$attachments_view?></div>
                    </div>
                </div>
                <div class="leadblockright">
                    <div class="lead-needbynotes">
                        <div class="lead-needby">
                            <div class="lead-needbytitle">Need by Date:</div>
                            <div class="lead-needbybox">
                                <input type="text" id="lead_needby" class="leadmainedit" data-fld="lead_needby" value="<?=empty($lead['lead_needby']) ? '' : date('D - M j, Y', strtotime($lead['lead_needby']))?>"/>
                            </div>
                        </div>
                        <div class="lead-notes">
                            <div class="lead-notestitle">Notes:</div>
                            <div class="lead-notesbox">
                                <textarea class="leadmainedit" data-fld="lead_note"><?=$lead['lead_note']?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="datarow">
                <div class="lead-quotes">
                    <div class="lead-quotestitle">Quotes:</div>
                    <div class="lead-quotesbody">
                        <div class="lead-quotesform"><?=$quote_form_view?></div>
                        <div class="btn-messagequote">Message on Quote <span class="quoteform-expandview"><i class="fa fa-caret-down" aria-hidden="true"></i></span></div>
                        <div class="lead-quotestable">
                            <div class="leadquotetabl-header">
                                <div class="leadquotetabl-tr">
                                    <div class="leadquotetabl-td leadquotetabl-date">Date</div>
                                    <div class="leadquotetabl-td leadquotetabl-quote">Quote #</div>
                                    <div class="leadquotetabl-td leadquotetabl-doc">&nbsp;</div>
                                    <div class="leadquotetabl-td leadquotetabl-web">Web</div>
                                    <div class="leadquotetabl-td leadquotetabl-qty">Qty</div>
                                    <div class="leadquotetabl-td leadquotetabl-prints">Prints</div>
                                    <div class="leadquotetabl-td leadquotetabl-descr">Description</div>
                                    <div class="leadquotetabl-td leadquotetabl-subtotal">Sub-total</div>
                                </div>
                            </div>
                            <div class="leadquotetabl-body"><?=$quotes_view?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="datarow">
                <div class="leadfooterleft">
                    <div class="lead-proofreq">
                        <div class="lead-proofreqtitle">Proof Requests:</div>
                        <div class="lead-proofreqbody">
                            <div class="btn-newproofreq">New Proof Request</div>
                            <div class="list-proofreq">
                                <div class="list-proofreqtitle">
                                    <div class="proofreqtitle-box">Art</div>
                                    <div class="proofreqtitle-box">Red</div>
                                    <div class="proofreqtitle-box">Vec</div>
                                    <div class="proofreqtitle-box">Pro</div>
                                    <div class="proofreqtitle-box">Apr</div>
                                </div>
                                <div class="list-proofreqbox"><?=$proofarts_list?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="leadfooterright">
                    <div class="lead-samplereq">
                        <div class="lead-samplereqtitle">Sample Request:</div>
                        <div class="lead-samplereqbody">&nbsp;</div>
                    </div>
                    <div class="lead-footerbtn">
                        <div class="duplicatelead">Duplicate Lead</div>
                        <div class="lead-savebtn">SAVE</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
