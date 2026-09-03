<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportService
{
    /**
     * Generate PDF response for Internship Certificate (A4 Landscape)
     */
    public function generateCertificatePdf(array $data, string $filename)
    {
        $pdf = Pdf::loadView('reports.certificate_pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Generate PDF response for 20-day Intern Logbook Report (A4 Portrait)
     */
    public function generateLogbookReportPdf(array $data, string $filename)
    {
        $pdf = Pdf::loadView('reports.intern_logbook_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }
}
