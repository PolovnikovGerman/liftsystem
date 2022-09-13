<?php $enable=($save_available==1 ? '' : 'disabled="disabled"')?>
<?php $read=($save_available==1 ? '' : 'readonly="readonly"')?>
<?php $display=($save_available==1 ? '' :'hide')?>
<form id="leadeditform">
    <input type="hidden" id="lead_id" name="lead_id" value="<?=$data['lead_id']?>"/>
    <input type="hidden" id="lead_item_id" value="<?=$data['lead_item_id']?>"/>
    <input type="hidden" id="brand" name="brand" value="<?=$data['brand']?>"/>
    <input type="hidden" id="session" value="<?=$session_id?>"/>
    <div class="lead_popup_content">
        <div class="lead_popupform_row">
            <div class="lead_popup_number">Lead # <?=$data['lead_number']?></div>
            <div class="lead_popup_status">
                <div class="lead_popup_statustitle">Status:</div>
                <div class="lead_popup_statusvalue">
                    <select <?=$enable?> class="leadpopstatus" name="lead_type" id="lead_type">
                        <option value="6" <?=($data['lead_type']==6 ? 'selected="selected"' : '')?>>Ordering Soon</option>
                        <option value="1" <?=($data['lead_type'] == 1 ? 'selected="selected"' : '') ?>>Priority</option>
                        <option value="2" <?=($data['lead_type'] == 2 ? 'selected="selected"' : '') ?>>Open</option>
                        <option value="4" <?=($data['lead_type'] == 4 ? 'selected="selected"' : '') ?>>Closed</option>
                        <?=$dead_option?>
                    </select>
                </div>
            </div>
            <div class="lead_popup_replicas">
                <?=$replica?>
            </div>
        </div>
        <div class="lead_popupform_row">
            <div class="lead_popup_usrdatas">
                <div class="lead_popup_usrdat_title">Company:</div>
                <div class="lead_popup_companyinpt">
                    <input type="text" <?=$read?> class="lead_company" name="lead_company" id="lead_company" value="<?= $data['lead_company'] ?>"/>
                </div>
            </div>
            <div class="lead_popup_usrcontacts">
                <div class="lead_popup_usrphone_title">Telephone:</div>
                <div class="lead_popup_phoneinpt">
                    <input type="text" <?=$read?> class="lead_phone" name="lead_phone" id="lead_phone" value="<?= $data['lead_phone'] ?>"/>
                </div>
            </div>
            <div class="lead_popup_values">
                <div class="lead_popup_value_title">Values:</div>
                <div class="lead_popup_valueinpt">
                    <input class="lead_value" <?=$read?> name="lead_value" id="lead_value" value="<?= $data['lead_value'] ?>"/>
                </div>
            </div>
            <div class="lead_popup_neadbys">
                <div class="lead_popup_neadbytitle">Need by</div>
                <div class="lead_popup_calendar">
                    <?=($display=='' ? '<img class="leadcalendbtn" src="/img/leads/calendar.gif" alt="Calendar" id="calendar_btn"/>' : '&nbsp;')?>
                </div>
                <div class="lead_popup_neadbyinpt">
                    <input type="text" <?=$read?> class="lead_needby" id="lead_needby" name="lead_needby" value="<?= $data['lead_needby'] ?>"/>
                </div>
            </div>
        </div>
        <div class="lead_popupform_row">
            <div class="lead_popup_usrdatas">
                <div class="lead_popup_usrdat_title">Contact:</div>
                <div class="lead_popup_companyinpt">
                    <input type="text" <?=$read?> class="lead_company" name="lead_customer" id="lead_customer" value="<?= $data['lead_customer'] ?>"/>
                </div>
            </div>
            <div class="lead_popup_usrcontacts">
                <div class="lead_popup_mailtitle">Email:</div>
                <div class="lead_popup_mailinpt">
                    <input class="lead_email" <?=$read?> name="lead_mail" id="lead_mail" value="<?= $data['lead_mail'] ?>"/>
                </div>
            </div>
            <div class="lead_popup_values">
                <div class="lead_popup_qtytitle">Qty:</div>
                <div class="lead_popup_qtyinpt">
                    <input class="lead_qty" <?=$read?> name="lead_itemqty" id="lead_itemqty" value="<?= $data['lead_itemqty'] ?>"/>
                </div>
            </div>
            <div class="lead_popup_neadbys">
                <div class="lead_popup_itemtitle">Item</div>
                <div class="lead_popup_iteminpt">
                    <!--<input class="lead_item" <?=$read?> name="lead_item" id="lead_item" value="<?= $data['lead_item'] ?>"/> -->
                    <select class="lead_item_select" id="lead_item">
                        <option value="">Select..</option>
                        <?php foreach ($items as $row) {?>
                            <option value="<?=$row['item_id']?>" <?=($row['item_id']==$data['lead_item_id'] ? 'selected="selected"' : '')?>>
                                <?=$row['item_name']?> <?=($row['item_id']<1 ? '' : '/ '.$row['item_number'])?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="lead_popupform_row">
            <div class="lead_popup_notearea">
                <div class="lead_popup_notetitle">Important Notes: <span>(specific concerns, dates, things to remember)</span></div>
                <div class="lead_popup_noteinpt">
                    <textarea class="lead_note" <?=$read?> id="lead_note" name="lead_note"><?= $data['lead_note']?></textarea>
                </div>
            </div>
            <div class="lead_popup_statusarea">
                <div class="lead_popup_statuses">Status:</div>
                <div class="lead_popup_statusinpt">
                    <textarea class="lead_statuses" <?=$read?> name="lead_status" id="lead_status"></textarea>
                </div>
                <div class="lead_popup_history">History:</div>
                <div class="lead_popup_statusinpt">
                    <div class="lead_history"><?=$history?></div>
                </div>
            </div>
            <div class="lead_popup_taskarea">
                <div class="item_otheritemarea">
                    <div class="item_otheritem_label"><?=$data['other_item_label']?></div>
                    <textarea id="other_item_name" class="other_item_name" name="other_item_name"><?=$data['other_item_name']?></textarea>
                </div>
                <div class="lead_popup_addrequest">
                    <img src="/img/leads/add_proofrequest_btn.png" alt="Add Proof Request"/>
                </div>
                <div class="lead_popup_attachs"><?=$attachs?></div>
            </div>
        </div>
        <div class="lead_popupform_row">
            <div class="lead_popup_questarea">
                <div class="lead_popup_questitle">Questions:</div>
                <div class="lead_popup_quescontent"><?=$questions?></div>
            </div>
            <div class="lead_onlinequotesarea">
                <div class="lead_popup_questitle">Quotes:</div>
                <div class="lead_onlinequotcontent"><?=$quotes?></div>
            </div>
            <div class="lead_onlineproofsarea">
                <div class="lead_popup_questitle">Proof Requests:</div>
                <div class="lead_popup_arttitle">Art</div>
                <div class="lead_popup_arttitle">Redr</div>
                <div class="lead_popup_arttitle">Vec</div>
                <div class="lead_popup_arttitle">Pro</div>
                <div class="lead_popup_arttitle">Apr</div>
                <div class="lead-onlineprofcontent"><?=$onlineproofs?></div>
            </div>
        </div>
        <div class="lead_popupform_row">
            <div class="lead_duplicate">Duplicate Lead</div>
            <div class="savelead">
                <?php if ($save_available==1) {?>
                    <a href="javascript:void(0);" class="saveleaddat">
                        <img alt="Save" src="/img/leads/saveticket.png">
                    </a>
                <?php } else {?>
                    &nbsp;
                <?php } ?>
            </div>
        </div>
    </div>
</form>