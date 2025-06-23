<div class="accreceiv-content-left <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
    <div class="accreceiv-totalown">
        <div class="accreceiv-totalown-title">Owed to Us:</div>
        <div class="accreceiv-totalown-value"><?=TotalOutput($totalown)?></div>
    </div>
    <div class="accreceiv-totalpast">
        <div class="accreceiv-totalpast-title">Past Due:</div>
        <div class="accreceiv-totalpast-value"><?=TotalOutput($pastown)?></div>
    </div>
<!--    <div class="accreceiv-datafilter">-->
<!--        <select class="ownsortselect" data-sort="ownsort1">-->
<!--            <option value="">Sort 1</option>-->
<!--            <option value="batch_due" --><?php //=$ownsort1=='batch_due' ? 'selected' : ''?><!--Due</option>-->
<!--            <option value="balance" --><?php //=$ownsort1=='balance' ? 'selected' : ''?><!--Balance</option>-->
<!--            <option value="order_num" --><?php //=$ownsort1=='order_num' ? 'selected' : ''?><!--Order</option>-->
<!--            <option value="customer_name" --><?php //=$ownsort1=='customer_name' ? 'selected' : ''?><!--Customer</option>-->
<!--            <option value="owntype" --><?php //=$ownsort1=='owntype' ? 'selected' : ''?><!--Type</option>-->
<!--            <option value="ownapprove" --><?php //=$ownsort1=='ownapprove' ? 'selected' : ''?><!--Approve</option>-->
<!--            <option value="debt_status" --><?php //=$ownsort1=='debt_status' ? 'selected' : ''?><!--Status</option>-->
<!--        </select>-->
<!--    </div>-->
<!--    <div class="accreceiv-datafilter">-->
<!--        <select class="ownsortselect" data-sort="ownsort2">-->
<!--            <option value="">Sort 2</option>-->
<!--            <option value="batch_due" --><?php //=$ownsort2=='batch_due' ? 'selected' : ''?><!--Due</option>-->
<!--            <option value="balance" --><?php //=$ownsort2=='balance' ? 'selected' : ''?><!--Balance</option>-->
<!--            <option value="order_num" --><?php //=$ownsort2=='order_num' ? 'selected' : ''?><!--Order</option>-->
<!--            <option value="customer_name" --><?php //=$ownsort2=='customer_name' ? 'selected' : ''?><!--Customer</option>-->
<!--            <option value="owntype" --><?php //=$ownsort2=='owntype' ? 'selected' : ''?><!--Type</option>-->
<!--            <option value="ownapprove" --><?php //=$ownsort2=='ownapprove' ? 'selected' : ''?><!--Approve</option>-->
<!--            <option value="debt_status" --><?php //=$ownsort2=='debt_status' ? 'selected' : ''?><!--Status</option>-->
<!--        </select>-->
<!--    </div>-->
    <div class="accreceiv-export">
        <div class="accreceiv-exportbtn">
            <i class="fa fa-file-excel-o"></i>
            Export
        </div>
    </div>
    <div class="accreceiv-print">
        <div class="accreceiv-printbtn">
            <i class="fa fa-print"></i>
            Print
        </div>
    </div>
</div>
<div class="accreceiv-content-center <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
    <div class="accreceiv-totalrefund">
        <div class="accreceiv-totalrefund-title">Refunds to Customers:</div>
        <div class="accreceiv-totalrefund-value">(<?=TotalOutput(abs($totalrefund))?>)</div>
    </div>
    <div class="totalrefund-export">
        <div class="totalrefund-exportbtn">
            <i class="fa fa-file-excel-o"></i>
            Export
        </div>
    </div>
    <div class="accreceiv-content-right"></div>
</div>
