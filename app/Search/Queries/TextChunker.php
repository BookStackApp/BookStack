<?php

declare(strict_types=1);

namespace BookStack\Search\Queries;

use InvalidArgumentException;

/**
 * Splits a given string into smaller chunks based on specified delimiters
 * and a predefined maximum chunk size. This will work through the given delimiters
 * to break down text further and further to fit into the chunk size.
 *
 * The last delimiter is always an empty string to ensure text can always be broken down.
 */
class TextChunker
{
    public function __construct(
        protected int $chunkSize,
        protected array $delimiterOrder,
    ) {
        if (count($this->delimiterOrder) === 0 || $this->delimiterOrder[count($this->delimiterOrder) - 1] !== '') {
            $this->delimiterOrder[] = '';
        }

        if ($this->chunkSize < 1) {
            throw new InvalidArgumentException('Chunk size must be greater than 0');
        }
    }

    public function chunk(string $text): array
    {
        $delimiter = $this->delimiterOrder[0];
        $delimiterLength = strlen($delimiter);
        $lines = ($delimiter === '') ? str_split($text, $this->chunkSize) : explode($delimiter, $text);

        $cChunk = ''; // Current chunk
        $cLength = 0; // Current chunk length
        $chunks = []; // Chunks to return
        $lDelim = ''; // Last delimiter

        foreach ($lines as $index => $line) {
            $lineLength = strlen($line);
            if ($cLength + $lineLength + $delimiterLength <= $this->chunkSize) {
                $cChunk .= $line . $delimiter;
                $cLength += $lineLength + $delimiterLength;
                $lDelim = $delimiter;
            } else if ($lineLength <= $this->chunkSize) {
                $chunks[] = trim($cChunk, $delimiter);
                $cChunk = $line . $delimiter;
                $cLength = $lineLength + $delimiterLength;
                $lDelim = $delimiter;
            } else {
                $subChunks = new static($this->chunkSize, array_slice($this->delimiterOrder, 1));
                $subDelimiter = $this->delimiterOrder[1] ?? '';
                $subDelimiterLength = strlen($subDelimiter);
                foreach ($subChunks->chunk($line) as $subChunk) {
                    $chunkLength = strlen($subChunk);
                    if ($cLength + $chunkLength + $subDelimiterLength <= $this->chunkSize) {
                        $cChunk .= $subChunk . $subDelimiter;
                        $cLength += $chunkLength + $subDelimiterLength;
                        $lDelim = $subDelimiter;
                    } else {
                        $chunks[] = trim($cChunk, $lDelim);
                        $cChunk = $subChunk . $subDelimiter;
                        $cLength = $chunkLength + $subDelimiterLength;
                        $lDelim = $subDelimiter;
                    }
                }
            }
        }

        if ($cChunk !== '') {
            $chunks[] = trim($cChunk, $lDelim);
        }

        return $chunks;
    }
}
