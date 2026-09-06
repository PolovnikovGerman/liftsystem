<div class="neworder_body">
    <div class="neworder_column1">
        <div class="ord_block ordblock_details">
            <div class="ordblock_tab">Order Details</div>
            <div class="ordblock_body">
                <div class="orddetails_row">
                    <?=$messages_view?>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_contacts">
                        <div class="orddtls_subtitle">CONTACTS</div>
                        <div class="orddtls_contactsbox">
                            <div class="tbl_contacts">
                                <div class="tblconts_tr tblconts_header">
                                    <div class="tblconts_td tblconts_name">
                                        <div class="tblconts_subtitle">Name:</div>
                                    </div>
                                    <div class="tblconts_td tblconts_phone">
                                        <div class="tblconts_subtitle">Telephone:</div>
                                    </div>
                                    <div class="tblconts_td tblconts_email">
                                        <div class="tblconts_subtitle">Email:</div>
                                    </div>
                                    <div class="tblconts_td tblconts_boxes">
                                        <div class="tblconts_subtitle">Art</div>
                                    </div>
                                    <div class="tblconts_td tblconts_boxes">
                                        <div class="tblconts_subtitle">Inv</div>
                                    </div>
                                    <div class="tblconts_td tblconts_boxes">
                                        <div class="tblconts_subtitle">Trk</div>
                                    </div>
                                </div>
                                <div id="ordercontacts_table" style="width: 100%">
                                    <?=$contacts?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_items">
                        <div class="orddtls_subtitle">ITEMS</div>
                        <div class="table_items">
                            <div class="tblitems_tr tblitems_header">
                                <div class="tblitems_td tblitems_item">Item#</div>
                                <div class="tblitems_td tblitems_descript">Description</div>
                                <div class="tblitems_td tblitems_color">Color</div>
                                <div class="tblitems_td tblitems_qty">Qty</div>
                                <div class="tblitems_td tblitems_each">Each</div>
                                <div class="tblitems_td tblitems_subtotal">Sub-total</div>
                            </div>
                            <div class="tblitems_tr whiterow">
                                <div class="tblitems_td tblitems_item">i020</div>
                                <div class="tblitems_td tblitems_descript">Light Bulb Stress Balls</div>
                                <div class="tblitems_td tblitems_color">White</div>
                                <div class="tblitems_td tblitems_qty">2500</div>
                                <div class="tblitems_td tblitems_each">0.72</div>
                                <div class="tblitems_td tblitems_subtotal">$1,800.00</div>
                            </div>
                            <div class="tblitems_tr doprow whiterow">
                                <div class="tblitems_td tblitems_item textgreen">
<!--                                        <span class="textgreen">-->
                                        Print Details:
<!--                                        </span>-->
                                </div>
                                <div class="tblitems_td tblitems_inforow">Loc 1: 1st Color Imprinting</div>
                                <div class="tblitems_td tblitems_qty">2500</div>
                                <div class="tblitems_td tblitems_each">--</div>
                                <div class="tblitems_td tblitems_subtotal">$0.00</div>
                            </div>
                            <div class="tblitems_tr greyrow">
                                <div class="tblitems_td tblitems_item">&nbsp;</div>
                                <div class="tblitems_td tblitems_inforow">One Time Art Setup Charge</div>
                                <div class="tblitems_td tblitems_qty">1</div>
                                <div class="tblitems_td tblitems_each">$28.00</div>
                                <div class="tblitems_td tblitems_subtotal">$28.00</div>
                            </div>
                        </div>
                        <div class="items_footer">
                            <div class="itemsfooter_message">
                                <label>Add’l message to appear on invoice:</label>
                                <textarea class="orderdata" name="invoice_message" data-fld="invoice_message" readonly><?=$order['invoice_message']?></textarea>
                            </div>
                            <div class="itemsfooter_inpts">
                                <div class="inpts_row">
                                    <input type="text" name="misc_charge1" class="orderdata" data-fld="mischrg_label1" readonly="readonly" placeholder="Misc Charge" value="<?=$order['mischrg_label1']?>"/>
                                </div>
                                <div class="inpts_row">
                                    <input type="text" name="misc_charge2" class="orderdata" data-fld="mischrg_label2" readonly="readonly" placeholder="Misc Charge" value="<?=$order['mischrg_label2']?>">
                                </div>
                                <div class="inpts_row">
                                    <input type="text" name="discount_label" class="orderdata" data-fld="discount_label" readonly="readonly" placeholder="Courtesy Discount" value="<?=$order['discount_label']?>">
                                </div>
                            </div>
                            <div class="itemsfooter_inptsprice">
                                <div class="inpts_row">
                                    <div class="inptsprice_box"><?=MoneyOutput($order['mischrg_val1'])?></div>
                                </div>
                                <div class="inpts_row">
                                    <div class="inptsprice_box"><?=MoneyOutput($order['mischrg_val2'])?></div>
                                </div>
                                <div class="inpts_row">
                                    <div class="inptsprice_box"><?=MoneyOutput($order['discount_val'])?></div>
                                </div>
                            </div>
                        </div>
                        <div class="item_subtotal">
                            <div class="itemsubtotal_price"><?=MoneyOutput($order['item_cost'])?></div>
                            <div class="itemsubtotal_txt">Item Sub-total:</div>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_shiptax">
                        <div class="orddtls_subtitle">SHIPPING & TAX</div>
                        <!-- Ship and Tax -->
                        <div class="shiptax_body">
                            <?=$shiptaxview?>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_dates">
                        <div class="ord_shipdate">
                            <div class="ord_datetitle">Ship Date:</div>
                            <div class="ord_datebox"><?=$shipping['out_shipdate']?></div>
                        </div>
                        <div class="ord_arrivaldate">
                            <div class="ord_datetitle">Arrival Date:</div>
                            <div class="ord_datebox"><?=$shipping['out_arrivedate']?></div>
                        </div>
                        <div class="ord_eventdate">
                            <div class="ord_datetitle">Event Date:</div>
                            <div class="ord_datebox"><?=$shipping['out_eventdate']?></div>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_ordtotal">
                        <div class="ordtotal_title">ORDER TOTAL:</div>
                        <div class="ordtotal_price"><?=MoneyOutput($order['revenue'])?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="neworder_column2">
        <div class="ord_block ordblock_artapproval">
            <div class="ordblock_tab">Art Approval</div>
            <div class="ordblock_body">
                <div class="artapprvl_row">
                    <div class="artapprvl_subtitle">Artwork:</div>
                    <div class="artapprvl_blankrush">
                        <div class="artapprvl_blank">
                            <input type="checkbox" class="">
                            <label>blank</label>
                        </div>
                        <div class="artapprvl_rush">
                            <input type="checkbox" class="">
                            <label>rush</label>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_instructitle">Instuctions:</div>
                    <div class="artapprvl_templates">
                        <div class="templates_title">Templates:</div>
                        <div class="templatebox">
                            <div class="templatebox_icon">
                                <img src="/img/leadorder/file-alt-green.svg">
                            </div>
                            <div class="templatebox_text">Master</div>
                        </div>
                        <div class="templatebox">
                            <div class="templatebox_icon">
                                <img src="/img/leadorder/file-alt-green.svg">
                            </div>
                            <div class="templatebox_text">Item</div>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_instrucbox">
                        <textarea></textarea>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_colors">Colors: Scarlet Red (485)</div>
                    <div class="artapprvl_font">
                        <label>Font:</label>
                        <select>
                            <option></option>
                            <option>Arial</option>
                        </select>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_subtitle">Art:</div>
                    <div class="artapprvl_art">
                        <div class="art_header">
                            <span class="artheader_original">Original:</span>
                            <span class="artheader_vector">Vector:</span>
                        </div>
                        <div class="art_boxes">
                            <div class="artapprvl_artbox">
                                <div class="artbox_number">1.</div>
                                <div class="artbox_filenameorg">examplefile.jpg</div>
                                <div class="artbox_iconfile">
                                    <img src="/img/leadorder/file-alt-grey.svg">
                                </div>
                                <div class="artbox_rush">
                                    <input type="checkbox" class="">
                                    <label>RUSH</label>
                                </div>
                                <div class="artbox_step arrow">
                                    <img src="/img/leadorder/artbox-arrow.svg">
                                </div>
                                <div class="artbox_filenamevect redrawing">Redrawing...</div>
                            </div>
                            <div class="artapprvl_artbox">
                                <div class="artbox_number">2.</div>
                                <div class="artbox_filenameorg unactive">examplefile.jpg</div>
                                <div class="artbox_iconfile unactive">
                                    <img src="/img/leadorder/file-alt-grey.svg">
                                </div>
                                <div class="artbox_rush unactive">
                                    <input type="checkbox" class="">
                                    <label>RUSH</label>
                                </div>
                                <div class="artbox_step tick">
                                    <img src="/img/leadorder/tick-blue.svg">
                                </div>
                                <div class="artbox_filenamevect">examplefile.ai</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_proofs">
                        <div class="artproofs_header">
                            <div class="artproofheader_title">Proofs:</div>
                            <div class="artproofheader_apprvl approval">
                                <span class="artproofapprvl_icon"><img src="/img/leadorder/tick-black.svg"></span>
                                <span>Approved</span>
                                <div class="artproofapprvl_days">6d</div>
                            </div>
                            <div class="artproofheader_open"><span>Open</span><span><img src="/img/leadorder/icon-link.svg"></span></div>
                            <div class="artproofheader_send">Send<span><i class="fa fa-envelope-o" aria-hidden="true"></i></span></div>
                        </div>
                        <div class="artproofs_body">
                            <div class="artproofs_optn">
                                <div class="artproofs_addoptn">+Add<br>Option</div>
                            </div>
                            <div class="artproofs_optn approved">
                                <div class="optn_header">
                                    <div class="optn_checkbox">
                                        <input type="checkbox" class="">
                                    </div>
                                    <div class="optn_title">Opt C</div>
                                    <div class="optn_star">
                                        <img src="/img/leadorder/star-yellow.svg">
                                    </div>
                                </div>
                                <div class="optn_box">
                                    <ul>
                                        <li>proof_10</li>
                                        <li>proof_11</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="artproofs_optn">
                                <div class="optn_header">
                                    <div class="optn_checkbox">
                                        <input type="checkbox" class="">
                                    </div>
                                    <div class="optn_title">Opt B</div>
                                    <div class="optn_star">
                                        <img src="/img/leadorder/star.svg">
                                    </div>
                                </div>
                                <div class="optn_box">
                                    <ul>
                                        <li>proof_08</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="artproofs_optn">
                                <div class="optn_header">
                                    <div class="optn_checkbox">
                                        <input type="checkbox" class="">
                                    </div>
                                    <div class="optn_title">Opt A</div>
                                    <div class="optn_star">
                                        <img src="/img/leadorder/star.svg">
                                    </div>
                                </div>
                                <div class="optn_box">
                                    <ul>
                                        <li>proof_01</li>
                                        <li>proof_02</li>
                                        <li>proof_07</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row"></div>
            </div>
        </div>
        <div class="ord_block ordblock_payment">
            <div class="ordblock_tab">Payment</div>
            <div class="ordblock_body">
                <div class="paymentsection_left">
                    <div class="po_inputgroup">
                        <label>PO#:</label>
                        <input type="text" name="" placeholder="CuPo">
                    </div>
                    <div class="billing_addressarea">
                        <div class="sameaddress">
                            <input type="checkbox" name="" checked>
                            <label>Same as shipping address</label>
                        </div>
                        <div class="inptaddress_country">
                            <select>
                                <option>United States</option>
                                <option>Canada</option>
                            </select>
                        </div>
                        <div class="billaddress_box">
                            <div class="copyaddress"><i class="fa fa-clone" aria-hidden="true"></i></div>
                            <input class="inpt_addressarea inptaddress_name" type="text" name="" placeholder="Contact Name">
                            <input class="inpt_addressarea inptaddress_company" type="text" name="" placeholder="Company">
                            <input class="inpt_addressarea inptaddress_addressline" type="text" name="" placeholder="Address Line 1">
                            <input class="inpt_addressarea inptaddress_addressline" type="text" name="" placeholder="Address Line 2">
                            <input class="inpt_addressarea inptaddress_city" type="text" name="" placeholder="City">
                            <select class="select_addressarea">
                                <option>ST</option>
                                <option>NJ</option>
                            </select>
                            <input class="inpt_addressarea inptaddress_zipcode" type="text" name="" placeholder="City">
                        </div>
                    </div>
                </div>
                <div class="paymentsection_right">
                    <div class="paymenthistory">
                        <div class="orddtls_subtitle">Payment History</div>
                        <div class="paymnt_viewdeclined">View Declined</div>
                        <div class="paymenthistory_box">
                            <div class="paymhist_tbl">
                                <div class="paymhist_tr paymhist_header">
                                    <div class="paymhist_td paymhist_date">Date</div>
                                    <div class="paymhist_td paymhist_payment">Payment</div>
                                    <div class="paymhist_td paymhist_amount">Amount</div>
                                </div>
                                <div class="paymhist_tr tr_white">
                                    <div class="paymhist_td paymhist_date">11/01/26</div>
                                    <div class="paymhist_td paymhist_payment">Visa 6883</div>
                                    <div class="paymhist_td paymhist_amount">$2,125.45</div>
                                </div>
                                <div class="paymhist_tr tr_grey">
                                    <div class="paymhist_td paymhist_date">9/17/26</div>
                                    <div class="paymhist_td paymhist_payment">Visa 6883</div>
                                    <div class="paymhist_td paymhist_amount">$200.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="totalpaymnt">
                        <div class="totalpaymnt_txt">Total Payments:</div>
                        <div class="totalpaymnt_price">$2,325.45</div>
                    </div>
                </div>
                <div class="paymentsection_bottom">
                    <div class="balancedueblock">
                        <div class="balanceduebox">
                            <div class="balancedue_link">
                                <div class="balancedue_linkicon">
                                    <img src="/img/leadorder/icon-link-grey.svg">
                                </div>
                            </div>
                            <div class="balancedue_send"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                            <div class="balancedue">
                                <div class="balancedue_txt">Balance Due:</div>
                                <div class="balancedue_price">$1,200.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="paymentblock">
                        <div class="orddtls_subtitle">PAYMENT</div>
                        <div class="paymentbox">
                            <div class="paymentbox_card">
                                <input class="paymentcard_price" type="text" name="" placeholder="$0.00">
                                <input class="paymentcard_number" type="text" name="" placeholder="XXXX-XXXX-XXXX-XXXX">
                                <input class="paymentcard_date" type="text" name="" placeholder="DD">
                                <div class="paymentcard_txt">/</div>
                                <input class="paymentcard_month" type="text" name="" placeholder="DD">
                                <input class="paymentcard_cvc" type="text" name="" placeholder="CVC">
                                <div class="paymentcard_lock"><i class="fa fa-lock" aria-hidden="true"></i></div>
                            </div>
                            <div class="btn_addcard">+add credit card</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="neworder_column3">
        <div class="ord_block ordblock_fulfillment">
            <div class="ordblock_tab">Fulfillment</div>
            <div class="ordblock_body">
                <div class="fulflm_row">
                    <div class="fulflm_profit">
                        <div class="fulflmprofit_price">$80.00</div>
                        <div class="fulflmprofit_interest">40% PROJ</div>
                        <div class="fulflmprofit_complt">0% COMPLETE</div>
                    </div>
                </div>
                <div class="fulflm_row">
                    <div class="fulflm_claymodels">
                        <div class="claymodels_header">Clay Models:</div>
                        <div class="claymodels_body">
                            <div class="claymodels_addoptn">+Add<br>Option</div>
                        </div>
                    </div>
                </div>
                <div class="fulflm_row">
                    <div class="fulflm_previewpict">
                        <div class="previewpict_header">Preview Pictures:</div>
                        <div class="previewpict_body">
                            <div class="previewpict_addoptn">+Add<br>Option</div>
                        </div>
                    </div>
                </div>
                <div class="fulflm_row">
                    <div class="fulflm_shipping">
                        <div class="fulflmshipping_header">1 Art Proof for Custom Robot Skull SBs</div>
                        <div class="fulflmshipping_body">
                            <div class="fulflmshipping_box">
                                <input class="fulflmship_inptqty" type="text" name="" >
                                <input class="fulflmship_inptdate" type="text" name="" placeholder="09/17/2026">
                                <select class="fulflmship_inptcarrier">
                                    <option>UPS</option>
                                </select>
                                <input class="fulflmship_inpttrack" type="text" name="" >
                            </div>
                            <div class="fulflmshipping_addnew">[add new]</div>
                        </div>
                        <div class="fulflmshipping_footer">
                            <div class="fulflmshipping_remaining">1 Remaining</div>
                            <div class="fulflmshipping_ship">To Ship 09/17/26</div>
                        </div>
                    </div>
                </div>
                <div class="fulflm_row"></div>
            </div>
        </div>
    </div>

</div>
