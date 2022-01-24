<?php

namespace BookStack\Entities\Tools;

use Barryvdh\DomPDF\Facade as DomPDF;
use Barryvdh\Snappy\Facades\SnappyPdf;

class PdfGenerator
{
    const ENGINE_DOMPDF = 'dompdf';
    const ENGINE_WKHTML = 'wkhtml';

    /**
     * Generate PDF content from the given HTML content.
     */
    public function fromHtml(string $html): string
    {
        if ($this->getActiveEngine() === self::ENGINE_WKHTML) {
            $pdf = SnappyPDF::loadHTML($html);
            $pdf->setOption('print-media-type', true);
        } else {
            $pdf = DomPDF::loadHTML($html);
        }

        return $pdf->output();
    }

    /**
     * Get the currently active PDF engine.
     * Returns the value of an `ENGINE_` const on this class.
     */
    public function getActiveEngine(): string
    {
        $useWKHTML = config('snappy.pdf.binary') !== false && config('app.allow_untrusted_server_fetching') === true;
        return $useWKHTML ? self::ENGINE_WKHTML : self::ENGINE_DOMPDF;
    }
}
