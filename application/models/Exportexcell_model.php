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
}