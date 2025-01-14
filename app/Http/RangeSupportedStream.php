<?php

namespace BookStack\Http;

use BookStack\Util\WebSafeMimeSniffer;
use Illuminate\Http\Request;

/**
 * Helper wrapper for range-based stream response handling.
 * Much of this used symfony/http-foundation as a reference during build.
 * URL: https://github.com/symfony/http-foundation/blob/v6.0.20/BinaryFileResponse.php
 * License: MIT license, Copyright (c) Fabien Potencier.
 */
class RangeSupportedStream
{
    protected string $sniffContent = '';
    protected array $responseHeaders = [];
    protected int $responseStatus = 200;

    protected int $responseLength = 0;
    protected int $responseOffset = 0;

    public function __construct(
        protected $stream,
        protected int $fileSize,
        Request $request,
    ) {
        $this->responseLength = $this->fileSize;
        $this->parseRequest($request);
    }

    /**
     * Sniff a mime type from the stream.
     */
    public function sniffMime(string $extension = ''): string
    {
        $offset = min(2000, $this->fileSize);
        $this->sniffContent = fread($this->stream, $offset);

        return (new WebSafeMimeSniffer())->sniff($this->sniffContent, $extension);
    }

    /**
     * Output the current stream to stdout before closing out the stream.
     */
    public function outputAndClose(): void
    {
        // End & flush the output buffer, if we're in one, otherwise we still use memory.
        // Output buffer may or may not exist depending on PHP `output_buffering` setting.
        // Ignore in testing since output buffers are used to gather a response.
        if (!empty(ob_get_status()) && !app()->runningUnitTests()) {
            ob_end_clean();
        }

        $outStream = fopen('php://output', 'w');
        $sniffLength = strlen($this->sniffContent);
        $bytesToWrite = $this->responseLength;

        if ($sniffLength > 0 && $this->responseOffset < $sniffLength) {
            $sniffEnd = min($sniffLength, $bytesToWrite + $this->responseOffset);
            $sniffOutLength = $sniffEnd - $this->responseOffset;
            $sniffOutput = substr($this->sniffContent, $this->responseOffset, $sniffOutLength);
            fwrite($outStream, $sniffOutput);
            $bytesToWrite -= $sniffOutLength;
        } else if ($this->responseOffset !== 0) {
            fseek($this->stream, $this->responseOffset);
        }

        stream_copy_to_stream($this->stream, $outStream, $bytesToWrite);

        fclose($this->stream);
        fclose($outStream);
    }

    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    public function getResponseStatus(): int
    {
        return $this->responseStatus;
    }

    protected function parseRequest(Request $request): void
    {
        $this->responseHeaders['Accept-Ranges'] = $request->isMethodSafe() ? 'bytes' : 'none';

        $range = $this->getRangeFromRequest($request);
        if ($range) {
            [$start, $end] = $range;
            if ($start < 0 || $start > $end) {
                $this->responseStatus = 416;
                $this->responseHeaders['Content-Range'] = sprintf('bytes */%s', $this->fileSize);
            } else {
                $this->responseLength = $end < $this->fileSize ? $end - $start + 1 : -1;
                $this->responseOffset = $start;
                $this->responseStatus = 206;
                $this->responseHeaders['Content-Range'] = sprintf('bytes %s-%s/%s', $start, $end, $this->fileSize);
                $this->responseHeaders['Content-Length'] = $end - $start + 1;
            }
        }

        if ($request->isMethod('HEAD')) {
            $this->responseLength = 0;
        }
    }

    protected function getRangeFromRequest(Request $request): ?array
    {
        $range = $request->headers->get('Range');
        if (!$range || !$request->isMethod('GET') || !str_starts_with($range, 'bytes=')) {
            return null;
        }

        if ($request->headers->has('If-Range')) {
            return null;
        }

        [$start, $end] = explode('-', substr($range, 6), 2) + [0];

        $end = ('' === $end) ? $this->fileSize - 1 : (int) $end;

        if ('' === $start) {
            $start = $this->fileSize - $end;
            $end = $this->fileSize - 1;
        } else {
            $start = (int) $start;
        }

        $end = min($end, $this->fileSize - 1);
        return [$start, $end];
    }
}
