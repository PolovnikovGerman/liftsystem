<?php $numpp=1; ?>
<?php foreach ($leads as $lead): ?>
<!-- bottomline -->
    <tr class="leadtabledatarow <?=empty($lead['leadrow_class']) ? ($numpp%2==0 ? 'whiteline' : 'greyline') : $lead['leadrow_class']?> <?=empty($lead['separate']) ? '' : 'bottomline'?>">
        <th><div class="tblleads-td tblleads-leadpriority"><?=$lead['lead_priority_icon']?></div></th>
        <th><div class="tblleads-td tblleads-leadnumber"><?=$brand=='SR' ? 'D' : 'L'?><?=str_pad($lead['lead_number'],5,'0',STR_PAD_LEFT)?></div></th>
        <th><div class="tblleads-td tblleads-date"><?=$lead['out_date']?></div></th>
        <th><div class="tblleads-td tblleads-value"><?=$lead['out_value']?></div></th>
        <th><div class="tblleads-td tblleads-customer"><?=$lead['contact']?></div></th>
        <th><div class="tblleads-td tblleads-qty"><?=$lead['lead_itemqty']?></div></th>
        <!--  -->
        <th><div class="tblleads-td tblleads-item <?=$lead['itemshow_class']=='custom' ? 'boldtxt' : ''?>"><?=$lead['out_lead_item']?></div></th>
        <th><div class="tblleads-td tblleads-rep"><?=$lead['usr_data']?></div></th>
    </tr>
    <?php $numpp++;?>
<?php endforeach; ?>
