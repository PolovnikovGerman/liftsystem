<div class="page_container">
    <input type="hidden" value="<?=$brand?>" id="notificationsviewbrand"/>
    <div class="left_maincontent" id="notificationsviewbrandmenu">
        <?=$left_menu?>
    </div>
    <div class="right_maincontent">
        <input type='hidden' id='totalrec' value="<?=$total?>"/>
        <input type="hidden" id='orderby' value="<?=$order_by?>"/>
        <input type="hidden" id="direction" value="<?=$direction?>"/>
        <input type="hidden" id="curpage" value="<?=$cur_page?>"/>
        <div class="notificationscontent">
            <div class="emailnotification_main">
                <div class="notification_acltions">
                    <a id="addnotification" href="javascript:void(0);">
                        <img src="/img/others/addnotif.png" alt='Add Email' title="Add New Nofigication"/>
                    </a>
                </div>
                <div class="notification_depart">
                    Type of Notification
                </div>
                <div class="notification_email" style="float: left; width: 250px; font-size: 16px; text-align: center;">
                    Email Address
                </div>
            </div>
            <div class="table_notification" id="tabinfo"></div>
            <div class="table_notification_foot"></div>
        </div>

    </div>
</div>
