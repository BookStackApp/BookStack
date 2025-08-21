<?php

namespace Search;

use BookStack\Search\Queries\TextChunker;
use Tests\TestCase;

class TextChunkerTest extends TestCase
{
    public function test_it_chunks_text()
    {
        $chunker = new TextChunker(3, []);
        $chunks = $chunker->chunk('123456789');

        $this->assertEquals(['123', '456', '789'], $chunks);
    }

    public function test_chunk_size_must_be_greater_than_zero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $chunker = new TextChunker(-5, []);
    }

    public function test_it_works_through_given_delimiters()
    {
        $chunker = new TextChunker(5, ['-', '.', '']);
        $chunks = $chunker->chunk('12-3456.789abcdefg');

        $this->assertEquals(['12', '3456', '789ab', 'cdefg'], $chunks);
    }

    public function test_it_attempts_to_pack_chunks()
    {
        $chunker = new TextChunker(8, [' ', '']);
        $chunks = $chunker->chunk('123 456 789 abc def');

        $this->assertEquals(['123 456', '789 abc', 'def'], $chunks);
    }

    public function test_it_attempts_to_pack_using_subchunks()
    {
        $chunker = new TextChunker(8, [' ', '-', '']);
        $chunks = $chunker->chunk('123 456-789abc');

        $this->assertEquals(['123 456', '789abc'], $chunks);
    }
}
