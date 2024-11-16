<?php

namespace Tests\Exports;

use BookStack\Exports\ZipExports\ZipImportRunner;
use Tests\TestCase;

class ZipImportRunnerTest extends TestCase
{
    protected ZipImportRunner $runner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runner = app()->make(ZipImportRunner::class);
    }

    // TODO - Test full book import
    // TODO - Test full chapter import
    // TODO - Test full page import
}
