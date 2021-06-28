<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestResponse as BaseTestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class TestResponse
 * Custom extension of the default Laravel TestResponse class.
 */
class TestResponse extends BaseTestResponse
{
    protected $crawlerInstance;

    /**
     * Get the DOM Crawler for the response content.
     */
    protected function crawler(): Crawler
    {
        if (!is_object($this->crawlerInstance)) {
            $this->crawlerInstance = new Crawler($this->getContent());
        }

        return $this->crawlerInstance;
    }

    /**
     * Assert the response contains the specified element.
     *
     * @return $this
     */
    public function assertElementExists(string $selector)
    {
        $elements = $this->crawler()->filter($selector);
        PHPUnit::assertTrue(
            $elements->count() > 0,
            'Unable to find element matching the selector: ' . PHP_EOL . PHP_EOL .
            "[{$selector}]" . PHP_EOL . PHP_EOL .
            'within' . PHP_EOL . PHP_EOL .
            "[{$this->getContent()}]."
        );

        return $this;
    }

    /**
     * Assert the response does not contain the specified element.
     *
     * @return $this
     */
    public function assertElementNotExists(string $selector)
    {
        $elements = $this->crawler()->filter($selector);
        PHPUnit::assertTrue(
            $elements->count() === 0,
            'Found elements matching the selector: ' . PHP_EOL . PHP_EOL .
            "[{$selector}]" . PHP_EOL . PHP_EOL .
            'within' . PHP_EOL . PHP_EOL .
            "[{$this->getContent()}]."
        );

        return $this;
    }

    /**
     * Assert the response includes a specific element containing the given text.
     * If an nth match is provided, only that will be checked otherwise all matching
     * elements will be checked for the given text.
     *
     * @return $this
     */
    public function assertElementContains(string $selector, string $text, ?int $nthMatch = null)
    {
        $elements = $this->crawler()->filter($selector);
        $matched = false;
        $pattern = $this->getEscapedPattern($text);

        if (!is_null($nthMatch)) {
            $elements = $elements->eq($nthMatch - 1);
        }

        foreach ($elements as $element) {
            $element = new Crawler($element);
            if (preg_match("/$pattern/i", $element->html())) {
                $matched = true;
                break;
            }
        }

        PHPUnit::assertTrue(
            $matched,
            'Unable to find element of selector: ' . PHP_EOL . PHP_EOL .
            ($nthMatch ? ("at position {$nthMatch}" . PHP_EOL . PHP_EOL) : '') .
            "[{$selector}]" . PHP_EOL . PHP_EOL .
            'containing text' . PHP_EOL . PHP_EOL .
            "[{$text}]" . PHP_EOL . PHP_EOL .
            'within' . PHP_EOL . PHP_EOL .
            "[{$this->getContent()}]."
        );

        return $this;
    }

    /**
     * Assert the response does not include a specific element containing the given text.
     * If an nth match is provided, only that will be checked otherwise all matching
     * elements will be checked for the given text.
     *
     * @return $this
     */
    public function assertElementNotContains(string $selector, string $text, ?int $nthMatch = null)
    {
        $elements = $this->crawler()->filter($selector);
        $matched = false;
        $pattern = $this->getEscapedPattern($text);

        if (!is_null($nthMatch)) {
            $elements = $elements->eq($nthMatch - 1);
        }

        foreach ($elements as $element) {
            $element = new Crawler($element);
            if (preg_match("/$pattern/i", $element->html())) {
                $matched = true;
                break;
            }
        }

        PHPUnit::assertTrue(
            !$matched,
            'Found element of selector: ' . PHP_EOL . PHP_EOL .
            ($nthMatch ? ("at position {$nthMatch}" . PHP_EOL . PHP_EOL) : '') .
            "[{$selector}]" . PHP_EOL . PHP_EOL .
            'containing text' . PHP_EOL . PHP_EOL .
            "[{$text}]" . PHP_EOL . PHP_EOL .
            'within' . PHP_EOL . PHP_EOL .
            "[{$this->getContent()}]."
        );

        return $this;
    }

    /**
     * Assert there's a notification within the view containing the given text.
     *
     * @return $this
     */
    public function assertNotificationContains(string $text)
    {
        return $this->assertElementContains('[notification]', $text);
    }

    /**
     * Get the escaped text pattern for the constraint.
     *
     * @return string
     */
    protected function getEscapedPattern(string $text)
    {
        $rawPattern = preg_quote($text, '/');
        $escapedPattern = preg_quote(e($text), '/');

        return $rawPattern == $escapedPattern
            ? $rawPattern : "({$rawPattern}|{$escapedPattern})";
    }
}
