<div class="msgmovefoldersarea">
    <div class="datarow sortactionmsg <?=$postsort=='date_desc' ? '' : 'active'?>" data-action="date_desc">
        <?=$postsort=='date_desc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Date: Newest on top
    </div>
    <div class="datarow sortactionmsg <?=$postsort=='date_asc' ? '' : 'active'?>" data-action="date_asc">
        <?=$postsort=='date_asc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Date: Oldest on top
    </div>
    <div class="datarow sortactionmsg <?=$postsort=='unread_asc' ? '' : 'active'?>" data-action="unread_asc">
        <?=$postsort=='unread_asc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Unread
    </div>
    <div class="datarow sortactionmsg <?=$postsort=='starred_asc' ? '' : 'active'?>" data-action="starred_asc">
        <?=$postsort=='starred_asc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Starred
    </div>
    <div class="datarow sortactionmsg <?=$postsort=='sender_asc' ? '' : 'active'?>" data-action="sender_asc">
        <?=$postsort=='sender_asc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Sender
    </div>
    <div class="datarow sortactionmsg <?=$postsort=='subject_asc' ? '' : 'active'?>" data-action="subject_asc">
        <?=$postsort=='subject_asc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Subject
    </div>
    <div class="datarow sortactionmsg <?=$postsort=='attach_asc' ? '' : 'active'?>" data-action="attach_asc">
        <?=$postsort=='attach_asc' ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?> Attachments
    </div>
</div>
