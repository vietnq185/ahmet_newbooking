<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}

class pjPdf
{
    private $hash = '';

    public function __construct()
    {
        mt_srand();
        $this->hash = mt_rand(1000, 9999);
    }

    function generatePdf($filename, $html)
    {
        require_once(PJ_LIBS_PATH . 'tcpdf/config/lang/eng.php');
        require_once(PJ_LIBS_PATH . 'tcpdf/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetFont('dejavusans', '', 8);

        $pdf->AddPage();
        $pdf->writeHTMLCell(0, null, 0, 0, $html, 0);

        $filename = $this->hash . '_' . $filename;

        $pdf->Output(PJ_UPLOAD_PATH . 'downloads/' . $filename, 'F');
        $filename = PJ_UPLOAD_PATH . 'downloads/' . $filename;
        return $filename;
    }

    function downloadPdf($filename, $html)
    {
        require_once(PJ_LIBS_PATH . 'tcpdf/config/lang/eng.php');
        require_once(PJ_LIBS_PATH . 'tcpdf/tcpdf.php');

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();
        $pdf->writeHTMLCell(0, null, 0, 0, $html, 0);

        $filename = $this->hash . '_' . $filename;

        $pdf->Output($filename, 'D');
    }
}

?>