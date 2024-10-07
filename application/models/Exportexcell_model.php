<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Exportexcell_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function export_signup($res)
    {

        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet

        $sheet = $spreadsheet->getActiveSheet();

        // manually set table data value
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Signup Email');
        $i = 2;
        foreach ($res as $row) {
            $sheet->setCellValue('A' . $i, date('m/d/y H:i:s', strtotime($row['email_date'])))->setCellValue('B' . $i, $row['email_sendermail']);
            $i++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

        $report_name = 'signup_export_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        // $writer->save('php://output');	// download file
        $writer->save($filename);    // download file
        return $report_name;
    }

    function export_onboatcontent($options) {
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $options['title']);
        $sheet->setCellValue('A2', 'Item #');
        $sheet->setCellValue('B2', 'Shape');
        $sheet->setCellValue('C2', 'Color');
        // $sheet->setCellValue('D2', 'Pantone');
        $sheet->setCellValue('D2', 'Quantity');
        $sheet->setCellValue('E2', 'Cost Ea');
        $sheet->setCellValue('F2', 'Total Cost');

        $j=3;
        foreach ($options['res'] as $row) {
            $price=round($row['price'],3);
            $total=round($row['qty']*$price,2);
            // Write Row
            $sheet->setCellValue('A'.$j, $row['item_num']);
            $sheet->setCellValue('B'.$j, $row['item_name']);
            $sheet->setCellValue('C'.$j, $row['color']);
            // $sheet->setCellValue('D'.$j, $row['color_descript']);
            $sheet->setCellValue('D'.$j, $row['qty']);
            $sheet->setCellValue('E'.$j, $price);
            $sheet->setCellValue('F'.$j, $total);
            $j++;
        }

        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

        $report_name = 'onboat_export_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        return $report_name;
    }

    public function export_inventory($res, $brand) {
        $filename='export_inventory_'.time().'.xls';
        $filesrc=$this->config->item('upload_path_preload').$filename;
        $namesheet = 'inventory_export';
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($namesheet);

        $sheet->setCellValue('A1','Item #');
        $sheet->setCellValue('B1','Shape/ Color');
        $sheet->setCellValue('C1','Color Descript');
        $sheet->setCellValue('D1','%');
        $sheet->setCellValue('E1','In Stock');
        $sheet->setCellValue('F1','Reserved');
        $sheet->setCellValue('G1','Available');
        $sheet->setCellValue('H1','Cost Ea');
        $sheet->setCellValue('I1','Total Ea');
        $numrow=2;
        foreach ($res as $row) {
            if ($row['type']=='item') {
                $sheet->setCellValue('A'.$numrow,$row['item_num']);
                $sheet->setCellValue('B'.$numrow, $row['item_name']);
            } else {
                $sheet->setCellValue('B'.$numrow,$row['item_name']);
                $sheet->setCellValue('C'.$numrow,$row['color_descript']);
            }
            $sheet->setCellValue('D'.$numrow, str_replace('&nbsp;','0',$row['percent']));
            $sheet->setCellValue('E'.$numrow, $row['instock_int']);
            $sheet->setCellValue('F'.$numrow, $row['reserved_int']);
            $sheet->setCellValue('G'.$numrow, $row['availabled_int']);
            $sheet->setCellValue('H'.$numrow, $row['price_int']);
            $sheet->setCellValue('I'.$numrow, $row['total_int']);
            $numrow++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $report_name = 'export_inventory_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        return $report_name;
    }

    public function export_orderreport($data) {
        // Prepare Export file
        ini_set("memory_limit",-1);
        $namesheet = 'report_export';
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($namesheet);

        $sheet->setCellValue('A1','Date');
        $sheet->setCellValue('B1','Order');
        $sheet->setCellValue('C1','Customer');
        $sheet->setCellValue('D1','Shape');
        $sheet->setCellValue('E1','Color');
        $sheet->setCellValue('F1','Shipped');
        $sheet->setCellValue('G1','We Kept');
        $sheet->setCellValue('H1','Misprnt');
        $sheet->setCellValue('I1','%');
        $sheet->setCellValue('J1','Total QTY');
        $sheet->setCellValue('K1','Cost EA');
        $sheet->setCellValue('L1','Add&apos;l Extra');
        $sheet->setCellValue('M1','Total EA');
        $sheet->setCellValue('N1','Total Extra');
        $sheet->setCellValue('O1','Items Cost');
        $sheet->setCellValue('P1','Oran Plate');
        $sheet->setCellValue('Q1','Blue Plate');
        $sheet->setCellValue('R1','Beige Plate');
        $sheet->setCellValue('S1','Total Plate');
        $sheet->setCellValue('T1','Plate Cost');
        $sheet->setCellValue('U1','Total Cost');
        $sheet->setCellValue('V1','Misprint Cost');
        $sheet->setCellValue('W1','Profit $$');
        $sheet->setCellValue('X1','Profit %');
        $j=2;
        foreach ($data as $row) {
            $misprint_proc=($row['shipped']==0 ? 0 : $row['misprint']/$row['shipped']*100);
            $sheet->setCellValue('A'.$j, date('m/d/Y', $row['printshop_date']));
            $sheet->setCellValue('B'.$j, $row['order_num']);
            $sheet->setCellValue('C'.$j, $row['customer_name']);
            $sheet->setCellValue('D'.$j, $row['item_num'].' '.str_replace('Stress Balls', '', $row['item_name']));
            $sheet->setCellValue('E'.$j, $row['color']);
            $sheet->setCellValue('F'.$j, $row['shipped']);
            $sheet->setCellValue('G'.$j, $row['kepted']);
            $sheet->setCellValue('H'.$j, $row['misprint']);
            $sheet->setCellValue('I'.$j,round($misprint_proc,0).'%');
            $sheet->setCellValue('J'.$j, $row['totalitem']);
            $sheet->setCellValue('K'.$j, $row['price']);
            $sheet->setCellValue('L'.$j, $row['extracost']);
            $sheet->setCellValue('M'.$j, round($row['priceea'],3));
            $sheet->setCellValue('N'.$j, round($row['extraitem'],2));
            $sheet->setCellValue('O'.$j, round($row['costitem'],2));
            $sheet->setCellValue('P'.$j, $row['orangeplate']);
            $sheet->setCellValue('Q'.$j, $row['blueplate']);
            $sheet->setCellValue('R'.$j, $row['beigeplate']);
            $sheet->setCellValue('S'.$j, $row['totalplates']);
            $sheet->setCellValue('T'.$j, $row['platescost']);
            $sheet->setCellValue('U'.$j, $row['totalitemcost']);
            $sheet->setCellValue('V'.$j, $row['misprintcost']);
            $sheet->setCellValue('W'.$j, $row['profit']);
            $sheet->setCellValue('X'.$j, $row['profit_perc']);
            $j++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $report_name = 'export_report_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        $url = $this->config->item('pathpreload').$report_name;
        return $url;
    }

    // Export Profit (orders)
    public function export_profitorders($data, $labels) {
        ini_set("memory_limit","-1");
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet

        $sheet = $spreadsheet->getActiveSheet();

        // manually set table data value
        $headcell = 0;
        $headrow = 1;
        foreach ($labels as $lrow) {
            $sheet->setCellValue(getNameFromNumber($headcell).$headrow,$lrow);
            $headcell++;
        }
        $numrow=2;
        foreach ($data as $row) {
            $numcell=0;
            foreach ($row as $key=>$val) {
                $sheet->setCellValue(getNameFromNumber($numcell).$numrow, $val);
                $numcell++;
            }
            $numrow++;
        }

        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

        $report_name = 'profitorder_export_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        return $this->config->item('pathpreload').$report_name;
    }
    // Export Attempts Report
    public function expot_attemptreport($data, $attach, $start) {
        $this->load->library('email');
        if (count($data)>0) {
            $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1','Checkout Start');
            $sheet->setCellValue('B1','Last Activity');
            $sheet->setCellValue('C1','Customer');
            $sheet->setCellValue('D1','Contact');
            $sheet->setCellValue('E1','Address');
            $sheet->setCellValue('F1','Item Name');
            $sheet->setCellValue('G1','Item #');
            $sheet->setCellValue('H1','Qty');
            $sheet->setCellValue('I1','Item Colors');
            $sheet->setCellValue('J1','Imprint');
            $sheet->setCellValue('K1','Item Cost');
            $sheet->setCellValue('L1','Imprint Cost');
            $sheet->setCellValue('M1','Setup');
            $sheet->setCellValue('N1','Rush Price');
            $sheet->setCellValue('O1','Tax');
            $sheet->setCellValue('P1','UPS code');
            $sheet->setCellValue('Q1','Shipping');
            $sheet->setCellValue('R1','Total');
            $sheet->setCellValue('S1','Rush Date');
            $sheet->setCellValue('T1','Art Upload');
            $sheet->setCellValue('U1','User IP');
            $sheet->setCellValue('V1','User Location');
            $sheet->setCellValue('W1','CC details');
            $sheet->setCellValue('X1','Last Entered');
            $i=2;
            foreach ($data as $row) {
                // Write Row
                $sheet->setCellValue('A' . $i, $row['checkout_start']);
                $sheet->setCellValue('B' . $i, $row['last_action']);
                $sheet->setCellValue('C' . $i, $row['user']);
                $sheet->setCellValue('D' . $i, $row['user_contact']);
                $spreadsheet->getActiveSheet()->getStyle('D'.$i)
                    ->getAlignment()
                    ->setWrapText(true);
                $sheet->setCellValue('E' . $i, $row['user_address']);
                $spreadsheet->getActiveSheet()->getStyle('E'.$i)
                    ->getAlignment()
                    ->setWrapText(true);
                $sheet->setCellValue('F' . $i, $row['item_name']);
                $sheet->setCellValue('G' . $i, $row['item_number']);
                $sheet->setCellValue('H' . $i, ($row['item_qty']=='' ? '' : intval($row['item_qty'])));
                $sheet->setCellValue('I' . $i, $row['item_colors']);
                $sheet->setCellValue('J' . $i, $row['imprint']);
                $sheet->setCellValue('K' . $i, ($row['itemcost']=='' ? '' : number_format($row['itemcost'],2,',','')));
                $sheet->setCellValue('L' . $i, ($row['imprintval']=='' ? '' : number_format($row['imprintval'],2,',','')));
                $sheet->setCellValue('M' . $i, ($row['setup']=='' ? '' : number_format($row['setup'],2,',','')));
                $sheet->setCellValue('N' . $i, ($row['rushprice']=='' ? '' : number_format($row['rushprice'],2,',','')));
                $sheet->setCellValue('O' . $i, ($row['tax']=='' ? '' : number_format($row['tax'],2,',','')));
                $sheet->setCellValue('P' . $i, $row['ship_method']);
                $sheet->setCellValue('Q' . $i, ($row['shipping']=='' ? '' : number_format($row['shipping'],2,',','')));
                $sheet->setCellValue('R' . $i, ($row['total']=='' ? '' : number_format($row['total'],2,',','')));
                $sheet->setCellValue('S' . $i, $row['rushdate']);
                $sheet->setCellValue('T' . $i, $row['art']);
                $spreadsheet->getActiveSheet()->getStyle('T'.$i)
                    ->getAlignment()
                    ->setWrapText(true);
                $sheet->setCellValue('U' . $i, $row['user_ip']);
                $sheet->setCellValue('V'  .$i, $row['user_location']);
                $sheet->setCellValue('W'  .$i, $row['cc_details']);
                $sheet->setCellValue('X'  .$i, $row['last_field']);

                $i++;
            }
            $writer = new Xlsx($spreadsheet); // instantiate Xlsx

            $report_name = 'attempt_export_' . (microtime(TRUE) * 10000) . '.xlsx';
            $filename = $this->config->item('upload_path_preload') . $report_name;
            $writer->save($filename);    // download file
            /* Send Mail with attach */
            $mail_to=$this->config->item('mail_research');
            $mail_cc=array('sage@bluetrack.com','shanequa.hall@bluetrack.com', $this->config->item('developer_email'));


            $email_conf = array(
                'protocol'=>'sendmail',
                'charset'=>'utf-8',
                'wordwrap'=>TRUE,
                'mailtype'=>'html',
            );

            $this->email->initialize($email_conf);
            $this->email->to($mail_to);
            $this->email->cc($mail_cc);
            $this->email->from('no-replay@bluetrack.com');
            $this->email->subject('Dayly report about unended checkouts ('.date('m/d/Y',$start).')');
            $mail_body='Report in attachment';
            $this->email->attach($filename);
            if (count($attach)>0) {
                foreach ($attach as $row) {
                    $file=  str_replace('//', '/', $this->config->item('base_upload').$row);
                    if (file_exists($file)) {
                        $this->email->attach($file);
                    }
                }
            }
            $this->email->message($mail_body);
            $this->email->send();
            // echo $ci->email->print_debugger();
            $this->email->clear(TRUE);
            // unlink($filename);
        } else {
            $mail_to=$this->config->item('mail_research');

            $this->load->library('email');

            $email_conf = array(
                'protocol'=>'sendmail',
                'charset'=>'utf-8',
                'wordwrap'=>TRUE,
                'mailtype'=>'html',
            );

            $this->email->initialize($email_conf);
            $this->email->to($mail_to);
            $this->email->from('no-replay@bluetrack.com');
            $this->email->subject('Dayly report about unended checkouts ('.date('m/d/Y',$start).')');
            $mail_body='All checkouts ended successfully';
            $this->email->message($mail_body);
            $this->email->send();
        }
        $this->email->clear();
        $this->email->to('to_german@yahoo.com');
        $this->email->from('no-replay@bluetrack.com');
        $this->email->subject('Dayly report about unended checkouts ('.date('m/d/Y',$start).')');
        $mail_body='Report sends successfully';
        $this->email->message($mail_body);
        $this->email->send();
        $this->email->clear();
        return TRUE;
    }

    public function export_itemdata($results, $options, $flds, $report_name) {
        $cols_array=array(
            '0'=>'A',
            '1'=>'B',
            '2'=>'C',
            '3'=>'D',
            '4'=>'E',
            '5'=>'F',
            '6'=>'G',
            '7'=>'H',
            '8'=>'I',
            '9'=>'J',
            '10'=>'K',
            '11'=>'L',
            '12'=>'M',
            '13'=>'N',
            '14'=>'O',
            '15'=>'P',
            '16'=>'Q',
            '17'=>'R',
            '18'=>'S',
            '19'=>'T',
            '20'=>'U',
            '21'=>'V',
            '22'=>'W',
            '23'=>'X',
            '24'=>'Y',
            '25'=>'Z',
            '26'=>'AA',
            '27'=>'AB',
            '28'=>'AC',
            '29'=>'AD',
            '30'=>'AE',
            '31'=>'AF',
            '32'=>'AG',
            '33'=>'AH',
            '34'=>'AI',
            '35'=>'AJ',
            '36'=>'AK',
            '37'=>'AL',
            '38'=>'AM',
            '39'=>'AN',
            '40'=>'AO',
            '41'=>'AP',
            '42'=>'AQ',
            '43'=>'AR',
            '44'=>'AS',
            '45'=>'AT',
            '46'=>'AU',
            '47'=>'AV',
            '48'=>'AW',
            '49'=>'AX',
            '50'=>'AY',
            '51'=>'AZ',
            '52'=>'BA',
            '53'=>'BB',
            '54'=>'BC',
            '55'=>'BD',
            '56'=>'BE',
            '57'=>'BF',
            '58'=>'BG',
            '59'=>'BH',
            '60'=>'BI',
            '61'=>'BJ',
            '62'=>'BK',
            '63'=>'BL',
            '64'=>'BM',
            '65'=>'BN',
            '66'=>'BO',
            '67'=>'BP',
            '68'=>'BQ',
            '69'=>'BR',
            '70'=>'BS',
            '71'=>'BT',
            '72'=>'BU',
            '73'=>'BV',
            '74'=>'BW',
            '75'=>'BX',
            '76'=>'BY',
            '77'=>'BZ',
        );
        $i=0;
        $min_col='A';
        $max_col='A';
        foreach ($flds as $row) {
            $flds[$i]['column']=$cols_array[$i];
            $max_col=$cols_array[$i];
            $i++;
        }

        /* create report */
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();

//        $styleWhite = array(
//            'font' => array(
//                'bold' => false,
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//            ),
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN,
//                )
//            ),
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_NONE
//            ),
//        );
//
//
//        $styleGray = array(
//            'font' => array(
//                'bold' => true,
//                'color' => array(
//                    'argb' => 'FFFFFFFF')
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//            ),
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN,
//                )
//            ),
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY
//            ),
//        );
        /* sheet */
        $sheet->setTitle('DB Export');
        // $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        // $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//        foreach ($flds as $row) {
//            $sheet->getColumnDimension($row['column'])->setWidth($row['expfield_length']);
//        }
//
//        $spreadsheet->getActiveSheet()->getStyle("{$min_col}1:{$max_col}1")->applyFromArray($styleGray);
        foreach ($flds as $row) {
            $sheet->setCellValue("{$row['column']}1",$row['expfield_description']);
        }
        $i = 2;
        foreach ($results as $row) {
            foreach ($flds as $frow) {
                $sheet->setCellValue("{$frow['column']}" . $i, $row[$frow['expfield_name']]);
            }
            $i++;
        }

        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

        $report_name = 'profitorder_export_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        return ['result' => 1, 'msg' => 'All OK', 'url'=> $this->config->item('pathpreload').$report_name];
    }

    public function export_master_inventory($lists, $file_label) {
        ini_set("memory_limit","-1");
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet

        $sheet = $spreadsheet->getActiveSheet();
        $styleHeadArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '00000000', // argb => FFA0A0A0
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];
        $styleItemArray = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleHeadArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:L1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->setCellValue('A1','Status');
        $sheet->setCellValue('B1','Master #/Seq');
        $sheet->setCellValue('C1','Description');
        $sheet->setCellValue('D1','%');
        $sheet->setCellValue('E1','Maximum');
        $sheet->setCellValue('F1','In Stock');
        $sheet->setCellValue('G1','Reserved');
        $sheet->setCellValue('H1','Available');
        $sheet->setCellValue('I1','Unit');
        $sheet->setCellValue('J1','On Order');
        $sheet->setCellValue('K1','Avg Price');
        $sheet->setCellValue('L1','Total Value');
        $i=2;
        foreach ($lists as $list) {
            if ($list['item_flag']==1) {
                $currow = 'A'.$i.':L'.$i;
                $spreadsheet->getActiveSheet()->getStyle($currow)->applyFromArray($styleItemArray);
            }
            $spreadsheet->getActiveSheet()->getStyle('D'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('G'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('I'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('J'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('K'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('L'.$i)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('A'.$i,$list['status']);
            if ($list['item_flag']==1) {
                $sheet->setCellValue('B'.$i, $list['item_code']);
            } else {
                $sheet->setCellValue('B'.$i, $list['item_seq']);
            }
            $sheet->setCellValue('C'.$i, $list['description']);
            $sheet->setCellValue('D'.$i, $list['percent'].'%');
            $sheet->setCellValue('E'.$i, empty($list['max']) ? '' : QTYOutput($list['max']));
            $sheet->setCellValue('F'.$i, $list['instock']);
            $sheet->setCellValue('G'.$i, empty($list['reserved']) ? '' : QTYOutput($list['reserved']));
            $sheet->setCellValue('H'.$i, $list['available']);
            $sheet->setCellValue('I'.$i, $list['unit']);
            $sheet->setCellValue('J'.$i, empty($list['onorder']) ? '' : QTYOutput($list['onorder']));
            $sheet->setCellValue('K'.$i, MoneyOutput($list['price'],3));
            $sheet->setCellValue('L'.$i, MoneyOutput($list['total']));
            $i++;
        }


        $writer = new Xlsx($spreadsheet); // instantiate Xlsx

        $report_name = $file_label.'_export_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        return ['result' => 1, 'msg' => 'All OK', 'url'=> $this->config->item('pathpreload').$report_name];

    }

    public function export_sritems()
    {
        $filename = $this->config->item('upload_path_preload').'sr_lift-database_items_2023_master.xlsx';
        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet 	= $reader->load($filename);
        $spreadsheet->setActiveSheetIndex(1);
        $sheet_data 	= $spreadsheet->getActiveSheet()->toArray();
        $list 			= [];
        $idx = 0;

        foreach($sheet_data as $row) {
            if ($idx > 1) {
                $url = str_replace('http://www.stressrelievers.com/','',$row[10]);
                $this->db->set('item_number', $row[0]);
                $this->db->set('item_name', $row[1]);
                $this->db->set('price_500', $row[2]);
                $this->db->set('price_1000', $row[3]);
                $this->db->set('price_2000', $row[4]);
                $this->db->set('price_3000', $row[5]);
                $this->db->set('sale_500', $row[6]);
                $this->db->set('sale_1000', $row[7]);
                $this->db->set('sale_2000', $row[8]);
                $this->db->set('sale_3000', $row[9]);
                $this->db->set('item_url', $url);
                $this->db->insert('srspecial_export');
//                $this->db->set('item_num', $row[0]);
//                $this->db->set('item_name', $row[1]);
//                $this->db->set('category', $row[2]);
//                $this->db->set('active', $row[3]);
//                $this->db->set('template', $row[4]);
//                $this->db->set('new', $row[5]);
//                $this->db->set('sale', $row[6]);
//                $this->db->set('top_seller', $row[7]);
//                $this->db->set('sub_categ1', $row[8]);
//                $this->db->set('size', $row[9]);
//                $this->db->set('material', $row[10]);
//                $this->db->set('description', $row[11]);
//                $this->db->set('bullet_1', $row[12]);
//                $this->db->set('bullet_2', $row[13]);
//                $this->db->set('bullet_3', $row[14]);
//                $this->db->set('bullet_4', $row[15]);
//                $this->db->set('similar_1', $row[16]);
//                $this->db->set('similar_2', $row[17]);
//                $this->db->set('similar_3', $row[18]);
//                $this->db->set('similar_4', $row[19]);
//                $this->db->set('vendor', $row[20]);
//                $this->db->set('vendor_item_num', $row[21]);
//                $this->db->set('vendor_item_name', $row[22]);
//                $this->db->set('ship_country', $row[23]);
//                $this->db->set('ship_zip', $row[24]);
//                $this->db->set('interval', $row[25]);
//                $this->db->set('po_notes', $row[26]);
//                $this->db->set('su_price', $row[27]);
//                $this->db->set('su_addl_price', $row[28]);
//                $this->db->set('su_new_setup', $row[29]);
//                $this->db->set('su_repeat_setup', $row[30]);
//                $this->db->set('su_stad_days', $row[31]);
//                $this->db->set('su_rush1_days', $row[32]);
//                $this->db->set('su_rush1_price', $row[33]);
//                $this->db->set('su_rush2_days', $row[34]);
//                $this->db->set('su_rush2_price', $row[35]);
//                $this->db->set('su_pantone', $row[36]);
//                $this->db->set('price_qty1', $row[37]);
//                $this->db->set('price_1', $row[38]);
//                $this->db->set('price_qty2', $row[39]);
//                $this->db->set('price_2', $row[40]);
//                $this->db->set('price_qty3', $row[41]);
//                $this->db->set('price_3', $row[42]);
//                $this->db->set('price_qty4', $row[43]);
//                $this->db->set('price_4', $row[44]);
//                $this->db->set('price_qty5', $row[45]);
//                $this->db->set('price_5', $row[46]);
//                $this->db->set('price_discount', $row[47]);
//                $this->db->set('add_print', $row[48]);
//                $this->db->set('addprint_discount', $row[49]);
//                $this->db->set('new_setup', $row[50]);
//                $this->db->set('newsetup_discount', $row[51]);
//                $this->db->set('repeat_setup', $row[52]);
//                $this->db->set('repeat_discount', $row[53]);
//                $this->db->set('rush1_price', $row[54]);
//                $this->db->set('rush1_discount', $row[55]);
//                $this->db->set('rush2_price', $row[56]);
//                $this->db->set('rush2_discount', $row[57]);
//                $this->db->set('pantone_price', $row[58]);
//                $this->db->set('pantone_discount', $row[59]);
//                $this->db->set('options', $row[60]);
//                $this->db->set('options_images', $row[61]);
//                $this->db->set('options_val', $row[62]);
//                $this->db->set('print_method', $row[63]);
//                $this->db->set('print_colors', $row[64]);
//                $this->db->set('location_1_name', $row[65]);
//                $this->db->set('location_1_size', $row[66]);
//                $this->db->set('location_2_name', $row[67]);
//                $this->db->set('location_2_size', $row[68]);
//                $this->db->set('location_3_name', $row[69]);
//                $this->db->set('location_3_size', $row[70]);
//                $this->db->set('location_4_name', $row[71]);
//                $this->db->set('location_4_size', $row[72]);
//                $this->db->set('location_5_name', $row[73]);
//                $this->db->set('location_5_size', $row[74]);
//                $this->db->set('location_6_name', $row[75]);
//                $this->db->set('location_6_size', $row[76]);
//                $this->db->set('location_7_name', $row[77]);
//                $this->db->set('location_7_size', $row[78]);
//                $this->db->set('location_8_name', $row[79]);
//                $this->db->set('location_8_size', $row[80]);
//                $this->db->set('location_9_name', $row[81]);
//                $this->db->set('location_9_size', $row[82]);
//                $this->db->set('location_10_name', $row[83]);
//                $this->db->set('location_10_size', $row[84]);
//                $this->db->set('location_11_name', $row[85]);
//                $this->db->set('location_11_size', $row[86]);
//                $this->db->set('location_12_name', $row[87]);
//                $this->db->set('location_12_size', $row[88]);
//                $this->db->set('meta_title', $row[89]);
//                $this->db->set('meta_desc', $row[90]);
//                $this->db->set('meta_keywords', $row[91]);
//                $this->db->set('internal_search', $row[92]);
//                $this->db->set('page_url', $row[93]);
//                $this->db->set('item_weight', $row[94]);
//                $this->db->set('ship_special_ea', $row[95]);
//                $this->db->set('box1_qty', $row[96]);
//                $this->db->set('box1_width', $row[97]);
//                $this->db->set('box1_length', $row[98]);
//                $this->db->set('box1_height', $row[99]);
//                $this->db->set('box2_qty', $row[100]);
//                $this->db->set('box2_width', $row[101]);
//                $this->db->set('box2_length', $row[102]);
//                $this->db->set('box2_height', $row[103]);
//                $this->db->set('box3_qty', $row[104]);
//                $this->db->set('box3_width', $row[105]);
//                $this->db->set('box3_length', $row[106]);
//                $this->db->set('box3_height', $row[107]);
//                $this->db->set('box4_qty', $row[108]);
//                $this->db->set('box4_width', $row[109]);
//                $this->db->set('box4_length', $row[110]);
//                $this->db->set('box4_height', $row[111]);
//                $this->db->insert('sritems_export');
            }
            // if ($idx>=90) {
//                break;
//            }
            $idx++;
        }
//        foreach ($sheet->getRowIterator() as $row) {
//            if ($i > 2 && $i < 92) {
//                $cells = $row->getCells();
//                var_dump($cells);die();
//                echo 'Item # '.$cells[0]->getvalue().' Name '.$cells[1]->getvalue();
//                die();
//            }
//            $i++;
//        }

    }

    public function fullitems_list($filename, $heads, $items)
    {
        $cols_array=array(
            '0'=>'A',
            '1'=>'B',
            '2'=>'C',
            '3'=>'D',
            '4'=>'E',
            '5'=>'F',
            '6'=>'G',
            '7'=>'H',
            '8'=>'I',
            '9'=>'J',
            '10'=>'K',
            '11'=>'L',
            '12'=>'M',
            '13'=>'N',
            '14'=>'O',
            '15'=>'P',
            '16'=>'Q',
            '17'=>'R',
            '18'=>'S',
            '19'=>'T',
            '20'=>'U',
            '21'=>'V',
            '22'=>'W',
            '23'=>'X',
            '24'=>'Y',
            '25'=>'Z',
            '26'=>'AA',
            '27'=>'AB',
            '28'=>'AC',
            '29'=>'AD',
            '30'=>'AE',
            '31'=>'AF',
            '32'=>'AG',
            '33'=>'AH',
            '34'=>'AI',
            '35'=>'AJ',
            '36'=>'AK',
            '37'=>'AL',
            '38'=>'AM',
            '39'=>'AN',
            '40'=>'AO',
            '41'=>'AP',
            '42'=>'AQ',
            '43'=>'AR',
            '44'=>'AS',
            '45'=>'AT',
            '46'=>'AU',
            '47'=>'AV',
            '48'=>'AW',
            '49'=>'AX',
            '50'=>'AY',
            '51'=>'AZ',
            '52'=>'BA',
            '53'=>'BB',
            '54'=>'BC',
            '55'=>'BD',
            '56'=>'BE',
            '57'=>'BF',
            '58'=>'BG',
            '59'=>'BH',
            '60'=>'BI',
            '61'=>'BJ',
            '62'=>'BK',
            '63'=>'BL',
            '64'=>'BM',
            '65'=>'BN',
            '66'=>'BO',
            '67'=>'BP',
            '68'=>'BQ',
            '69'=>'BR',
            '70'=>'BS',
            '71'=>'BT',
            '72'=>'BU',
            '73'=>'BV',
            '74'=>'BW',
            '75'=>'BX',
            '76'=>'BY',
            '77'=>'BZ',
            '78'=>'CA',
            '79'=>'CB',
            '80'=>'CC',
            '81'=>'CD',
            '82'=>'CE',
            '83'=>'CF',
            '84'=>'CG',
            '85'=>'CH',
            '86'=>'CI',
            '87'=>'CJ',
            '88'=>'CK',
            '89'=>'CL',
            '90'=>'CM',
            '91'=>'CN',
            '92'=>'CO',
        );
        $i=0;

        /* create report */
        $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();

        /* sheet */
        $sheet->setTitle('DB Export');
        $colidx = 0;
        foreach ($heads as $head) {
            $sheet->setCellValue("{$cols_array[$colidx]}1",$head);
            $colidx++;
        }
        // Body
        $nrow = 2;
        foreach ($items as $item) {
            $sheet->setCellValue( $cols_array[0].$nrow, $item['item_number']);
            $sheet->setCellValue( $cols_array[1].$nrow, $item['item_name']);
            $sheet->setCellValue( $cols_array[2].$nrow, $item['item_active']==1 ? 'Yes' : 'No');
            $sheet->setCellValue( $cols_array[3].$nrow, $item['item_new']==1 ? 'Yes' : 'No');
            $sheet->setCellValue( $cols_array[4].$nrow, $item['item_template']);
            $sheet->setCellValue( $cols_array[5].$nrow, $item['item_lead_a']);
            $sheet->setCellValue( $cols_array[6].$nrow, $item['item_lead_b']);
            $sheet->setCellValue( $cols_array[7].$nrow, $item['item_lead_c']);
            $sheet->setCellValue( $cols_array[8].$nrow, $item['item_material']);
            $sheet->setCellValue( $cols_array[9].$nrow, $item['item_weigth']);
            $sheet->setCellValue( $cols_array[10].$nrow, $item['item_size']);
            $sheet->setCellValue( $cols_array[11].$nrow, $item['item_keywords']);
            $sheet->setCellValue( $cols_array[12].$nrow, $item['item_url']);
            $sheet->setCellValue( $cols_array[13].$nrow, $item['item_meta_title']);
            $sheet->setCellValue( $cols_array[14].$nrow, $item['item_metadescription']);
            $sheet->setCellValue( $cols_array[15].$nrow, $item['item_metakeywords']);
            $sheet->setCellValue( $cols_array[16].$nrow, $item['item_description1']);
            $sheet->setCellValue( $cols_array[17].$nrow, $item['item_description2']);
            $sheet->setCellValue( $cols_array[18].$nrow, $item['bottom_text']);
            $sheet->setCellValue( $cols_array[19].$nrow, $item['options']);
            $sheet->setCellValue( $cols_array[20].$nrow, $item['colors']);
            $sheet->setCellValue( $cols_array[21].$nrow, $item['cartoon_qty']);
            $sheet->setCellValue( $cols_array[22].$nrow, $item['cartoon_width']);
            $sheet->setCellValue( $cols_array[23].$nrow, $item['cartoon_heigh']);
            $sheet->setCellValue( $cols_array[24].$nrow, $item['cartoon_depth']);
            $sheet->setCellValue( $cols_array[25].$nrow, $item['charge_pereach']);
            $sheet->setCellValue( $cols_array[26].$nrow, $item['charge_perorder']);
            $sheet->setCellValue( $cols_array[27].$nrow, $item['special']);
            $sheet->setCellValue( $cols_array[28].$nrow, $item['vendor_item_number']);
            $sheet->setCellValue( $cols_array[29].$nrow, $item['vendor_item_name']);
            $sheet->setCellValue( $cols_array[30].$nrow, $item['vendor_item_cost']);
            $sheet->setCellValue( $cols_array[31].$nrow, $item['vendor_item_exprint']);
            $sheet->setCellValue( $cols_array[32].$nrow, $item['vendor_item_setup']);
            $sheet->setCellValue( $cols_array[33].$nrow, $item['vendor_name']);
            $prices = $item['prices'];
            $ncol = 34;
            foreach ($prices as $price) {
                $sheet->setCellValue( $cols_array[$ncol].$nrow, $price['qty']);
                $ncol++;
                $sheet->setCellValue( $cols_array[$ncol].$nrow, $price['price']);
                $ncol++;
                $sheet->setCellValue( $cols_array[$ncol].$nrow, $price['sale']);
                $ncol++;
            }

            $sheet->setCellValue( $cols_array[$ncol].$nrow, $item['price_print']);
            $ncol++;
            $sheet->setCellValue( $cols_array[$ncol].$nrow, $item['sale_print']);
            $ncol++;
            $sheet->setCellValue( $cols_array[$ncol].$nrow, $item['price_setup']);
            $ncol++;
            $sheet->setCellValue( $cols_array[$ncol].$nrow, $item['sale_setup']);
            $ncol++;
            $imprints = $item['imprints'];
            foreach ($imprints as $imprint) {
                $sheet->setCellValue( $cols_array[$ncol].$nrow, $imprint['location']);
                $ncol++;
                $sheet->setCellValue( $cols_array[$ncol].$nrow, $imprint['size']);
                $ncol++;
            }
            /*
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            $sheet->setCellValue( $cols_array[].$nrow, $item['']);
            */
            $nrow++;
        }
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $writer->save($filename);    // download file

        return ['result' => 1];
    }
}