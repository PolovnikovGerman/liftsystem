<div class="fulfillmentblock">
    <div class="fulfillm_historical">
        <div class="fulfillm_hist_title">HISTORICAL</div>
        <div class="btbox_list">
            <?php foreach ($orders as $order) : ?>
            <div class="btbox" data-btbox="<?=$order['netdata_order_id']?>">
                <div class="btbox_arrow" data-btbox="<?=$order['netdata_order_id']?>"><i class="fa fa-caret-right" aria-hidden="true"></i></div>
                <div class="btbox_title">BT <?=$order['order_num']?>-<?=$order['po_code']?></div>
                <div class="btbox_info">
                    - <?=empty($order['po_date']) ? '----' : date('m/d/y', $order['po_date'])?> - <?=empty($order['po_total']) ? '' : MoneyOutput($order['po_total'])?> - <?=$order['vendorname']?></div>
                <?php if (!empty($order['po_attach_path'])) : ?>
                <div class="btbox_icon" data-url="<?=$order['po_attach_path']?>'?>" data-file="<?=$order['po_attach_name']?>" title="<?=$order['po_attach_name']?>">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </div>
                <div class="btbox_net">.net</div>
                <?php endif; ?>
                <div class="btbox_body" data-btbox="<?=$order['netdata_order_id']?>">
                    <div class="btbox_bodyrow">
                        <div class="btbox_status">Status:</div>
                        <div class="btbox_billmethod">Bill Method:</div>
                        <div class="btbox_rep">Rep:</div>
                    </div>
                    <div class="btbox_bodyrow">
                        <div class="btbox_shipdate">
                            <div class="btbox_shipdate_label">Ship Date:</div>
                            <div class="btbox_shipdate_box"><?=!empty($order['po_ship_date']) ? date('m/d/y', $order['po_ship_date']) : ''?></div>
                        </div>
                        <div class="btbox_shipact">
                            <div class="btbox_shipact_label">Ship Act:</div>
                            <select disabled="disabled">
                                <option><?=$order['po_ship_act']?></option>
                            </select>
                        </div>
                    </div>
                    <div class="btbox_bodyrow">
                        <div class="btbox_messagevendor">
                            <div class="btbox_messagevendor_label">Message to Vendor:</div>
                            <div class="btbox_messagevendor_box">
                                <textarea readonly="readonly"><?=$order['po_vendor_msg']?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="btbox_bodyrow">
                        <div class="shipaddrs">
                            <?php $methods = $order['methods'];?>
                            <?php $numpp = 1; ?>
                            <div class="shipaddrs_tabs">
                                <?php foreach ($methods as $method) : ?>
                                <div class="shipaddrs_tab <?=$numpp==1 ? 'active' : ''?>" data-method="<?=$method['netdata_method_id']?>">Ship To <?=$numpp?></div>
                                <?php $numpp++; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php $numpp=1; ?>
                            <?php foreach ($methods as $method) : ?>
                                <div class="shipaddrs_body <?=$numpp==1 ? 'active' : ''?>" data-method="<?=$method['netdata_method_id']?>">
                                    <div class="shipaddrs_left">
                                        <div class="shipaddrs_addressbox">
                                            <textarea readonly="readonly"><?=$method['ship_address']?></textarea>
                                        </div>
                                    </div>
                                    <div class="shipaddrs_right">
                                        <div class="shipaddrs_method">
                                            <label>Method:</label>
                                            <select disabled="disabled">
                                                <option><?=$method['ship_method']?></option>
                                            </select>
                                        </div>
                                        <div class="shipaddrs_billdate">
                                            <label>Bill Date:</label>
                                            <input type="text" readonly="readonly" value="<?=!empty($method['ship_date']) ? date('m/d/y', $method['ship_date']) : ''?>"/>
                                        </div>
                                    </div>
                                </div>
                                <?php $numpp++; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="btbox_bodyrow">
                        <div class="btbox_table">
                            <div class="btboxtable_header">
                                <div class="btboxtable_td btboxtable_item">Item#</div>
                                <div class="btboxtable_td btboxtable_descr">Description</div>
                                <div class="btboxtable_td btboxtable_qty">Qty</div>
                                <div class="btboxtable_td btboxtable_price">Price</div>
                                <div class="btboxtable_td btboxtable_subtotal">Subtotal</div>
                            </div>
                            <div class="btboxtable_body">
                                <?$numpp=1; ?>
                                <?php $items = $order['items'];?>
                                <?php foreach ($items as $item) : ?>
                                <div class="btboxtable_tr <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                                    <div class="btboxtable_td btboxtable_item"><?=$item['item_number']?></div>
                                    <div class="btboxtable_td btboxtable_descr truncateoverflowtext"><?=$item['item_name']?></div>
                                    <div class="btboxtable_td btboxtable_qty"><?=$item['item_qty']?></div>
                                    <div class="btboxtable_td btboxtable_price"><?=$item['item_price']?></div>
                                    <div class="btboxtable_td btboxtable_subtotal"><?=empty($item['subtotal']) ? '---' : MoneyOutput($item['subtotal'])?></div>
                                </div>
                                <?php $numpp++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
