<?php

namespace BookStack\Uploads;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;

class FaviconHandler
{
    public function __construct(
        protected ImageManager $imageTool
    ) {
    }

    /**
     * Save the given UploadedFile instance as the application favicon.
     */
    public function saveForUploadedImage(UploadedFile $file): void
    {
        $targetPath = public_path('favicon.ico');
        if (!is_writeable($targetPath)) {
            return;
        }

        $imageData = file_get_contents($file->getRealPath());
        $image = $this->imageTool->make($imageData);
        $image->resize(32, 32);
        $bmpData = $image->encode('png');
        $icoData = $this->pngToIco($bmpData, 32, 32);

        file_put_contents($targetPath, $icoData);
    }

    /**
     * Restore the original favicon image.
     */
    public function restoreOriginal(): void
    {
        $targetPath = public_path('favicon.ico');
        $original = public_path('icon.ico');
        if (!is_writeable($targetPath)) {
            return;
        }

        copy($original, $targetPath);
    }

    /**
     * Convert PNG image data to ICO file format.
     * Built following the file format info from Wikipedia:
     * https://en.wikipedia.org/wiki/ICO_(file_format)
     */
    protected function pngToIco(string $bmpData, int $width, int $height): string
    {
        // ICO header
        $header = pack('v', 0x00); // Reserved. Must always be 0
        $header .= pack('v', 0x01); // Specifies ico image
        $header .= pack('v', 0x01); // Specifies number of images

        // ICO Image Directory
        $entry = hex2bin(dechex($width)); // Image width
        $entry .= hex2bin(dechex($height)); // Image height
        $entry .= "\0"; // Color palette, typically 0
        $entry .= "\0"; // Reserved

        // Color planes, Appears to remain 1 for bmp image data
        $entry .= pack('v', 0x01);
        // Bits per pixel, can range from 1 to 32. From testing conversion
        // via intervention from png typically provides this as 24.
        $entry .= pack('v', 0x00);
        // Size of the image data in bytes
        $entry .= pack('V', strlen($bmpData));
        // Offset of the bmp data from file start
        $entry .= pack('V', strlen($header) + strlen($entry) + 4);

        // Join & return the combined parts of the ICO image data
        return $header . $entry . $bmpData;
    }
}
