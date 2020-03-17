<?php if (count($dat)!=0) {?>
    <?php $nrow=1;?>
    <?php $indexdat=0;?>
    <?php $lendata=count($dat);?>
    <div class="table-SearchResults-2">
    <table cellspacing="0" cellpadding="0">
    <tr class="table-SearchResults-text5">
        <td width="44" class="table-01-umber"><b>Rank</b></td>
        <td width="109" class="table-02-umber"><b>IP Address</b></td>
        <td width="80" class="table-02-umber"><b>Searches</b></td>
        <td width="92" class="table-03-umber"><b>Nickname</b></td>
    </tr>
    <?php foreach($dat as $row) { ?>
        <tr class="<?=($nrow%2==0 ? 'grey-line' : 'line')?>">
            <td width="43" class="table-04-umber table-SearchResults-text5 TableTitle-b6"><b><?=$nrow?></b></td>
            <td width="100" class="table-SearchResults-text6 TableTitle-b6"><?=$row['search_ip']?></td>
            <td width="79" class="TableTitle-b6 table-SearchResults-text3"><?=$row['cnt']?></td>
            <td width="83" class="TableTitle-b7 table-SearchResults-text6" id="row<?=$nrow?>">
                <div class='overflowtext' style="padding-left:5px;height: 22px;width: 73px;padding-top: 2px; padding-right: 5px;" title="<?=($row['search_user']=='' ? '' : $row['search_user'])?>">
                    <a href=javascript:void(0) onclick="ins_user('<?=$row['search_ip']?>','<?=$row['search_user']?>');" style="color:#000000">
                        <?=($row['search_user']=='' ? '...' : $row['search_user'])?>
                    </a>
                </div>
            </td>
        </tr>
        <?php $nrow++;?>
        <?php $indexdat++;?>
        <?php if ($indexdat==20) {?>
            <?php $indexdat=0;?>
            </table>
            </div>
            <div class="table-SearchResults-2">
            <table cellspacing="0" cellpadding="0">
            <tr class="table-SearchResults-text5">
                <td width="44" class="table-01-umber"><b>Rank</b></td>
                <td width="109" class="table-02-umber"><b>IP Address</b></td>
                <td width="80" class="table-02-umber"><b>Searches</b></td>
                <td width="92" class="table-03-umber"><b>Nickname</b></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </table>
    </div>
<?php }  else { ?>
    <div style="font-size: 14px; font-weight: bold">
        No data about Searches for this period
    </div>
<?php } ?>

