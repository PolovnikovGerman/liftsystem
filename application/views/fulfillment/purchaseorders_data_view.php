<?php $nrow=0;?>
<div id="purchaseord0"></div>
<?php foreach ($orders  as $row) {?>
    <div class="trpo <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?> <?=$row['rowclass']?>" data-amountid="<?=$row['amount_id']?>" id="purchaseord<?=$row['amount_id']?>">
        <div class="purchase-order-profit-data <?=$row['profit_class']?>"><?=$row['profit']?></div>
        <div class="purchase-order-profitperc-data <?=$row['profit_class']?>"><?=$row['profit_perc']?></div>
        <div class="purchase-order-actions-data <?=$row['rowclass']?>">
            <?php if ($row['printshop']==0) { ?>
                <div class="subaction editpodata" data-amountid="<?=$row['amount_id']?>">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div>
                <div class="subaction delpodata" data-amountid="<?=$row['amount_id']?>">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <!--
            <a href="javascript:void(0)" data-amountid="<?=$row['amount_id']?>" class="edtpurch"><img src="/img/edit.png"/></a>
            <a href="javascript:void(0)" data-amountid="<?=$row['amount_id']?>" class="delpurch"><img src="/img/delete-black.png"/></a>
            -->
            <?php } else {?>
                &nbsp;
            <?php } ?>
        </div>
        <div class="purchase-order-date-data"><?=$row['amount_date']?></div>
        <div class="purchase-order-attach-data <?=$row['attclass']?>" <?=$row['atttitle']?> >
            <a href="javascript:void(0)" class="showpodoc"><?=$row['out_attach']?></a>
        </div>
        <div class="purchase-order-ordnum-data" title="<?=$row['potitle']?>"><?=$row['order_num']?></div>
        <div class="purchase-order-amount-data"><?=$row['amount_sum']?></div>
        <div class="purchase-order-vendor-data"><?=$row['vendor_name']?></div>
        <div class="purchase-order-method-data"><?=$row['method_name']?></div>
        <div class="purchase-order-reason-data"><?=$row['out_reason']?></div>
        <div class="purchase-order-reason-data"><?=$row['out_lowprofit']?></div>
    </div>
    <?php $nrow++?>
<?php }?>
