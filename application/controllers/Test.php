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

}