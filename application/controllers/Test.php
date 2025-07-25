<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function addinventory() {
        $this->db->select('*');
        $this->db->from('ts_printshop_items');
        // $this->db->limit(15, 28); // id - 4
        // $this->db->limit(3, 25); // id - 3
        // $this->db->limit(5, 20); // id - 2
        // $this->db->limit(20); // id - 1
        $this->db->order_by('item_num');
        $items = $this->db->get()->result_array();
        $itemnum=1;
        $this->db->select('*');
        $this->db->from('ts_inventory_types');
        $this->db->where('inventory_type_id',1);
        $typeres = $this->db->get()->row_array();
        // $type_sh = 'SOT'; // id=4
        // $type_sh = 'SHS'; // id=3
        // $type_sh = 'SMA'; // id=2
        // $type_sh = 'SSB'; // id=1
        // $type_id = 4;
        // $type_id = 3;
        // $type_id = 2;
        // $type_id = 1;
        $type_id = $typeres['inventory_type_id'];
        $type_sh = $typeres['type_short'];
        foreach ($items as $item) {
            echo 'Item '.$item['item_name'].' insert '.PHP_EOL;
            $unit='pc';
            // if (in_array($item['printshop_item_id'], $lbsitem)) {
            //     $unit='lbs';
            // } elseif (in_array($item['printshop_item_id'],$yrditem)) {
            //    $unit='yd';
            // }
            $this->db->set('inventory_type_id', $type_id);
            $this->db->set('item_num',$type_sh.str_pad($itemnum,3,'0',STR_PAD_LEFT));
            $this->db->set('item_name',$item['item_name']);
            $this->db->set('item_order', $itemnum);
            $this->db->set('item_unit', $unit);
            $this->db->set('proof_template', $item['proof_temp']);
            $this->db->set('proof_template_source',$item['proof_temp_source']);
            $this->db->set('plate_template', $item['plate_temp']);
            $this->db->set('plate_template_source', $item['plate_temp_source']);
            $this->db->set('box_template', $item['item_label']);
            $this->db->set('box_template_source', $item['item_label_source']);
            $this->db->set('inserted_at', date('Y-m-d H:i:s'));
            $this->db->insert('ts_inventory_items');
            $newitemid = $this->db->insert_id();
            // Add colors
            $this->db->select('*');
            $this->db->from('ts_printshop_colors');
            $this->db->where('printshop_item_id', $item['printshop_item_id']);
            $colors = $this->db->get()->result_array();
            foreach ($colors as $color) {
                // $diff = random_int(-15,15);
                $newprice = $color['price']; // round($color['price']*(100+$diff)/100,3);
                $this->db->set('inventory_item_id', $newitemid);
                $this->db->set('color', $color['color']);
                $this->db->set('color_order', $color['color_order']);
                $this->db->set('price', $newprice); // $color['price']
                $this->db->set('avg_price', $newprice);
                $this->db->set('color_unit', $unit);
                $this->db->set('suggeststock', $color['suggeststock']);
                $this->db->set('reserved', $color['reserved']);
                $this->db->set('onroutestock', $color['onroutestock']);
                $this->db->set('notreorder', $color['notreorder']);
                $this->db->set('pantones', empty($color['specfile']) ? $color['color_descript'] : $color['specfile']);
                $this->db->set('color_image','');
                $this->db->set('color_image_source','');
                $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                $this->db->insert('ts_inventory_colors');
                $newcolorid = $this->db->insert_id();
                // Insert 5 empty rows
                for ($i=0; $i<5; $i++) {
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->insert('ts_invcolor_vendors');
                }
                //  Get Income
                $this->db->select('*');
                $this->db->from('ts_printshop_instock');
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $this->db->where('instock_amnt > 0');
                $incomes = $this->db->get()->result_array();
                foreach ($incomes as $income) {
                    // $diff = 0; // random_int(0,15);
                    // $calcprice = round($newprice*(100+$diff)/100,3);
                    if (substr($income['instock_descrip'],0,9)=='Container') {
                        $recnum = 'CON-'.substr($income['instock_descrip'],10);
                        $descr = 'Purchased - '.$income['instock_descrip'];
                    } else {
                        $this->db->set('adjust_date', date('Y-m-d H:i:s', $income['instock_date']));
                        $this->db->set('adjust_type', 'S');
                        $this->db->insert('ts_inventory_adjusts');
                        $newrec = $this->db->insert_id();
                        $recnum = 'AJ'.str_pad($newrec,5,'0',STR_PAD_LEFT); // strtoupper(uniq_link(2,'chars')).uniq_link(4,'digits');
                        // AJ00001
                        $descr = $income['instock_descrip'];
                    }
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->set('income_date', $income['instock_date']);
                    $this->db->set('income_qty', $income['instock_amnt']);
                    // $this->db->set('income_price', $calcprice); // $color['price']
                    $this->db->set('income_price', $color['price']);
                    $this->db->set('income_description', $descr);
                    $this->db->set('income_record', $recnum);
                    $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                    $this->db->insert('ts_inventory_incomes');
                }
                // Negative Income
                $this->db->select('*');
                $this->db->from('ts_printshop_instock');
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $this->db->where('instock_amnt < 0');
                $corects = $this->db->get()->result_array();
                foreach ($corects as $corect) {
                    $qtyout = abs($corect['instock_amnt']);
                    $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
                    $this->db->from('ts_inventory_incomes');
                    $this->db->where('inventory_color_id', $newcolorid);
                    $this->db->having('leftqty > 0');
                    $this->db->order_by('income_date');
                    $candidats = $this->db->get()->result_array();
                    foreach ($candidats as $candidat) {
                        // $qtyout-$candidat['leftqty'];
                        if ($qtyout > $candidat['leftqty']) {
                            $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                        } else {
                            $newexp = $candidat['income_expense'] + $qtyout;
                        }
                        $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                        $this->db->set('income_expense', $newexp);
                        $this->db->update('ts_inventory_incomes');
                        $qtyout= $qtyout - $candidat['leftqty'];
                        if ($qtyout <= 0 ) {
                            break;
                        }
                    }
                    $outcome_type = 'X';
//                    $this->db->select('count(inventory_outcome_id) as cnt, max(outcome_number) as outnumb');
//                    $this->db->from('ts_inventory_outcomes');
//                    $this->db->where('outcome_type', $outcome_type);
//                    $outdat = $this->db->get()->row_array();
//                    if ($outdat['cnt']==1) {
//                        $recnum = 0;
//                    } else {
//                        $recnum = $outdat['outnumb'];
//                    }
//                    $newrecnum = $recnum + 1;
//                    $recnummask = str_pad($newrecnum, 5,'0', STR_PAD_LEFT);
                    $this->db->set('adjust_date', date('Y-m-d H:i:s', $income['instock_date']));
                    $this->db->set('adjust_type', 'S');
                    $this->db->insert('ts_inventory_adjusts');
                    $newrec = $this->db->insert_id();
                    $recnum = 'AJ'.str_pad($newrec,5,'0',STR_PAD_LEFT); // strtoupper(uniq_link(2,'chars')).uniq_link(4,'digits');
                    // $recnum = strtoupper(uniq_link(2,'chars')).uniq_link(4,'digits');
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->set('outcome_date', $corect['instock_date']);
                    $this->db->set('outcome_qty', abs($corect['instock_amnt']));
                    $this->db->set('outcome_description', $corect['instock_descrip']);
                    $this->db->set('outcome_record', $recnum);
                    $this->db->set('outcome_type', $outcome_type);
                    // $this->db->set('outcome_number', $newrecnum);
                    $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                    $this->db->insert('ts_inventory_outcomes');
                }
                // Get outcome
                $this->db->select('oa.amount_id, oa.shipped, oa.kepted, oa.misprint, o.order_num, oa.amount_date, o.order_id, o.order_num, o.brand');
                $this->db->from('ts_order_amounts oa');
                $this->db->join('ts_orders o','o.order_id=oa.order_id');
                $this->db->where('printshop',1);
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $outcomes = $this->db->get()->result_array();
                foreach ($outcomes as $outcome) {
                    $qtyout = intval($outcome['shipped'])+intval($outcome['misprint'])+intval($outcome['kepted']);
                    $outcome_type = 'P';
//                    $this->db->select('count(inventory_outcome_id) as cnt, max(outcome_number) as outnumb');
//                    $this->db->from('ts_inventory_outcomes');
//                    $this->db->where('outcome_type', $outcome_type);
//                    $outdat = $this->db->get()->row_array();
//                    if ($outdat['cnt']==1) {
//                        $recnum = -1;
//                    } else {
//                        $recnum = $outdat['outnumb'];
//                    }
//                    $newrecnum = $recnum + 1;
//                    $recnummask = str_pad($newrecnum, 5,'0', STR_PAD_LEFT);
//                    $recnum = $outcome_type.substr($recnummask,0,1).'-'.substr($recnummask,1);
                    // $recnum = 'A0-'.$outcome['order_num'];
                    $recnum = $outcome['brand']=='SR' ? 'SR' : 'BT'.$outcome['order_num'];
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->set('outcome_date', $outcome['amount_date']);
                    $this->db->set('outcome_qty', $qtyout);
                    $this->db->set('outcome_description','Order # '.$outcome['order_num']);
                    $this->db->set('outcome_record', $recnum);
                    $this->db->set('order_id', $outcome['order_id']);
                    // $this->db->set('outcome_number', $newrecnum);
                    $this->db->set('outcome_type', $outcome_type);
                    $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                    $this->db->insert('ts_inventory_outcomes');
                    // Update balance
                    $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
                    $this->db->from('ts_inventory_incomes');
                    $this->db->where('inventory_color_id', $newcolorid);
                    $this->db->having('leftqty > 0');
                    $this->db->order_by('income_date');
                    $candidats = $this->db->get()->result_array();
                    foreach ($candidats as $candidat) {
                        if ($qtyout > $candidat['leftqty']) {
                            $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                            $ordinv = $candidat['leftqty'];
                        } else {
                            $newexp = $candidat['income_expense'] + $qtyout;
                            $ordinv = $qtyout;
                        }
                        // echo 'QTY '.$qtyout.' New Expens '.$newexp.' Get INV '.$ordinv.PHP_EOL;
                        $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                        $this->db->set('income_expense', $newexp);
                        $this->db->update('ts_inventory_incomes');
                        // Insert to order inventory
                        $this->db->set('order_id', $outcome['order_id']);
                        $this->db->set('inventory_income_id', $candidat['inventory_income_id']);
                        $this->db->set('amount_id', $outcome['amount_id']);
                        $this->db->set('qty',$ordinv);
                        $this->db->insert('ts_order_inventory');
                        $qtyout= $qtyout - $candidat['leftqty'];
                        if ($qtyout <= 0 ) {
                            break;
                        }
                    }
                    // Update amouts
                    $this->db->where('amount_id', $outcome['amount_id']);
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->update('ts_order_amounts');
                }
                // Onboats
                $this->db->select('*');
                $this->db->from('ts_printshop_onboats');
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $onboats = $this->db->get()->result_array();
                foreach ($onboats as $onboat) {
                    $this->db->set('inventory_color_id',$newcolorid);
                    $this->db->set('onroutestock', $onboat['onroutestock']);
                    $this->db->set('onboat_date', $onboat['onboat_date']);
                    $this->db->set('onboat_status', $onboat['onboat_status']);
                    $this->db->set('onboat_container', $onboat['onboat_container']);
                    $this->db->set('vendor_price', $color['price']);
                    $this->db->set('onboat_type','C');
                    $this->db->set('brand', $onboat['brand']);
                    $this->db->insert('ts_inventory_onboats');
                }
                echo 'Color '.$color['color'].' added successfully '.PHP_EOL;
            }
            $itemnum++;
        }
        $this->updcolor_price();
    }

    public function updcolor_price() {
        $this->db->select('*');
        $this->db->from('ts_inventory_colors');
        $colors = $this->db->get()->result_array();
        foreach ($colors as $color) {
            $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense, income_price');
            $this->db->from('ts_inventory_incomes');
            $this->db->where('inventory_color_id', $color['inventory_color_id']);
            $this->db->having('leftqty > 0');
            $datas = $this->db->get()->result_array();
            $restqty = $resttotal = 0;
            foreach ($datas as $data) {
                $restqty+=$data['leftqty'];
                $resttotal+=$data['leftqty']*$data['income_price'];
            }
            if ($restqty > 0) {
                $newprice = round($resttotal / $restqty,3);
                $this->db->where('inventory_color_id', $color['inventory_color_id']);
                $this->db->set('avg_price', $newprice);
                $this->db->update('ts_inventory_colors');
            }
        }
    }

    public function update_invincome() {
        $this->db->select('*');
        $this->db->from('ts_inventory_outcomes');
        $this->db->where('substr(outcome_record,1,3)','A0-');
        $lists = $this->db->get()->result_array();
        foreach ($lists as $list) {
            $ordnum=substr($list['outcome_record'],3);
            echo 'Order # '.$ordnum.PHP_EOL;
            $this->db->select('order_id');
            $this->db->from('ts_orders');
            $this->db->where('order_num', $ordnum);
            $res = $this->db->get()->row_array();
            if (isset($res['order_id'])) {
                $this->db->where('inventory_outcome_id', $list['inventory_outcome_id']);
                $this->db->set('order_id', $res['order_id']);
                $this->db->update('ts_inventory_outcomes');
            }
        }
    }

    public function update_lotnumbers() {
        $this->db->select('*');
        $this->db->from('v_inventory_instock');
        $this->db->order_by('instock_date');
        $lists = $this->db->get()->result_array();
        foreach ($lists as $list) {
            if ($list['instock_type']=='S') {
                if (substr($list['instock_record'],0,3)!='CON') {
                    $this->db->select('max(inventory_adjust_id) as ordnum, count(inventory_adjust_id) as cnt');
                    $this->db->from('ts_inventory_adjusts');
                    $numdat=$this->db->get()->row_array();
                    if ($numdat['cnt']==0) {
                        $newrec = $numdat['cnt'];
                    } else {
                        $newrec = $numdat['ordnum'];
                    }
                    $recnum = 'D-'.str_pad($newrec,5,'0',STR_PAD_LEFT);
                    $this->db->where('inventory_income_id', $list['instock_id']);
                    $this->db->set('income_record', $recnum);
                    $this->db->update('ts_inventory_incomes');
                    $this->db->set('adjust_type', 'S');
                    $this->db->insert('ts_inventory_adjusts');
                }
            } elseif ($list['instock_type']=='O') {
                if (!empty($list['order_id'])) {
                    $this->db->select('order_num');
                    $this->db->from('ts_orders');
                    $this->db->where('order_id', $list['order_id']);
                    $orddat = $this->db->get()->row_array();
                    $newrec = $orddat['order_num'];
                    $recnum = 'Z0-'.str_pad($newrec,5,'0',STR_PAD_LEFT);
                    $this->db->where('inventory_outcome_id', $list['instock_id']);
                    $this->db->set('outcome_record', $recnum);
                    $this->db->update('ts_inventory_outcomes');
                } else {
                    $this->db->select('max(inventory_adjust_id) as ordnum, count(inventory_adjust_id) as cnt');
                    $this->db->from('ts_inventory_adjusts');
                    $numdat=$this->db->get()->row_array();
                    if ($numdat['cnt']==0) {
                        $newrec = $numdat['cnt'];
                    } else {
                        $newrec = $numdat['ordnum'];
                    }
                    $recnum = 'D-'.str_pad($newrec,5,'0',STR_PAD_LEFT);
                    $this->db->where('inventory_outcome_id', $list['instock_id']);
                    $this->db->set('outcome_record', $recnum);
                    $this->db->update('ts_inventory_outcomes');
                    $this->db->set('adjust_type', 'O');
                    $this->db->insert('ts_inventory_adjusts');
                }
            }
        }
        echo 'Ready'.PHP_EOL;
    }

    public function add_reliever_items() {
        $this->db->where('brand','SR');
        $this->db->delete('sb_items');
        $this->db->select('*');
        $this->db->from('sr_categories');
        $categories = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('ts_inventory_items');
        $this->db->order_by('inventory_type_id, item_num');
        $items = $this->db->get()->result_array();
        $categ = 0;
        $numpp = 1;
        foreach ($items as $item) {
            if ($item['inventory_type_id']!==$categ) {
                // New category
                $numpp=1;
                $categ = $item['inventory_type_id'];
                $srcat = $categories[$categ-1];
            }
            $newcode = $srcat['category_code'].str_pad($numpp,3,'0', STR_PAD_LEFT);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('create_user', 1);
            $this->db->set('update_user', 1);
            $this->db->set('item_number', $newcode);
            $this->db->set('item_name', $item['item_name']);
            $this->db->set('category_id', $srcat['category_id']);
            $this->db->set('brand','SR');
            $this->db->insert('sb_items');
            $numpp++;
        }
    }

    // Transform BT items to new format
    public function transformbtitems() {
//        $this->load->config('siteart_config');
//        $pricetypes = $this->config->item('price_types');
//        $this->db->select('item_id, item_number, item_template, vendor_item_id, main_image, cartoon_width, cartoon_heigh, cartoon_depth,cartoon_qty');
//        $this->db->from('sb_items');
//        $this->db->where('brand','BT');
//        $this->db->order_by('item_number');
//        $items = $this->db->get()->result_array();
//        foreach ($items as $item) {
//            $item_id = $item['item_id'];
//            echo "Item ".$item['item_number'];
//            // Similar
//            $this->db->where('item_similar_item', $item_id);
//            $this->db->delete('sb_item_similars');
//            $this->db->select('*');
//            $this->db->from('sb_simulars');
//            $this->db->where('item_id', $item_id);
//            $simres = $this->db->get()->row_array();
//            if (!empty($simres['sim_1'])) {
//                $this->db->set('item_similar_item', $item_id);
//                $this->db->set('item_similar_similar', $simres['sim_1']);
//                $this->db->insert('sb_item_similars');
//            }
//            if (!empty($simres['sim_2'])) {
//                $this->db->set('item_similar_item', $item_id);
//                $this->db->set('item_similar_similar', $simres['sim_2']);
//                $this->db->insert('sb_item_similars');
//            }
//            if (!empty($simres['sim_3'])) {
//                $this->db->set('item_similar_item', $item_id);
//                $this->db->set('item_similar_similar', $simres['sim_3']);
//                $this->db->insert('sb_item_similars');
//            }
//            echo ' Similar OK';
//            // Images
//            if (empty($item['main_image'])) {
//                $this->db->select('*');
//                $this->db->from('sb_item_images');
//                $this->db->where('item_img_item_id', $item_id);
//                $imgs = $this->db->get()->result_array();
//                if (count($imgs)>0) {
//                    $this->db->set('main_image', $imgs[0]['item_img_name']);
//                    $this->db->where('item_id', $item_id);
//                    $this->db->update('sb_items');
//                    // Delete first image
//                    // $this->db->where('item_img_id', $imgs[0]['item_img_id']);
//                    // $this->db->delete('sb_item_images');
//                }
//            }
//            echo ' Images OK';
//            // Prices
//            if ($item['item_template']=='Stressball') {
//                $this->db->select('*');
//                $this->db->from('sb_item_prices');
//                $this->db->where('item_price_itemid', $item_id);
//                $itmprice = $this->db->get()->row_array();
//                if (ifset($itmprice,'item_price_id',0) > 0) {
//                    $this->db->where('item_id', $item_id);
//                    $this->db->delete('sb_promo_price');
//                    foreach ($pricetypes as $pricetype) {
//                        $this->db->set('item_id', $item_id);
//                        $this->db->set('item_qty', $pricetype['base']);
//                        $this->db->set('price', (empty($itmprice['item_price_'.$pricetype['type']]) ? null :  $itmprice['item_price_'.$pricetype['type']]));
//                        $this->db->set('sale_price', (empty($itmprice['item_sale_'.$pricetype['type']]) ? null :  $itmprice['item_sale_'.$pricetype['type']]));
//                        $this->db->set('profit', $itmprice['profit_'.$pricetype['type']]);
//                        $this->db->insert('sb_promo_price');
//                    }
//                }
//            }
//            echo ' Prices OK'.PHP_EOL;
//            // Add box
//            $this->db->where('item_id', $item['item_id']);
//            $this->db->delete('sb_item_shipping');
//            $this->db->set('item_id', $item['item_id']);
//            $this->db->set('box_qty', $item['cartoon_qty']);
//            $this->db->set('box_width', $item['cartoon_width']);
//            $this->db->set('box_length', $item['cartoon_depth']);
//            $this->db->set('box_height', $item['cartoon_heigh']);
//            $this->db->insert('sb_item_shipping');
//        }
        $this->db->select('item_id, item_number, item_template, vendor_item_id, main_image, cartoon_width, cartoon_heigh, cartoon_depth,cartoon_qty');
        $this->db->from('sb_items');
        $this->db->where('brand','BT');
        $this->db->order_by('item_number');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            $item_id = $item['item_id'];
            echo 'Item # '.$item['item_number'].PHP_EOL;
            $this->db->select('*');
            $this->db->from('sb_item_images');
            $this->db->where('item_img_item_id', $item_id);
            $this->db->order_by('item_img_order');
            $imgs = $this->db->get()->result_array();
            if (count($imgs)>0) {
                $this->db->set('main_image', $imgs[0]['item_img_name']);
                $this->db->where('item_id', $item_id);
                $this->db->update('sb_items');
            }
        }
        echo 'Convert finished'.PHP_EOL;
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

    public function init_masterinventory() {
        $this->db->select('*');
        $this->db->from('ts_printshop_items');
        $this->db->order_by('item_num');
        $items = $this->db->get()->result_array();
        $itemnum=1;
        $type_sh = 'i'; // id=4
        $type_id = 1;
        foreach ($items as $item) {
            echo 'Item '.$item['item_name'].' insert '.PHP_EOL;
            $unit='pc';
            // if (in_array($item['printshop_item_id'], $lbsitem)) {
            //    $unit='lbs';
            // } elseif (in_array($item['printshop_item_id'],$yrditem)) {
            //    $unit='yd';
            // }
            $this->db->set('inventory_type_id', $type_id);
            $this->db->set('item_num',$type_sh.'-'.str_pad($itemnum,3,'0',STR_PAD_LEFT));
            $this->db->set('item_name',$item['item_name']);
            $this->db->set('item_order', $itemnum);
            $this->db->set('item_unit', $unit);
            $this->db->set('proof_template', $item['proof_temp']);
            $this->db->set('proof_template_source',$item['proof_temp_source']);
            $this->db->set('plate_template', $item['plate_temp']);
            $this->db->set('plate_template_source', $item['plate_temp_source']);
            $this->db->set('box_template', $item['item_label']);
            $this->db->set('box_template_source', $item['item_label_source']);
            $this->db->set('inserted_at', date('Y-m-d H:i:s'));
            $this->db->insert('ts_inventory_items');
            $newitemid = $this->db->insert_id();
            // Update sb_items
            // Add colors
            $this->db->select('*');
            $this->db->from('ts_printshop_colors');
            $this->db->where('printshop_item_id', $item['printshop_item_id']);
            $colors = $this->db->get()->result_array();
            foreach ($colors as $color) {
                // $diff = random_int(-15,15);
                $newprice = $color['price']; // round($color['price']*(100+$diff)/100,3);
                $this->db->set('inventory_item_id', $newitemid);
                $this->db->set('color', $color['color']);
                $this->db->set('color_order', $color['color_order']);
                $this->db->set('price', $newprice); // $color['price']
                $this->db->set('color_unit', $unit);
                $this->db->set('suggeststock', $color['suggeststock']);
                $this->db->set('reserved', $color['reserved']);
                $this->db->set('onroutestock', $color['onroutestock']);
                $this->db->set('notreorder', $color['notreorder']);
                $this->db->set('pantones', empty($color['specfile']) ? $color['color_descript'] : $color['specfile']);
                $this->db->set('color_image','');
                $this->db->set('color_image_source','');
                $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                $this->db->insert('ts_inventory_colors');
                $newcolorid = $this->db->insert_id();
                // Insert 5 empty rows
                for ($i=0; $i<5; $i++) {
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->insert('ts_invcolor_vendors');
                }
                //  Get Income
                $this->db->select('*');
                $this->db->from('ts_printshop_instock');
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $this->db->where('instock_amnt > 0');
                $incomes = $this->db->get()->result_array();
                foreach ($incomes as $income) {
                    // $diff = random_int(0,15);
                    $calcprice = $newprice; // round($newprice*(100+$diff)/100,3);
                    if (substr($income['instock_descrip'],0,9)=='Container') {
                        $recnum = 'CON-'.substr($income['instock_descrip'],10);
                        $descr = 'Purchased - '.$income['instock_descrip'];
                    } else {
                        $recnum = strtoupper(uniq_link(2,'chars')).uniq_link(4,'digits');
                        $descr = $income['instock_descrip'];
                    }
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->set('income_date', $income['instock_date']);
                    $this->db->set('income_qty', $income['instock_amnt']);
                    $this->db->set('income_price', $calcprice); // $color['price']
                    $this->db->set('income_description', $descr);
                    $this->db->set('income_record', $recnum);
                    $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                    $this->db->insert('ts_inventory_incomes');
                }
                // Negative Income
                $this->db->select('*');
                $this->db->from('ts_printshop_instock');
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $this->db->where('instock_amnt < 0');
                $corects = $this->db->get()->result_array();
                foreach ($corects as $corect) {
                    $qtyout = abs($corect['instock_amnt']);
                    $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
                    $this->db->from('ts_inventory_incomes');
                    $this->db->where('inventory_color_id', $newcolorid);
                    $this->db->having('leftqty > 0');
                    $this->db->order_by('income_date');
                    $candidats = $this->db->get()->result_array();
                    foreach ($candidats as $candidat) {
                        // $qtyout-$candidat['leftqty'];
                        if ($qtyout > $candidat['leftqty']) {
                            $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                        } else {
                            $newexp = $candidat['income_expense'] + $qtyout;
                        }
                        $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                        $this->db->set('income_expense', $newexp);
                        $this->db->update('ts_inventory_incomes');
                        $qtyout= $qtyout - $candidat['leftqty'];
                        if ($qtyout <= 0 ) {
                            break;
                        }
                    }
                    $outcome_type = 'X';
                    $this->db->select('count(inventory_outcome_id) as cnt, max(outcome_number) as outnumb');
                    $this->db->from('ts_inventory_outcomes');
                    $this->db->where('outcome_type', $outcome_type);
                    $outdat = $this->db->get()->row_array();
                    if ($outdat['cnt']==1) {
                        $recnum = -1;
                    } else {
                        $recnum = $outdat['outnumb'];
                    }
                    $newrecnum = $recnum + 1;
                    $recnummask = str_pad($newrecnum, 5,'0', STR_PAD_LEFT);
                    $recnum = $outcome_type.substr($recnummask,0,1).'-'.substr($recnummask,1);
                    // $recnum = strtoupper(uniq_link(2,'chars')).uniq_link(4,'digits');
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->set('outcome_date', $corect['instock_date']);
                    $this->db->set('outcome_qty', abs($corect['instock_amnt']));
                    $this->db->set('outcome_description', $corect['instock_descrip']);
                    $this->db->set('outcome_record', $recnum);
                    $this->db->set('outcome_type', $outcome_type);
                    $this->db->set('outcome_number', $newrecnum);
                    $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                    $this->db->insert('ts_inventory_outcomes');
                }
                // Get outcome
                $this->db->select('oa.shipped, oa.kepted, oa.misprint, o.order_num, oa.amount_date, o.order_id');
                $this->db->from('ts_order_amounts oa');
                $this->db->join('ts_orders o','o.order_id=oa.order_id');
                $this->db->where('printshop',1);
                $this->db->where('printshop_color_id', $color['printshop_color_id']);
                $outcomes = $this->db->get()->result_array();
                foreach ($outcomes as $outcome) {
                    $qtyout = intval($outcome['shipped'])+intval($outcome['misprint'])+intval($outcome['kepted']);
                    $outcome_type = 'P';
                    $this->db->select('count(inventory_outcome_id) as cnt, max(outcome_number) as outnumb');
                    $this->db->from('ts_inventory_outcomes');
                    $this->db->where('outcome_type', $outcome_type);
                    $outdat = $this->db->get()->row_array();
                    if ($outdat['cnt']==1) {
                        $recnum = -1;
                    } else {
                        $recnum = $outdat['outnumb'];
                    }
                    $newrecnum = $recnum + 1;
                    $recnummask = str_pad($newrecnum, 5,'0', STR_PAD_LEFT);
                    $recnum = $outcome_type.substr($recnummask,0,1).'-'.substr($recnummask,1);
                    // $recnum = 'A0-'.$outcome['order_num'];
                    $this->db->set('inventory_color_id', $newcolorid);
                    $this->db->set('outcome_date', $outcome['amount_date']);
                    $this->db->set('outcome_qty', $qtyout);
                    $this->db->set('outcome_description','Order # '.$outcome['order_num']);
                    $this->db->set('outcome_record', $recnum);
                    $this->db->set('order_id', $outcome['order_id']);
                    $this->db->set('outcome_number', $newrecnum);
                    $this->db->set('outcome_type', $outcome_type);
                    $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                    $this->db->insert('ts_inventory_outcomes');
                    // Update balance
                    $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
                    $this->db->from('ts_inventory_incomes');
                    $this->db->where('inventory_color_id', $newcolorid);
                    $this->db->having('leftqty > 0');
                    $this->db->order_by('income_date');
                    $candidats = $this->db->get()->result_array();
                    foreach ($candidats as $candidat) {
                        if ($qtyout > $candidat['leftqty']) {
                            $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                            $ordinv = $candidat['leftqty'];
                        } else {
                            $newexp = $candidat['income_expense'] + $qtyout;
                            $ordinv = $qtyout;
                        }
                        // echo 'QTY '.$qtyout.' New Expens '.$newexp.' Get INV '.$ordinv.PHP_EOL;
                        $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                        $this->db->set('income_expense', $newexp);
                        $this->db->update('ts_inventory_incomes');
                        // Insert to order inventory
                        $this->db->set('order_id', $outcome['order_id']);
                        $this->db->set('inventory_income_id', $candidat['inventory_income_id']);
                        $this->db->set('qty',$ordinv);
                        $this->db->insert('ts_order_inventory');
                        $qtyout= $qtyout - $candidat['leftqty'];
                        if ($qtyout <= 0 ) {
                            break;
                        }
                    }
                }
                echo 'Color '.$color['color'].' added successfully '.PHP_EOL;
            }
            $itemnum++;
        }
        $this->updcolor_price();
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
        $this->db->select('b.*, o.order_num, o.customer_name');
        $this->db->from('ts_order_batches b');
        $this->db->join('ts_orders o','o.order_id=b.order_id');
        $this->db->where('b.batch_date >= ', strtotime('2024-01-01'));
        $this->db->where('b.batch_date < ', strtotime('2024-09-01'));
        $batchs = $this->db->get()->result_array();
        $out = [];
        foreach ($batchs as $batch) {
            $cc_paym = 0; $other_paym = 0;
            $other_type = '';
            if ($batch['batch_amex'] != 0 || $batch['batch_vmd'] != 0 ) {
                $cc_paym = $batch['batch_amount'];
            } else {
                $other_paym = $batch['batch_amount'];
                if ($batch['batch_writeoff']!=0) {
                    $other_type = 'Write OFF';
                } elseif ($batch['batch_term']!=0) {
                    $other_type = 'Term';
                } else {
                    if (!empty($batch['batch_type'])) {
                        $other_type = $batch['batch_type'];
                    } else {
                        $other_type = 'Other';
                    }
                }
            }
            $out[] = [
                'date' => date('m/d/Y', $batch['batch_date']),
                'order_num' => $batch['order_num'],
                'customer' => $batch['customer_name'],
                'amount' => $batch['batch_amount'],
                'cc_payment' => $cc_paym,
                'other_paym' => $other_paym,
                'payment_type' => ($batch['batch_amount'] < 0 ? 'Refund' : 'Payment'),
                'other_type' => $other_type,
                'cc_type' => ($cc_paym != 0 ? $batch['batch_type'] : ''),
            ];
        }
        echo count($out).' Batches '.PHP_EOL;
        $this->load->config('uploader');
        $file_name = $this->config->item('upload_path_preload').'payment_report_2024_new.csv';
        @unlink($file_name);
        $fh = fopen($file_name, FOPEN_WRITE_CREATE);
        if ($fh) {
            $msg = 'Date;Order #;Customer;Total Payment;By Credit Card;Other Payment;CC System;Other Type;Payment Type;'.PHP_EOL;
            fwrite($fh, $msg);
            foreach ($out as $row) {
                $msg = $row['date'].';'.$row['order_num'].';"'.$row['customer'].'";'.$row['amount'].';'.$row['cc_payment'].';'.$row['other_paym'].';'.$row['cc_type'].';'.$row['other_type'].';'.$row['payment_type'].';'.PHP_EOL;
                fwrite($fh, $msg);
            }
            fclose($fh);
            echo 'File '.$file_name.' Ready '.PHP_EOL;
        } else {
            echo 'Create file Error'.PHP_EOL;
        }
    }

    public function merge_quotenotes() {
        $this->db->select('*');
        $this->db->from('ts_quotes');
        $this->db->where('quote_note !=','');
        $rows = $this->db->get()->result_array();
        foreach ($rows as $row) {
            echo 'Quote '.$row['quote_number'].PHP_EOL;
            $newnote = $row['quote_repcontact'].PHP_EOL.''.PHP_EOL.$row['quote_note'];
            $this->db->where('quote_id', $row['quote_id']);
            $this->db->set('quote_repcontact', $newnote);
            $this->db->set('quote_note','');
            $this->db->update('ts_quotes');
        }
    }

    public function itemsalesreport() {
//        $this->db->select('i.item_id, i.item_number, i.item_name, vi.vendor_item_number, v.vendor_name');
//        $this->db->from('sb_items i');
//        $this->db->join('sb_vendor_items vi','vi.vendor_item_id=i.vendor_item_id','left');
//        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor','left');
//        $this->db->order_by('i.item_number');
//        $items = $this->db->get()->result_array();
        for ($year=2018; $year < 2023; $year++) {
            $start = strtotime($year.'-01-01');
            $finish = strtotime(($year+1).'-01-01');
            $this->db->select('oi.item_id, count(distinct(oi.order_id)) as cnt_order, sum(oi.item_qty) as sold_item');
            $this->db->from('ts_order_items oi');
            $this->db->join('ts_orders o','o.order_id=oi.order_id');
            $this->db->where('o.order_date >= ', $start);
            $this->db->where('o.order_date < ', $finish);
            $this->db->where('o.is_canceled',0);
            $this->db->group_by('oi.item_id');
            $this->db->order_by('oi.item_id');
            $itemsrows  = $this->db->get()->result_array();
            $items  = [];
            foreach ($itemsrows as $itemsrow) {
                $this->db->select('item_id, item_number, item_name, vendor_name');
                $this->db->from('v_itemsearch');
                $this->db->where('item_id', $itemsrow['item_id']);
                if ($itemsrow['item_id'] > 0) {
                    $this->db->where('brand', 'BT');
                }
                $res = $this->db->get()->row_array();
                if (ifset($res,'item_id', 0) !== 0) {
                    $items[] = [
                        'item_number' => $res['item_number'],
                        'item_name' => $res['item_name'],
                        'orders' => $itemsrow['cnt_order'],
                        'items' => $itemsrow['sold_item'],
                        'vendor' => $res['vendor_name'],
                    ];
                } else {
                    $this->db->select('item_id, item_number, item_name, \'\' as vendor_name');
                    $this->db->from('sb_items');
                    $this->db->where('item_id', $itemsrow['item_id']);
                    $res = $this->db->get()->row_array();
                    if (ifset($res, 'item_id',0) !==0) {
                        $items[] = [
                            'item_number' => $res['item_number'],
                            'item_name' => $res['item_name'],
                            'orders' => $itemsrow['cnt_order'],
                            'items' => $itemsrow['sold_item'],
                            'vendor' => $res['vendor_name'],
                        ];
                    } else {
                        echo 'Year '.$year.' Item ID '.$itemsrow['item_id'].' Not Found'.PHP_EOL;
                    }

                }
            }
            $filename = $this->config->item('upload_path_preload').'solditems'.$year.'.csv';
            @unlink($filename);
            $fh = fopen($filename, FOPEN_WRITE_CREATE);
            if ($fh) {
                $msg = 'Item Number; Item Name; # orders with this item sold; # of quantity of this item sold; Vendor in database'.PHP_EOL;
                fwrite($fh, $msg);
                foreach ($items as $item) {
                    $msg = $item['item_number'].';"'.$item['item_name'].'";'.$item['orders'].';'.$item['items'].';'.$item['vendor'].';'.PHP_EOL;
                    fwrite($fh, $msg);
                }
                fclose($fh);
                echo 'Report '.$filename.' Ready'.PHP_EOL;
            }
        }
    }

    public function search_report() {
        for ($year=2018; $year < 2023; $year++) {
            $start = strtotime($year . '-01-01');
            $finish = strtotime(($year + 1) . '-01-01');
            $this->db->select('search_text, count(search_result_id) as cnt');
            $this->db->from('sb_search_results');
            $this->db->where('search_result', 1);
            $this->db->where('unix_timestamp(search_time) >= ', $start);
            $this->db->where('unix_timestamp(search_time) < ', $finish);
            $this->db->group_by('search_text');
            $results = $this->db->get()->result_array();
            $filename = $this->config->item('upload_path_preload').'findresults'.$year.'.csv';
            @unlink($filename);
            $fh = fopen($filename, FOPEN_WRITE_CREATE);
            if ($fh) {
                $msg = 'Search Text;Quantity searched in '.$year.PHP_EOL;
                fwrite($fh, $msg);
                foreach ($results as $result) {
                    $msg = '"'.$result['search_text'].'";'.$result['cnt'].PHP_EOL;
                    fwrite($fh, $msg);
                }
                fclose($fh);
                echo 'Report '.$filename.' Ready'.PHP_EOL;
            }
            $this->db->select('search_text, count(search_result_id) as cnt');
            $this->db->from('sb_search_results');
            $this->db->where('search_result', 0);
            $this->db->where('unix_timestamp(search_time) >= ', $start);
            $this->db->where('unix_timestamp(search_time) < ', $finish);
            $this->db->group_by('search_text');
            $results = $this->db->get()->result_array();
            $filename = $this->config->item('upload_path_preload').'nofindresults'.$year.'.csv';
            @unlink($filename);
            $fh = fopen($filename, FOPEN_WRITE_CREATE);
            if ($fh) {
                $msg = 'Search Text;Quantity searched in '.$year.PHP_EOL;
                fwrite($fh, $msg);
                foreach ($results as $result) {
                    $msg = '"'.$result['search_text'].'";'.$result['cnt'].PHP_EOL;
                    fwrite($fh, $msg);
                }
                fclose($fh);
                echo 'Report '.$filename.' Ready'.PHP_EOL;
            }
        }
    }

    public function check_fee() {
        $start = strtotime('2021-01-01');
        $finish = strtotime('2022-01-01');
        $this->db->select('order_id, order_num, cc_fee,weborder');
        $this->db->from('ts_orders');
        $this->db->where('cc_fee != 0');
        $this->db->where('order_date >= ', $start);
        $this->db->where('order_date < ', $finish);
        $this->db->order_by('order_id','desc');
        $orders = $this->db->get()->result_array();
        foreach ($orders as $order) {
            $this->db->select('sum(batch_amount) as batch_amount, sum(batch_vmd) as batch_vmd, sum(batch_amex) as batch_amex, count(batch_id) as cnt');
            $this->db->from('ts_order_batches');
            $this->db->where('order_id', $order['order_id']);
            $this->db->where('(batch_vmd != 0 or batch_amex != 0)');
            $batch = $this->db->get()->row_array();
            if ($batch['cnt'] > 0 && ($batch['batch_vmd'])!==0 || $batch['batch_amex']!==0) {
                $fee = $batch['batch_amount'] - ($batch['batch_vmd'] + $batch['batch_amex']);
                if (round($fee,2) !== round($order['cc_fee'],2)) {
                    echo 'Order '.$order['order_num'].' Fee - '.$order['cc_fee'].' Calc '.$fee.PHP_EOL;
                    if ($order['weborder']==1) {
                        $this->db->where('order_id', $order['order_id']);
                        $this->db->set('cc_fee', $fee);
                        $this->db->update('ts_orders');
                    } else {
                        if (intval($fee)==$order['cc_fee']) {
                            $this->db->where('order_id', $order['order_id']);
                            $this->db->set('cc_fee', $fee);
                            $this->db->update('ts_orders');
                        }
                    }

                }
            }
        }
    }

    public function customquote_number() {
        $brands = ['SR','SB'];
        foreach ($brands as $brand) {
            $this->db->select('*');
            $this->db->from('ts_custom_quotes');
            // $this->db->where('active',1);
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
            $this->db->order_by('date_add');
            $quotes = $this->db->get()->result_array();
            $numpp = 1;
            foreach ($quotes as $quote) {
                $this->db->where('custom_quote_id', $quote['custom_quote_id']);
                $this->db->set('quote_number', $numpp);
                $this->db->update('ts_custom_quotes');
                $numpp++;
            }
        }
        echo 'Quote # updated '.PHP_EOL;
    }

    public function exponboat() {
        $this->db->select('b.*');
        $this->db->from('ts_printshop_onboats b');
        $this->db->join('ts_inventory_colors c','c.inventory_color_id=b.printshop_color_id');
        $onboats = $this->db->get()->result_array();

        foreach ($onboats as $onboat) {
            $this->db->set('inventory_color_id', $onboat['printshop_color_id']);
            $this->db->set('onroutestock', $onboat['onroutestock']);
            $this->db->set('onboat_date', $onboat['onboat_date']);
            $this->db->set('onboat_container', $onboat['onboat_container']);
            $this->db->set('onboat_status', $onboat['onboat_status']);
            $this->db->insert('ts_inventory_onboats');
        }
        echo 'Ready '.PHP_EOL;
    }

    public function update_avgprice() {
        $this->db->select('inventory_color_id, sum(income_qty) as total_qty, sum(income_qty*income_price) as total');
        $this->db->from('ts_inventory_incomes');
        $this->db->group_by('inventory_color_id');
        $incomes = $this->db->get()->result_array();
        foreach ($incomes as $income) {
            if ($income['total_qty'] !== 0) {
                echo 'Inv Color '.$income['inventory_color_id'].' Total '.$income['total'].' QTY '.$income['total_qty'].PHP_EOL;
                $avg_price = round($income['total']/$income['total_qty'],3);
                $this->db->where('inventory_color_id', $income['inventory_color_id']);
                $this->db->set('avg_price', $avg_price);
                $this->db->update('ts_inventory_colors');
            }
        }
        $this->db->select('inventory_color_id, price');
        $this->db->from('ts_inventory_colors');
        $this->db->where('avg_price',0);
        $colors = $this->db->get()->result_array();
        foreach ($colors as $color) {
            $this->db->where('inventory_color_id', $color['inventory_color_id']);
            $this->db->set('avg_price', $color['price']);
            $this->db->update('ts_inventory_colors');
        }
        $this->db->select('b.inventory_onboat_id, b.inventory_color_id, b.vendor_price, c.price');
        $this->db->from('ts_inventory_onboats b');
        $this->db->join('ts_inventory_colors c','c.inventory_color_id=b.inventory_color_id');
        $boats = $this->db->get()->result_array();
        foreach ($boats as $boat) {
            if ($boat['vendor_price']==0) {
                echo 'Inv Color '.$boat['inventory_color_id'].' Boat '.$boat['inventory_onboat_id'].PHP_EOL;
                $this->db->where('inventory_onboat_id', $boat['inventory_onboat_id']);
                $this->db->set('vendor_price', $boat['price']);
                $this->db->update('ts_inventory_onboats');
            }
        }
        echo 'Updated successfully'.PHP_EOL;
    }

    public function checkfee() {
        $this->db->select('o.order_num, o.order_date, s.order_shipaddr_id, s.zip, s.country_id');
        $this->db->from('ts_order_shipaddres s');
        $this->db->join('ts_orders o','o.order_id=s.order_id');
        $this->db->where('o.order_date >= ', strtotime('2022-01-01'));
        $this->db->where('s.state_id', NULL);
        $this->db->where_in('s.country_id', ['39','223']);
        $this->db->where('s.zip != ','');
        $address = $this->db->get()->result_array();
        foreach ($address as $addres) {
            echo 'Country '.$addres['country_id'].' Zip '.$addres['zip'].PHP_EOL;
            $this->db->select('state_id, state_code');
            $this->db->from('ts_states');
            $this->db->where('country_id', $addres['country_id']);
            $stateselect = $this->db->get_compiled_select();
            $this->db->reset_query();

            $this->db->select('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code as state, t.state_id, count(c.geoip_city_id) as cntcity');
            $this->db->from('ts_geoipdata gdata');
            $this->db->join('ts_geoip_city c','c.geoname_id=gdata.geoname_id');
            $this->db->join('ts_countries cntr','cntr.country_iso_code_2=c.country_iso_code');
            $this->db->join("({$stateselect}) as t",'t.state_code=c.subdivision_1_iso_code','left');
            $this->db->where('gdata.postal_code',$addres['zip']);
            $this->db->where('cntr.country_id',$addres['country_id']);
            $this->db->group_by('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code, t.state_id');
            $this->db->order_by('cntcity','desc');
            $validdata = $this->db->get()->row_array();
            if (!empty($validdata['geoip_city_id']) && !empty($validdata['state_id'])) {
                echo 'Valid Country '.$validdata['city_name'].' Zip '.$validdata['state'].' Id '.$validdata['state_id'].PHP_EOL;
                $this->db->where('order_shipaddr_id', $addres['order_shipaddr_id']);
                $this->db->set('state_id', $validdata['state_id']);
                $this->db->update('ts_order_shipaddres');
            }
            // echo 'State '.$validdata['subdivision_1_iso_code'].' ID '.$validdata['state_id'].PHP_EOL;
        }
    }

    public function claypreviewcheck() {
        $this->db->select('c.artwork_id, o.order_id, count(c.artwork_clay_id) as cnt');
        $this->db->from('ts_artwork_clays c');
        $this->db->join('ts_artworks a','a.artwork_id=c.artwork_id');
        $this->db->join('ts_orders o', 'o.order_id=a.order_id');
        $this->db->group_by('c.artwork_id, o.order_id');
        $clays = $this->db->get()->result_array();
        foreach ($clays as $clay) {
            $this->db->where('order_id', $clay['order_id']);
            $this->db->set('art_clay',1);
            $this->db->update('ts_orders');
        }
        $this->db->select('p.artwork_id, o.order_id, count(p.artwork_preview_id) as cnt');
        $this->db->from('ts_artwork_previews p');
        $this->db->join('ts_artworks a','a.artwork_id=p.artwork_id');
        $this->db->join('ts_orders o', 'o.order_id=a.order_id');
        $this->db->group_by('p.artwork_id, o.order_id');
        $previews = $this->db->get()->result_array();
        foreach ($previews as $preview) {
            $this->db->where('order_id', $preview['order_id']);
            $this->db->set('art_preview',1);
            $this->db->update('ts_orders');
        }
    }

    public function duplicate_vendor_items() {
        $this->db->select('vendor_item_id, count(*) as cnt');
        $this->db->from('sb_items');
        $this->db->group_by('vendor_item_id');
        $this->db->having('cnt > 1');
        $vresults = $this->db->get()->result_array();
        foreach ($vresults as $vresult) {
            $this->db->select('*');
            $this->db->from('sb_vendor_items');
            $this->db->where('vendor_item_id', $vresult['vendor_item_id']);
            $vitem = $this->db->get()->row_array();
            $this->db->select('*');
            $this->db->from('sb_vendor_prices');
            $this->db->where('vendor_item_id', $vresult['vendor_item_id']);
            $vprices = $this->db->get()->result_array();
            $this->db->select('item_id, item_number, item_name');
            $this->db->from('sb_items i');
            $this->db->where('vendor_item_id', $vresult['vendor_item_id']);
            $this->db->order_by('item_id');
            $items = $this->db->get()->result_array();
            echo 'Vendor Item '.$vitem['vendor_item_number'].' Prices '.count($vprices).PHP_EOL;
            $itemnum=1;
            foreach ($items as $item) {
                if ($itemnum > 1) {
                    $this->db->set('vendor_item_vendor', $vitem['vendor_item_vendor']);
                    $this->db->set('vendor_item_number', $vitem['vendor_item_number']);
                    $this->db->set('vendor_item_name', $vitem['vendor_item_name']);
                    $this->db->set('vendor_item_blankcost', $vitem['vendor_item_blankcost']);
                    $this->db->set('vendor_item_cost', $vitem['vendor_item_cost']);
                    $this->db->set('vendor_item_exprint', $vitem['vendor_item_exprint']);
                    $this->db->set('vendor_item_setup', $vitem['vendor_item_setup']);
                    $this->db->set('vendor_item_repeat', $vitem['vendor_item_repeat']);
                    $this->db->set('vendor_item_notes', $vitem['vendor_item_notes']);
                    $this->db->set('vendor_item_zipcode', $vitem['vendor_item_zipcode']);
                    $this->db->set('printshop_item_id', $vitem['printshop_item_id']);
                    $this->db->set('stand_days', $vitem['stand_days']);
                    $this->db->set('rush1_days', $vitem['rush1_days']);
                    $this->db->set('rush2_days', $vitem['rush2_days']);
                    $this->db->set('rush1_price', $vitem['rush1_price']);
                    $this->db->set('rush2_price', $vitem['rush2_price']);
                    $this->db->set('pantone_match', $vitem['pantone_match']);
                    $this->db->insert('sb_vendor_items');
                    $newid = $this->db->insert_id();
                    $this->db->where('item_id', $item['item_id']);
                    $this->db->set('vendor_item_id', $newid);
                    $this->db->update('sb_items');
                    if (count($vprices) > 0) {
                        foreach ($vprices as $vprice) {
                            $this->db->set('vendorprice_qty', $vprice['vendorprice_qty']);
                            $this->db->set('vendorprice_val', $vprice['vendorprice_val']);
                            $this->db->set('vendorprice_color', $vprice['vendorprice_color']);
                            $this->db->set('vendor_item_id', $newid);
                            $this->db->insert('sb_vendor_prices');
                        }
                    }
                }
                echo 'Item # '.$item['item_number'].' - '.$item['item_name'].PHP_EOL;
                $itemnum++;
            }
        }
    }

    public function update_vendoritem_ship() {
        $this->db->select('vi.vendor_item_id, vi.vendor_item_zipcode, v.shipaddr_city, v.shipaddr_state, v.shipaddr_country, v.vendor_zipcode');
        $this->db->from('sb_vendor_items vi');
        $this->db->join('vendors v','v.vendor_id = vi.vendor_item_vendor');
        $vaddrs = $this->db->get()->result_array();
        foreach ($vaddrs as $vaddr) {
            if (!empty($vaddr['vendor_item_zipcode'])) {
                $this->db->where('vendor_item_id', $vaddr['vendor_item_id']);
                $this->db->set('item_shipcountry', 223);
                $this->db->update('sb_vendor_items');
            } else {
                $this->db->where('vendor_item_id', $vaddr['vendor_item_id']);
                $this->db->set('vendor_item_zipcode', $vaddr['vendor_zipcode']);
                if (!empty($vaddr['shipaddr_country'])) {
                    $this->db->set('item_shipcountry', 223);
                    $this->db->set('item_shipstate', $vaddr['shipaddr_state']);
                    $this->db->set('item_shipcity', $vaddr['shipaddr_city']);
                }
                $this->db->update('sb_vendor_items');
            }
        }
        $this->db->select('vi.vendor_item_zipcode, vi.item_shipcountry, count(vi.vendor_item_id) as cnt');
        $this->db->from('sb_vendor_items vi');
        $this->db->where('vi.item_shipcity',null);
        $this->db->group_by('vi.vendor_item_zipcode, vi.item_shipcountry');
        $vaddrs = $this->db->get()->result_array();
        $this->load->model('shipping_model');
        foreach ($vaddrs as $vaddr) {
            // Get shipping data
            $shipres = $this->shipping_model->get_zip_address($vaddr['item_shipcountry'], $vaddr['vendor_item_zipcode']);
            if ($shipres['result']==1) {
                $this->db->where('vendor_item_zipcode', $vaddr['vendor_item_zipcode']);
                $this->db->where('item_shipcountry', $vaddr['item_shipcountry']);
                $this->db->set('item_shipstate', $shipres['state']);
                $this->db->set('item_shipcity', $shipres['city']);
                $this->db->update('sb_vendor_items');
            }
        }
    }

    public function internal_item_transform() {
        $this->load->model('inventory_model');
        $this->db->select('i.item_id, i.item_number, i.item_name, i.printshop_inventory_id');
        $this->db->select('v.vendor_item_number, v.vendor_item_id');
        $this->db->from('sb_items i');
        $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
        $this->db->where('v.vendor_item_vendor', $this->config->item('inventory_vendor'));
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            echo $item['item_number'].' '.$item['item_name'].' INV '.$item['printshop_inventory_id'].PHP_EOL;
            if (empty($item['printshop_inventory_id'])) {
                $this->db->select('*');
                $this->db->from('ts_inventory_items');
                $this->db->where('item_num', $item['vendor_item_number']);
                $invres = $this->db->get()->row_array();
                if (ifset($invres,'inventory_item_id',0)>0) {
                    $this->db->where('item_id', $item['item_id']);
                    $this->db->set('printshop_inventory_id', $invres['inventory_item_id']);
                    $this->db->update('sb_items');
                    $res = $this->inventory_model->get_inventory_item($invres['inventory_item_id']);
                    if ($res['result']==1) {
                        $invdata = $res['data'];
                        $this->db->where('vendor_item_id', $item['vendor_item_id']);
                        $this->db->set('vendor_item_cost', $invdata['avg_price']);
                        $this->db->set('vendor_item_blankcost', $invdata['avg_price']);
                        $this->db->update('sb_vendor_items');
                    }
                    $this->db->where('vendor_item_id', $item['vendor_item_id']);
                    $this->db->set('vendor_item_number', $invres['item_num']);
                    $this->db->set('vendor_item_name', $invres['item_name']);
                    $this->db->update('sb_vendor_items');
                    // Delete vendor prices
                    $this->db->where('vendor_item_id', $item['vendor_item_id']);
                    $this->db->delete('sb_vendor_prices');
                    $this->db->where('item_color_itemid', $item['item_id']);
                    $this->db->delete('sb_item_colors');
                    $this->db->select('*');
                    $this->db->from('ts_inventory_colors');
                    $this->db->where('inventory_item_id', $invres['inventory_item_id']);
                    $colors = $this->db->get()->result_array();
                    foreach ($colors as $color) {
                        $this->db->set('item_color_itemid', $item['item_id']);
                        $this->db->set('item_color', $color['color']);
                        $this->db->set('item_color_order', $color['color_order']);
                        $this->db->set('printshop_color_id', $color['inventory_color_id']);
                        $this->db->insert('sb_item_colors');
                    }
                    echo 'Transform '.$invres['item_num'].' '.$invres['item_name'].PHP_EOL;
                }
            }
        }
    }
    public function getUpsRates() {
        $this->load->config('shipping');
        $this->load->library('UPS_service');
        $upsservice = new UPS_service();
        $shipFrom = array(
            "Name" => "BLUETRACK Internal",
            "Address" => array(
                "City" => "Clifton",
                "StateProvinceCode" => "NJ",
                "PostalCode" => "07012",
                "CountryCode" => "US"
            )
        );
        /*
        $shipTo = array(
            "Name" => "Test Company",
            "Address" => array(
                "AddressLine" => array(
                    "106 960 Yankee valley Blvd SE",
                ),
                "City" => "Toronto",
                "StateProvinceCode" => "ON",
                "PostalCode" => "M8Y1H8",
                "CountryCode" => "CA"
            )
        );
        */
        $shipTo = [
            "Name" => "Test Company",
            "Address" => [
                "AddressLine" => [
                    "106 960 Yankee valley Blvd SE",
                ],
                "City" => "CINCINNATI",
                "StateProvinceCode" => "OH",
                "PostalCode" => "45202",
                "CountryCode" => "US"
            ],
        ];
        $packWeight = 7.2;
        $packDimens = [];
        $packDimens[] = [
            "PackagingType" => array(
                "Code" => "02",
                "Description" => "Packaging"
            ),
            "Dimensions" => array(
                "UnitOfMeasurement" => array(
                    "Code" => "IN",
                    "Description" => "Inches"
                ),
                "Length" => "15",
                "Width" => "15",
                "Height" => "15"
            ),
            "PackageWeight" => array(
                "UnitOfMeasurement" => array(
                    "Code" => "LBS",
                    "Description" => "Pounds"
                ),
                "Weight" => "7.2"
            )
        ];

        $tokenres = $this->getUpsToken();
        if ($tokenres['result']==0) {
            echo 'Rates request break on stage Token Generation, reason - '.$tokenres['msg'];
        } else {
            $token = $tokenres['token'];
            // Time in transit
            $res = $upsservice->getRates($token, $shipTo, $shipFrom, 1,  $packDimens, $packWeight);
            if ($res['error'] > 0) {
                echo 'Error, code '.$res['msg'];
            } else {
                if (isset($res['errors'])) {
                    $error = $res['errors'][0];
                    echo 'Error, code '.$error['code'].' - '.$error['message'].PHP_EOL;
                } else {
                    echo 'SUCCESS'.PHP_EOL;
                    var_dump($res['rates']);
                }
            }
        }
    }

    public function getTimeinTransit() {
        $this->load->config('shipping');
        $this->load->library('UPS_service');
        $upsservice = new UPS_service();
        $shipFrom = array(
            "Name" => "BLUETRACK Internal",
            "Address" => array(
                "City" => "Clifton",
                "StateProvinceCode" => "NJ",
                "PostalCode" => "07012",
                "CountryCode" => "US"
            )
        );
//        $shipTo = array(
//            "Name" => "Test Company",
//            "Address" => array(
//                "AddressLine" => array(
//                    "The Landing",
//                ),
//                "City" => "Trafford Park",
//                "StateProvinceCode" => "",
//                "PostalCode" => "M502ST",
//                "CountryCode" => "GB"
//            )
//        );
        $shipTo = [
            "Name" => "Test Company",
            "Address" => [
                "AddressLine" => [
                    "106 960 Yankee valley Blvd SE",
                ],
                "City" => "CINCINNATI",
                "StateProvinceCode" => "OH",
                "PostalCode" => "45202",
                "CountryCode" => "US"
            ],
        ];

        $weight = "7.2";
        $shipdate = "2023-07-31";
        $shiptime = "10:00:00";
        $tokenres = $this->getUpsToken();
        if ($tokenres['result']==1) {
            $token = $tokenres['token'];
            $tntres = $upsservice->timeInTransit($token, $shipFrom['Address'], $shipTo['Address'], $weight, 1, 100.5, $shipdate, $shiptime);
            if ($tntres['error']==0) {
                $services = $tntres['services'];
                var_dump($services);
            }
        }
    }

    public function getUpsToken()  {
        $out = ['result' => 0, 'msg' => 'Error during Token generation'];
        $this->load->library('UPS_service');
        $upsservice = new UPS_service();
        $sessionId =  uniq_link();
        echo 'Session ID '.$sessionId.PHP_EOL;
        $tokenresult = $upsservice->generateToken($sessionId);
        if ($tokenresult['error']==1) {

        } else {
            if (isset($tokenresult['errors'])) {
                $errors = $tokenresult['errors'][0];
                $out['msg'] = 'Error Code '.$errors['code'].' - '.$errors['message'];
            } else {
                $out['result'] = 1;
                $out['token'] = $tokenresult['access_token'];
                $out['session'] = $sessionId;
                // echo 'Success Token Type '.$tokenresult['token_type'].' Issued '.$tokenresult['issued_at'].'('.date('Y-m-d H:i:s', $tokenresult['issued_at']).' Expired '.(intval($tokenresult['expires_in'])/60).' min';
            }
        }
        return $out;
    }

    public function clean_schema() {
        $this->db->select('TABLE_NAME, TABLE_TYPE');
        $this->db->from('information_schema.TABLES');
        $this->db->where('TABLE_SCHEMA', 'lift_test');
        $items = $this->db->get()->result_array();
        echo 'Find '.count($items).' objects'.PHP_EOL;
        $filename = $this->config->item('upload_path_preload').'cleanobj.sql';
        @unlink($filename);
        $fh = fopen($filename, FOPEN_READ_WRITE_CREATE);
        foreach ($items as $item) {
            if ($item['TABLE_TYPE']=='BASE TABLE') {
                $msg='DROP TABLE IF EXISTS '.$item['TABLE_NAME'].';'.PHP_EOL;
            } else {
                $msg='DROP VIEW IF EXISTS '.$item['TABLE_NAME'].';'.PHP_EOL;
            }
            fwrite($fh, $msg);
        }
        fclose($fh);
    }

    public function export_dbitems() {
        ini_set("memory_limit","-1");
        $normal_template = 'Stressball';
        $other_template = 'Other Item';
        $this->load->config('siteart_config');
        // Stressball items
        // Imprints - 12
        $this->db->select('*')->from('sb_items')->where('item_template', $normal_template)->order_by('item_number');
        $items = $this->db->get()->result_array();
        $this->load->config('uploader');
        $filenorm = $this->config->item('upload_path_preload').'stressballs_items.xlsx';
        @unlink($filenorm);
        $titles = [
            'Item #','Item Name','Active','New','Sale Tag','Template','Lead A','Lead B','Lead C','Lead Blank','Material','Weight','Size','Options','Colors',
            'Similar 1','Similar 2','Similar 3',
            'Meta Title','URL','Keywords for search','Meta Keywords','Meta Description','Item Description','Cartoon: QTY','Width','Height','Deep','Add Price Each',
            'Vendor','Vendor Item #','Vendor Item Name','Vendor min cost (blank)','Vendor min cost',
        ];
        $numpp=1;
        for ($i=1; $i<=7; $i++) {
            array_push($titles, 'Vendor Price QTY '.$numpp);
            array_push($titles, 'Price (blank) '.$numpp);
            array_push($titles, 'Price '.$numpp);
            $numpp++;
        }
        array_push($titles, 'Vendor Price Exprint');
        array_push($titles, 'Vendor Price Setup');
        // Imprints
        $numpp=1;
        for ($i=1;$i<=12;$i++) {
            array_push($titles, 'Imprint Location '.$numpp);
            array_push($titles,'Imprint Size '.$numpp);
            $numpp++;
        }
        // Prices
        $pricetypes = $this->config->item('price_types');
        foreach ($pricetypes as $pricetype) {
            array_push($titles,'Price '.$pricetype['type']);
            array_push($titles, 'Sale '.$pricetype['type']);
        }
        array_push($titles, 'Price Exprint');
        array_push($titles, 'Sale Exprint');
        array_push($titles, 'Price Setup');
        array_push($titles, 'Sale Setup');
        $cols = [];
        $cellname = '';
        $numpp = 1;
        $ncel = 1;
        foreach ($titles as $title) {
            $newcell = $cellname.chr(64 + $numpp);
            array_push($cols, $newcell);
            $numpp++;
            if ($numpp==27) {
                $cellname=chr(64+$ncel);
                $numpp=1;
                $ncel++;
            }
        }
        /* create report */
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DB Export');
        $ncol = 0;
        foreach ($titles as $title) {
            $sheet->setCellValue($cols[$ncol].'1', $title);
            $ncol++;
        }
        $nrow = 2;
        foreach ($items as $item) {
            $ncol = 0;
            $this->db->select('group_concat(item_color) as colorstr')->from('sb_item_colors')->where('item_color_itemid', $item['item_id']);
            $colors = $this->db->get()->row_array();

            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_number']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_name']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_active']==1 ? 'Yes' : 'No');$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_new']==1 ? 'Yes' : 'No');$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_sale']==1 ? 'Yes' : 'No');$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_template']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_lead_a']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_lead_b']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_lead_c']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_lead_blank']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_material']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_weigth']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_size']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['options']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $colors['colorstr']);$ncol++;
            // Similar
            $this->db->select('concat(i.item_number,\'-\', i.item_name) as simitem');
            $this->db->from('sb_item_similars s');
            $this->db->join('sb_items i','s.item_similar_similar = i.item_id');
            $this->db->where('s.item_similar_item', $item['item_id']);
            $simils = $this->db->get()->result_array();
            $numpp=0;
            foreach ($simils as $simil) {
                $sheet->setCellValue($cols[$ncol].$nrow, $simil['simitem']);$ncol++;
                $numpp++;
            }
            if ($numpp < 3) {
                for ($i=$numpp; $i<3; $i++) {
                    $sheet->setCellValue($cols[$ncol].$nrow, '');$ncol++;
                }
            }
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_meta_title']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_url']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_keywords']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_metakeywords']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_metadescription']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['item_description1']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['cartoon_qty']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['cartoon_width']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['cartoon_heigh']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['cartoon_depth']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $item['charge_pereach']);$ncol++;
            // Get vendor, vendor item
            $this->db->select('v.vendor_name, vi.*');
            $this->db->from('sb_vendor_items vi');
            $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor','left');
            $this->db->where('vi.vendor_item_id', $item['vendor_item_id']);
            $vdata = $this->db->get()->row_array();
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_name']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_item_number']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_item_name']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_item_blankcost']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_item_cost']);$ncol++;
            // Vendor Prices
            $this->db->select('vendorprice_qty, vendorprice_val, vendorprice_color')->from('sb_vendor_prices')->where('vendor_item_id', $item['vendor_item_id']);
            $vprices = $this->db->get()->result_array();
            $numpp = 0;
            foreach ($vprices as $vprice) {
                $sheet->setCellValue($cols[$ncol].$nrow, $vprice['vendorprice_qty']);$ncol++;
                $sheet->setCellValue($cols[$ncol].$nrow, $vprice['vendorprice_val']);$ncol++;
                $sheet->setCellValue($cols[$ncol].$nrow, $vprice['vendorprice_color']);$ncol++;
                $numpp++;
            }
            if ($numpp < 7) {
                for ($i=$numpp;$i<7;$i++) {
                    $sheet->setCellValue($cols[$ncol].$nrow, '');$ncol++;
                    $sheet->setCellValue($cols[$ncol].$nrow, '');$ncol++;
                    $sheet->setCellValue($cols[$ncol].$nrow, '');$ncol++;
                }
            }
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_item_exprint']);$ncol++;
            $sheet->setCellValue($cols[$ncol].$nrow, $vdata['vendor_item_setup']);$ncol++;
            // Get inprints
            $this->db->select('item_inprint_location, item_inprint_size')->from('sb_item_inprints')->where('item_inprint_item', $item['item_id']);
            $prints = $this->db->get()->result_array();
            $numpp=0;
            foreach ($prints as $print) {
                $sheet->setCellValue($cols[$ncol].$nrow, $print['item_inprint_location']);$ncol++;
                $sheet->setCellValue($cols[$ncol].$nrow, $print['item_inprint_size']);$ncol++;
                $numpp++;
            }
            if ($numpp < 12) {
                for ($i=$numpp; $i<12; $i++) {
                    $sheet->setCellValue($cols[$ncol].$nrow, '');$ncol++;
                    $sheet->setCellValue($cols[$ncol].$nrow, '');$ncol++;
                }
            }
            $this->db->select('*')->from('sb_item_prices')->where('item_price_itemid', $item['item_id']);
            $iprice = $this->db->get()->row_array();
            if (ifset($iprice, 'item_price_id',0)==$item['item_id']) {
                foreach ($pricetypes as $pricetype) {
                    $sheet->setCellValue($cols[$ncol].$nrow, $iprice['item_price_'.$pricetype['type']]);$ncol++;
                }
                foreach ($pricetypes as $pricetype) {
                    $sheet->setCellValue($cols[$ncol].$nrow, $iprice['item_sale_'.$pricetype['type']]);$ncol++;
                }
                $sheet->setCellValue($cols[$ncol].$nrow, $iprice['item_price_print']);$ncol++;
                $sheet->setCellValue($cols[$ncol].$nrow, $iprice['item_sale_print']);$ncol++;
                $sheet->setCellValue($cols[$ncol].$nrow, $iprice['item_price_setup']);$ncol++;
                $sheet->setCellValue($cols[$ncol].$nrow, $iprice['item_sale_setup']);$ncol++;
            }
            $nrow++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $writer->save($filenorm);    // download file
        echo 'File '.$filenorm.' ready'.PHP_EOL;
        // Promo Items
        $this->db->select('*')->from('sb_items')->where('item_template', $other_template)->order_by('item_number');
        $items = $this->db->get()->result_array();
        $this->load->config('uploader');
        $fileprom = $this->config->item('upload_path_preload').'promo_items.xlsx';
        @unlink($fileprom);
        $titles = [
            'Item #','Item Name','Active','New','Sale Tag','Template','Lead A','Lead B','Lead C','Lead Blank','Material','Weight','Size','Options','Colors',
            'Similar 1','Similar 2','Similar 3',
            'Meta Title','URL','Keywords for search','Meta Keywords','Meta Description','Item Description','Cartoon: QTY','Width','Height','Deep','Add Price Each',
            'Vendor','Vendor Item #','Vendor Item Name','Vendor min cost (blank)','Vendor min cost',
        ];
        $numpp=1;
        for ($i=1; $i<=7; $i++) {
            array_push($titles, 'Vendor Price QTY '.$numpp);
            array_push($titles, 'Price (blank) '.$numpp);
            array_push($titles, 'Price '.$numpp);
            $numpp++;
        }
        array_push($titles, 'Vendor Price Exprint');
        array_push($titles, 'Vendor Price Setup');
        // Imprints
        $numpp=1;
        for ($i=1;$i<=12;$i++) {
            array_push($titles, 'Imprint Location '.$numpp);
            array_push($titles,'Imprint Size '.$numpp);
            $numpp++;
        }
        // Prices
        $numpp=1;
        for ($i=0; $i<9; $i++) {
            array_push($titles, 'Price QTY '.$numpp);
            array_push($titles, 'Price '.$numpp);
            array_push($titles, 'Sale '.$numpp);
            $numpp++;
        }
        array_push($titles, 'Price Exprint');
        array_push($titles, 'Sale Exprint');
        array_push($titles, 'Price Setup');
        array_push($titles, 'Sale Setup');
        $cols = [];
        $cellname = '';
        $numpp = 1;
        $ncel = 1;
        foreach ($titles as $title) {
            $newcell = $cellname.chr(64 + $numpp);
            array_push($cols, $newcell);
            $numpp++;
            if ($numpp==27) {
                $cellname=chr(64+$ncel);
                $numpp=1;
                $ncel++;
            }
        }
        /* create report */
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DB Export');
        $ncol = 0;
        foreach ($titles as $title) {
            $sheet->setCellValue($cols[$ncol].'1', $title);
            $ncol++;
        }
        $nrow = 2;
        foreach ($items as $item) {
            $ncol = 0;
            $this->db->select('group_concat(item_color) as colorstr')->from('sb_item_colors')->where('item_color_itemid', $item['item_id']);
            $colors = $this->db->get()->row_array();
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_number']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_name']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_active'] == 1 ? 'Yes' : 'No');
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_new'] == 1 ? 'Yes' : 'No');
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_sale'] == 1 ? 'Yes' : 'No');
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_template']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_lead_a']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_lead_b']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_lead_c']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_lead_blank']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_material']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_weigth']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_size']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['options']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $colors['colorstr']);
            $ncol++;
            // Similar
            $this->db->select('concat(i.item_number,\'-\', i.item_name) as simitem');
            $this->db->from('sb_item_similars s');
            $this->db->join('sb_items i', 's.item_similar_similar = i.item_id');
            $this->db->where('s.item_similar_item', $item['item_id']);
            $simils = $this->db->get()->result_array();
            $numpp = 0;
            foreach ($simils as $simil) {
                $sheet->setCellValue($cols[$ncol] . $nrow, $simil['simitem']);
                $ncol++;
                $numpp++;
            }
            if ($numpp < 3) {
                for ($i = $numpp; $i < 3; $i++) {
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');
                    $ncol++;
                }
            }
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_meta_title']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_url']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_keywords']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_metakeywords']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_metadescription']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['item_description1']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['cartoon_qty']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['cartoon_width']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['cartoon_heigh']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['cartoon_depth']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $item['charge_pereach']);
            $ncol++;
            // Get vendor, vendor item
            $this->db->select('v.vendor_name, vi.*');
            $this->db->from('sb_vendor_items vi');
            $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor', 'left');
            $this->db->where('vi.vendor_item_id', $item['vendor_item_id']);
            $vdata = $this->db->get()->row_array();
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_name']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_item_number']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_item_name']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_item_blankcost']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_item_cost']);
            $ncol++;
            // Vendor Prices
            $this->db->select('vendorprice_qty, vendorprice_val, vendorprice_color')->from('sb_vendor_prices')->where('vendor_item_id', $item['vendor_item_id']);
            $vprices = $this->db->get()->result_array();
            $numpp = 0;
            foreach ($vprices as $vprice) {
                $sheet->setCellValue($cols[$ncol] . $nrow, $vprice['vendorprice_qty']);
                $ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $vprice['vendorprice_val']);
                $ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $vprice['vendorprice_color']);
                $ncol++;
                $numpp++;
            }
            if ($numpp < 7) {
                for ($i = $numpp; $i < 7; $i++) {
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');
                    $ncol++;
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');
                    $ncol++;
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');
                    $ncol++;
                }
            }
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_item_exprint']);
            $ncol++;
            $sheet->setCellValue($cols[$ncol] . $nrow, $vdata['vendor_item_setup']);
            $ncol++;
            // Get inprints
            $this->db->select('item_inprint_location, item_inprint_size')->from('sb_item_inprints')->where('item_inprint_item', $item['item_id']);
            $prints = $this->db->get()->result_array();
            $numpp = 0;
            foreach ($prints as $print) {
                $sheet->setCellValue($cols[$ncol] . $nrow, $print['item_inprint_location']);
                $ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $print['item_inprint_size']);
                $ncol++;
                $numpp++;
            }
            if ($numpp < 12) {
                for ($i = $numpp; $i < 12; $i++) {
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');$ncol++;
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');$ncol++;
                }
            }
            $this->db->select('item_qty, price, sale_price')->from('sb_promo_price')->where('item_id', $item['item_id'])->order_by('item_qty');
            $promprices = $this->db->get()->result_array();
            $numpp=0;
            foreach ($promprices as $promprice) {
                $sheet->setCellValue($cols[$ncol] . $nrow, $promprice['item_qty']);$ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $promprice['price']);$ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $promprice['sale_price']);$ncol++;
                $numpp++;
            }
            if ($numpp < 9) {
                for($i=$numpp; $i<9; $i++) {
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');$ncol++;
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');$ncol++;
                    $sheet->setCellValue($cols[$ncol] . $nrow, '');$ncol++;
                }
            }
            $this->db->select('*')->from('sb_item_prices')->where('item_price_itemid', $item['item_id']);
            $iprice = $this->db->get()->row_array();
            if (ifset($iprice, 'item_price_id', 0) == $item['item_id']) {
                $sheet->setCellValue($cols[$ncol] . $nrow, $iprice['item_price_print']);$ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $iprice['item_sale_print']);$ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $iprice['item_price_setup']);$ncol++;
                $sheet->setCellValue($cols[$ncol] . $nrow, $iprice['item_sale_setup']);$ncol++;
            }
            $nrow++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $writer->save($fileprom);    // download file
        echo 'File '.$fileprom.' ready'.PHP_EOL;

    }

    public function rename_proofdocs()
    {
        $this->load->config('uploader');
        $logfile = $this->config->item('upload_path_preload').'badproofdocs.txt';
        $fh = fopen($logfile,'a+');
        $this->db->select('artwork_id, order_id')->from('ts_artworks')->where('order_id is not null')->order_by('artwork_id','desc');
        $arts = $this->db->get()->result_array();
        $shname = $this->config->item('artwork_proofs_relative');
        $flname = $this->config->item('artwork_proofs');
        foreach ($arts as $art) {
            // Folder
            $path = $shname.$art['artwork_id'];
            // echo 'Path '.$path.PHP_EOL;
            createPath($path);
            // Get Order
            $this->db->select('order_num, brand')->from('ts_orders')->where('order_id', $art['order_id']);
            $order = $this->db->get()->row_array();
            // Get proofs
            $this->db->select('artwork_proof_id, proof_name, proof_ordnum')->from('ts_artwork_proofs');
            $this->db->where('artwork_id', $art['artwork_id'])->order_by('artwork_proof_id');
            $proofs = $this->db->get()->result_array();
            $numpp = 1;
            foreach ($proofs as $proof) {
                $filedat = extract_filename($proof['proof_name']);
                $newname =  ($order['brand']=='SR' ? 'SR' : 'BT').'_'.$order['order_num'].'_proof_'.str_pad($numpp,2, '0', STR_PAD_LEFT).'.'.$filedat['ext'];
                $chkname = $shname.$art['artwork_id'].'/'.$newname;
                if ($chkname!==$proof['proof_name']) {
                    // echo 'ArtW '.$art['artwork_id'].' Old Name '.$proof['proof_name'].' New Name '.$newname.PHP_EOL;
                    $sourcefile = str_replace($shname, $flname, $proof['proof_name']);
                    if (file_exists($sourcefile)) {
                        // echo 'Source file not exist '.$targetfile.' Order '.$order['order_num'].PHP_EOL;
                        $targetfile = $flname.$art['artwork_id'].'/'.$newname;
                        if (!file_exists($targetfile)) {
                            $cpres = @copy($sourcefile, $targetfile);
                            if ($cpres) {
                                $this->db->where('artwork_proof_id', $proof['artwork_proof_id']);
                                $this->db->set('proof_name', $shname.$art['artwork_id'].'/'.$newname);
                                $this->db->set('proof_ordnum', $numpp);
                                $this->db->update('ts_artwork_proofs');
                                $numpp++;
                                @unlink($sourcefile);
                            } else {
                                echo 'Error copy '.$sourcefile.' to '.$targetfile.PHP_EOL;
                                die();
                            }
                        } else {
                            echo 'File '.$targetfile.' NON UNIQUE '.$proof['artwork_proof_id'].PHP_EOL;
                            die();
                        }
                    } else {
                        $msg = 'Proof '.$proof['artwork_proof_id'].' '.$proof['proof_name'].' not exist. Order '.$order['order_num'].PHP_EOL;
                        fwrite($fh, $msg);
                    }
                }
            }
        }
    }

    public function sales_report()
    {
        $start_date = strtotime('2016-01-01');
        $reportres = [];
        // Web
        $this->db->select('DATE_FORMAT(FROM_UNIXTIME(o.order_date),\'%Y\') as yearorder, count(order_id) as total');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.weborder',1);
        $this->db->group_by('yearorder');
        $this->db->order_by('yearorder');
        $webres = $this->db->get()->result_array();
        $reportres[] = [
            'label' => 'Web Orders',
            '2016' => 0,
            '2017' => 0,
            '2018' => 0,
            '2019' => 0,
            '2020' => 0,
            '2021' => 0,
            '2022' => 0,
            '2023' => 0,
        ];
        $repidx = count($reportres) - 1;
        foreach ($webres as $row) {
            $reportres[$repidx][$row['yearorder']] = $row['total'];
        }
        $users = [];
        $users[] = ['label' => 'Sage', 'id' => 3];
        $users[] = ['label' => 'Sean', 'id' => 1];
        $users[] = ['label' => 'Robert', 'id' => 19];
        $users[] = ['label' => 'Shanequa', 'id' => 23];
        $other = [3,1,19,23];
        // Users
        foreach ($users as $user) {
            $this->db->select('DATE_FORMAT(FROM_UNIXTIME(o.order_date),\'%Y\') as yearorder, count(order_id) as total');
            $this->db->from('ts_orders o');
            $this->db->where('o.order_date >= ', $start_date);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.weborder',0);
            $this->db->where('o.order_usr_repic',$user['id']);
            $this->db->group_by('yearorder');
            $this->db->order_by('yearorder');
            $userres = $this->db->get()->result_array();
            $reportres[] = [
                'label' => $user['label'],
                '2016' => 0,
                '2017' => 0,
                '2018' => 0,
                '2019' => 0,
                '2020' => 0,
                '2021' => 0,
                '2022' => 0,
                '2023' => 0,
            ];
            $repidx = count($reportres) - 1;
            foreach ($userres as $row) {
                $reportres[$repidx][$row['yearorder']] = $row['total'];
            }
        }
        // Other
        $this->db->select('DATE_FORMAT(FROM_UNIXTIME(o.order_date),\'%Y\') as yearorder, count(order_id) as total');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.weborder',0);
        $this->db->where_not_in('o.order_usr_repic',$other);
        $this->db->group_by('yearorder');
        $this->db->order_by('yearorder');
        $otherres = $this->db->get()->result_array();
        $reportres[] = [
            'label' => 'Other',
            '2016' => 0,
            '2017' => 0,
            '2018' => 0,
            '2019' => 0,
            '2020' => 0,
            '2021' => 0,
            '2022' => 0,
            '2023' => 0,
        ];
        $repidx = count($reportres) - 1;
        foreach ($otherres as $row) {
            $reportres[$repidx][$row['yearorder']] = $row['total'];
        }
        // TOTAL
        $this->db->select('DATE_FORMAT(FROM_UNIXTIME(o.order_date),\'%Y\') as yearorder, count(order_id) as total');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.is_canceled',0);
        $this->db->group_by('yearorder');
        $this->db->order_by('yearorder');
        $allres = $this->db->get()->result_array();
        $reportres[] = [
            'label' => 'TOTAL',
            '2016' => 0,
            '2017' => 0,
            '2018' => 0,
            '2019' => 0,
            '2020' => 0,
            '2021' => 0,
            '2022' => 0,
            '2023' => 0,
        ];
        $repidx = count($reportres) - 1;
        foreach ($allres as $row) {
            $reportres[$repidx][$row['yearorder']] = $row['total'];
        }
        $filename = $this->config->item('upload_path_preload').'sales_report_16_23.csv';
        @unlink($filename);
        $fh = fopen($filename,'a+');
        $msg=';';
        for($i=2016; $i<2024; $i++) {
            $msg.=$i.';';
        }
        $msg.=PHP_EOL;
        fwrite($fh, $msg);
        foreach ($reportres as $row) {
            $msg=$row['label'].';';
            for($i=2016; $i<2024; $i++) {
                $msg.=$row[$i]==0 ? '' : $row[$i].';';
            }
            $msg.=PHP_EOL;
            fwrite($fh, $msg);
        }
        echo 'Report '.$filename.' ready!'.PHP_EOL;
    }

    public function ordercog_report() {
        $start = strtotime('2023-01-01');
        $this->db->select('order_id, order_num, date_format(from_unixtime(order_date),\'%m/%d/%Y\') as orderdate, brand, revenue, profit, profit_perc, customer_name, order_qty, order_items, order_cog');
        $this->db->from('ts_orders');
        $this->db->where('order_date >= ', $start);
        $this->db->where('is_canceled', 0);
        $orders = $this->db->get()->result_array();
        $reports = [];
        $maxvend = 0;
        foreach ($orders as $order) {
            $order['brand'] = $order['brand']=='SR' ? 'SR' : 'BT';
            if ($order['order_cog']=='') {
                $vendors = [];
                $order['profit_perc'] = 'PROJ';
                $order['order_cog']='-';
            } else {
                $this->db->select('v.vendor_name, sum(oa.amount_sum) as total');
                $this->db->from('ts_order_amounts oa');
                $this->db->join('vendors v','oa.vendor_id = v.vendor_id');
                $this->db->where('oa.order_id', $order['order_id']);
                $this->db->group_by('v.vendor_name');
                $vendors = $this->db->get()->result_array();
                if (count($vendors)>$maxvend) {
                    $maxvend = count($vendors);
                }
            }
            $order['vendors'] = $vendors;
            $reports[] = $order;
        }
        $file = $this->config->item('upload_path_preload').'orders_cog_2023.csv';
        @unlink($file);
        $fh = fopen($file, 'a+');
        $msg = 'Order #;Date;Brand;Revenue;Profit;%;Customer;Item;QTY;COG;';
        for ($i=1; $i<=$maxvend; $i++) {
            $msg.='PO '.$i.' Vendor;PO '.$i.' Amount;';
        }
        fwrite($fh, $msg.PHP_EOL);
        foreach ($reports as $report) {
            $msg=$report['order_num'].';'.$report['orderdate'].';'.$report['brand'].';'.$report['revenue'].';'.$report['profit'].';';
            $msg.=$report['profit_perc'].';"'.$report['customer_name'].'";"'.$report['order_items'].'";'.$report['order_qty'].';'.$report['order_cog'].';';
            foreach ($report['vendors'] as $vendor) {
                $msg.='"'.$vendor['vendor_name'].'";'.$vendor['total'].';';
            }
            if (count($report['vendors'])<$maxvend) {
                $diff = $maxvend - count($report['vendors']);
                for ($i=0; $i<$diff;$i++) {
                    $msg.=';;';
                }
            }
            fwrite($fh, $msg.PHP_EOL);
        }
        fclose($fh);
        echo 'Report '.$file.' READY '.PHP_EOL;
    }

    public function export_sritems()
    {
        $this->load->model('exportexcell_model');
        $res = $this->exportexcell_model->export_sritems();
    }

    public function convert_sritems()
    {
        $this->load->model('sritems_model');
        // $res = $this->sritems_model->convert_sritems();
        $res = $this->sritems_model->convert_srspecial();
    }

    public function sritems_images()
    {
        $this->load->model('sritems_model');
        // $res = $this->sritems_model->sritems_images();
        $res = $this->sritems_model->srspecial_images();
    }

    public function fixarrive()
    {
        $this->db->select('o.order_id, o.order_num, o.update_usr, o.item_id, s.order_shipping_id, t.order_shipaddr_id, c.arrive_date');
        $this->db->from('ts_orders o')->join('ts_order_shippings s','s.order_id=o.order_id')->join('ts_order_shipaddres t','o.order_id = t.order_id');
        $this->db->join('ts_order_shipcosts c','c.order_shipaddr_id=t.order_shipaddr_id')->where('s.arrive_date',0)->where('c.current',1)->where('o.is_canceled',0);
        $this->db->order_by('o.order_id','desc');
        $dats = $this->db->get()->result_array();
        foreach ($dats as $dat) {
            echo 'Order '.$dat['order_num'].' Date '.date('Y-m-d', $dat['arrive_date']).PHP_EOL;
            // $this->db->where('order_shipping_id', $dat['order_shipping_id']);
            // $this->db->set('arrive_date', $dat['arrive_date']);
            // $this->db->update('ts_order_shippings');
            // $this->db->where('order_shipaddr_id', $dat['order_shipaddr_id']);
            // $this->db->set('arrive_date', $dat['arrive_date']);
            // $this->db->update('ts_order_shipaddres');
        }
//        $this->db->select('o.order_num, p.arrive_date, p.order_shipping_id, s.order_shipaddr_id');
//        $this->db->from('ts_orders o');
//        $this->db->join('ts_order_shippings p','p.order_id=o.order_id');
//        $this->db->join('ts_order_shipaddres s','s.order_id=o.order_id');
//        $this->db->where('s.arrive_date',0);
//        $this->db->where('p.arrive_date > ',0);
//        $this->db->order_by('o.order_id','desc');
//        $datas = $this->db->get()->result_array();
//        foreach ($datas as $data) {
//            $this->db->where('order_shipaddr_id',$data['order_shipaddr_id']);
//            $this->db->set('arrive_date', $data['arrive_date']);
//            $this->db->update('ts_order_shipaddres');
//            echo 'Order '.$data['order_num'].' Arrive '.date('Y-m-d', $data['arrive_date']).PHP_EOL;
//        }
    }

    public function customorderslist()
    {
        $this->db->select('o.order_id, o.order_num, o.order_date, o.customer_name, o.revenue, t.shipdate, t.arrive_date, t.event_date');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings t','o.order_id = t.order_id');
        $this->db->where('o.item_id', $this->config->item('custom_id'));
        $this->db->where('o.order_num >= ',63000);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.brand != ','SR');
        $orders = $this->db->get()->result_array();
        $filename = $this->config->item('upload_path_preload').'custom_orders.csv';
        @unlink($filename);
        $fh = fopen($filename, FOPEN_READ_WRITE_CREATE);
        if ($fh) {
            $msg = 'Order #;Order Date;Customer;Revenue;Ship Date;Arrival Date;Event Date;'.PHP_EOL;
            fwrite($fh, $msg);
            foreach ($orders as $order) {
                $msg=$order['order_num'].';'.date('m/d/Y', $order['order_date']).';"'.$order['customer_name'].'";'.$order['revenue'].';';
                $msg.=date('m/d/Y', $order['shipdate']).';'.date('m/d/Y', $order['arrive_date']).';';
                if (!empty($order['event_date'])) {
                    $msg.=date('m/d/Y', $order['event_date']);
                } else {
                    $msg.="";
                }
                $msg.=';'.PHP_EOL;
                fwrite($fh,$msg);
            }
            fclose($fh);
            echo 'Report '.$filename.' READY'.PHP_EOL;
        } else {
            echo 'Error create file'.PHP_EOL;
        }

    }

    public function prepare_customercode() {
        $this->db->select('order_id');
        $this->db->from('ts_orders');
        $this->db->where('order_date >=', strtotime('2020-01-01'));
        $this->db->where('is_canceled',0);
        $this->db->where('customer_code',NULL);
        $this->db->order_by('order_id', 'desc');
        $orders = $this->db->get()->result_array();
        foreach ($orders as $order) {
            $custcode = uniq_link('3','chars').'-'.uniq_link('10','digits');
            $this->db->where('order_id', $order['order_id']);
            $this->db->set('customer_code', $custcode);
            $this->db->update('ts_orders');
        }
        echo 'Orders Ready '.PHP_EOL;
        $this->db->select('quote_id');
        $this->db->from('ts_quotes');
        $this->db->where('customer_code', NULL);
        $this->db->order_by('quote_id','desc');
        $quotes = $this->db->get()->result_array();
        foreach ($quotes as $quote) {
            $custcode = uniq_link('3','chars').'-'.uniq_link('10','digits');
            $this->db->where('quote_id', $quote['quote_id']);
            $this->db->set('customer_code', $custcode);
            $this->db->update('ts_quotes');
        }
        echo 'All Ready '.PHP_EOL;
    }

    public function update_customers() {
        $this->db->select('order_id, customer_name');
        $this->db->from('ts_orders');
        $this->db->where('brand','SR');
        $orders = $this->db->get()->result_array();
        foreach ($orders as $order) {
            $this->db->select('*');
            $this->db->from('ts_customers');
            $this->db->where('customer_name', $order['customer_name']);
            $customer = $this->db->get()->row_array();
            if (ifset($customer,'customer_id','')!=='') {
                $this->db->where('order_id', $order['order_id']);
                $this->db->set('customer_id', $customer['customer_id']);
                $this->db->update('ts_orders');
            }
        }
        echo 'All Ready '.PHP_EOL;
    }

    public function sbitems_list()
    {
        ini_set('memory_limit', '-1');
        $this->db->select('i.*, vi.vendor_item_number, vi.vendor_item_name, vi.vendor_item_cost, vi.vendor_item_exprint, vi.vendor_item_setup, v.vendor_name')
            ->select('ip.item_price_print as price_print , ip.item_price_setup as price_setup , ip.item_sale_print as sale_print , ip.item_sale_setup as sale_setup')
            ->from('sb_items i')->join("sb_vendor_items vi","vi.vendor_item_id=i.vendor_item_id","left")
            ->join("vendors v","v.vendor_id=vi.vendor_item_vendor","left")->join('sb_item_prices ip','ip.item_price_itemid=i.item_id','left')
            ->where('i.brand','BT')->order_by('item_number');
        $items = $this->db->get()->result_array();
        $heads = [
            'Item #', 'Item name', "Item active?", "Item new?", "Item Template", "Lead A", "Lead B", "Lead C", "Material", "Weight (in lbs)", "Item Size (in)",
            "Internal Keywords", "Page URL", 'Meta Title', 'Meta description', 'Meta Keywords', 'Item attribute - row 1', 'Item attribute - row 2',
            'Item Page - bottom Text', 'Type of options (color, size, etc)', 'Colors', 'Number of Items in Cartoon', 'Cartoon Width (in)', 'Cartoon Height (in)',
            'Cartoon Depth (in)', 'Special charges (shipping) per each Item', 'Special charges (shipping) per order', 'Special offer', 'Vendor Item #',
            'Vendor Item Name', 'Vendor Item Cost', 'Vendor Item Exprint Cost', 'Vendor Item Setup Cost', 'Vendor Name', 'Qty Price 1', 'Price 1', 'Sale Price 1',
            'Qty Price 2', 'Price 2', 'Sale Price 2', 'Qty Price 3', 'Price 3', 'Sale Price 3', 'Qty Price 4', 'Price 4', 'Sale Price 4', 'Qty Price 5',
            'Price 5', 'Sale Price 5', 'Qty Price 6', 'Price 6', 'Sale Price 6', 'Qty Price 7', 'Price 7', 'Sale Price 7', 'Qty Price 8', 'Price 8', 'Sale Price 8',
            'Qty Price 9', 'Price 9', 'Sale Price 9', 'Qty Price 10', 'Price 10', 'Sale Price 10',
            'Exprint Price', 'Exprint Sale Price', 'Setup Price','Setup Sale Price'
        ];
        for ($i=1; $i<13; $i++) {
            array_push($heads,'Imprit Location '.$i);
            array_push($heads,'Imprit Size '.$i);
        }
        $filename = $this->config->item('upload_path_preload').'bt_items_list.xlsx';
//        @unlink($filename);
//        $fh = fopen($filename, FOPEN_WRITE_CREATE);
//        $msg = implode($heads,';');
//        fwrite($fh, $msg.PHP_EOL);
        $itemidx = 0;
        foreach ($items as $item) {
            // colors
            $this->db->select('group_concat(item_color) as color')->from('sb_item_colors')->where('item_color_itemid', $item['item_id']);
            $coldat = $this->db->get()->row_array();
            $color = $coldat['color'];
            // Prices
            $prices = [];
            if ($item['item_template']=='Stressball') {
                $this->db->select('*')->from('sb_item_prices')->where('item_price_itemid', $item['item_id']);
                $pricedat = $this->db->get()->row_array();
                $bases = $this->config->item('price_types');
                foreach ($bases as $based) {
                    if (!empty($pricedat['item_price_'.$based['base']]) || !empty($pricedat['item_sale_'.$based['base']])) {
                        $prices[] = [
                            'qty' => $based['base'],
                            'price' => $pricedat['item_price_'.$based['base']],
                            'sale' => $pricedat['item_sale_'.$based['base']],
                        ];
                    }
                }
            } else {
                $this->db->select('*')->from('sb_promo_price')->where('item_id', $item['item_id'])->order_by('item_qty');
                $promodats = $this->db->get()->result_array();
                foreach ($promodats as $promodat) {
                    $prices[] = [
                        'qty' => $promodat['item_qty'],
                        'price' => $promodat['price'],
                        'sale' => $promodat['sale_price'],
                    ];
                }
            }
            if (count($prices)<10) {
                for ($j=count($prices); $j<10; $j++) {
                    $prices[] = [
                        'qty' => '',
                        'price' => '',
                        'sale' => '',
                    ];
                }
            }
//            if ($item['item_number']=='23-PP002') {
//                var_dump($prices);
//                die();
//            }
            // Imprints
            $this->db->select('item_inprint_location, item_inprint_size')->from('sb_item_inprints')->where('item_inprint_item',$item['item_id']);
            $impritems = $this->db->get()->result_array();
            $imprints = [];
            foreach ($impritems as $impritem) {
                $imprints[] = [
                    'location' => $impritem['item_inprint_location'],
                    'size' => $impritem['item_inprint_size'],
                ];
            }
            if (count($imprints)<12) {
                for ($j=count($imprints); $j<12; $j++) {
                    $imprints[] = [
                        'location' => '',
                        'size' => '',
                    ];
                }
            }
            $items[$itemidx]['prices'] = $prices;
            $items[$itemidx]['colors'] = $color;
            $items[$itemidx]['imprints'] = $imprints;
            $itemidx++;
        }
        $this->load->model('exportexcell_model');
        $res = $this->exportexcell_model->fullitems_list($filename, $heads, $items);
        if ($res['result']==1) {
            echo 'File '.$filename.' READY'.PHP_EOL;
        }
//        fclose($fh);
    }

    public function hide_payments()
    {
        // $this->db->select('*')->from('ts_order_payments')->where('order_id > ', 42941); // 42941
        $this->db->select('*')->from('ts_order_payments')->order_by('order_payment_id', 'desc')->limit(400); // 42941
        $payments = $this->db->get()->result_array();
        foreach ($payments as $payment) {
            // echo 'Order ' . $payment['order_id'] . ' CC # ' . $payment['cardnum'] . PHP_EOL;
            $cardnum = $payment['cardnum'];
            if (!empty($payment['cardnum'])) {
                $newcc = hide_cardnumber($cardnum);
                $newcvv = str_repeat('X',strlen($payment['cardcode']));
                $this->db->set('cardnum',$newcc);
                $this->db->set('cardcode',$newcvv);
                $this->db->where('order_payment_id', $payment['order_payment_id']);
                $this->db->update('ts_order_payments');
            }
        }
        echo 'Change log '.PHP_EOL;
        $this->db->select('*')->from('ts_order_paymentlog')->order_by('order_paymentlog_id','desc');
        $paylogs = $this->db->get()->result_array();
        foreach ($paylogs as $paylog) {
            if (!empty($paylog['card_num'])) {
                $newcardn = hide_cardnumber($paylog['card_num']);
                $this->db->where('order_paymentlog_id', $paylog['order_paymentlog_id']);
                $this->db->set('card_num', $newcardn);
                $this->db->update('ts_order_paymentlog');
            }
        }
    }

    public function generate_secret()
    {
        // GA init
        $this->load->library('GoogleAuthenticator');
        $ga = new GoogleAuthenticator();
        // Email init
        $this->load->library('email');
        $email_conf = array(
            'protocol' => 'sendmail',
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'mailtype' => 'html',
        );
        $this->email->initialize($email_conf);
        $email_from = 'admin@bluetrack.com';
        $email_cc = 'to_german@yahoo.com';
        $this->db->select('*')->from('users')->where('user_status',1)->where('user_status',1);
        $users = $this->db->get()->result_array();
        foreach ($users as $user) {
            $secret = $ga->generateSecret();
            $this->db->where('user_id', $user['user_id']);
            $this->db->set('user_secret', $secret);
            $this->db->update('users');
//            // URL
//            $usrlogin = $user['userlogin'];
//            if (empty($usrlogin)) {
//                $usrlogin = $user['user_email'];
//            }
//            $url = $ga->getUrl($usrlogin, 'lift.bluetrack.com', $secret);
            echo 'Email '.$user['user_email'].' Secret '.$secret.PHP_EOL;
//            echo 'URL '.$url.PHP_EOL;
//            $options = [
//                'user_name' => $user['user_name'],
//                'secret' => $secret,
//                'url' => $url,
//                'manual_url' => 'https://support.google.com/accounts/answer/1066447?hl=en',
//            ];
//            $message_body = $this->load->view('messages/secret_update_view', $options, TRUE);
//            $this->email->to($user['user_email']);
//            $this->email->cc($email_cc);
//            // $this->email->bcc($this->config->item('developer_email'));
//            $this->email->from($email_from);
//            $mail_subj = 'Update account security';
//            $this->email->subject($mail_subj);
//            $this->email->message($message_body);
//            $this->email->send();
//            $this->email->clear(TRUE);
//            echo 'Email Send'.PHP_EOL;
        }
    }

    public function updatechecklist()
    {
        $inputFileType = 'Xlsx';
        $this->load->config('uploader');
        $inputFileName = $this->config->item('upload_path_preload').'list_customers-paidbycheck.xlsx';
        $outFileName = $this->config->item('upload_path_preload').'list_customers-paidbycheck_finale.xlsx';
        @unlink($outFileName);
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $worksheet = $spreadsheet->getSheetByName('Final Data');

        $dataArray = $worksheet->toArray();

        $outdata = [];

        $idx = 0;
        foreach ($dataArray as $row) {
            if (!empty($row['0']) && $row[0]!=='Year') {
                $outdata[] = [
                    'idx' => $idx,
                    'order_num' => $row['1'],
                    'order_id' => 0,
                    'email' => '',
                    'billing_address' => '',
                ];
            } else {
                $outdata[] = [
                    'idx' => $idx,
                    'order_num' => '',
                ];
            }
            $idx++;
        }
        // Check orders
        $idx = 0;
        foreach ($outdata as $item) {
            if (!empty($item['order_num'])) {
                $this->db->select('*')->from('ts_orders')->where('order_num', $item['order_num']);
                $order = $this->db->get()->row_array();
                if (ifset($order,'order_id',0) > 0) {
                    $outdata[$idx]['order_id'] = $order['order_id'];
                    $outdata[$idx]['email'] = $order['customer_email'];
                    if (!empty($order['order_system']) && $order['order_system']=='new') {
                        // echo 'New Order '.$order['order_num'].PHP_EOL;
                        $outdata[$idx]['billing_address'] = $this->_order_biladdress($order['order_id']);
                    }
                }
            }
            $idx++;
        }
        // write date to xls file
        foreach ($outdata as $item) {
            if (!empty($item['order_id'])) {
                $coordY = $item['idx']+1;
                $worksheet->setCellValue('G'.$coordY,$item['email']);
                $worksheet->setCellValue('H'.$coordY,$item['billing_address']);
            }
        }
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        // $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // $writer->setIncludeCharts(true);
        $writer->save($outFileName);

        echo 'Ended '.PHP_EOL;
    }

    private function _order_biladdress($order_id)
    {
        $billaddr = '';
        $this->db->select('b.order_billing_id, b.company, b.customer_name, b.address_1, b.address_2, b.city, b.zip, tc.country_iso_code_2 as cntcode, coalesce(t.state_code) as stcode');
        $this->db->from('ts_order_billings b');
        $this->db->join('ts_countries tc', 'b.country_id = tc.country_id');
        $this->db->join('ts_states t', 'b.state_id = t.state_id', 'left');
        $this->db->where('b.order_id', $order_id);
        $bildat = $this->db->get()->row_array();
        if (ifset($bildat, 'order_billing_id',0) > 0) {
            if ($order_id==42663) {
                echo 'Bill Address 42663'.PHP_EOL;
            }
            if (!empty($bildat['company'])) {
                $billaddr.=$bildat['company'].PHP_EOL;
            }
            if (!empty($bildat['customer_name'])) {
                $billaddr.=$bildat['customer_name'].PHP_EOL;
            }
            $billaddr.=$bildat['address_1'].PHP_EOL;
            if (!empty($bildat['address_2'])) {
                $billaddr.=$bildat['address_2'].PHP_EOL;
            }
            $billaddr.=$bildat['city'];
            if (!empty($bildat['stcode'])) {
                $billaddr.=' '.$bildat['stcode'];
            }
            $billaddr.=' '.$bildat['zip'];
            if ($order_id==42663) {
                echo $billaddr;
            }
        }
        return $billaddr;
    }

    public function inventory_rest()
    {
        $daterest = strtotime('2022-01-01');
        $invents = [];

        $this->db->select('inventory_item_id, item_num, item_name')->from('ts_inventory_items')->order_by('item_num');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            $this->db->select('inventory_color_id, color')->from('ts_inventory_colors')->where('inventory_item_id',$item['inventory_item_id'])->order_by('color_order');
            $colors = $this->db->get()->result_array();
            foreach ($colors as $color) {
                $avgprice = 0;
                $this->db->select('count(inventory_income_id) as cnt, sum(income_qty) as rest, sum(income_qty*income_price) as totalinc')->from('ts_inventory_incomes')->where(['inventory_color_id' => $color['inventory_color_id'],'income_date < ' => $daterest]);
                $restincome = $this->db->get()->row_array();
                if ($restincome['cnt']!==0) {
                    $restqty = $restincome['rest'];
                    $resttotal = $restincome['totalinc'];
                    if ($restqty !=0) {
                        $avgprice = round($resttotal/$restqty,3);
                    } else {
                        echo 'Item '.$item['item_num'].' Color '.$color['color'].PHP_EOL;
                    }

                }
                $this->db->select('count(inventory_outcome_id) as cnt, sum(outcome_qty) as outcom')->from('ts_inventory_outcomes')->where(['inventory_color_id' => $color['inventory_color_id'],'outcome_date < ' => $daterest]);
                $restoutcome = $this->db->get()->row_array();
                if ($restoutcome['cnt']!==0) {
                    $restqty = $restqty - $restoutcome['outcom'];
                    $resttotal = $restqty * $avgprice;
                }
                if ($restqty != 0) {
                    $out[] = [
                        'item_num' => $item['item_num'],
                        'item_name' => $item['item_name'],
                        'color' => $color['color'],
                        'rest' => $restqty,
                        'price' => $avgprice,
                        'total' => $resttotal,
                    ];
                }
            }
        }
        if (count($out) > 0) {
            $this->load->config('uploader');
            $filename = $this->config->item('upload_path_preload').'inventory_rest_2021.csv';
            @unlink($filename);
            $fh = fopen($filename,'a+');
            $msg='Item #;Item;Color;QTY;Price;Total;'.PHP_EOL;
            fwrite($fh, $msg);
            foreach ($out as $row) {
                $msg = $row['item_num'].';"'.$row['item_name'].'";'.$row['color'].';'.$row['rest'].';'.$row['price'].';'.$row['total'].';'.PHP_EOL;
                fwrite($fh, $msg);
            }
            fclose($fh);
            echo 'File '.$filename.' ready'.PHP_EOL;
        }
    }

    public function inventory_rest_fix()
    {
        // Get Items
        $this->db->select('*')->from('ts_inventory_items')->order_by('item_num');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            $this->db->select('*')->from('ts_inventory_colors')->where('inventory_item_id', $item['inventory_item_id'])->order_by('color');
            $colors = $this->db->get()->result_array();
            foreach ($colors as $color) {
                // Get total income
                $this->db->select('count(*) as cnt, sum(income_qty) as total_income, sum(income_expense) as total_exp')->from('ts_inventory_incomes')->where('inventory_color_id', $color['inventory_color_id']);
                $incomeres = $this->db->get()->row_array();
                if ($incomeres['cnt']>0) {
                    // Count outcome
                    $this->db->select('count(*) as cnt, sum(outcome_qty) as total_outcome')->from('ts_inventory_outcomes')->where('inventory_color_id', $color['inventory_color_id']);
                    $outcomeres = $this->db->get()->row_array();
                    if ($outcomeres['total_outcome']!=$incomeres['total_exp'] && $outcomeres['cnt']>0) {
                        $diff = $incomeres['total_exp'] - $outcomeres['total_outcome'];
                        echo 'Item '.$item['item_num'].' - '.$item['item_name'].' Color '.$color['color'].' Balance Diff '.$diff.' Outcome '.$outcomeres['total_outcome'].' Expense '.$incomeres['total_exp'].PHP_EOL;
                        $this->_inventory_expand_fix($color['inventory_color_id']);
                    }
                }
            }

        }
    }

    private function _inventory_expand_fix($inventory_color_id)
    {
        $this->db->select('*')->from('ts_inventory_incomes')->where('inventory_color_id', $inventory_color_id)->order_by('income_date','asc');
        $incomes = $this->db->get()->result_array();
        // Set expand = 0
        foreach ($incomes as $income) {
            $this->db->where('inventory_income_id', $income['inventory_income_id']);
            $this->db->set('income_expense',0);
            $this->db->update('ts_inventory_incomes');
        }
        // Get Outcome
        $this->db->select('*')->from('ts_inventory_outcomes')->where('inventory_color_id', $inventory_color_id)->order_by('outcome_date','asc');
        $outcomes = $this->db->get()->result_array();
        foreach ($outcomes as $outcome) {
            $qtyout = $outcome['outcome_qty'];
            $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
            $this->db->from('ts_inventory_incomes');
            $this->db->where('inventory_color_id', $inventory_color_id);
            $this->db->having('leftqty > 0');
            $this->db->order_by('income_date');
            $candidats = $this->db->get()->result_array();
            foreach ($candidats as $candidat) {
                if ($qtyout > $candidat['leftqty']) {
                    $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                    $ordinv = $candidat['leftqty'];
                } else {
                    $newexp = $candidat['income_expense'] + $qtyout;
                    $ordinv = $qtyout;
                }
                // echo 'QTY '.$qtyout.' New Expens '.$newexp.' Get INV '.$ordinv.PHP_EOL;
                $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                $this->db->set('income_expense', $newexp);
                $this->db->update('ts_inventory_incomes');
                // Insert to order inventory
//                $this->db->set('order_id', $outcome['order_id']);
//                $this->db->set('inventory_income_id', $candidat['inventory_income_id']);
//                $this->db->set('qty',$ordinv);
//                $this->db->insert('ts_order_inventory');
                $qtyout= $qtyout - $candidat['leftqty'];
                if ($qtyout <= 0 ) {
                    break;
                }
            }
        }
        return true;
    }
    public function fix_printreport_prices()
    {
        ini_set('memory_limit', '-1');
        $brands = ['SR', 'SB'];
        $changes = [];
        foreach ($brands as $brand) {
            $this->db->select('oa.*, o.order_num');
            $this->db->from('ts_order_amounts oa');
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            $this->db->where('oa.printshop',1);
            $this->db->where('(oa.shipped+oa.kepted+oa.misprint) > ',0);
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand',['SB','BT']);
            }
            $this->db->order_by('oa.printshop_date');
            $amnts = $this->db->get()->result_array();

            foreach ($amnts as $amnt) {
                echo 'Order # '.$amnt['order_num'].PHP_EOL;
                $this->db->select('sum(oi.qty*i.income_price) as totalrev, sum(oi.qty) as totalqty, count(oi.order_inventory_id) as cnt');
                $this->db->from('ts_order_inventory oi');
                $this->db->join('ts_inventory_incomes i', 'i.inventory_income_id=oi.inventory_income_id');
                $this->db->where('oi.amount_id', $amnt['amount_id']);
                $baseprice = $this->db->get()->row_array();
                if ($baseprice['cnt']>0) {
                    // Calc new price
                    $newprice = round($baseprice['totalrev']/$baseprice['totalqty'],3);
                    if (round($amnt['price'],3)!==$newprice) {
                        // echo 'Calc Price '.$newprice.' Rep Price '.$amnt['price'].PHP_EOL;
                        $newtotal = ($amnt['shipped']+$amnt['kepted']+$amnt['misprint'])*($newprice+$amnt['extracost']);
                        $newtotal+=($amnt['orangeplate']*$amnt['orangeplate_price']);
                        $newtotal+=($amnt['blueplate']*$amnt['blueplate_price']);
                        $newtotal+=($amnt['beigeplate']*$amnt['beigeplate_price']);
                        $changes[] = [
                            'order_num' => $amnt['order_num'],
                            'date' => date('m/d/Y', $amnt['printshop_date']),
                            'oldprice' => round($amnt['price'],3),
                            'oldtotal' => $amnt['printshop_total'],
                            'newprice' => $newprice,
                            'newtotal' => $newtotal,
                            'diff' => ($newtotal-$amnt['printshop_total']),
                            'amount' => $amnt['amount_id'],
                        ];
                    }
                }
            }
        }
        if (count($changes)>0) {
            $this->load->config('uploader');
            $file = $this->config->item('upload_path_preload').'fix_printreport.csv';
            @unlink($file);
            $fh = fopen($file, FOPEN_WRITE_CREATE);
            $head = 'Order #;Date,Old Price;Old Total;New Price;New Total;Diff;'.PHP_EOL;
            fwrite($fh, $head);
            foreach ($changes as $change) {
                $msg=$change['order_num'].';'.$change['date'].';'.$change['oldprice'].';'.$change['oldtotal'].';';
                $msg.=$change['newprice'].';'.$change['newtotal'].';'.$change['diff'].';'.PHP_EOL;
                fwrite($fh, $msg);
            }
            fclose($fh);
            echo 'File '.$file.' ready'.PHP_EOL;
        }

    }

    public function transform_trackpackages()
    {
        ini_set("memory_limit","-1");
        $this->db->select('sp.track_code, ic.item_qty, o.order_num, ic.order_itemcolor_id, o.shipdate, sp.deliver_service'); // sa.item_qty
        $this->db->from('ts_order_shippacks sp');
        $this->db->join('ts_order_shipaddres sa', 'sp.order_shipaddr_id=sa.order_shipaddr_id');
        $this->db->join('ts_orders o', 'o.order_id=sa.order_id');
        $this->db->join('ts_order_items oi', 'o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors ic','ic.order_item_id=oi.order_item_id');
        $this->db->where("coalesce(sp.track_code,'') != ''");
        $packs = $this->db->get()->result_array();
        foreach ($packs as $pack) {
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->set('created_by', 1);
            $this->db->set('updated_by', 1);
            $this->db->set('order_itemcolor_id', $pack['order_itemcolor_id']);
            $this->db->set('qty', $pack['item_qty']);
            $this->db->set('trackdate', $pack['shipdate']);
            $this->db->set('trackservice', $pack['deliver_service']);
            $this->db->set('trackcode', $pack['track_code']);
            $this->db->insert('ts_order_trackings');
            echo 'Order # '.$pack['order_num'].' add Track'.PHP_EOL;
        }
        // Add empty tracks
        $this->db->select('o.order_num, o.order_id, o.shipdate, ic.order_itemcolor_id')->from('ts_orders o')->join('ts_order_items oi','o.order_id=oi.order_id')->join('ts_order_itemcolors ic','ic.order_item_id=oi.order_item_id')->where(['o.is_canceled'=>0, 'o.order_system'=> 'new'])->order_by('order_id','desc');
        $orderitms = $this->db->get()->result_array();
        foreach ($orderitms as $orderitm) {
            $this->db->select('count(tracking_id) as cnt')->from('ts_order_trackings')->where('order_itemcolor_id', $orderitm['order_itemcolor_id']);
            $trackres = $this->db->get()->row_array();
            if ($trackres['cnt']==0) {
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->set('created_by', 1);
                $this->db->set('updated_by', 1);
                $this->db->set('order_itemcolor_id', $orderitm['order_itemcolor_id']);
                $this->db->set('qty', 0);
                $this->db->set('trackdate', null);
                $this->db->set('trackservice', 'UPS');
                $this->db->set('trackcode', '');
                $this->db->insert('ts_order_trackings');
                echo 'Order # '.$orderitm['order_num'].' add Track Template'.PHP_EOL;
            }
        }
    }


    public function customitemsreport()
    {
        $years = [2019,2022,2023,2024];
        $this->load->config('uploader');
        foreach ($years as $year) {
            $datbgn = strtotime($year.'-01-01');
            $nxtyear = intval($year)+1;
            $datend = strtotime($nxtyear.'-01-01');
            $this->db->select('*')->from('ts_orders')->where(['item_id' => -3, 'order_date >= ' => $datbgn, 'order_date < ' => $datend])->order_by('order_date');
            $orders = $this->db->get()->result_array();
            $outs = [];
            foreach ($orders as $order) {
                $this->db->select('group_concat(i.item_description) as itmname')->from('ts_order_itemcolors i')->join('ts_order_items o','o.order_item_id=i.order_item_id')->where('o.order_id', $order['order_id']);
                $itmres = $this->db->get()->row_array();
                $itemname = ifset($itmres,'itmname','Custom Item');
                $outs[] = [
                    'date' => date('m/d/Y', $order['order_date']),
                    'order_num' => $order['order_num'],
                    'canceled' => $order['is_canceled']==1 ? 'YES' : 'NO',
                    'revenue' => $order['revenue'],
                    'profit' => $order['profit'],
                    'profit_perc' => $order['profit_perc']==null ? 'Proj' : $order['profit_perc'],
                    'qty' => $order['order_qty'],
                    'item_description' => $itemname,
                    'customer' => $order['customer_name'],
                    'ship_amount' => $order['shipping'],
                ];
            }
            $filenorm = $this->config->item('upload_path_preload').'customitem_orders_'.$year.'.xlsx';
            @unlink($filenorm);
            $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Custom Item Orders '.$year);
            $sheet->setCellValue('A1', 'Date');
            $sheet->setCellValue('B1','Order #');
            $sheet->setCellValue('C1','Cancelled');
            $sheet->setCellValue('D1', 'Revenue');
            $sheet->setCellValue('E1', 'Profit');
            $sheet->setCellValue('F1', 'Profit %');
            $sheet->setCellValue('G1', 'Quantity');
            $sheet->setCellValue('H1', 'Item Description');
            $sheet->setCellValue('I1', 'Customer');
            $sheet->setCellValue('J1', 'Shipping Amount');
            $nrow = 2;
            foreach ($outs as $out) {
                $sheet->setCellValue('A'.$nrow, $out['date']);
                $sheet->setCellValue('B'.$nrow,$out['order_num']);
                $sheet->setCellValue('C'.$nrow,$out['canceled']);
                $sheet->setCellValue('D'.$nrow, $out['revenue']);
                $sheet->setCellValue('E'.$nrow, $out['profit']);
                $sheet->setCellValue('F'.$nrow, $out['profit_perc']);
                $sheet->setCellValue('G'.$nrow, $out['qty']);
                $sheet->setCellValue('H'.$nrow, $out['item_description']);
                $sheet->setCellValue('I'.$nrow, $out['customer']);
                $sheet->setCellValue('J'.$nrow, $out['ship_amount']);
                $nrow++;
            }
            $writer = new Xlsx($spreadsheet); // instantiate Xlsx
            $writer->save($filenorm);    // download file
            echo 'File '.$filenorm.' ready'.PHP_EOL;
        }
    }

    public function updateprintdate()
    {
        $this->load->model('orders_model');
        $this->orders_model->order_schedule_transform();
    }

    public function emaillist()
    {
        $orders = $leads = $signup = [];
        $datebgn = strtotime(date('2019-08-23'));
        // Orders
        $this->db->select('toc.contact_emal as email')->from('ts_orders o')->join('ts_order_contacts toc','o.order_id = toc.order_id');
        $this->db->where('o.order_date >= ', $datebgn);
        $this->db->where('o.is_canceled',0);
        $this->db->where('coalesce(toc.contact_emal,\'\') != ','');
        $this->db->where_in('brand',['SB','BT']);
        $lists = $this->db->get()->result_array();
        foreach ($lists as $list) {
            if (array_key_exists($list['email'], $orders)===FALSE) {
                array_push($orders, $list['email']);
            }
        }
        // Leads
        $this->db->select('lead_mail as email')->from('ts_leads')->where('update_date >= ','2019-08-23')->where_in('brand',['SB','BT'])->where('coalesce(lead_mail,\'\') != ','');
        $lists = $this->db->get()->result_array();
        foreach ($lists as $list) {
            if (array_key_exists($list['email'], $leads)===FALSE) {
                array_push($leads, $list['email']);
            }
        }
        // signup
        $this->db->select('email_sendermail as email')->from('ts_emails')->where('email_date >= ','2019-08-23')->where('email_type','Signups')->where_in('brand',['SB','BT']);
        $lists = $this->db->get()->result_array();
        foreach ($lists as $list) {
            if (array_key_exists($list['email'], $signup)===FALSE) {
                array_push($signup, $list['email']);
            }
        }
        // Create file
        $filenorm = $this->config->item('upload_path_preload').'emails_list.xlsx';
        @unlink($filenorm);
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Email Lists');
        $sheet->setCellValue('A1', 'Orders');
        $sheet->setCellValue('B1','Leads');
        $sheet->setCellValue('C1','Sign Up');
        $numpp = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A'.$numpp, $order);
            $numpp++;
        }
        $numpp = 2;
        foreach ($leads as $lead) {
            $sheet->setCellValue('B'.$numpp, $lead);
            $numpp++;
        }
        $numpp = 2;
        foreach ($signup as $list) {
            $sheet->setCellValue('C'.$numpp, $list);
            $numpp++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $writer->save($filenorm);    // download file
        echo 'File '.$filenorm.' ready'.PHP_EOL;
    }

    public function order_invamount_transform()
    {
        $this->load->model('orders_model');
        // $this->orders_model->order_invamount_srtransform();
        // $this->orders_model->order_invamount_sbtransform();
        $this->orders_model->order_invamount();
    }

    public function customleads()
    {
        $leaddata = [];
        $dbgn = strtotime('2022-10-23');
        $this->db->select('profit_week, profit_year, datebgn, dateend')->from('netprofit')->where(['profit_month' => NULL, 'datebgn >= '=>  $dbgn]);
        $weeks = $this->db->get()->result_array();
        foreach ($weeks as $week) {
            // $this->db->select('if(brand=\'SR\', \'SR\', \'SB\') as brandname, count(lead_id) as cnt')->from('ts_leads')->where(['unix_timestamp(update_date) >= ' => $week['datebgn'], 'unix_timestamp(update_date) < ' => $week['dateend'], 'lead_item_id' => '-3'])->group_by('brandname');
            $this->db->select('brand as brandname, count(custom_quote_id) as cnt')->from('ts_custom_quotes')->where(['unix_timestamp(date_add) >= ' => $week['datebgn'], 'unix_timestamp(date_add) < ' => $week['dateend']])->group_by('brandname');
            $data = $this->db->get()->result_array();
            echo $this->db->last_query().PHP_EOL;
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $leaddata[] = [
                        'week' => $week['profit_week'],
                        'year' => $week['profit_year'],
                        'datebgn' => date('m/d/Y', $week['datebgn']),
                        'dateend' => date('m/d/Y', $week['dateend']),
                        'brand' => $item['brandname'],
                        'leads' => $item['cnt'],
                    ];
                }
            }
        }
        // Build leads report
        // Create file
        $filenorm = $this->config->item('upload_path_preload').'customforms.xlsx';
        @unlink($filenorm);
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Custom Leads');
        $sheet->setCellValue('A1', 'Week');
        $sheet->setCellValue('B1','Year');
        $sheet->setCellValue('C1','Date Bgn');
        $sheet->setCellValue('D1','Date End');
        $sheet->setCellValue('E1','Brand');
        $sheet->setCellValue('F1','Leads');
        $numpp = 2;
        foreach ($leaddata as $lead) {
            $sheet->setCellValue('A'.$numpp, $lead['week']);
            $sheet->setCellValue('B'.$numpp, $lead['year']);
            $sheet->setCellValue('C'.$numpp, $lead['datebgn']);
            $sheet->setCellValue('D'.$numpp, $lead['dateend']);
            $sheet->setCellValue('E'.$numpp, $lead['brand']);
            $sheet->setCellValue('F'.$numpp, $lead['leads']);
            $numpp++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $writer->save($filenorm);    // download file
        echo 'File '.$filenorm.' ready'.PHP_EOL;
    }


    public function readnewitems() {
        $this->load->config('shipping');
        $filenorm = $this->config->item('upload_path_preload').'step1_bluetrack-item-db_INTERNAL.xlsx';
        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filenorm);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $numpp = 0;
        $items = [];
        foreach ($sheetData as $itemrow) {
            if ($numpp > 0) {
                $itemdata = $this->db->select('item_id, item_name')->from('sb_items')->where('item_number',$itemrow['A'])->get()->row_array();
                if (ifset($itemdata,'item_id', 0)==0) {
                    echo 'Item '.$itemrow['A'].' Not Found'.PHP_EOL;
                    die();
                } else {
                    $status = (strtoupper($itemrow['C'])=='YES' ? 1 : 0);
                    $items[] = [
                        'item_id' => $itemdata['item_id'],
                        'item_number' => $itemrow['A'],
                        'item_name' => $itemrow['B'],
                        'item_active' => $status,
                        'vendor_item' => $itemrow['E'],
                        'vendor' => $itemrow['D'],
                    ];
                }
            }
            $numpp++;
        }

        foreach ($items as $item) {
            if ($item['item_active']==0) {
                $this->db->where('item_id', $item['item_id']);
                $this->db->set('item_active',0);
                $this->db->update('sb_items');
            } else {
                // Add vendor Item
                if (substr($item['vendor_item'],0,1)=='S') {
                    $this->db->select('vendor_id, vendor_zipcode')->from('vendors')->where('vendor_name','Bella');
                    $vendordat = $this->db->get()->row_array();
                    $vendor_id = $vendordat['vendor_id'];
                    $vendor_zip = $vendordat['vendor_zipcode'];
                } else {
                    $vendor_id = $this->config->item('inventory_vendor');
                    $vendor_zip = $this->config->item('zip');
                }
                $vendor_cnt = $this->config->item('default_country');
                $this->db->set('vendor_item_vendor', $vendor_id);
                $this->db->set('vendor_item_number', $item['vendor_item']);
                $this->db->set('vendor_item_name', $item['item_name']);
                $this->db->set('vendor_item_zipcode', $vendor_zip);
                $this->db->set('item_shipcountry', $vendor_cnt);
                $this->db->insert('sb_vendor_items');
                $vendor_item_id = $this->db->insert_id();
                if (substr($item['vendor_item'],0,1)=='i') {
                    $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $item['vendor_item']);
                    $invdat = $this->db->get()->row_array();
                    $invent_id = $invdat['inventory_item_id'];
                    $this->db->select('avg(price) as price')->from('ts_inventory_colors')->where('inventory_item_id', $invent_id);
                    $pricedat = $this->db->get()->row_array();
                    $this->db->where('vendor_item_id', $vendor_item_id);
                    $this->db->set('vendor_item_cost', $pricedat['price']);
                    $this->db->update('sb_vendor_items');
                }
                $this->db->where('item_id', $item['item_id']);
                $this->db->set('vendor_item_id', $vendor_item_id);
                $this->db->set('item_active', 1);
                if (substr($item['vendor_item'],0,1)=='i') {
                    $this->db->set('printshop_inventory_id', $invent_id);
                }
                $this->db->update('sb_items');
                if (substr($item['vendor_item'],0,1)=='i') {
                    // Update colors
                    $colors = $this->db->select('color, inventory_color_id')->from('ts_inventory_colors')->where('inventory_item_id', $invent_id)->get()->result_array();
                    $this->db->where('item_color_itemid', $item['item_id']);
                    $this->db->delete('sb_item_colors');
                    foreach ($colors as $color) {
                        $this->db->set('item_color_itemid', $item['item_id']);
                        $this->db->set('item_color', $color['color']);
                        $this->db->set('printshop_color_id', $color['inventory_color_id']);
                        $this->db->insert('sb_item_colors');
                    }
                }
            }
            echo 'Item '.$item['item_name'].' # '.$item['item_number'].' updated '.PHP_EOL;
        }
        $this->db->select('item_id, item_number, item_name')->from('sb_items')->where('brand','SR');
        $sritems = $this->db->get()->result_array();
        foreach ($sritems as $sritem) {
            $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $sritem['item_number']);
            $invdat = $this->db->get()->row_array();
            if (ifset($invdat,'inventory_item_id',0) > 0) {
                $this->db->where('item_id', $sritem['item_id']);
                $this->db->set('printshop_inventory_id', $invdat['inventory_item_id']);
                $this->db->update('sb_items');
            } else {
                echo 'Item '.$sritem['item_number'].' NOT Found';
            }
        }
    }

    public function updatesrorders()
    {
        echo 'Start SR orders transform'.PHP_EOL;
        $this->db->select('oi.order_item_id, v.item_number, o.order_num')->from('ts_order_items oi')->join('v_itemsearch v','oi.item_id=v.item_id')->join('ts_orders o','oi.order_id=o.order_id')->where('o.brand' , 'SR')->where('v.item_id > 0')->where('oi.inventory_item_id',NULL)->order_by('o.order_num');
        $orditems = $this->db->get()->result_array();
        foreach ($orditems as $orditem) {
            $invitm = $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $orditem['item_number'])->get()->row_array();
            $inv_item = $invitm['inventory_item_id'];
            $this->db->where('order_item_id', $orditem['order_item_id']);
            $this->db->set('inventory_item_id', $inv_item);
            $this->db->update('ts_order_items');
            // Colors
            $ordcolors = $this->db->select('order_itemcolor_id, item_color')->from('ts_order_itemcolors')->where('order_item_id', $orditem['order_item_id'])->get()->result_array();
            foreach ($ordcolors as $ordcolor) {
                $colordat = $this->db->select('color, inventory_color_id')->from('ts_inventory_colors')->where('inventory_item_id', $inv_item)->like('color', $ordcolor['item_color'],'after')->get()->row_array();
                if (ifset($colordat,'inventory_color_id',0) > 0) {
                    $this->db->where('order_itemcolor_id', $ordcolor['order_itemcolor_id']);
                    $this->db->set('inventory_color_id', $colordat['inventory_color_id']);
                    $this->db->update('ts_order_itemcolors');
                } else {
                    echo 'Color '.$ordcolor['item_color'].' Order '.$orditem['order_num'].' Item '.$orditem['item_number'].' not found '.PHP_EOL;
                }
            }
        }
        // Quotes
        echo 'Start SR quotes transform'.PHP_EOL;
        $this->db->select('qi.quote_item_id, v.item_number, q.quote_number')->from('ts_quote_items qi')->join('v_itemsearch v','qi.item_id=v.item_id')->join('ts_quotes q','qi.quote_id=q.quote_id')->where('q.brand' , 'SR')->where('v.item_id > 0')->where('qi.inventory_item_id',NULL)->order_by('q.quote_number');
        $quoteitems = $this->db->get()->result_array();
        foreach ($quoteitems as $orditem) {
            $invitm = $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $orditem['item_number'])->get()->row_array();
            $inv_item = $invitm['inventory_item_id'];
            $this->db->where('quote_item_id', $orditem['quote_item_id']);
            $this->db->set('inventory_item_id', $inv_item);
            $this->db->update('ts_quote_items');
            // Colors
            $ordcolors = $this->db->select('quote_itemcolor_id, item_color')->from('ts_quote_itemcolors')->where('quote_item_id', $orditem['quote_item_id'])->get()->result_array();
            foreach ($ordcolors as $ordcolor) {
                $colordat = $this->db->select('color, inventory_color_id')->from('ts_inventory_colors')->where('inventory_item_id', $inv_item)->like('color', $ordcolor['item_color'],'after')->get()->row_array();
                if (ifset($colordat,'inventory_color_id',0) > 0) {
                    $this->db->where('quote_itemcolor_id', $ordcolor['quote_itemcolor_id']);
                    $this->db->set('inventory_color_id', $colordat['inventory_color_id']);
                    $this->db->update('ts_quote_itemcolors');
                } else {
                    echo 'Color '.$ordcolor['item_color'].' Quote '.$orditem['quote_number'].' Item '.$orditem['item_number'].' not found '.PHP_EOL;
                }
            }
        }
    }

    public function updatesborders()
    {
        echo 'Start SB orders transform'.PHP_EOL;
//        $this->db->select('oi.order_item_id, v.item_number, v.printshop_inventory_id, o.order_num')->from('ts_order_items oi')->join('v_itemsearch v','oi.item_id=v.item_id')->join('ts_orders o','oi.order_id=o.order_id')->where_in('o.brand' , ['SB','BT'])
//            ->where('v.item_id > 0')->where('oi.inventory_item_id is not NULL')->where('print_date is not NULL')->where('shipped_date',0)->order_by('o.order_num');
//        $orditems = $this->db->get()->result_array();
//        foreach ($orditems as $orditem) {
//            echo 'Order # '.$orditem['order_num'].PHP_EOL;
//            $invitm = $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $orditem['item_number'])->get()->row_array();
//            $inv_item = $invitm['inventory_item_id'];
//            $this->db->where('order_item_id', $orditem['order_item_id']);
//            $this->db->set('inventory_item_id', $orditem['printshop_inventory_id']);
//            $this->db->update('ts_order_items');
            // Colors
//            $ordcolors = $this->db->select('order_itemcolor_id, item_color')->from('ts_order_itemcolors')->where('order_item_id', $orditem['order_item_id'])->get()->result_array();
//            foreach ($ordcolors as $ordcolor) {
//                $colordat = $this->db->select('color, inventory_color_id')->from('ts_inventory_colors')->where('inventory_item_id', $orditem['printshop_inventory_id'])->like('color', $ordcolor['item_color'],'after')->get()->row_array();
//                if (ifset($colordat,'inventory_color_id',0) > 0) {
//                    $this->db->where('order_itemcolor_id', $ordcolor['order_itemcolor_id']);
//                    $this->db->set('inventory_color_id', $colordat['inventory_color_id']);
//                    $this->db->update('ts_order_itemcolors');
//                } else {
//                    echo 'Color '.$ordcolor['item_color'].' Order '.$orditem['order_num'].' Item '.$orditem['item_number'].' not found '.PHP_EOL;
//                }
//            }
//        }
        // Quotes
        echo 'Start SB quotes transform'.PHP_EOL;
        $this->db->select('qi.quote_item_id, v.item_number, q.quote_number')->from('ts_quote_items qi')->join('v_itemsearch v','qi.item_id=v.item_id')->join('ts_quotes q','qi.quote_id=q.quote_id')->where('q.brand' , 'SR')->where('v.item_id > 0')->where('qi.inventory_item_id',NULL)->order_by('q.quote_number');
        $quoteitems = $this->db->get()->result_array();
        foreach ($quoteitems as $orditem) {
            $invitm = $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $orditem['item_number'])->get()->row_array();
            $inv_item = $invitm['inventory_item_id'];
            $this->db->where('quote_item_id', $orditem['quote_item_id']);
            $this->db->set('inventory_item_id', $inv_item);
            $this->db->update('ts_quote_items');
            // Colors
            $ordcolors = $this->db->select('quote_itemcolor_id, item_color')->from('ts_quote_itemcolors')->where('quote_item_id', $orditem['quote_item_id'])->get()->result_array();
            foreach ($ordcolors as $ordcolor) {
                $colordat = $this->db->select('color, inventory_color_id')->from('ts_inventory_colors')->where('inventory_item_id', $inv_item)->like('color', $ordcolor['item_color'],'after')->get()->row_array();
                if (ifset($colordat,'inventory_color_id',0) > 0) {
                    $this->db->where('quote_itemcolor_id', $ordcolor['quote_itemcolor_id']);
                    $this->db->set('inventory_color_id', $colordat['inventory_color_id']);
                    $this->db->update('ts_quote_itemcolors');
                } else {
                    echo 'Color '.$ordcolor['item_color'].' Quote '.$orditem['quote_number'].' Item '.$orditem['item_number'].' not found '.PHP_EOL;
                }
            }
        }
    }

    public function testquote()
    {
//        $this->load->config('uploader');
//        $pathsh = $this->config->item('itemimages_relative');
//        $pathfl = $this->config->item('itemimages');
//        $items = $this->db->select('item_id, item_number, item_name, main_image')->from('sb_items')->where(['brand' => 'BT', 'item_active' => 1])->order_by('item_number')->get()->result_array();
//        foreach ($items as $item) {
//            if (!empty($item['main_image'])) {
//                $image = str_replace($pathsh, $pathfl, $item['main_image']);
//                if (file_exists($image)) {
//                    list($width, $height) = getimagesize($image);
//                    if (round($width,-1) !== round($height,-1)) {
//                        echo 'Item # '.$item['item_number'].' '.$item['item_name'].' Main Image issue W '.$width.' H '.$height.PHP_EOL;
//                    }
//                }
//            }
//            // Get image images
//            $lists = $this->db->select('item_img_id, item_img_name, item_img_order')->from('sb_item_images')->where('item_img_item_id', $item['item_id'])->get()->result_array();
//            foreach ($lists as $list) {
//                if ($list['item_img_order'] > 1) {
//                    $image = str_replace($pathsh, $pathfl, $list['item_img_name']);
//                    if (file_exists($image)) {
//                        list($width, $height) = getimagesize($image);
//                        if (round($width,-1) !== round($height,-1)) {
//                            echo 'Item # '.$item['item_number'].' '.$item['item_name'].' Image # '.$list['item_img_order'].' issue W '.$width.' H '.$height.PHP_EOL;
//                        }
//                    }
//                }
//            }
//        }
//        echo 'That is All, folk '.PHP_EOL;
        $this->load->model('email_model');
        $this->email_model->generate_quota(26662); // 26662
    }

    public function rebuild_item_titles()
    {
        $this->db->select('item_id, item_number, item_name, item_number, item_meta_title')->from('sb_items')->where('brand','BT');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            if (empty($item['item_name'])) {
                echo 'Item ID '.$item['item_id'].' Empty Name'.PHP_EOL;die();
            } else {
                $metanew = str_replace(['Vesrion A'],'', $item['item_name']);
                $verpos = strpos($metanew,'Version');
                if ($verpos > 0) {
                    $version = substr($metanew, $verpos+8);
                    $metanew = substr($metanew, 0, $verpos-2).'- STRESSBALLS.com&reg; - Custom Printed - Ver '.$version;
                    echo $item['item_number'].' Meta '.$metanew.PHP_EOL;
                } else {
                    $metanew.=' - STRESSBALLS.com&reg; - Custom Printed';
                }
                $this->db->where('item_id', $item['item_id']);
                $this->db->set('item_meta_title', $metanew);
                $this->db->update('sb_items');
            }
        }
    }

    public function update_shippedorders()
    {
        $this->load->model('orders_model');
        $this->orders_model->update_shipped_orders();
    }

//    public function updateprintready() {
//        $this->db->select('oic.order_itemcolor_id, o.order_num, o.print_ready')->from('ts_order_itemcolors oic')->join('ts_order_items oi','oi.order_item_id=oic.order_item_id');
//        $this->db->join('ts_orders o','o.order_id=oi.order_id')->where('o.print_ready > ',0);
//        $items = $this->db->get()->result_array();
//        foreach ($items as $item) {
//            $this->db->where('order_itemcolor_id', $item['order_itemcolor_id']);
//            $this->db->set('print_ready', $item['print_ready']);
//            $this->db->update('ts_order_itemcolors');
//            echo 'Order '.$item['order_num'].' Updated '.PHP_EOL;
//        }
//        $this->db->select('oi.order_item_id, o.order_num, o.plates_ready')->from('ts_order_items oi')->join('ts_orders o','o.order_id=oi.order_id')->where('o.plates_ready > ',0);
//        $items = $this->db->get()->result_array();
//        foreach ($items as $item) {
//            $this->db->where('order_item_id', $item['order_item_id']);
//            $this->db->set('plates_ready', $item['plates_ready']);
//            $this->db->update('ts_order_items');
//            echo 'Order '.$item['order_num'].' Updated '.PHP_EOL;
//        }
//    }

    public function testcolors()
    {
        $startdate = strtotime('2025-03-17');
        $this->db->select('o.order_num, o.order_date, o.brand, ii.inventory_item_id, ii.item_num, ii.item_name, oic.item_color, oic.order_itemcolor_id');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','oi.order_id = o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = oi.inventory_item_id');
        $this->db->where('o.order_date >= ', $startdate);
        $this->db->where('oi.inventory_item_id is not null');
        $this->db->where('oic.inventory_color_id',null);
        $this->db->order_by('o.order_id');
        $orders = $this->db->get()->result_array();

        foreach ($orders as $order) {
            $newcolor = $this->_inventory_color($order['inventory_item_id'], $order['item_color']);
            if (!empty($newcolor)) {
                $this->db->where('order_itemcolor_id', $order['order_itemcolor_id']);
                $this->db->set('inventory_color_id', $newcolor);
                $this->db->update('ts_order_itemcolors');
            } else {
                echo 'Order # '.$order['order_num'].' ('.$order['brand'].')'.' '.$order['item_num'].'-'.$order['item_name'].' '.$order['item_color'].PHP_EOL;
            }
        }
    }

    private function _inventory_color($inventory_item_id, $color)
    {
        $outcolor = '';
        $colordat = $this->db->select('max(inventory_color_id) as color_id, count(inventory_color_id) as cnt')->from('ts_inventory_colors')->where('inventory_item_id', $inventory_item_id)->get()->row_array();
        if ($colordat['cnt']==1) {
            // Only one color
            $outcolor = $colordat['color_id'];
        } else {
            $invdat = $this->db->select('inventory_color_id')->from('ts_inventory_colors')->where(['inventory_item_id' => $inventory_item_id, 'color' => $color])->get()->row_array();
            if (ifset($invdat, 'inventory_color_id', 0) > 0) {
                $outcolor = $invdat['inventory_color_id'];
            } else {
                $invdat = $this->db->select('inventory_color_id')->from('ts_inventory_colors')->where('inventory_item_id', $inventory_item_id)->like('color', $color, 'after')->get()->row_array();
                if (ifset($invdat, 'inventory_color_id', 0) > 0) {
                    $outcolor = $invdat['inventory_color_id'];
                }
            }
        }
        return $outcolor;
    }

    public function filledorders() {
        $brands = [
            'SB', 'SR',
        ];
        $startdate = strtotime('2024-01-01');
        $enddate = strtotime('2025-06-18');
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        foreach ($brands as $brand) {
            $this->db->select('o.order_id, o.order_date, o.order_num, o.customer_name, coalesce(p.cnt,0) approved, o.revenue, o.balance_manage, o.balance_term');
            $this->db->from('ts_orders o');
            $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
            $this->db->where('o.order_date >=', $startdate);
            $this->db->where('o.order_date < ', $enddate);
            $this->db->where('o.is_canceled',0);
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
            $this->db->order_by('o.order_num');
            $results = $this->db->get()->result_array();
            $orders = [];
            foreach ($results as $result) {
                if ($result['approved'] > 0) {
                    $result['approved'] = 'Approved';
                } else {
                    $result['approved'] = 'Not Approved';
                }
                $result['fulfilled'] = $result['shipped'] = '';
                // Items
                $this->db->select('GROUP_CONCAT(v.item_number) as item_number, GROUP_CONCAT(v.item_name) as item_name, sum(oi.item_qty) as item_qty');
                $this->db->from('ts_order_items oi')->join('v_itemsearch v','v.item_id=oi.item_id')->where('oi.order_id', $result['order_id']);
                $itemdat = $this->db->get()->row_array();
                $result['item_number'] = $itemdat['item_number'];
                $result['item_name'] = $itemdat['item_name'];
                $result['item_qty'] = $itemdat['item_qty'];
                $this->db->select('GROUP_CONCAT(oic.item_color) as itemcolor')->from('ts_order_items oi')->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
                $colordat = $this->db->where('oi.order_id', $result['order_id'])->get()->row_array();
                $result['item_color'] = $colordat['itemcolor'];
                // Fullfillment
                $this->db->select('count(amount_id) as cnt, sum(shipped) as shipped')->from('ts_order_amounts')->where('order_id', $result['order_id']);
                $fulfil = $this->db->get()->row_array();
                if ($fulfil['cnt'] > 0) {
                    $fulfill = intval($fulfil['shipped']);
                    if ($fulfill >= intval($result['item_qty'])) {
                        $result['fulfilled'] = '100%';
                    } else {
                        $result['fulfilled'] = round($fulfill/$result['item_qty']*100,1).'%';
                    }
                }
                // Shipping
                $this->db->select('count(ot.tracking_id) as cnt, sum(ot.qty) as shipqty')->from('ts_order_trackings ot')->join('ts_order_itemcolors oic','ot.order_itemcolor_id = oic.order_itemcolor_id')
                ->join('ts_order_items oi','oi.order_item_id = oic.order_item_id')->where('oi.order_id', $result['order_id']);
                $shipdat = $this->db->get()->row_array();
                if ($shipdat['cnt'] > 0) {
                    $shipqty = $shipdat['shipqty'];
                    if ($shipqty >= intval($result['item_qty'])) {
                        $result['shipped'] = '100%';
                    } else {
                        $result['shipped'] = round($shipqty/$result['item_qty']*100,1).'%';
                    }
                }
                // Balance Owed
                if (intval($result['balance_manage'])==1) {
                    $result['balance'] = 'Paid';
                } else {
                    $result['balance'] = 'Balance Owed';
                }
                $orders[] = $result;
            }
            // Build XLS
            $this->load->config('uploader');
            $filenorm = $this->config->item('upload_path_preload').$brand.'_orders.xlsx';
            @unlink($filenorm);
            $titles = ['Date','Order #','Customer','Qty','Item #','Item Name','Item Color','Approved?','% Fulfilled','% Shipping','Paid?'];
            $numpp = 1;
            $ncel = 1;
            $cols=[];
            $cellname = '';
            foreach ($titles as $title) {
                $newcell = $cellname.chr(64 + $numpp);
                array_push($cols, $newcell);
                $numpp++;
            }
            $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($brand.' Orders');
            $ncol = 0;
            foreach ($titles as $title) {
                $sheet->setCellValue($cols[$ncol].'1', $title);
                $ncol++;
            }
            $numpp = 2;
            foreach ($orders as $order) {
                $sheet->setCellValue('A'.$numpp, date('m/d/y', $order['order_date']));
                $sheet->setCellValue('B'.$numpp, $order['order_num']);
                $sheet->setCellValue('C'.$numpp, $order['customer_name']);
                $sheet->setCellValue('D'.$numpp, $order['item_qty']);
                $sheet->setCellValue('E'.$numpp, $order['item_number']);
                $sheet->setCellValue('F'.$numpp, $order['item_name']);
                $sheet->setCellValue('G'.$numpp, $order['item_color']);
                $sheet->setCellValue('H'.$numpp, $order['approved']);
                $sheet->setCellValue('I'.$numpp, $order['fulfilled']);
                $sheet->setCellValue('J'.$numpp, $order['shipped']);
                $sheet->setCellValue('K'.$numpp, $order['balance']);
                $numpp++;
            }
            $writer = new Xlsx($spreadsheet); // instantiate Xlsx
            $writer->save($filenorm);    // download file
            echo 'File '.$filenorm.' ready'.PHP_EOL;
        }
    }

    public function leadsreport()
    {
        $reportbgn = strtotime('2018-01-01');
        $this->db->select('lu.user_id, u.user_leadname, count(l.lead_id) as cnt');
        $this->db->from('ts_lead_users lu');
        $this->db->join('users u','u.user_id = lu.user_id');
        $this->db->join('ts_leads l','l.lead_id=lu.lead_id');
        $this->db->where('unix_timestamp(l.update_date) >= ', $reportbgn);
        $this->db->group_by('lu.user_id, u.user_leadname');
        $leadusers = $this->db->get()->result_array();
        $startdate = strtotime('2025-06-23');
        $enddate = strtotime('+7 day', $startdate)-1;
        $out=[];
        while (1==1) {
            // echo 'Start '.date('d.m.Y H:i:s', $startdate).' - End '.date('d.m.Y H:i:s', $enddate).PHP_EOL;
            $this->db->select('u.user_leadname as username, count(l.lead_id) as cnt');
            $this->db->from('ts_lead_users tlu');
            $this->db->join('users u','on u.user_id = tlu.user_id');
            $this->db->join('ts_leads l','l.lead_id=tlu.lead_id');
            $this->db->where('unix_timestamp(create_date) >= ', $startdate)->where('unix_timestamp(create_date) <= ', $enddate);
            $this->db->group_by('u.user_leadname');
            $crdat = $this->db->get()->result_array();
            $repl = '';
            foreach ($crdat as $item) {
                $repl.=$item['username'].', ';
            }
            $repl = substr($repl,0,-2);
            $this->db->select('count(lead_id) as cnt')->from('ts_leads')->where('unix_timestamp(create_date) >= ', $startdate)->where('unix_timestamp(create_date) <= ', $enddate);
            $newdat = $this->db->get()->row_array();
            $this->db->select('count(lead_id) as cnt')->from('ts_leads')->where('unix_timestamp(update_date) >= ', $startdate)->where('unix_timestamp(update_date) <= ', $enddate);
            $updat = $this->db->get()->row_array();
            $out[] = [
                'period' => date('m/d/Y', $startdate).' - '.date('m/d/Y', $enddate),
                'replica' => $repl,
                'updleads' => $updat['cnt'],
                'newleads' => $newdat['cnt'],
            ];
            // new dates
            $enddate = $startdate - 1;
            $startdate = strtotime('-7 day', $startdate);

            if ($enddate <= strtotime('2018-01-01')) {
                break;
            }
        }
        echo count($out).PHP_EOL;
        // Build XLS
        $this->load->config('uploader');
        $filenorm = $this->config->item('upload_path_preload').'leads_perweek.xlsx';
        @unlink($filenorm);
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads');
        $sheet->setCellValue('A1', 'Period');
        $sheet->setCellValue('B1','Sales Repl');
        $sheet->setCellValue('C1','# of worked leads');
        $sheet->setCellValue('D1','New Leads');
        $numpp = 2;
        foreach ($out as $item) {
            $sheet->setCellValue('A'.$numpp, $item['period']);
            $sheet->setCellValue('B'.$numpp, $item['replica']);
            $sheet->setCellValue('C'.$numpp, $item['updleads']);
            $sheet->setCellValue('D'.$numpp, $item['newleads']);
            $numpp++;
        }
        // Userss
        $i=1;
        foreach ($leadusers as $user) {
            $startdate = strtotime('2025-06-23');
            $enddate = strtotime('+7 day', $startdate)-1;
            $out=[];
            while (1==1) {
                // echo 'Start ' . date('d.m.Y H:i:s', $startdate) . ' - End ' . date('d.m.Y H:i:s', $enddate) . PHP_EOL;
                $this->db->select('count(l.lead_id) as cnt')->from('ts_leads l');
                $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
                $this->db->where('unix_timestamp(l.create_date) >= ', $startdate)->where('unix_timestamp(l.create_date) <= ', $enddate);
                $this->db->where('lu.user_id', $user['user_id']);
                $newdat = $this->db->get()->row_array();
                $this->db->select('count(l.lead_id) as cnt')->from('ts_leads l');
                $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
                $this->db->where('unix_timestamp(l.update_date) >= ', $startdate)->where('unix_timestamp(l.update_date) <= ', $enddate);
                $this->db->where('lu.user_id', $user['user_id']);
                $updat = $this->db->get()->row_array();
                if ($newdat['cnt']+$updat['cnt'] > 0) {
                    $out[] = [
                        'period' => date('m/d/Y', $startdate) . ' - ' . date('m/d/Y', $enddate),
                        'updleads' => $updat['cnt'],
                        'newleads' => $newdat['cnt'],
                    ];
                }
                // new dates
                $enddate = $startdate - 1;
                $startdate = strtotime('-7 day', $startdate);
                if ($enddate <= strtotime('2018-01-01')) {
                    break;
                }
            }
            echo $user['user_leadname'].' found '.count($out).PHP_EOL;
            // Add Tab
            $spreadsheet->createSheet();
            $sheet = $spreadsheet->setActiveSheetIndex($i);
            echo 'Sheet ACTIVE '.$i.PHP_EOL;
            $sheet->setTitle($user['user_leadname'].' Leads');
            $sheet->setCellValue('A1', 'Period');
            $sheet->setCellValue('B1','# of worked leads');
            $sheet->setCellValue('C1','New Leads');
            $numpp = 2;
            foreach ($out as $item) {
                $sheet->setCellValue('A'.$numpp, $item['period']);
                $sheet->setCellValue('B'.$numpp, $item['updleads']);
                $sheet->setCellValue('C'.$numpp, $item['newleads']);
                $numpp++;
            }
            $i++;
        }
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $writer->save($filenorm);    // download file
        echo 'File '.$filenorm.' ready'.PHP_EOL;
    }
}
