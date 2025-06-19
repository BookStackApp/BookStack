<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\DrawioPngReaderException;

/**
 * Reads the PNG file format: https://www.w3.org/TR/2003/REC-PNG-20031110/
 * So that it can extract embedded drawing data for alternative use.
 */
class DrawioPngReader
{
    /**
     * @param resource $fileStream
     */
    public function __construct(
        protected $fileStream
    ) {
    }

    /**
     * @throws DrawioPngReaderException
     */
    public function extractDrawing(): string
    {
        $signature = fread($this->fileStream, 8);
        $pngSignature = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A";
        if ($signature !== $pngSignature) {
            throw new DrawioPngReaderException('File does not appear to be a valid PNG file');
        }

        $offset = 8;
        $searching = true;

        while ($searching) {
            fseek($this->fileStream, $offset);

            $lengthBytes = $this->readData(4);
            $chunkTypeBytes = $this->readData(4);
            $length = unpack('Nvalue', $lengthBytes)['value'];

            if ($chunkTypeBytes === 'tEXt') {
                fseek($this->fileStream, $offset + 8);
                $data = $this->readData($length);
                $crc = $this->readData(4);
                $drawingData = $this->readTextForDrawing($data);
                if ($drawingData !== null) {
                    $crcResult = $this->calculateCrc($chunkTypeBytes . $data);
                    if ($crc !== $crcResult) {
                        throw new DrawioPngReaderException('Drawing data withing PNG file appears to be corrupted');
                    }
                    return $drawingData;
                }
            } else if ($chunkTypeBytes === 'IEND') {
                $searching = false;
            }

            $offset += 12 + $length; // 12 = length + type + crc bytes
        }

        throw new DrawioPngReaderException('Unable to find drawing data within PNG file');
    }

    protected function readTextForDrawing(string $data): ?string
    {
        // Check the keyword is mxfile to ensure we're getting the right data
        if (!str_starts_with($data, "mxfile\u{0}")) {
            return null;
        }

        // Extract & cleanup the drawing text
        $drawingText = substr($data, 7);
        return urldecode($drawingText);
    }

    protected function readData(int $length): string
    {
        $bytes = fread($this->fileStream, $length);
        if ($bytes === false || strlen($bytes) < $length) {
            throw new DrawioPngReaderException('Unable to find drawing data within PNG file');
        }
        return $bytes;
    }

    protected function getCrcTable(): array
    {
        $table = [];

        for ($n = 0; $n < 256; $n++) {
            $c = $n;
            for ($k = 0; $k < 8; $k++) {
                if ($c & 1) {
                    $c = 0xedb88320 ^ ($c >> 1);
                } else {
                    $c = $c >> 1;
                }
            }
            $table[$n] = $c;
        }

        return $table;
    }

    /**
     * Calculate a CRC for the given bytes following:
     * https://www.w3.org/TR/2003/REC-PNG-20031110/#D-CRCAppendix
     */
    protected function calculateCrc(string $bytes): string
    {
        $table = $this->getCrcTable();

        $length = strlen($bytes);
        $c = 0xffffffff;

        for ($n = 0; $n < $length; $n++) {
            $tableIndex = ($c ^ ord($bytes[$n])) & 0xff;
            $c = $table[$tableIndex] ^ ($c >> 8);
        }

        return pack('N', $c ^ 0xffffffff);
    }
}
