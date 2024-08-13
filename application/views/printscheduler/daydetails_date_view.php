<div class="dates-block">
    <div class="list-dates-tabs">
        <div class="ld-arrow-left <?=$prevactive==1 ? 'active' : ''?>" data-print="<?=$printdate?>">
            <img class="lda-left" src="/img/printscheduler/chevron-left-dark.svg">
        </div>
        <ul>
            <?php foreach ($dates as $date): ?>
                <?php if ($date['printdate']==$printdate): ?>
                    <li class="tab-date active-date" data-printdate="<?=$date['printdate']?>"><?=date('D - M j, Y', strtotime($date['printdate']))?></li>
                <?php else: ?>
                    <li class="tab-date" data-printdate="<?=$date['printdate']?>"><?=date('D - M j', strtotime($date['printdate']))?></li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
        <div class="ld-arrow-right <?=$nxtactive==1 ? 'active' : ''?>" data-print="<?=$printdate?>">
            <img class="lda-right" src="/img/printscheduler/chevron-right-dark.svg">
        </div>
    </div>
    <div class="select-date">
        <label>Go to:</label>
        <input class="selectdate-input" type="text" name="selectdate" placeholder="07/17/2024">
        <div class="btn-selectdate">
            <img class="btn-selectdate-img" src="/img/printscheduler/icon-calendar.svg">
        </div>
    </div>
</div>
