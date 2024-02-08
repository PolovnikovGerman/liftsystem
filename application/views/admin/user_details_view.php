<div class="userdata">
    <!-- <form id="userdat"> -->
        <input type="hidden" id="session" value="<?=$session?>"/>
        <div class="userpersondata">
            <fieldset>
                <legend>User Personal Data</legend>
                <div class="clearfix"></div>
                <div class="input_row">
                    <div class="labeltxt">User Email (login):</div>
                    <div class="inputval">
                        <input type="text" class="large userpersdata" data-name="user_email" id="user_email" value="<?=$user['user_email']?>"/>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">User Name:</div>
                    <div class="inputval">
                        <input type="text" class="large userpersdata" data-name="user_name" id="user_name" value="<?=$user['user_name']?>"/>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Account Status:</div>
                    <div class="inputval">
                        <select data-name="user_status" id="user_status" class="permisselect userpersdata">
                            <option value="1" <?=($user['user_status']==1 ? 'selected="selected"' : '')?>>ACTIVE</option>
                            <option value="2" <?=($user['user_status']==2 ? 'selected="selected"' : '')?>>SUSPEND</option>
                        </select>
                    </div>
                </div>
                <div class="input_row userpasswd">
                    <div class="labeltxt">User Password:</div>
                    <div class="inputval">
                        <input type="user_passwd_txt" class="large userpersdata" data-name="user_passwd_txt1" id="user_passwd_txt1" value=""/>
                    </div>
                </div>
                <div class="input_row">(leave blank if not want to change)</div>
                <div class="input_row retypepasswd">
                    <div class="labeltxt">Retype password:</div>
                    <div class="inputval">
                        <input type="user_passwd_txt2" class="large userpersdata" data-name="user_passwd_txt2" id="user_passwd_txt2" value=""/>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Add'l Info</legend>
                <div class="input_row">
                    <div class="labeltxt">Leads Rep:</div>
                    <div class="inputval">
                        <input type="checkbox" class="userpersdatachk" data-name="user_leadrep" id="user_leadrep" value="1" <?=($user['user_leadrep']==1 ? 'checked="checked"' : '')?> />
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Finance User:</div>
                    <div class="inputval">
                        <input type="checkbox" class="userpersdatachk" data-name="finuser" id="finuser" value="1" <?=($user['finuser']==1 ? 'checked="checked"' : '')?> />
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Leads rep name</div>
                    <div class="inputval">
                        <input type="text" data-name="user_leadname" class="userpersdata" style="width:60px;" id="user_leadname" value="<?=$user['user_leadname']?>"/>
                        <div style="float:left; width: 10px;"></div>
                        <input type="text" data-name="user_initials" class="userpersdata" style="width:30px;" id="user_initials" maxlength="5" value="<?=$user['user_initials']?>"/>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Time Restrict</div>
                    <div class="inputval">
                        <select id="time_restrict" data-name="time_restrict" class="timerestict userpersdata">
                            <option value="0" <?=($user['time_restrict']==0 ? 'selected="selected"' : '')?>>No restrictions</option>
                            <option value="1" <?=($user['time_restrict']==1 ? 'selected="selected"' : '')?>>9:00AM - 5:00PM EST</option>
                            <option value="2" <?=($user['time_restrict']==2 ? 'selected="selected"' : '')?>>9:00AM - 7:00PM EST</option>
                            <option value="3" <?=($user['time_restrict']==3 ? 'selected="selected"' : '')?>>7:00AM - 7:00PM EST</option>
                            <option value="4" <?=($user['time_restrict']==4 ? 'selected="selected"' : '')?>>8:00AM - 8:00PM EST</option>
                        </select>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">IP Restrict:</div>
                    <div class="inputval">
                        <div id="iprestrictarea"><?=$iprestricts?></div>
                        <div class="addrestict"><i class="fa fa-plus" aria-hidden="true"></i> Add Restrict</div>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">From Email:</div>
                    <div class="inputval">
                        <input type="text" class="large userpersdata" data-name="personal_email" id="personal_email" value="<?=$user['personal_email']?>"/>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Email Signature:</div>
                    <div class="inputval">
                        <textarea class="emailsignature userpersdata" data-name="email_signature" id="email_signature"><?=$user['email_signature']?></textarea>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Lead Contact Info (Bluetrack):</div>
                    <div class="inputval">
                        <textarea class="emailsignature userpersdata" data-name="contactnote_bluetrack" id="contactnote_bluetrack"><?=$user['contactnote_bluetrack']?></textarea>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Lead Contact Info (StressRelievers):</div>
                    <div class="inputval">
                        <textarea class="emailsignature userpersdata" data-name="contactnote_relievers" id="contactnote_relievers"><?=$user['contactnote_relievers']?></textarea>
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">Profit View:</div>
                    <div class="inputval">
                        <select id="profit_view" data-name="profit_view" class="timerestict userpersdata">
                            <option value="Points" <?=($user['profit_view']=='Points' ? 'selected="selected"' : '')?>>In Points</option>
                            <option value="Profit" <?=($user['profit_view']=='Profit' ? 'selected="selected"' : '')?>>Normal Profit</option>
                        </select>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="permissionsdata">
            <fieldset>
                <legend>Pages Permissions</legend>
                <div class="permissioninfo">
                    <?=$webpages?>
                </div>
                <div class="input_row">
                    <div class="labeltxt exportaccess">Access to Order Export: </div>
                    <div class="inputval exportaccess">
                        <input type="checkbox" class="userpersdatachk" data-name="user_leadrep" id="user_leadrep" value="1" <?=($user['user_leadrep']==1 ? 'checked="checked"' : '')?> />
                    </div>
                </div>
                <div class="input_row">
                    <div class="labeltxt">User Default page</div>
                    <div class="inputval"><?=$pages_select?></div>
                </div>
            </fieldset>
        </div>
    <!-- </form> -->
</div>
