<?php

namespace BookStack\Search;

/**
 * A custom text tokenizer which records & provides insight needed for our search indexing.
 * We used to use basic strtok() but this class does the following which that lacked:
 * - Tracks and provides the current/previous delimiter that we've stopped at.
 * - Returns empty tokens upon parsing a delimiter.
 */
class SearchTextTokenizer
{
    protected int $currentIndex = 0;
    protected int $length;
    protected string $currentDelimiter = '';
    protected string $previousDelimiter = '';

    public function __construct(
        protected string $text,
        protected string $delimiters = ' '
    ) {
        $this->length = strlen($this->text);
    }

    /**
     * Get the current delimiter to be found.
     */
    public function currentDelimiter(): string
    {
        return $this->currentDelimiter;
    }

    /**
     * Get the previous delimiter found.
     */
    public function previousDelimiter(): string
    {
        return $this->previousDelimiter;
    }

    /**
     * Get the next token between delimiters.
     * Returns false if there's no further tokens.
     */
    public function next(): string|false
    {
        $token = '';

        for ($i = $this->currentIndex; $i < $this->length; $i++) {
            $char = $this->text[$i];
            if (str_contains($this->delimiters, $char)) {
                $this->previousDelimiter = $this->currentDelimiter;
                $this->currentDelimiter = $char;
                $this->currentIndex = $i + 1;
                return $token;
            }

            $token .= $char;
        }

        if ($token) {
            $this->currentIndex = $this->length;
            $this->previousDelimiter = $this->currentDelimiter;
            $this->currentDelimiter = '';
            return $token;
        }

        return false;
    }
}
