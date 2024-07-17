<div class="table-emails">
    <table>
        <?php foreach ($messages as $message) { ?>
            <?php if ($message['message_udate']>=$today_bgn) { ?>
                <?php $outdate = date('g i A', $message['message_udate']);?>
            <?php } elseif ($message['message_udate']<$year_bgn) { ?>
                <?php $outdate = date('m/d/Y', $message['message_udate']); ?>
            <?php  } else { ?>
                <?php $outdate = date('M j', $message['message_udate']); ?>
            <?php } ?>
            <tr class="tab-tr <?=$message['message_seen']==1 ? 'tr-read' : 'tr-unread'?>">
                <th class="tab-th-01">
                    <input class="eb-checkbox" type="checkbox" data-message="<?=$message['message_id']?>">
                </th>
                <th class="tab-th-02" data-message="<?=$message['message_id']?>">
                    <?php if ($message['message_seen']==0) { ?>
                        <span class="ic-blue" data-message="<?=$message['message_id']?>"><i class="fa fa-circle" aria-hidden="true"></i></span>
                    <?php } elseif ($message['message_answered']==1) { ?>
                        <span class="ic-grey"><i class="fa fa-reply" aria-hidden="true"></i></span>
                    <?php } else { ?>
                        <span class="ic-normal" data-message="<?=$message['message_id']?>">&nbsp;</span>
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
                        <span class="ic-grey"><i class="fa fa-paperclip" aria-hidden="true"></i></span>
                        <!-- <span class="ic-grey"><i class="fa fa-file-image-o" aria-hidden="true"></i></span> -->
                    <?php } else { ?>
                        &nbsp;
                    <?php } ?>
                </th>
                <th class="tab-th-07" data-message="<?=$message['message_id']?>"><?=$outdate?></th>
            </tr>
        <?php } ?>
    </table>
</div>