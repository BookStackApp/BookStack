<?php

namespace BookStack\Entities\Tools;

use Barryvdh\DomPDF\Facade as DomPDF;
use Barryvdh\Snappy\Facades\SnappyPdf;

class PdfGenerator
{
    /**
     * Generate PDF content from the given HTML content.
     */
    public function fromHtml(string $html): string
    {
        $useWKHTML = config('snappy.pdf.binary') !== false && config('app.allow_untrusted_server_fetching') === true;

        if ($useWKHTML) {
            $pdf = SnappyPDF::loadHTML($html);
            $pdf->setOption('print-media-type', true);
        } else {
            $pdf = DomPDF::loadHTML($html);
        }

        return $pdf->output();
    }
}
