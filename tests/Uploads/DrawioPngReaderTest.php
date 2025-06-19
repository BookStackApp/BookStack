<?php

namespace Tests\Uploads;

use BookStack\Exceptions\DrawioPngReaderException;
use BookStack\Uploads\DrawioPngReader;
use Tests\TestCase;

class DrawioPngReaderTest extends TestCase
{
    public function test_exact_drawing()
    {
        $file = $this->files->testFilePath('test.drawio.png');
        $stream = fopen($file, 'r');

        $reader = new DrawioPngReader($stream);
        $drawing = $reader->extractDrawing();

        $this->assertStringStartsWith('<mxfile ', $drawing);
        $this->assertStringEndsWith("</mxfile>\n", $drawing);
    }

    public function test_extract_drawing_with_non_drawing_image_throws_exception()
    {
        $file = $this->files->testFilePath('test-image.png');
        $stream = fopen($file, 'r');
        $reader = new DrawioPngReader($stream);

        $exception = null;
        try {
            $drawing = $reader->extractDrawing();
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(DrawioPngReaderException::class, $exception);
        $this->assertEquals($exception->getMessage(), 'Unable to find drawing data within PNG file');
    }

    public function test_extract_drawing_with_non_png_image_throws_exception()
    {
        $file = $this->files->testFilePath('test-image.jpg');
        $stream = fopen($file, 'r');
        $reader = new DrawioPngReader($stream);

        $exception = null;
        try {
            $drawing = $reader->extractDrawing();
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(DrawioPngReaderException::class, $exception);
        $this->assertEquals($exception->getMessage(), 'File does not appear to be a valid PNG file');
    }
}
