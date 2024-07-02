<?php
$todaystart = $yesterdaystart = $weekstart = $yearstart = $otherstart = 0;
$curmonth = 0; $opentable =0; $otheryear = 0;
$curdate = '';
?>
<?php foreach ($messages as $message) { ?>
    <?php if ($message['message_udate']>=$today_bgn) { ?>
        <?php $outdate = date('g i A', $message['message_udate']);?>
    <?php } elseif ($message['message_udate']<$year_bgn) { ?>
        <?php $outdate = date('m/d/Y', $message['message_udate']); ?>
    <?php  } else { ?>
        <?php $outdate = date('M j', $message['message_udate']); ?>
    <?php } ?>
    <?php if ($curdate!==date('Y-m-d', $message['message_udate'])) { ?>
        <?php $curdate = date('Y-m-d', $message['message_udate']);?>
    <?php } ?>
    <?php if ($message['message_udate']>=$today_bgn && $todaystart==0) { ?>
        <div class="emailes-date">Today</div>
        <div class="table-emails">
        <table>
        <?php $todaystart = 1; $opentable = 1;?>
    <?php } elseif ($message['message_udate'] >= $yesterday_bgn && $yesterdaystart == 0) { ?>
        <?php if ($opentable==1) { ?>
            </table>
            </div>
        <?php } ?>
        <div class="emailes-date">Yesterday</div>
        <div class="table-emails">
        <table>
        <?php $yesterdaystart = 1; $opentable = 1; ?>
    <?php } elseif ($message['message_udate'] >= $week_bgn && $message['message_udate'] < $yesterday_bgn && $weekstart==0) { ?>
        <?php if ($opentable==1) { ?>
            </table>
            </div>
        <?php } ?>
        <div class="emailes-date">Last week</div>
        <div class="table-emails">
        <table>
        <?php $weekstart = 1; $opentable = 1; ?>
    <?php } elseif ($message['message_udate'] >= $year_bgn && $message['message_udate'] < $week_bgn) {?> <!--  && $yearstart==0 -->
        <?php if ($curmonth!==date('m', $message['message_udate'])) { ?>
            <?php if ($opentable==1) { ?>
                </table>
                </div>
            <?php } ?>
            <?php $yearstart=1; $opentable = 0;?>
            <?php $curmonth = date('m', $message['message_udate']);?>
            <div class="emailes-date"><?=date('F', $message['message_udate'])?></div>
            <div class="table-emails">
            <table>
            <?php $opentable = 1;?>
        <?php } ?>
    <?php } elseif ($message['message_udate'] < $year_bgn && $otherstart==0) {?>
        <?php if ($opentable==1) { ?>
            </table>
            </div>
        <?php } ?>
        <?php $otherstart=1; $opentable = 0;?>
        <?php if ($otheryear!==date('Y', $message['message_udate'])) { ?>
            <?php $otheryear = date('Y', $message['message_udate']);?>
            <div class="emailes-date"><?=date('Y', $message['message_udate'])?></div>
            <div class="table-emails">
            <table>
            <?php $opentable = 1;?>
        <?php } ?>
    <?php } ?>
    <tr class="tab-tr <?=$message['message_seen']==1 ? 'tr-read' : 'tr-unread'?>" data-message="<?=$message['message_id']?>">
        <th class="tab-th-01">
            <input class="eb-checkbox" type="checkbox" data-message="<?=$message['message_id']?>">
        </th>
        <th class="tab-th-02" data-message="<?=$message['message_id']?>">
            <?php if ($message['message_seen']==0) { ?>
                <span class="ic-blue" data-message="<?=$message['message_id']?>" title="Mark As Read"><i class="fa fa-circle" aria-hidden="true"></i></span>
            <?php } elseif ($message['message_answered']==1) { ?>
                <span class="ic-grey"><i class="fa fa-reply" aria-hidden="true"></i></span>
            <?php } else { ?>
                <span class="ic-normal" data-message="<?=$message['message_id']?>" title="Mark As Unread"><i class="fa fa-circle-thin"></i></span>
            <?php } ?>
        </th>
        <th class="tab-th-03" data-message="<?=$message['message_id']?>"><?=$message['message_from']?></th>
        <th class="tab-th-04" data-message="<?=$message['message_id']?>">
            <?php if ($message['message_flagged']==1) { ?>
                <span class="ic-orange" data-message="<?=$message['message_id']?>"><i class="fa fa-star" aria-hidden="true"></i></span>
            <?php } else { ?>
                <span class="ic-grey" data-message="<?=$message['message_id']?>"><i class="fa fa-star-o" aria-hidden="true"></i></span>
            <?php } ?>
        </th>
        <th class="tab-th-05" data-message="<?=$message['message_id']?>"><span class="subject-email"><?=$message['message_subject']?></span></th>
        <th class="tab-th-06" data-message="<?=$message['message_id']?>">
            <?php if ($message['numattach']>0) { ?>
                <span class="ic-grey"><i class="fa fa-file-image-o" aria-hidden="true"></i></span>
            <?php } else { ?>
                &nbsp;
            <?php } ?>
        </th>
        <th class="tab-th-07" data-message="<?=$message['message_id']?>"><?=$outdate?></th>
    </tr>
<?php } ?>
<?php if ($opentable==1) { ?>
    </table>
    </div>
<?php } ?>