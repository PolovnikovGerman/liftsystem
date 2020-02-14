<div class="signupdata_head">
    <div class="numpp">&nbsp;</div>
    <div class="date">Date</div>
    <div class="name">Name</div>
    <div class="email">Email</div>
</div>
<div class="signupdata_content">
    <?php if (count($data)==0) { ?>
        <div class="rowdata emptyvalue">The are no Email messages with category "Signups"</div>
    <?php } else { ?>
        <?php foreach ($data as $row) { ?>
            <div class="rowdata <?=$row['numpp']%2==0 ? 'greydatarow' : 'whitedatarow'?>">
                <div class="numpp"><?= $row['numpp'] ?></div>
                <div class="date"><?= date('m/d/y', strtotime($row['email_date'])) ?></div>
                <div class="name"><?= $row['email_sender'] ?></div>
                <div class="email truncateoverflowtext last_column_left"><?= $row['email_sendermail'] ?></div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
