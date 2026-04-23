<?php

namespace BookStack\Util;

class HtmlToPlainText
{
    /**
     * Inline tags types where the content should not be put on a new line.
     */
    protected array $inlineTags = [
        'a', 'b', 'i', 'u', 'strong', 'em', 'small', 'sup', 'sub', 'span', 'div',
    ];

    /**
     * Convert the provided HTML to relatively clean plain text.
     */
    public function convert(string $html): string
    {
        $doc = new HtmlDocument($html);
        $text = $this->nodeToText($doc->getBody());

        // Remove repeated newlines
        $text = preg_replace('/\n+/', "\n", $text);
        // Remove leading/trailing whitespace
        $text = trim($text);

        return $text;
    }

    protected function nodeToText(\DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return $node->textContent;
        }

        $text = '';
        if (!in_array($node->nodeName, $this->inlineTags)) {
            $text .= "\n";
        }

        foreach ($node->childNodes as $childNode) {
            $text .= $this->nodeToText($childNode);
        }

        return $text;
    }
}
