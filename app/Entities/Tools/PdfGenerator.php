<?php

namespace BookStack\Entities\Tools;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Dompdf\Dompdf;

class PdfGenerator
{
    const ENGINE_DOMPDF = 'dompdf';
    const ENGINE_WKHTML = 'wkhtml';
    const ENGINE_COMMAND = 'command';

    /**
     * Generate PDF content from the given HTML content.
     */
    public function fromHtml(string $html): string
    {
        $engine = $this->getActiveEngine();

        if ($engine === self::ENGINE_WKHTML) {
            $pdf = SnappyPDF::loadHTML($html);
            $pdf->setOption('print-media-type', true);
            return $pdf->output();
        } else if ($engine === self::ENGINE_COMMAND) {
            // TODO - Support PDF command
            return '';
        }

        return $this->renderUsingDomPdf($html);
    }

    /**
     * Get the currently active PDF engine.
     * Returns the value of an `ENGINE_` const on this class.
     */
    public function getActiveEngine(): string
    {
        $wkhtmlBinaryPath = config('snappy.pdf.binary');
        if (file_exists(base_path('wkhtmltopdf'))) {
            $wkhtmlBinaryPath = base_path('wkhtmltopdf');
        }

        if (is_string($wkhtmlBinaryPath) && config('app.allow_untrusted_server_fetching') === true) {
            return self::ENGINE_WKHTML;
        }

        return self::ENGINE_DOMPDF;
    }

    protected function renderUsingDomPdf(string $html): string
    {
        $options = config('exports.dompdf');
        $domPdf = new Dompdf($options);
        $domPdf->setBasePath(base_path('public'));

        $domPdf->loadHTML($this->convertEntities($html));
        $domPdf->render();

        return (string) $domPdf->output();
    }

    /**
     * Taken from https://github.com/barryvdh/laravel-dompdf/blob/v2.1.1/src/PDF.php
     * Copyright (c) 2021 barryvdh, MIT License
     * https://github.com/barryvdh/laravel-dompdf/blob/v2.1.1/LICENSE
     */
    protected function convertEntities(string $subject): string
    {
        $entities = [
            '€' => '&euro;',
            '£' => '&pound;',
        ];

        foreach ($entities as $search => $replace) {
            $subject = str_replace($search, $replace, $subject);
        }
        return $subject;
    }
}
