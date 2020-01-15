<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbcontent">
    <input type='hidden' id='totalrecdbprice' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbydbprice' value="<?= $order_by ?>"/>
    <input type="hidden" id="directiondbprice" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagedbprice" value="<?= $cur_page ?>"/>
    <input type="hidden" id="searchdbprice" value="<?= $search ?>"/>
    <input type="hidden" id="perpagedbprice" value="<?=$perpage?>"/>
    <div class="table-price">
        <table cellspacing="0" cellpadding="0">
            <tr class="table_head">
                <td class="gradient1 bor-1">
                    <div style="width: 30px;">&nbsp;</div>
                </td>
                <td class="gradient1 text-1 head-center">
                    <div style="width:26px;font-size: 11px;">edit</div>
                </td>
                <td class="<?= ($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> text-1  head-center"
                    id="itemnum">
                    <div style="width:61px;">
                        <a href="javascript:void(0);">Item #</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> text-1  head-center"
                    id="itemname">
                    <div style="width:179px;">
                        <a href="javascript:void(0);">Name</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'competitor' ? 'gradient2' : 'gradient1') ?> text-1 head-center">
                    <div style="width:85px">Competitor</div>
                </td>
                <td class="<?= ($order_by == 'price_25' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price25">
                    <div style="width:48px">
                        <a href="javascript:void(0);">25</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_75' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price75">
                    <div style="width:48px">
                        <a href="javascript:void(0);">75</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_150' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price150">
                    <div style="width:48px">
                        <a href="javascript:void(0);">150</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_250' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price250">
                    <div style="width:48px">
                        <a href="javascript:void(0);">250</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_500' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price500">
                    <div style="width:48px">
                        <a href="javascript:void(0);">500</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_1000' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price1000">
                    <div style="width:48px">
                        <a href="javascript:void(0);">1000</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_2500' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price2500">
                    <div style="width:48px">
                        <a href="javascript:void(0);">2500</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_5000' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price5000">
                    <div style="width:48px">
                        <a href="javascript:void(0);">5000</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_10000' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price10000">
                    <div style="width:48px">
                        <a href="javascript:void(0);">10K</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_20000' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price20000">
                    <div style="width:48px">
                        <a href="javascript:void(0);">20K</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'price_setup' ? 'gradient2' : 'gradient1') ?> text-1  head-center" id="price_setup">
                    <div style="width:48px">
                        <a href="javascript:void(0);">Setup</a>
                    </div>
                </td>
                <td class="gradient1">
                    <div style="width:10px">&nbsp;</div>
                </td>
                <td class="gradient1 text-1  head-center">
                    <div style="width:76px">
                        Updated
                    </div>
                </td>
            </tr>
        </table>
        <div class="tabinfo" id="dbpricetabinfo"></div>
    </div>
</div>