<?php

namespace BookStack\Http;

use BookStack\Util\WebSafeMimeSniffer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadResponseFactory
{
    public function __construct(
        protected Request $request
    ) {
    }

    /**
     * Create a response that directly forces a download in the browser.
     */
    public function directly(string $content, string $fileName): Response
    {
        return response()->make($content, 200, $this->getHeaders($fileName));
    }

    /**
     * Create a response that forces a download, from a given stream of content.
     */
    public function streamedDirectly($stream, string $fileName, int $fileSize): StreamedResponse
    {
        $rangeStream = new RangeSupportedStream($stream, $fileSize, $this->request->headers);
        return response()->stream(function () use ($rangeStream) {
            $rangeStream->outputAndClose();
        }, 200, $this->getHeaders($fileName));
    }

    /**
     * Create a file download response that provides the file with a content-type
     * correct for the file, in a way so the browser can show the content in browser,
     * for a given content stream.
     */
    public function streamedInline($stream, string $fileName, int $fileSize): StreamedResponse
    {
        $rangeStream = new RangeSupportedStream($stream, $fileSize, $this->request->headers);
        $mime = $rangeStream->sniffMime();

        return response()->stream(function () use ($rangeStream) {
            $rangeStream->outputAndClose();
        }, 200, $this->getHeaders($fileName, $mime));
    }

    /**
     * Get the common headers to provide for a download response.
     */
    protected function getHeaders(string $fileName, string $mime = 'application/octet-stream'): array
    {
        $disposition = ($mime === 'application/octet-stream') ? 'attachment' : 'inline';
        $downloadName = str_replace('"', '', $fileName);

        return [
            'Content-Type'           => $mime,
            'Content-Disposition'    => "{$disposition}; filename=\"{$downloadName}\"",
            'X-Content-Type-Options' => 'nosniff',
        ];
    }
}
