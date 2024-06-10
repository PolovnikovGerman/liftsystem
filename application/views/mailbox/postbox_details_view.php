<div class="body-page">
    <div class="left-nav">
        <?=$folders?>
    </div>
    <div class="emails-block">
        <div class="emails-block-header">
            <div class="choose-all">
                <input class="eb-checkbox" type="checkbox" name="option1" value="a0">
                <span class="arrow-select">
             <i class="fa fa-chevron-down" aria-hidden="true"></i>
           </span>
            </div>
            <div class="menu-icons">
                <ul>
                    <li><span><img src="/img/mailbox/icon-archive.svg"></span> Archive</li>
                    <li><span><img src="/img/mailbox/icon-move.svg"></span> Move</li>
                    <li><span><img src="/img/mailbox/icon-delete.svg"></span> Delete</li>
                    <li><span><img src="/img/mailbox/icon-spam.svg"></span> Spam</li>
                    <li><span><img src="/img/mailbox/icon-more.svg"></span></li>
                </ul>
            </div>
            <div class="choose-sort">
                <label>Sort</label>
                <span class="arrow-select">
             <i class="fa fa-chevron-down" aria-hidden="true"></i>
           </span>
            </div>
        </div>
        <div class="emailes-date">Today</div>
        <div class="emails-block-body"><?=$messages?></div>
    </div>
</div>