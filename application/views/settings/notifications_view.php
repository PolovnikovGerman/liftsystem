<div class="page_container">
    <div class="right_maincontent">
        <input type='hidden' class='totalrec' data-brand="<?=$brand?>" value="<?=$total?>"/>
        <input type="hidden" class='orderby' data-brand="<?=$brand?>" value="<?=$order_by?>"/>
        <input type="hidden" class="direction" data-brand="<?=$brand?>" value="<?=$direction?>"/>
        <input type="hidden" class="curpage" data-brand="<?=$brand?>" value="<?=$cur_page?>"/>
        <div class="notificationscontent">
            <div class="emailnotification_main">
                <div class="notification_acltions">
                    <a class="addnotification" data-brand="<?=$brand?>" href="javascript:void(0);">
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
            <div class="table_notification" data-brand="<?=$brand?>"></div>
            <div class="table_notification_foot"></div>
        </div>
    </div>
</div>
