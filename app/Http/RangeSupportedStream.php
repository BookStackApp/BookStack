<?php

namespace BookStack\Http;

use BookStack\Util\WebSafeMimeSniffer;
use Symfony\Component\HttpFoundation\HeaderBag;

class RangeSupportedStream
{
    protected string $sniffContent;

    public function __construct(
        protected $stream,
        protected int $fileSize,
        protected HeaderBag $requestHeaders,
    ) {
    }

    /**
     * Sniff a mime type from the stream.
     */
    public function sniffMime(): string
    {
        $offset = min(2000, $this->fileSize);
        $this->sniffContent = fread($this->stream, $offset);

        return (new WebSafeMimeSniffer())->sniff($this->sniffContent);
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
        $offset = 0;

        if (!empty($this->sniffContent)) {
            fwrite($outStream, $this->sniffContent);
            $offset = strlen($this->sniffContent);
        }

        $toWrite = $this->fileSize - $offset;
        stream_copy_to_stream($this->stream, $outStream, $toWrite);
        fpassthru($this->stream);

        fclose($this->stream);
        fclose($outStream);
    }
}
