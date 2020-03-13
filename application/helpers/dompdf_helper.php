<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once 'vendor/autoload.php';
use Dompdf\Dompdf;

function pdf_create($html, $filename='', $stream=TRUE) {
    // require_once("dompdf/dompdf_config.inc.php");

    $dompdf = new Dompdf(['enable_remote'=>true]);
    $dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');


// Render the HTML as PDF
    $dompdf->render();
// Output the generated PDF to Browser
    // $dompdf->stream();
    if ($stream) {
        // $dompdf->stream($filename.".pdf");
        // $dompdf->output($filename.".pdf");
        /// file_put_contents($filename, $output);
        file_put_contents($filename, $dompdf->output());
        return true;
    } else {
        return $dompdf->stream();
    }
}
?>
