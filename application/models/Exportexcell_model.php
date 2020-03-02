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
        $sheet->setCellValue('D2', 'Pantone');
        $sheet->setCellValue('E2', 'Quantity');
        $sheet->setCellValue('F2', 'Cost Ea');
        $sheet->setCellValue('G2', 'Total Cost');
        $j=3;
        foreach ($options['res'] as $row) {
            $price=round($row['price'],3);
            $total=round($row['qty']*$price,2);
            // Write Row
            // $sheet->getStyle('E'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            /* $sheet->getStyle('F'.$j)->getNumberFormat()->setFormatCode("$0#.###");
            $sheet->getStyle('G'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->getStyle('A'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->setActiveSheetIndex($i)
*/
            $sheet->setCellValue('A'.$j, $row['item_num']);
            $sheet->setCellValue('B'.$j, $row['item_name']);
            $sheet->setCellValue('C'.$j, $row['color']);
            $sheet->setCellValue('D'.$j, $row['color_descript']);
            $sheet->setCellValue('E'.$j, $row['qty']);
            $sheet->setCellValue('F'.$j, $price);
            $sheet->setCellValue('G'.$j, $total);
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
        /*
        $styleWhite = array(
            'font' => array(
                'bold' => false,
            ),
            'alignment' => array(
                'horizontal' => Spreadsheet::HORIZONTAL_LEFT,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_NONE
            ),
        );


        $styleGray = array(
            'font' => array(
                'bold' => true,
                'color' => array(
                    'argb' => 'FFFFFFFF')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY
            ),
        );

        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Price Report');
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        */
        // $sheet->getColumnDimension('A')->setAutoSize(); // Item #
        // $sheet->getColumnDimension('B')->setAutoSize(); // Shape/ Color
        // $sheet->getColumnDimension('C')->setAutoSize(); // Color Descript
        // $sheet->getColumnDimension('D')->setAutoSize(); // In Stock
        // $sheet->getColumnDimension('E')->setAutoSize(); // Reserved
        // $sheet->getColumnDimension('F')->setAutoSize(); // Available
        // $sheet->getColumnDimension('G')->setAutoSize(); // Cost Ea
        // $sheet->getColumnDimension('H')->setAutoSize(); // Total Ea
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
        /*
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filesrc);
        $out['result']=$this->success_result;
        $out['url']=$this->config->item('pathpreload').$filename;
        return $out;
        */
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
        $report_name = 'export_inventory_' . (microtime(TRUE) * 10000) . '.xlsx';
        $filename = $this->config->item('upload_path_preload') . $report_name;
        $writer->save($filename);    // download file
        return $report_name;
    }
}