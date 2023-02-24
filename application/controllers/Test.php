<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller
{
    public function index() {

    }

    public function testdoc() {
        define('FPDF_FONTPATH', FCPATH.'font');
        $this->load->library('fpdf/fpdf');
        // Prepare
        $logoFile = FCPATH."/img/invoice/invoice_logo_bluetrack-stressballs.jpg";
        $logoXPos = 15;
        $logoYPos = 15;
        $logoWidth = 105.655;
        $logoHeight = 12.88;
        $logoType = 'JPG';

        $invnumImg = FCPATH.'/img/invoice/invoice_num.png';
        $invnumXPos = 137;
        $invnumYPos = 15;
        $invnumWidth = 65.42;
        $invnumHeigth = 12.88;
        $invnumType = 'PNG';

        $ponumImage = FCPATH.'/img/invoice/customer_code_bg.png';
        $ponumXPos = 139;
        $ponumYPos = 48;
        $ponumWidth = 64;

        $termsImage = FCPATH.'/img/invoice/terms_head_bg.png';
        $termsXPos = 15;
        $termsYPos = 60;
        $termsWidth = 38;

        $paydueImage = FCPATH.'/img/invoice/paymentdue_head_bg.png';
        $paydueXPos = 58;
        $paydueYPos = 60;
        $paydueWidth = 38;

        $shipdateImage = FCPATH.'/img/invoice/shipdate_head_bg.png';
        $shipdateXPos = 123;
        $shipdateYPos = 60;
        $shipdateWidth = 38;

        $arivdateImage = FCPATH.'/img/invoice/deliverydate_head_bg.png';
        $arivdateXPos = 166;
        $arivdateYPos = 60;
        $arivdateWidth = 38;

        $billadrImage = FCPATH.'/img/invoice/billto_head_bg.png';
        $billadrXPos = 15;
        $billadrYPos = 75;
        $billadrWidth = 82;

        $shipadrImage = FCPATH.'/img/invoice/shipto_head_bg.png';
        $shipadrXPos = 123;
        $shipadrYPos = 75;
        $shipadrWidth = 82;


        $file = $this->config->item('upload_path_preload').'hello.pdf';
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',12);
        $pdf->SetTextColor(65, 65, 65);
        // $pdf->SetMargins(14,14,14);
        // Logo
        $pdf->Image( $logoFile, $logoXPos, $logoYPos, $logoWidth, $logoHeight, $logoType );
        // Inv #
        $pdf->Image($invnumImg, $invnumXPos, $invnumYPos, $invnumWidth, $invnumHeigth, $invnumType);
        $pdf->SetXY(171,16);
        $pdf->SetFont('Arial','B',14);
        $pdf->SetTextColor(0, 0, 255);
        $pdf->Cell(32,12,'MJ-42738',1,0,'C');
        $pdf->Ln(5);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',14);
        $pdf->Text(15, 32, '855 Bloomfield Ave');
        $pdf->Text(15, 40, 'Clifton, NJ 07012');
        $pdf->Text(15,48, 'Call Us at');
        $pdf->SetTextColor(0,0,255);
        $pdf->Text(38,48, '1-800-790-6090');
        $pdf->text(15,56,'www.bluetrack.com','http://www.bluetrack.com');
        $pdf->SetTextColor(0,0,0);
        $pdf->Text(147, 40, 'Invoice Date: 11/18/2020');
        $pdf->Image($ponumImage, $ponumXPos, $ponumYPos, $ponumWidth);
        $pdf->SetXY(178,49);
        $pdf->SetFont('Arial','B');
        $pdf->Cell(24,8,'42738',0,0,'C');
        $pdf->SetFont('Arial','', 12);
        $pdf->SetTextColor(65, 65, 65);
        // Terms
        $pdf->Image($termsImage, $termsXPos, $termsYPos, $termsWidth);
        $pdf->Text(26,73, '12/18/20');
        // Payment Due
        $pdf->Image($paydueImage, $paydueXPos, $paydueYPos, $paydueWidth);
        $pdf->Text(68,73, '12/18/20');
        // Ship Date
        $pdf->Image($shipdateImage, $shipdateXPos, $shipdateYPos, $shipdateWidth);
        $pdf->Text(134,73, '12/18/20');
        // Delivery Date
        $pdf->Image($arivdateImage, $arivdateXPos, $arivdateYPos, $arivdateWidth);
        $pdf->Text(175,73, '12/18/20');
        // Billing Address
        $pdf->SetFont('Arial','', 9.5);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Image($billadrImage, $billadrXPos, $billadrYPos, $billadrWidth);
        $pdf->Text(17, 88, 'Donna Insixiengmay');
        $pdf->Text(17, 93, 'NCTCOG');
        $pdf->Text(17, 98, 'CENTERPOINT TWO');
        $pdf->Text(17, 103, '616 SIX FLAGS DR');
        $pdf->Text(17, 108, 'ARLINGTON TX 76011');
        // Shipping Address
        $pdf->Image($shipadrImage, $shipadrXPos, $shipadrYPos, $shipadrWidth);
        $pdf->Text(125, 88, 'Donna Insixiengmay');
        $pdf->Text(125, 93, 'NCTCOG');
        $pdf->Text(125, 98, 'CENTERPOINT TWO');
        $pdf->Text(125, 103, '616 SIX FLAGS DR');
        $pdf->Text(125, 108, 'ARLINGTON TX 76011');
        // Table
        $tableHeadYPos = 110;
        $itemnumImage = FCPATH.'/img/invoice/itemnum_head_bg.png';
        $itemnumXPos = 15;
        $itemnumWidth = 26;

        $descripImage = FCPATH.'/img/invoice/itemdescript_head_bg.png';
        $descripXPos = 43;
        $descripWidth = 75;

        $itemqtyImage = FCPATH.'/img/invoice/itemqty_head_bg.png';
        $itemqtyXPos = 121;
        $itemqtyWidth = 13;

        $priceImage = FCPATH.'/img/invoice/priceeach_head_bg.png';
        $priceXPos = 136;
        $priceWidth = 27;

        $totalImage = FCPATH.'/img/invoice/subtotal_head_bg.png';
        $totalXPos = 166;
        $totalWidth = 27;

        $pdf->Image($itemnumImage, $itemnumXPos, $tableHeadYPos, $itemnumWidth);
        $pdf->Image($descripImage, $descripXPos, $tableHeadYPos, $descripWidth);
        $pdf->Image($itemqtyImage, $itemqtyXPos, $tableHeadYPos, $itemqtyWidth);
        $pdf->Image($priceImage, $priceXPos, $tableHeadYPos, $priceWidth);
        $pdf->Image($totalImage, $totalXPos, $tableHeadYPos, $totalWidth);
        // Table Data
        $tableWidths = [
            28,
            78,
            13,
            31,
            31,
        ];
        $numpp = 1;

        $pdf->SetFillColor(225, 225, 225);
        $pdf->SetXY(15, 117);
        $pdf->Cell($tableWidths[0], 10, '00-ZZ000', 0, 0,'C', true);
        $pdf->Cell($tableWidths[1], 10, 'Custom Shaped Stress Balls - Custom Logo',0,0,'L',true);
        $pdf->Cell($tableWidths[2], 10,'1000',0, 0, 'C', true);
        $pdf->Cell($tableWidths[3], 10, '2.49',0,0,'C', true);
        $pdf->Cell($tableWidths[4], 10, '$2,490.00',0, 1,'C', true);
        // Next row
        //$pdf->Cell($tableWidths[0], 10,'',0,0,'C', false);
        $pdf->SetX(15);
        $pdf->Cell($tableWidths[0]);
        $pdf->Cell($tableWidths[1], 10, 'Loc 1: 1st Color Imprinting');
        $pdf->Cell($tableWidths[2], 10, '1000',0,0,'C');
        $pdf->Cell($tableWidths[3], 10, '0.00',0,0, 'C');
        $pdf->Cell($tableWidths[4], 10, '$0.00',0, 1,'C');

        $pdf->Cell(10,5,'',0,1);
        // Totals
        $invtotalImage = FCPATH.'/img/invoice/totals_bg.png';
        $invtotalXPos = 115;
        $invtotalYPos = 212;
        $invtotalWidth = 80;

        $pdf->Image($invtotalImage, $invtotalXPos, $invtotalYPos, $invtotalWidth);
        // Totals
        $pdf->SetXY(116,212);
        $pdf->SetFont('Arial','',13);
        $pdf->Cell(75, 8, 'NJ 6.625% Sales Tax (0.0%) $0.00',0,1);
        $pdf->SetX(116);
        $pdf->SetFont('','B');
        $pdf->Cell(52, 8, 'Total');
        $pdf->SetTextColor(8,0,255);
        $pdf->Cell(23, 8, '$2,649.00',0,1);
        $pdf->SetX(116);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('','');
        $pdf->Cell(52, 8, 'Payment - 11/12/20',0,0,'L',true);
        $pdf->Cell(26.2, 8,'$2,649.00',0,1,'L',true);
        $pdf->SetX(116);
        $pdf->SetFont('','B');
        $pdf->Cell(52,8,'Balance Due');
        $pdf->SetTextColor(0,0,255);
        $pdf->Cell(23,8,'$0.00',0,1);

        $pdf->Output('F', $file);
    }

    public function paymentreport_new() {
        $datebgn = strtotime('2019-10-01');
        $dateend = strtotime('2021-01-01');
        // $datewhere = '((batch_due>='.$datebgn.' and batch_due<'.$dateend.') or (batch_due is NULL and batch_date>='.$datebgn.' and batch_date<'.$datebgn.'))';
        $this->db->select('b.*, o.order_num, o.customer_name');
        $this->db->select('case when batch_due is null then batch_date else batch_due END AS datesort', FALSE);
        $this->db->from('ts_order_batches b');
        $this->db->join('ts_orders o','o.order_id=b.order_id');
        // $this->db->where('batch_received',1);
        // $this->db->where($datewhere);
        $this->db->where('b.batch_date >= ', $datebgn);
        $this->db->where('b.batch_date < ', $dateend);
        $this->db->order_by('datesort');
        $res = $this->db->get()->result_array();
        $out = [];
        foreach ($res  as $batch) {
            // $date = date('m/d/Y', $batch['batch_due']);
            // if (empty($batch['batch_due'])) {
            $date=date('m/d/Y', $batch['batch_date']);
            // }
            $batchtype = '';
            if ($batch['batch_type']=='Amex') {
                $batchtype='American Express';
            } elseif ($batch['batch_type']=='American Express') {
                $batchtype='American Express';
            }  elseif ($batch['batch_type']=='Visa') {
                $batchtype = 'Visa';
            } elseif ($batch['batch_type']=='Mastercard') {
                $batchtype = 'Mastercard';
            } elseif ($batch['batch_type']=='Discover') {
                $batchtype = 'Discover';
            } elseif (abs($batch['batch_amex'])>0) {
                $batchtype='American Express';
            } elseif (abs($batch['batch_vmd'])>0) {
                $batchtype='Visa';
            } elseif (abs($batch['batch_term'])>0) {
                $batchtype=(!empty($batch['batch_type']) ? $batch['batch_type'] : 'Check');
            } else {
                $batchtype=(!empty($batch['batch_type']) ? $batch['batch_type'] : 'Terms');
            }
            $out[]=[
                'date'=> $date,
                'order_num'=>$batch['order_num'],
                'customer'=>$batch['customer_name'],
                'payment_type'=>$batchtype,
                'payment_amout'=>$batch['batch_amount'],
            ];
        }
        $file=$this->config->item('upload_path_preload').'payreport2018.csv';
        @unlink($file);
        $fh=fopen($file,'w+');
        $rowdat='Date;Order #;Customer;Payment Type;Payment Amount;'.PHP_EOL;
        fwrite($fh, $rowdat);
        foreach ($out as $row) {
            $rowdat=$row['date'].';'.$row['order_num'].';"'.$row['customer'].'";'.$row['payment_type'].';'.$row['payment_amout'].';'.PHP_EOL;
            fwrite($fh, $rowdat);
        }
        fclose($fh);
        echo 'File '.$file.' ready '.PHP_EOL;

    }

    public function billing_report() {
        $this->db->select('o.order_id, o.order_num, o.customer_name, b.customer_name as billig_name, b.company');
        $this->db->select('b.address_1, b.address_2, b.city, s.state_code, b.zip, tc.country_name, o.revenue, o.order_itemnumber, o.order_items');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_billings b','b.order_id=o.order_id');
        $this->db->join('ts_states s', 'b.state_id = s.state_id','left');
        $this->db->join('ts_countries tc', 'b.country_id = tc.country_id','left');
        $this->db->where('o.is_canceled',0);
        $this->db->order_by('o.order_num');
        $results = $this->db->get()->result_array();
        $file=$this->config->item('upload_path_preload').'billing_report.csv';
        @unlink($file);
        $fh=fopen($file,'w+');
        $rowdat='Order#;Customer;Billing Name;Billing Company;Billing Address 1;Billing Address 2;Billing City;Billing State;Billing Zip;Billing Country;Revenue of order;Item #;Item Description;'.PHP_EOL;
        fwrite($fh, $rowdat);
        foreach ($results as $result) {
            $msg=$result['order_num'].';"'.$result['customer_name'].'";"'.$result['billig_name'].'";"'.$result['company'].'";"'.$result['address_1'];
            $msg.='";"'.$result['address_2'].'";"'.$result['city'].'";"'.$result['state_code'].'";"'.$result['zip'].'";"'.$result['country_name'];
            $msg.='";"'.$result['revenue'].'";"'.$result['order_itemnumber'].'";"'.$result['order_items'].'"'.PHP_EOL;
            fwrite($fh, $msg);
        }
        fclose($fh);
        echo 'File '.$file.' ready '.PHP_EOL;
    }

    public function compare_payments() {
        $this->db->select('o.order_id, count(p.order_paymentlog_id) as cnt');
        $this->db->from('ts_order_paymentlog p');
        $this->db->join('ts_orders o','p.order_id = o.order_id');
        $this->db->where('p.paysucces',1);
        $this->db->where('o.order_date >= ', strtotime('2021-08-01'));
        $this->db->group_by('o.order_id');
        $logs=$this->db->get()->result_array();
        $this->db->select('o.order_id, count(b.batch_id) as cnt');
        $this->db->from('ts_order_batches b');
        $this->db->join('ts_orders o','o.order_id=b.order_id');
        $this->db->where('b.batch_transaction is not null');
        $this->db->where('o.order_date >= ', strtotime('2021-08-01'));
        $this->db->group_by('o.order_id');
        $batches = $this->db->get()->result_array();
        foreach ($logs as $log) {
            $found=0;
            foreach ($batches as $batch) {
                if ($batch['order_id']==$log['order_id']) {
                    if ($batch['cnt']!==$log['cnt']) {
                        echo 'Order ID ' . $log['order_id'] . ' Logs ' . $log['cnt'] . ' Batch ' . $batch['cnt'] . PHP_EOL;
                    }
                    $found=1;
                    break;
                }
            }
            if ($found==0) {
                echo 'Order ID '.$log['order_id'].' Batches not found'.PHP_EOL;
            }
        }
        echo 'Check Baches '.PHP_EOL;
        foreach ($batches as $batch) {
            $found=0;
            foreach ($logs as $log) {
                if ($batch['order_id']==$log['order_id']) {
                    if ($batch['cnt']!==$log['cnt']) {
                        echo 'Order ID ' . $batch['order_id'] . ' Logs ' . $log['cnt'] . ' Batch ' . $batch['cnt'] . PHP_EOL;
                    }
                    $found=1;
                    break;
                }
            }
            if ($found==0) {
                echo 'Order ID '.$batch['order_id'].' Logs not found'.PHP_EOL;
            }
        }

        echo 'Finished'.PHP_EOL;
    }

    public function inventory_year_report() {
        $datebgn=strtotime('2018-01-01');
        $dateend=strtotime('2019-01-01');
        // $dateend = strtotime(date('Y-m-d'));
        // $this->load->model('printshop_model');
        // $extracost=$this->printshop_model->invaddcost();
        $this->db->select('c.printshop_color_id, i.item_num, i.item_name, c.color, c.price');
        $this->db->from('ts_printshop_colors c');
        $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
        $this->db->order_by('i.item_num, c.color');
        $items=$this->db->get()->result_array();
        $data=[];
        $keys=[];
        foreach ($items as $irow) {
            $data[]=[
                'printshop_color_id'=>$irow['printshop_color_id'],
                'item_num'=>$irow['item_num'],
                'item_name'=>$irow['item_name'],
                'color'=>$irow['color'],
                'rest'=>0,
                'income'=>0,
                'outcome'=>0,
                'saved'=>0,
                'price'=>$irow['price'],
            ];
            array_push($keys, $irow['printshop_color_id']);
        }
        // Get a rest
        $this->db->select('printshop_color_id, sum(shipped) as shipped, sum(kepted) as kepted, sum(misprint) as misprint');
        $this->db->from('ts_order_amounts');
        $this->db->where('printshop_color_id is not null');
        $this->db->where('amount_date < ', $datebgn);
        $this->db->group_by('printshop_color_id');
        $restout=$this->db->get()->result_array();
        foreach ($restout as $rrow) {
            $key= array_search($rrow['printshop_color_id'], $keys);
            $data[$key]['rest']-=($rrow['shipped']+$rrow['kepted']+$rrow['misprint']);
        }
        $this->db->select('printshop_color_id, sum(instock_amnt) as instock_amnt');
        $this->db->from('ts_printshop_instock');
        $this->db->where('instock_date < ', $datebgn);
        $this->db->group_by('printshop_color_id');
        $restin=$this->db->get()->result_array();
        foreach ($restin as $rrow) {
            $key= array_search($rrow['printshop_color_id'], $keys);
            $data[$key]['rest']+=$rrow['instock_amnt'];
        }
        // Income
        $this->db->select('printshop_color_id, sum(instock_amnt) as instock_amnt');
        $this->db->from('ts_printshop_instock');
        $this->db->where('instock_date >= ', $datebgn);
        $this->db->where('instock_date < ', $dateend);
        $this->db->group_by('printshop_color_id');
        $income=$this->db->get()->result_array();
        foreach ($income as $rrow) {
            $key= array_search($rrow['printshop_color_id'], $keys);
            $data[$key]['income']+=$rrow['instock_amnt'];
        }
        // Outcome
        $this->db->select('printshop_color_id, price, sum(shipped) as shipped, sum(kepted) as kepted, sum(misprint) as misprint');
        $this->db->from('ts_order_amounts');
        $this->db->where('printshop_color_id is not null');
        $this->db->where('amount_date >= ', $datebgn);
        $this->db->where('amount_date < ', $dateend);
        $this->db->group_by('printshop_color_id, price');
        $outcome=$this->db->get()->result_array();
        foreach ($outcome as $rrow) {
            $key= array_search($rrow['printshop_color_id'], $keys);
            $data[$key]['outcome']+=($rrow['shipped']+$rrow['kepted']+$rrow['misprint']);
            $data[$key]['price']=($rrow['price']);
        }
        $file=$this->config->item('upload_path_preload').'inventoryreport_price_'.date('Y', $datebgn).'.csv';
        @unlink($file);
        $fh=fopen($file,FOPEN_READ_WRITE_CREATE);
        if ($fh) {
            $msg='Item #;Item Name;Color;Qty at '.date('M d, Y', $datebgn).';Qty deducted;Qty added;Qty at '.date('M d, Y', ($dateend-1)).';Price EA;Total Cost;'.PHP_EOL;
            fwrite($fh, $msg);
            foreach ($data as $row) {
                if (abs($row['rest'])+abs($row['income'])+abs($row['outcome'])>0) {
                    $rest=$row['rest']+$row['income']-$row['outcome'];
                    $total=intval($row['rest'])*floatval($row['price']);
                    if ($row['item_num']=='i001' && $row['color']=='Beige') {
                        echo 'Rest '.$rest.' Price '.$row['price'].' Total '.$total.PHP_EOL;
                    }
                    $msg='"'.$row['item_num'].'";"'.$row['item_name'].'";"'.$row['color'].'";'.$row['rest'].';'.$row['outcome'].';'.$row['income'].';'.$rest.';'.$row['price'].';'.$total.';'.PHP_EOL;
                    fwrite($fh, $msg);
                }
            }
            fclose($fh);
        }
        echo $file.' ready '.PHP_EOL;
        //
    }

    public function vendor_items() {
        $this->db->select('vendor_id, vendor_name');
        $this->db->from('vendors');
        $this->db->where_not_in('vendor_id',[1,5,3,4,81, 151, 152, 158]);
        $vendors = $this->db->get()->result_array();
        foreach ($vendors as $vendor) {
            $file_name = str_replace([' ','.',',',"'",'/'],'_',$vendor['vendor_name']).'.csv';
            $vendor_id = $vendor['vendor_id'];
            // $file_name = 'pinnacle_items_correct.csv';
            // Calc max # of prices
            $this->db->select('*');
            $this->db->from('sb_vendor_items');
            $this->db->where('vendor_item_vendor', $vendor_id);
            $items = $this->db->get()->result_array();
            if (count($items > 0)) {
                $maxcnt = 0;
                foreach ($items as $item) {
                    $this->db->select('count(vendorprice_id) as cnt');
                    $this->db->from('sb_vendor_prices');
                    $this->db->where('vendor_item_id', $item['vendor_item_id']);
                    $cntres = $this->db->get()->row_array();
                    if ($cntres['cnt']>$maxcnt) {
                        $maxcnt=$cntres['cnt'];
                    }
                }
                $vendoritems = [];
                foreach ($items as $item) {
                    $vendoritems[] = [
                        'vendor_item_id' => $item['vendor_item_id'],
                        'vendor_item_number' => $item['vendor_item_number'],
                        'vendor_item_name' => $item['vendor_item_name'],
                        'base_cost' => $item['vendor_item_cost'],
                    ];
                    $vendidx = count($vendoritems) - 1;
                    if ($maxcnt > 0) {
                        for ($i=1; $i<=$maxcnt; $i++) {
                            $vendoritems[$vendidx]['qty'.$i]='';
                        }
                        for ($i=1; $i<=$maxcnt; $i++) {
                            $vendoritems[$vendidx]['price'.$i]='';
                        }
                        $this->db->select('vendorprice_qty, vendorprice_val, vendorprice_color');
                        $this->db->from('sb_vendor_prices');
                        $this->db->where('vendor_item_id', $item['vendor_item_id']);
                        $prices = $this->db->get()->result_array();
                        if (count($prices)>0) {
                            $priceidx = 1;
                            foreach ($prices as $price) {
                                $vendoritems[$vendidx]['qty'.$priceidx]=$price['vendorprice_qty'];
                                $priceidx++;
                            }
                            $priceidx = 1;
                            foreach ($prices as $price) {
                                $vendoritems[$vendidx]['price'.$priceidx]=$price['vendorprice_color'];
                                $priceidx++;
                            }
                        }
                    }
                }
                $file = $this->config->item('upload_path_preload').$file_name;
                @unlink($file);
                $fh=fopen($file,FOPEN_READ_WRITE_CREATE);
                if ($fh) {
                    $msg='VItem ID;Vendor Item #;Vendor Item Name; Base Cost;';
                    for ($i=1; $i<=$maxcnt; $i++) {
                        $msg.='Qty '.$i.';';
                    }
                    for ($i=1; $i<=$maxcnt; $i++) {
                        $msg.='Price '.$i.';';
                    }
                    $msg.='Item #; Item Name; Active;';
                    $j=1;
                    foreach ($this->config->item('price_types') as $ptype) {
                        $msg.='QTY'.$j.';';
                        $j++;
                    }
                    $msg.=PHP_EOL;
                    fwrite($fh, $msg);
                    foreach ($vendoritems as $vendoritem) {
                        $msg='';
                        foreach ($vendoritem as $row) {
                            $msg.='"'.$row.'";';
                        }
                        $this->db->select('*');
                        $this->db->from('sb_items');
                        $this->db->where('vendor_item_id', $vendoritem['vendor_item_id']);
                        $itemres = $this->db->get()->row_array();
                        if (ifset($itemres,'item_id',0)>0) {
                            $msg.='"'.$itemres['item_number'].'";"'.$itemres['item_name'].'";"'.($itemres['item_active']==1 ? 'YES': 'NO').'";';
                            if ($itemres['item_template']=='Stressball') {
                                $this->db->select('*');
                                $this->db->from('sb_item_prices');
                                $this->db->where('item_price_itemid', $itemres['item_id']);
                                $prices = $this->db->get()->row_array();
                                if (ifset($prices,'item_price_id',0)>0) {
                                    foreach ($this->config->item('price_types') as $ptype) {
                                        if (!empty($prices['item_price_'.$ptype['type']]) || !empty($prices['item_sale_'.$ptype['type']])) {
                                            $msg.='"'.$ptype['type'].'";';
                                        }
                                    }
                                    $msg.=PHP_EOL;
                                    fwrite($fh, $msg);
                                    // empty row
                                    $msg='';
                                    foreach ($vendoritem as $row) {
                                        $msg.='" ";';
                                    }
                                    for ($i=0; $i<3; $i++) {
                                        $msg.='" ";';
                                    }
                                    foreach ($this->config->item('price_types') as $ptype) {
                                        if (!empty($prices['item_price_'.$ptype['type']]) || !empty($prices['item_sale_'.$ptype['type']])) {
                                            $msg.='"'.$prices['item_price_'.$ptype['type']].'";';
                                        }
                                    }
                                    $msg.=PHP_EOL;
                                    fwrite($fh, $msg);
                                    $msg='';
                                    foreach ($vendoritem as $row) {
                                        $msg.='" ";';
                                    }
                                    for ($i=0; $i<3; $i++) {
                                        $msg.='" ";';
                                    }
                                    foreach ($this->config->item('price_types') as $ptype) {
                                        if (!empty($prices['item_price_'.$ptype['type']]) || !empty($prices['item_sale_'.$ptype['type']])) {
                                            $msg.='"'.$prices['item_sale_'.$ptype['type']].'";';
                                        }
                                    }
                                }
                            }
                        }
                        $msg.=PHP_EOL;
                        fwrite($fh, $msg);
                    }
                    fclose($fh);
                    echo $file.' Ready'.PHP_EOL;
                }
            }
        }

    }

    function set_item_profit() {
        $price_types = $this->config->item('price_types');

        $this->db->select('i.item_id, i.item_template, i.item_number, i.item_name, ip.*');
        $this->db->from('sb_items i');
        $this->db->join('sb_item_prices ip', 'ip.item_price_itemid=i.item_id', 'left');
        $this->db->order_by('i.item_number');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            echo '# '.$row['item_number'].' - '.$row['item_name'].PHP_EOL;
            $profit = array();
            $promoprof = array();
            if ($row['item_template'] != 'Stressball') {
                $this->db->select('*');
                $this->db->from('sb_promo_price');
                $this->db->where('item_id', $row['item_id']);
                $promos = $this->db->get()->result_array();
                foreach ($promos as $promo) {
                    $base = 0;
                    if (floatval($promo['sale_price']) != 0) {
                        $base = floatval($promo['sale_price']);
                    } elseif (floatval($promo['price']) != 0) {
                        $base = floatval($promo['price']);
                    }
                    if ($base) {
                        $this->db->select('get_profit_qty(' . $base . ' , ' . $row['item_id'] . ' , ' . $promo['item_qty'] . ' ) as itm_profit', FALSE);
                        $prof = $this->db->get()->row_array();
                        if ($prof['itm_profit']) {
                            $promoprof[] = array('idx' => $promo['promo_price_id'], 'value' => $prof['itm_profit']);
                        }
                    }
                }
            } else {
                foreach ($price_types as $pricerow) {
                    $base = 0;
                    if (floatval($row['item_sale_' . $pricerow['type']]) != 0) {
                        $base = floatval($row['item_sale_' . $pricerow['type']]);
                    } elseif (floatval($row['item_price_' . $pricerow['type']]) != 0) {
                        $base = floatval($row['item_price_' . $pricerow['type']]);
                    }

                    if ($base) {
                        $this->db->select('get_profit_qty(' . $base . ' , ' . $row['item_id'] . ' , ' . $pricerow['type'] . ' ) as itm_profit', FALSE);
                        $prof = $this->db->get()->row_array();

                        if ($prof['itm_profit']) {
                            $profit[] = array('type' => 'profit_' . $pricerow['type'], 'value' => $prof['itm_profit']);
                        }
                    }
                }
            }
            $base = 0;
            if (floatval($row['item_sale_print']) != 0) {
                $base = floatval($row['item_sale_print']);
            } elseif (floatval($row['item_price_print']) != 0) {
                $base = floatval($row['item_price_print']);
            }
            if ($base) {
                $this->db->select('get_profit_print(' . $base . ',' . $row['item_id'] . ') as itm_profit', FALSE);
                $prof = $this->db->get()->row_array();
                if ($prof['itm_profit']) {
                    $profit[] = array('type' => 'profit_print', 'value' => $prof['itm_profit']);
                }
            }
            $base = 0;
            if (floatval($row['item_sale_setup']) != 0) {
                $base = floatval($row['item_sale_setup']);
            } elseif (floatval($row['item_price_setup']) != 0) {
                $base = floatval($row['item_price_setup']);
            }
            if ($base) {
                $this->db->select('get_profit_setup(' . $base . ',' . $row['item_id'] . ') as itm_profit', FALSE);
                $prof = $this->db->get()->row_array();
                if ($prof['itm_profit']) {
                    $profit[] = array('type' => 'profit_setup', 'value' => $prof['itm_profit']);
                }
            }

            if (count($profit) > 0) {
                foreach ($profit as $prof) {
                    $this->db->set($prof['type'], $prof['value']);
                }
                $this->db->where('item_price_id', $row['item_price_id']);
                $this->db->update('sb_item_prices');
            }
            if (count($promoprof) > 0) {
                foreach ($promoprof as $prof) {
                    $this->db->set('profit', $prof['value']);
                    $this->db->where('promo_price_id', $prof['idx']);
                    $this->db->update('sb_promo_price');
                }
            }
        }
    }

    function updatekeyinfo() {
        $this->db->select('item_id, item_number, item_size');
        $this->db->from('sb_items');
        $this->db->like('item_size','&quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find SIZE '.count($res).PHP_EOL;
        foreach ($res as $row) {
            $new_size=str_replace('&quot;','"', $row['item_size']);
            echo 'Item '.$row['item_number'].' Size '.$new_size.PHP_EOL;
            $this->db->where('item_id', $row['item_id']);
            $this->db->set('item_size', $new_size);
            $this->db->update('sb_items');
        }
        $this->db->select('item_id, item_number, item_name');
        $this->db->from('sb_items');
        $this->db->like('item_name','&amp;amp;quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find NAME '.count($res).PHP_EOL;
        $this->db->select('item_id, item_number, item_description1');
        $this->db->from('sb_items');
        $this->db->like('item_description1','&amp;amp;quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find Descript_1 '.count($res).PHP_EOL;
        foreach ($res as $row) {
            $new_size=str_replace('&amp;amp;quot;','"', $row['item_description1']);
            echo 'Item '.$row['item_number'].' Descrip '.$new_size.PHP_EOL;
            $this->db->where('item_id', $row['item_id']);
            $this->db->set('item_description1', $new_size);
            $this->db->update('sb_items');
        }
        $this->db->select('item_id, item_number, item_description2');
        $this->db->from('sb_items');
        $this->db->like('item_description2','&amp;amp;quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find Descript_2 '.count($res).PHP_EOL;
        $this->db->select('item_id, item_number, item_metadescription');
        $this->db->from('sb_items');
        $this->db->like('item_metadescription','&amp;amp;quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find MetaDescript '.count($res).PHP_EOL;
        $this->db->select('item_id, item_number, item_metakeywords');
        $this->db->from('sb_items');
        $this->db->like('item_metakeywords','&amp;amp;quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find MetaKeywords '.count($res).PHP_EOL;
        $this->db->select('item_id, item_number, item_meta_title');
        $this->db->from('sb_items');
        $this->db->like('item_meta_title','&amp;amp;quot;','both');
        $res = $this->db->get()->result_array();
        echo 'Find MetaTitle '.count($res).PHP_EOL;

    }

    public function conversation_vendors() {
        $this->db->select('*');
        $this->db->from('convesation_vendors');
        $rows = $this->db->get()->result_array();
        foreach ($rows as $row) {
            // search in lift_vendors
            $this->db->select('count(vendor_id) as cnt, max(vendor_id) max_id');
            $this->db->from('lift_vendors');
            $this->db->where('vendor_name',$row['name_old']);
            $chkold = $this->db->get()->row_array();
            if ($chkold['cnt']==1) {
                $this->db->set('old_vendor_id',$chkold['max_id']);
                $this->db->where('id', $row['id']);
                $this->db->update('convesation_vendors');
                $this->db->where('vendor_id', $chkold['max_id']);
                $this->db->set('convert_id', $row['id']);
                $this->db->update('lift_vendors');
            } elseif ($chkold['cnt']>1) {
                echo 'OLD Multi '.$row['name_old'].PHP_EOL;
            }
            // Search in vendors
            $this->db->select('count(vendor_id) as cnt, max(vendor_id) max_id');
            $this->db->from('vendors');
            $this->db->where('vendor_name', $row['name_new']);
            $chknew = $this->db->get()->row_array();
            if ($chknew['cnt']==1) {
                $this->db->set('new_vendor_id',$chkold['max_id']);
                $this->db->where('id', $row['id']);
                $this->db->update('convesation_vendors');
            } elseif ($chknew['cnt']>1) {
                echo 'NEW Multi '.$row['name_new'].PHP_EOL;
            } else {
                $this->db->select('count(vendor_id) as cnt, max(vendor_id) max_id');
                $this->db->from('vendors');
                $this->db->like('vendor_name', $row['name_old'],'after');
                $chknew = $this->db->get()->row_array();
                if ($chknew['cnt']==1) {
                    $this->db->set('new_vendor_id',$chkold['max_id']);
                    $this->db->where('id', $row['id']);
                    $this->db->update('convesation_vendors');
                }
            }
        }
    }

    public function update_vendors() {
        $this->db->select('*');
        $this->db->from('convesation_vendors');
        $rows = $this->db->get()->result_array();
        foreach ($rows as $row) {
            echo 'Vendor # '.$row['vendor_number'].PHP_EOL;
            $this->db->select('v.*, c.country_id');
            $this->db->from('new_vendors v');
            $this->db->join('sb_countries c','c.country_name=v.country');
            $this->db->where('vendor_num', $row['vendor_number']);
            $details = $this->db->get()->row_array();
            if (ifset($details,'id',0)!==0) {
                $vcode = intval(str_replace('v-', '', $details['vendor_num']));
                $paytype = $details['pay_type'];
                $this->db->set('vendor_code', $vcode);
                $this->db->set('vendor_slug', 'V-'.$vcode);
                $this->db->set('vendor_zipcode', $row['zip_old']);
                $this->db->set('calendar_id', $this->config->item('bank_calendar'));
                $this->db->set('vendor_name', $details['name']);
                $this->db->set('alt_name', $details['alt_name']);
                $this->db->set('vendor_type', $details['vend_type']);
                $this->db->set('country_id', $details['country_id']);
                $this->db->set('vendor_asinumber', $details['asi_num']);
                $this->db->set('our_account_number', $details['our_acct']);
                $this->db->set('vendor_website', $details['website']);
                $this->db->set('vendor_phone',$details['main_phone']);
                $this->db->set('address_line1',$details['address_l1']);
                $this->db->set('address_line2', $details['address_l2']);
                $this->db->set('address_city', $details['city']);
                $this->db->set('address_state', $details['state']);
                $this->db->set('address_zip', $details['zip']);
                $this->db->set('address_country', $details['Country_1']);
                $this->db->set('general_note',$details['notes_Internal']);
                $this->db->set('po_contact', $details['po_contact']);
                $this->db->set('po_phone', $details['po_phone']);
                $this->db->set('po_email', $details['po_email']);
                $this->db->set('po_ccemail', $details['po_email_2']);
                $this->db->set('po_bcemail', $details['po_email_3']);
                $this->db->set('shipaddr_line1', $details['ship_from_address']);
                $this->db->set('shipaddr_line2', $details['ship_from_address_2']);
                $this->db->set('shipaddr_city', $details['ship_from_city']);
                $this->db->set('shipaddr_state', $details['ship_from_state']);
                if (!empty($details['ship_from_zip'])) {
                    $this->db->set('vendor_zipcode', $details['ship_from_zip']);
                }
                $this->db->set('shipaddr_country', $details['ship_from_country']);
                $this->db->set('po_note', $details['po_notes']);
                $this->db->set('payment_contact', $details['payment_contact']);
                $this->db->set('payment_phone', $details['payment_phone']);
                $this->db->set('payment_email', $details['payment_email']);
                // Pay Type
                if (!empty($paytype)) {
                    if ($paytype=='Prepay') {
                        $this->db->set('payment_prepay',1);
                    }
                    if ($paytype=='Terms') {
                        $this->db->set('payment_terms',1);
                    }
                }
                if (!empty($details['accepted_methods'])) {
                    if ($details['accepted_methods']=='Wire') {
                        $this->db->set('payment_accept_wire',1);
                    } elseif ($details['accepted_methods']=='Visa/MC, Check, ACH, Wire') {
                        $this->db->set('payment_accept_wire',1);
                        $this->db->set('payment_accept_visa',1);
                        $this->db->set('payment_accept_check',1);
                        $this->db->set('payment_accept_ach',1);
                    } elseif ($details['accepted_methods']=='Visa, MC, Amex, Check, ACH, Wire') {
                        $this->db->set('payment_accept_wire',1);
                        $this->db->set('payment_accept_visa',1);
                        $this->db->set('payment_accept_amex',1);
                        $this->db->set('payment_accept_check',1);
                        $this->db->set('payment_accept_ach',1);
                    }
                }
                // accepted_methods
                // $this->db->set('', $details['ach_info']);
                $this->db->set('payment_note', $details['payment_notes_internal']);
                $this->db->set('pricing_contact', $details['pricing_contact']);
                $this->db->set('pricing_phone', $details['pricing_phone']);
                $this->db->set('pricing_email', $details['pricing_email']);
                $this->db->set('customer_contact', $details['customer_service_contact']);
                $this->db->set('customer_phone', $details['customer_service_phone']);
                $this->db->set('customer_email', $details['customer_service_email']);
                if ($row['new_vendor_id']==0) {
                    $this->db->insert('vendors');
                } else {
                    $this->db->where('vendor_id', $row['new_vendor_id']);
                    $this->db->update('vendors');
                }
            } else {
                if ($row['new_vendor_id']>0) {
                    $vcode = intval(str_replace('v-', '', $row['vendor_number']));
                    $this->db->set('vendor_code', $vcode);
                    $this->db->set('vendor_slug', 'V-'.$vcode);
                    $this->db->where('vendor_id', $row['new_vendor_id']);
                    $this->db->update('vendors');
                }
            }
        }
    }

    public function merge_venditems() {
        $this->db->select('vendor_id');
        $this->db->from('vendors');
        $this->db->where('vendor_code',null);
        $rows = $this->db->get()->result_array();
        foreach ($rows as $row) {
            $this->db->select('new_vendor_id');
            $this->db->from('convesation_vendors');
            $this->db->where('old_vendor_id', $row['vendor_id']);
            $mergevend=$this->db->get()->row_array();
            if (ifset($mergevend,'new_vendor_id', 0)>0) {
                $this->db->where('vendor_item_vendor', $row['vendor_id']);
                $this->db->set('vendor_item_vendor', $mergevend['new_vendor_id']);
                $this->db->update('sb_vendor_items');
            }
            $this->db->where('vendor_id', $row['vendor_id']);
            $this->db->delete('vendors');
        }
    }

    public function blog_articles() {
        $this->load->helper('url');
        for ($i=1; $i<27; $i++) {
            $catid = rand(1, 5);
            $this->db->select('*');
            $this->db->from('sb_blog_categories');
            $this->db->where('blog_category_id', $catid);
            $catdat = $this->db->get()->row_array();
            $title = $catdat['category_name'].' -  Article '.$i;
            $slug = url_title($title, 'dash', TRUE);
            $this->db->set('brand','SB');
            $this->db->set('user_created',1);
            $this->db->set('date_created', date('Y-m-d H:i:s'));
            $this->db->set('user_updated',1);
            $this->db->set('user_published',1);
            $this->db->set('date_published', time());
            $this->db->set('article_title', $title);
            $this->db->set('article_slug', $slug);
            $this->db->set('article_annotation','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sed metus dictum lacus dictum interdum. Nunc et justo cursus justo condimentum pharetra ac et dolor. Integer sodales, lorem sed accumsan porttitor, nisl magna commodo dolor, malesuada convallis');
            $this->db->set('status',1);
            $this->db->insert('sb_blog_articles');
            $artid = $this->db->insert_id();
            $this->db->set('blog_article_id', $artid);
            $this->db->set('blog_category_id', $catid);
            $this->db->insert('sb_blog_articlecategory');
        }
    }

    public function markviewed() {
        $this->db->select('o.order_id, o.order_num');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_statuses a','a.order_id=o.order_id');
        $this->db->where('a.order_approved_view',0);
        $this->db->where('a.order_proj_status','01_notplaced');
        $results = $this->db->get()->result_array();
        foreach ($results as $result) {
            $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
            $this->db->from('ts_orders');
            $this->db->where('order_id', $result['order_id']);
            $ordres=$this->db->get()->row_array();
            $this->db->set('order_artview', $ordres['aprrovview']);
            $this->db->set('order_placed', $ordres['placeord']);
            $this->db->where('order_id', $result['order_id']);
            $this->db->update('ts_orders');
            echo 'Order # '.$result['order_num'].PHP_EOL;
        }
    }

    public function test_attempts() {
        $this->load->model('orders_model');
        /* Calculate time begin - end of previous day */
        // $start=strtotime(date("Y-m-d", time()) . " - 1 days");
        // $end=strtotime(date('m/d/Y',$start).'23:59:59');
        $start = strtotime('2022-08-08');
        $end = strtotime('2022-08-08 23:59:59');
        $filtr=array(
            'starttime'=>$start,
            'endtime'=>$end,
        );
        $this->orders_model->attempts_report($filtr);

    }

    public function customattach() {
        $this->db->select('q.custom_quote_id, l.lead_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_lead_emails le','q.custom_quote_id = le.custom_quote_id');
        $this->db->join('ts_leads l','le.lead_id = l.lead_id');
        $dats = $this->db->get()->result_array();
        foreach ($dats as $dat) {
            echo 'Custom ID '.$dat['custom_quote_id'].PHP_EOL;
            $this->db->select('*');
            $this->db->from('ts_customquote_attachment');
            $this->db->where('custom_quote_id', $dat['custom_quote_id']);
            $customattachs = $this->db->get()->result_array();
            // Get attach to lead
            $this->db->select('*');
            $this->db->from('ts_lead_attachs');
            $this->db->where('lead_id', $dat['lead_id']);
            $leadattachs = $this->db->get()->result_array();
            if (count($customattachs)>0 && count($leadattachs)==0) {
                foreach ($customattachs as $customattach) {
                    $this->db->set('lead_id', $dat['lead_id']);
                    $this->db->set('source_name', $customattach['source_name']);
                    $this->db->set('attachment', $customattach['attachment']);
                    $this->db->set('quoteattach', 1);
                    $this->db->insert('ts_lead_attachs');
                }
            }
        }
    }

    public function updatesbmenuitems() {
        $this->db->select('menu_item_id');
        $this->db->from('menu_items');
        $this->db->where('parent_id', NULL);
        $this->db->where('brand','SB');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            $this->db->select('menu_item_id');
            $this->db->from('menu_items');
            $this->db->where('parent_id', $item['menu_item_id']);
            $dats = $this->db->get()->result_array();
            foreach ($dats as $dat) {
                $this->db->set('brand', 'SB');
                $this->db->where('menu_item_id', $dat['menu_item_id']);
                $this->db->update('menu_items');
            }
        }
    }

    public function addsrmenu() {
        // Clear old attempts
        $this->db->where('brand','SR');
        $this->db->delete('menu_items');

        $this->db->select('*');
        $this->db->from('menu_items');
        $this->db->where('parent_id', NULL);
        $this->db->where('brand','SB');
        $rootitems = $this->db->get()->result_array();
        foreach ($rootitems as $rootitem) {
            // Insert root item
            $this->db->set('item_name', $rootitem['item_name']);
            $this->db->set('item_link', $rootitem['item_link']);
            $this->db->set('menu_order', $rootitem['menu_order']);
            $this->db->set('menu_section', $rootitem['menu_section']);
            $this->db->set('brand_access', $rootitem['brand_access']);
            $this->db->set('newver', $rootitem['newver']);
            $this->db->set('brand', 'SR');
            $this->db->insert('menu_items');
            $lastid=$this->db->insert_id();
            // Permissions for element
            $this->db->select('*');
            $this->db->from('user_permissions');
            $this->db->where('menu_item_id', $rootitem['menu_item_id']);
            $usrperms = $this->db->get()->result_array();
            foreach ($usrperms as $usrperm) {
                $this->db->set('user_id', $usrperm['user_id']);
                $this->db->set('menu_item_id', $lastid);
                $this->db->set('permission_type', $usrperm['permission_type']);
                $this->db->insert('user_permissions');
            }
            // Get subelements
            $this->db->select('*');
            $this->db->from('menu_items');
            $this->db->where('parent_id', $rootitem['menu_item_id']);
            $this->db->where('brand','SB');
            $subitems = $this->db->get()->result_array();
            foreach ($subitems as $subitem) {
                $this->db->set('parent_id', $lastid);
                $this->db->set('item_name', $subitem['item_name']);
                $this->db->set('item_link', $subitem['item_link']);
                $this->db->set('menu_order', $subitem['menu_order']);
                $this->db->set('menu_section', $subitem['menu_section']);
                $this->db->set('brand_access', $subitem['brand_access']);
                $this->db->set('newver', $subitem['newver']);
                $this->db->set('brand', 'SR');
                $this->db->insert('menu_items');
                $lastsubid=$this->db->insert_id();
                // Permissions
                $this->db->select('*');
                $this->db->from('user_permissions');
                $this->db->where('menu_item_id', $rootitem['menu_item_id']);
                $usrperms = $this->db->get()->result_array();
                foreach ($usrperms as $usrperm) {
                    $this->db->set('user_id', $usrperm['user_id']);
                    $this->db->set('menu_item_id', $lastsubid);
                    $this->db->set('permission_type', $usrperm['permission_type']);
                    $this->db->insert('user_permissions');
                }
            }
        }
    }

    public function update_netprofitdetails() {
        $this->db->set('category_type','Ads');
        $this->db->set('category_name','Google Ads');
        $this->db->insert('ts_netprofit_categories');
        $adscat = $this->db->insert_id();
        $this->db->select('d.profit_id, d.brand, d.netprofit_data_id, d.profit_advertising, d.profit_projects, n.datebgn, n.dateend');
        $this->db->from('netprofit_dat d');
        $this->db->join('netprofit n', 'n.profit_id=d.profit_id');
        $this->db->where('n.profit_month', NULL);
        $datas = $this->db->get()->result_array();
        foreach ($datas as $data) {
            echo 'Week '.date('d.m.Y', $data['datebgn']).' - '.date('d.m.Y', $data['dateend']).PHP_EOL;
            if (!empty($data['profit_advertising'])) {
                $this->db->select('count(*) as cnt, sum(amount) as total');
                $this->db->from('ts_netprofit_details');
                $this->db->where('profit_id', $data['profit_id']);
                $this->db->where('brand', $data['brand']);
                $this->db->where('details_type','Ads');
                $chkdat = $this->db->get()->row_array();
                if ($chkdat['cnt']==0) {
                    $amntval = $data['profit_advertising'];
                } else {
                    $amntval = $chkdat['total'] - $data['profit_advertising'];
                }
                $this->db->set('profit_id', $data['profit_id']);
                $this->db->set('details_type', 'Ads');
                $this->db->set('netprofit_category_id', $adscat);
                $this->db->set('amount', $amntval);
                $this->db->set('brand', $data['brand']);
                $this->db->insert('ts_netprofit_details');
            }
            if (!empty($data['profit_projects'])) {
                $this->db->select('count(*) as cnt, sum(amount) as total');
                $this->db->from('ts_netprofit_details');
                $this->db->where('profit_id', $data['profit_id']);
                $this->db->where('brand', $data['brand']);
                $this->db->where('details_type','Upwork');
                $chkdat = $this->db->get()->row_array();
                if ($chkdat['cnt']==0) {
                    $amntval = $data['profit_projects'];
                } else {
                    $amntval = $chkdat['total'] - $data['profit_projects'];
                }
                $this->db->set('profit_id', $data['profit_id']);
                $this->db->set('details_type', 'Upwork');
                $this->db->set('amount', $amntval);
                $this->db->set('brand', $data['brand']);
                $this->db->insert('ts_netprofit_details');
            }
        }
    }

    public function update_netprofitruns() {
        $this->db->select('d.profit_id, sum(d.debtinclude) as debtinclude, sum(d.runinclude) as runinclude, max(n.datebgn) as datebgn, max(n.dateend) as dateend');
        $this->db->from('netprofit_dat d');
        $this->db->join('netprofit n', 'n.profit_id=d.profit_id');
        $this->db->where('n.profit_month', NULL);
        $this->db->group_by('d.profit_id');
        $datas = $this->db->get()->result_array();
        foreach ($datas as $data) {
            echo 'Week '.date('d.m.Y', $data['datebgn']).' - '.date('d.m.Y', $data['dateend']).PHP_EOL;
            if (intval($data['runinclude']) > 0) {
                $this->db->where('profit_id', $data['profit_id']);
                $this->db->set('debtinclude',1);
                $this->db->set('runinclude',1);
                $this->db->update('netprofit_dat');
            }
        }
    }

    public function fix_leadnumbers() {
        $this->load->model('leads_model');
        $this->db->select('lead_number, count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->group_by('lead_number');
        $this->db->having('cnt > ',1);
        $results = $this->db->get()->result_array();
        foreach ($results as $result) {
            echo 'Lead # '.$result['lead_number'].' Count '.$result['cnt'].PHP_EOL;
            $this->db->select('lead_id, update_date, brand');
            $this->db->from('ts_leads');
            $this->db->where('lead_number', $result['lead_number']);
            $this->db->order_by('lead_id');
            $leads = $this->db->get()->result_array();
            $numpp=0;
            foreach ($leads as $lead) {
                $numpp++;
                if ($numpp > 1) {
                    $newleadnum = $this->leads_model->get_leadnum($lead['brand']);
                    $this->db->where('lead_id', $lead['lead_id']);
                    $this->db->set('lead_number', $newleadnum);
                    $this->db->set('update_date', $lead['update_date']);
                    $this->db->update('ts_leads');
                    echo 'ID '.$lead['lead_id'].' New Number '.$newleadnum.' Update '.$lead['update_date'].PHP_EOL;
                }
            }
            // die();
        }
    }

    public function fix_netprofit() {
        $this->db->select('order_date');
        $this->db->from('ts_orders');
        $this->db->where('brand','SR');
        $this->db->order_by('order_id');
        $order = $this->db->get()->row_array();
        $start_date = $order['order_date'];
        echo date('d.m.Y',$start_date).PHP_EOL;
        $this->db->select('profit_id, profit_week, profit_year');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('datebgn < ', $start_date);
        $this->db->where('dateend >= ', $start_date);
        $profdat = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('profit_week >= ', $profdat['profit_week']);
        $this->db->where('profit_year', $profdat['profit_year']);
        $neprofs = $this->db->get()->result_array();
        foreach ($neprofs as $neprof) {
            $this->db->set('profit_id', $neprof['profit_id']);
            $this->db->set('brand','SR');
            $this->db->insert('netprofit_dat');
            echo 'Week '.$neprof['profit_week'].'-'.$neprof['profit_year'].PHP_EOL;
        }

    }

    public function payments_rep() {
        $this->db->select('*');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_date', strtotime('2019-01-01'));
        $batchs = $this->db->get()->result_array();
        $out = [];
        foreach ($batchs as $batch) {
            $cc_paym = 0; $other_paym = 0;
            if ($batch['batch_amex'] != 0 || $batch['batch_vmd']) {
                $cc_paym = $batch['batch_amount'];
            } else {
                $other_paym = $batch['batch_amount'];
            }
            $out[] = [
                'date' => date('m/d/Y', $batch['batch_date']),
                'amount' => $batch['batch_amount'],
                'cc_payment' => $cc_paym,
                'other_paym' => $other_paym,
                'payment_type' => ($batch['batch_amount'] < 0 ? 'Refund' : 'Payment'),
                'cc_type' => ($cc_paym != 0 ? $batch['batch_type'] : ''),
            ];
        }
        $this->load->config('uploader');
        $file_name = $this->config->item('upload_path_preload').'payment_report.csv';
        @unlink($file_name);
        $fh = fopen($file_name, FOPEN_WRITE_CREATE);
        if ($fh) {
            $msg = 'Date;Total Payment;By Credit Card;Other Payment;CC System;Payment Type;';
            fwrite($fh, $msg);
            foreach ($out as $row) {
                $msg = $row['date'].';'.$row['amount'].';'.$row['cc_payment'].';'.$row['other_paym'].';'.$row['cc_type'].';'.$row['payment_type'].';'.PHP_EOL;
                fwrite($fh, $msg);
            }
            fclose($fh);
        } else {
            echo 'Create file Error'.PHP_EOL;
        }
    }
}