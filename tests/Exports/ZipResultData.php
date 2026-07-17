<?php

namespace Tests\Exports;

class ZipResultData
{
    public function __construct(
        public string $zipPath,
        public string $extractedDirPath,
        public array $data,
    ) {
    }

    /**
     * Build a path to a location the extracted content, using the given relative $path.
     */
    public function extractPath(string $path): string
    {
        $relPath = implode(DIRECTORY_SEPARATOR, explode('/', $path));
        return $this->extractedDirPath . DIRECTORY_SEPARATOR . ltrim($relPath, DIRECTORY_SEPARATOR);
    }
}
