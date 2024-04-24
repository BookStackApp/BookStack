<?php

namespace BookStack\Entities\Tools;

use Knp\Snappy\Pdf as SnappyPdf;
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
            return $this->renderUsingWkhtml($html);
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
        if ($this->getWkhtmlBinaryPath() && config('app.allow_untrusted_server_fetching') === true) {
            return self::ENGINE_WKHTML;
        }

        return self::ENGINE_DOMPDF;
    }

    protected function getWkhtmlBinaryPath(): string
    {
        $wkhtmlBinaryPath = config('exports.snappy.pdf_binary');
        if (file_exists(base_path('wkhtmltopdf'))) {
            $wkhtmlBinaryPath = base_path('wkhtmltopdf');
        }

        return $wkhtmlBinaryPath ?: '';
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

    protected function renderUsingWkhtml(string $html): string
    {
        $snappy = new SnappyPdf($this->getWkhtmlBinaryPath());
        $options = config('exports.snappy.options');
        return $snappy->getOutputFromHtml($html, $options);
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
