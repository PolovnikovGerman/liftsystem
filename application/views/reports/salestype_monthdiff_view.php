<div class="salestypemonthdiffarea">
    <div class="title"><?=$month?></div>
    <div class="subtitle">
        <div class="article">&nbsp;</div>
        <div class="yeardatavalue"><?=$prvyear?></div>
        <div class="yeardatavalue"><?=$curyear?></div>
        <div class="growth">Growth</div>
        <div class="growthproc">&percnt;</div>
    </div>
    <div class="dataarea">
        <?php foreach ($monthdata as $row) { ?>
            <div class="datarow">
                <div class="article"><?=$row['title']?></div>
                <div class="yeardatavalue"><?=$row['prvdata']?></div>
                <div class="yeardatavalue"><?=$row['curdata']?></div>
                <div class="growth <?=$row['diffclass']?>"><?=$row['diff']?></div>
                <div class="growthproc <?=$row['diffclass']?>"><?=$row['diffproc']?></div>
            </div>
        <?php } ?>
    </div>
    <div class="title">Quarter <?=$quoter?></div>
    <div class="subtitle">
        <div class="article">&nbsp;</div>
        <div class="yeardatavalue"><?=$prvyear?></div>
        <div class="yeardatavalue"><?=$curyear?></div>
        <div class="growth">Growth</div>
        <div class="growthproc">&percnt;</div>
    </div>
    <div class="dataarea">
        <?php foreach ($quotedata as $row) { ?>
            <div class="datarow">
                <div class="article"><?=$row['title']?></div>
                <div class="yeardatavalue"><?=$row['prvdata']?></div>
                <div class="yeardatavalue"><?=$row['curdata']?></div>
                <div class="growth <?=$row['diffclass']?>"><?=$row['diff']?></div>
                <div class="growthproc <?=$row['diffclass']?>"><?=$row['diffproc']?></div>
            </div>
        <?php } ?>
    </div>
</div>