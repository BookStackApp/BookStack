<?php

namespace BookStack\Http;

use BookStack\Util\WebSafeMimeSniffer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadResponseFactory
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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
    public function streamedDirectly($stream, string $fileName): StreamedResponse
    {
        return response()->stream(function () use ($stream) {

            // End & flush the output buffer, if we're in one, otherwise we still use memory.
            // Output buffer may or may not exist depending on PHP `output_buffering` setting.
            // Ignore in testing since output buffers are used to gather a response.
            if (!empty(ob_get_status()) && !app()->runningUnitTests()) {
                ob_end_clean();
            }

            fpassthru($stream);
            fclose($stream);
        }, 200, $this->getHeaders($fileName));
    }

    /**
     * Create a file download response that provides the file with a content-type
     * correct for the file, in a way so the browser can show the content in browser,
     * for a given content stream.
     */
    public function streamedInline($stream, string $fileName): StreamedResponse
    {
        $sniffContent = fread($stream, 2000);
        $mime = (new WebSafeMimeSniffer())->sniff($sniffContent);

        return response()->stream(function () use ($sniffContent, $stream) {
            echo $sniffContent;
            fpassthru($stream);
            fclose($stream);
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
