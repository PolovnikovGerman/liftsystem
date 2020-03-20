<?php foreach ($calendar_lines as $row) { ?>
    <div class="holiday">
        <div class="delete calenddelrow" data-calendline="<?=$row['calendar_line_id']?>">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </div>
        <div class="data-holidays"><?=date('M j, Y', $row['line_date']) ?></div>
    </div>
<?php } ?>
