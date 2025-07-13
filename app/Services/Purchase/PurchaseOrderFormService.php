<?php

namespace App\Services\Purchase;

use App\Exports\PurchaseOrderReissueExport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;
class PurchaseOrderFormService
{
    private const SUPPORTED_TYPES = ['pdf', 'xlsx'];
    
    /**
     * Download the purchase order reissue in either PDF or Excel format.
     *
     * @param string $type The type of file to download ('pdf' or 'xlsx')
     * @param array $purchaseOrderDetails The details of the purchase order
     * @param string $fileName The name of the file to be downloaded
     *
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function downloadOrderReissue(string $type, array $purchaseOrderDetails, string $fileName)
    {
        // Check if the type is supported
        if (!in_array($type, self::SUPPORTED_TYPES, true)) {
            throw new \InvalidArgumentException('サポートされていない出力形式です。サポートされている形式はpdf, xlsxとなります。');
        }

        $pExport = new PurchaseOrderReissueExport($purchaseOrderDetails);

        if ($type === 'pdf') {
            return $this->generatePdf($pExport, $fileName);
        }

        return Excel::download($pExport, $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Generate PDF file
     *
     * @param PurchaseOrderReissueExport $export
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    private function generatePdf(PurchaseOrderReissueExport $export, string $fileName): \Illuminate\Http\Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);

        // Set the font directory and cache directory
        $pdfOptions->set('fontDir', storage_path('dompdf/fonts/'));
        $pdfOptions->set('fontCache', storage_path('dompdf/fonts/'));

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($export->view()->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream($fileName);
    }
}
