<?php

namespace BookStack\Exports;

use BookStack\Exceptions\PdfExportException;
use Dompdf\Dompdf;
use FontLib\Font;
use Illuminate\Support\Str;
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

        $fontMetrics = $domPdf->getFontMetrics();
        $userFontfamilies = $this->getUserDomPdfFontFamilies();
        foreach ($userFontfamilies as $fontFamily => $fonts) {
            try {
                $fontMetrics->setFontFamily($fontFamily, $fonts);
            } catch (\Exception $exception) {
                $expectedPath = storage_path('fonts/dompdf');
                throw new PdfExportException("Failed to create required font data in {$expectedPath}, Ensure all content in this location is writable by the web server");
            }
        }

        $domPdf->loadHTML($this->convertEntities($html));
        $domPdf->render();

        return (string) $domPdf->output();
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function getUserDomPdfFontFamilies(): array
    {
        $fontStore = storage_path('fonts/dompdf');
        if (!is_dir($fontStore)) {
            return [];
        }

        $fontFamilies = [];
        $fontFiles = glob($fontStore . DIRECTORY_SEPARATOR . '*.ttf');
        foreach ($fontFiles as $fontFile) {
            $fontFileName = basename($fontFile, '.ttf');
            $expectedUfm = $fontStore . DIRECTORY_SEPARATOR . $fontFileName . '.ufm';
            if (!file_exists($expectedUfm)) {
                $font = Font::load($fontFile);
                $font->parse();
                try {
                    $font->saveAdobeFontMetrics($expectedUfm);
                } catch (\Exception $exception) {
                    throw new PdfExportException("Failed to create required font data at $expectedUfm, Ensure this location is writable by the web server");
                }
            }

            $nameParts = explode('-', $fontFileName);
            if (count($nameParts) === 1 || $nameParts[1] === 'Regular') {
                $nameParts[1] = 'Normal';
            }

            $family = trim(strtolower(preg_replace('/([A-Z])/', ' $1', $nameParts[0])));
            $variation = Str::snake($nameParts[1]);
            if (!isset($fontFamilies[$family])) {
                $fontFamilies[$family] = [];
            }

            $fontFamilies[$family][$variation] = $fontStore . DIRECTORY_SEPARATOR . $fontFileName;
        }

        return $fontFamilies;
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
