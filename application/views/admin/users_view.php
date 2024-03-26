<div class="userslistcontent">
    <input type="hidden" id="totalusers" value="<?=$total?>"/>
    <input type="hidden" id="perpageusr" value="<?=$perpage?>"/>
    <input type="hidden" id="orderusr" value="<?=$orderby?>"/>
    <input type="hidden" id="direcusr" value="<?=$direct?>"/>
    <input type="hidden" id="orderusr2" value="<?=$orderby2?>"/>
    <input type="hidden" id="direcusr2" value="<?=$direct2?>"/>
    <div class="users_filters">
        <div class="usersortlabel">Sort 1</div>
        <div class="usersortvalue">
            <select class="usrsorter" data-sortlevel="1">
                <option value="user_status">Status</option>
                <option value="user_id">ID #</option>
                <option value="level">Level</option>
                <option value="user_name">User Name</option>
            </select>
        </div>
        <div style="width: 25px">&nbsp;</div>
        <div class="usersortlabel">Sort 2</div>
        <div class="usersortvalue">
            <select class="usrsorter" data-sortlevel="2">
                <option value="lastactivity">Activity</option>
                <option value="user_id">ID #</option>
                <option value="level">Level</option>
                <option value="user_name">User Name</option>
            </select>
        </div>

        <!--
        <div class="row form-layout-5">
            <label class="col-xl-3 col-lg-3 col-3 form-control-label tx-15 pd-l-15 pd-r-0 pd-xl-l-0 pd-xl-r-0 ml-2 text-black">Sort By: </label>
            <div class="col-xl-8 col-xl-8 col-7 pl-1">
                <select class="form-control select2 activeusrsorter" data-placeholder="Choose Browser">
                    <option value="userid asc">User # &#9650;</option>
                    <option value="userid desc">User # &#9660;</option>
                    <option value="userlogin asc">User Name &#9650;</option>
                    <option value="userlogin desc">User Name &#9660;</option>
                    <option value="username asc">Real Name &#9650;</option>
                    <option value="username desc">Real Name &#9660;</option>
                    <option value="useremail asc">Email &#9650;</option>
                    <option value="useremail desc">Email &#9660;</option>
                    <option value="rolename asc">Level &#9650;</option>
                    <option value="rolename desc">Level &#9660;</option>
                    <option value="employee asc">Employee &#9650;</option>
                    <option value="employee asc">Employee &#9660;</option>
                    <option value="lastactivity asc">Last Activity &#9650;</option>
                    <option value="lastactivity desc">Last Activity &#9650;</option>
                </select>
            </div>
        </div>
        -->
    </div>
    <div class="userdata_head">
        <div class="userid">ID #</div>
        <div class="actions">
            <button class="btn btn-primary addnewusers" id="addnewuserbtn">
                <i class="fa fa-plus" aria-hidden="true"></i> New User
            </button>
        </div>
        <div class="username">User Name</div>
        <div class="userstatushead">Status</div>
        <div class="userrealname">Real Name</div>
        <div class="useremail">Email</div>
        <div class="userlevel">Level</div>
        <div class="userlastactivity">Last Activity</div>
    </div>
    <div class="usertabledata" id="userinfo"></div>
    <!-- <div id="userinfo" class="tab2"></div> -->
    <!-- <div id="userdata" style="display: none; width: 820px; height: 460px;"></div> -->
</div>
