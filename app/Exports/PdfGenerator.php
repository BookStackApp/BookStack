<?php

namespace BookStack\Exports;

use BookStack\Exceptions\PdfExportException;
use Dompdf\Dompdf;
use Knp\Snappy\Pdf as SnappyPdf;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class PdfGenerator
{
    const ENGINE_DOMPDF = 'dompdf';
    const ENGINE_WKHTML = 'wkhtml';
    const ENGINE_COMMAND = 'command';

    /**
     * Generate PDF content from the given HTML content.
     * @throws PdfExportException
     */
    public function fromHtml(string $html): string
    {
        return match ($this->getActiveEngine()) {
            self::ENGINE_COMMAND => $this->renderUsingCommand($html),
            self::ENGINE_WKHTML => $this->renderUsingWkhtml($html),
            default => $this->renderUsingDomPdf($html)
        };
    }

    /**
     * Get the currently active PDF engine.
     * Returns the value of an `ENGINE_` const on this class.
     */
    public function getActiveEngine(): string
    {
        if (config('exports.pdf_command')) {
            return self::ENGINE_COMMAND;
        }

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

    /**
     * @throws PdfExportException
     */
    protected function renderUsingCommand(string $html): string
    {
        $command = config('exports.pdf_command');
        $inputHtml = tempnam(sys_get_temp_dir(), 'bs-pdfgen-html-');
        $outputPdf = tempnam(sys_get_temp_dir(), 'bs-pdfgen-output-');

        $replacementsByPlaceholder = [
            '{input_html_path}' => $inputHtml,
            '{output_pdf_path}' => $outputPdf,
        ];

        foreach ($replacementsByPlaceholder as $placeholder => $replacement) {
            $command = str_replace($placeholder, escapeshellarg($replacement), $command);
        }

        file_put_contents($inputHtml, $html);

        $timeout = intval(config('exports.pdf_command_timeout'));
        $process = Process::fromShellCommandline($command);
        $process->setTimeout($timeout);

        $cleanup = function () use ($inputHtml, $outputPdf) {
            foreach ([$inputHtml, $outputPdf] as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        };

        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            $cleanup();
            throw new PdfExportException("PDF Export via command failed due to timeout at {$timeout} second(s)");
        }

        if (!$process->isSuccessful()) {
            $cleanup();
            throw new PdfExportException("PDF Export via command failed with exit code {$process->getExitCode()}, stdout: {$process->getOutput()}, stderr: {$process->getErrorOutput()}");
        }

        $pdfContents = file_get_contents($outputPdf);
        $cleanup();

        if ($pdfContents === false) {
            throw new PdfExportException("PDF Export via command failed, unable to read PDF output file");
        } else if (empty($pdfContents)) {
            throw new PdfExportException("PDF Export via command failed, PDF output file is empty");
        }

        return $pdfContents;
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
