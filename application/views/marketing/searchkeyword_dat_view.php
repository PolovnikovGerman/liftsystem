<?php if (count($dat)!=0) {?>
    <?php $nrow=1;?>
    <?php $indexdat=0;?>
    <?php $lendata=count($dat);?>
    <div class="table-SearchResults-2">
    <table cellspacing="0" cellpadding="0">
    <tr class="table-SearchResults-text5">
        <td width="44" class="table-01-brown"><b>Rank</b></td>
        <td width="204" colspan="2" class="table-02-brown"><b>Keyword</b></td>
        <td width="60" class="table-03-brown"><b>Searches</b></td>
    </tr>
    <?php foreach($dat as $row) { ?>
        <tr class="<?=($nrow%2==0 ? 'grey-line' : 'line')?>">
            <td width="43" class="table-04-brown table-SearchResults-text5 TableTitle-b6">
                <b><?=$nrow;?></b>
            </td>
            <td class="table-SearchResults-text6">
                <div class='overflowtext' style="padding-left:5px;height: 22px;width: 168px; padding-right: 5px;">
                    <?=$row['search_text']?>
                </div>
            </td>
            <td width="27" class="TableTitle-b6 negative_result"><?=($row['search_result']=='1'? '&nbsp;' : 'N')?></td>
            <td width="60" class="TableTitle-b7 table-SearchResults-text7"><?=$row['cnt']?></td>
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
                <td width="44" class="table-01-brown"><b>Rank</b></td>
                <td width="204" colspan="2" class="table-02-brown"><b>Keyword</b></td>
                <td width="60" class="table-03-brown"><b>Searches</b></td>
            </tr>

        <?php } ?>
    <?php } ?>
    </table>
    </div>
<?php } else { ?>
    <div style="float: left;width:969px;font-size: 14px; font-weight: bold">
        No data about Searches for this period
    </div>
<?php } ?>

