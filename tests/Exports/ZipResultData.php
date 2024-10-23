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
}
