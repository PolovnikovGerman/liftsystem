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
}