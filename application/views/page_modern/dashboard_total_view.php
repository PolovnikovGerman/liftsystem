<div class="period_lead_info" data-event="click" data-css="weekbrandtotals" data-bgcolor="#000000"
     data-bordercolor="#adadad" data-textcolor="#FFFFFF"
     data-position="down"
     data-balloon="{ajax} /welcome/weektotalvisitors/<?= $data['currweek'] ?>">
    <div class="leadsection visitors">
        <div class="period_name">Visitors:</div>
        <div class="param_value"><?=QTYOutput($data['visitors'])?></div>
    </div>
    <div class="leadsection leads">
        <div class="period_name">Leads:</div>
        <div class="param_value"><?=QTYOutput($data['leads'])?></div>
    </div>
</div>
<div class="period_review_info">
    <div class="period_name">Reviews:</div>
    <div class="param_value"><?=QTYOutput($data['reviews'])?></div>
</div>
<div class="period_analitic_info">
    <div class="period_name"><?=$data['label']?></div>
    <div class="totalsalesprev <?= $data['prev_navig'] == 1 ? 'active' : '' ?>" data-week="<?= $data['prev_week'] ?>">
        <i class="fa fa-caret-left"></i>
    </div>
    <div class="period_results">
        <div class="param_value" id="totalsales" data-event="click" data-css="weekbrandtotals" data-bgcolor="#000000"
             data-bordercolor="#adadad" data-textcolor="#FFFFFF"
             data-position="down"
             data-balloon="{ajax} /welcome/weektotalorders/<?= $data['currweek'] ?>"><?= $data['sales'] ?></div>
        <div class="param_value_label">Orders <span>@</span></div>
        <div class="param_value money" id="totalrevenue" data-event="click" data-css="weekbrandtotals"
             data-bgcolor="#000000" data-bordercolor="#adadad" data-textcolor="#FFFFFF"
             data-position="down"
             data-balloon="{ajax} /welcome/weektotals/<?= $data['currweek'] ?>"><?= MoneyOutput($data['revenue'], 0) ?></div>
    </div>
    <div class="totalsalesnext <?= $data['next_navig'] == 1 ? 'active' : '' ?>" data-week="<?= $data['next_week'] ?>">
        <i class="fa fa-caret-right"></i>
    </div>
</div>
