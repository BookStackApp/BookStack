<?php namespace Tests;

use \Illuminate\Foundation\Testing\TestResponse as BaseTestResponse;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * Class TestResponse
 * Custom extension of the default Laravel TestResponse class.
 * @package Tests
 */
class TestResponse extends BaseTestResponse {

    protected $crawlerInstance;

    /**
     * Get the DOM Crawler for the response content.
     * @return Crawler
     */
    protected function crawler()
    {
        if (!is_object($this->crawlerInstance)) {
            $this->crawlerInstance = new Crawler($this->getContent());
        }
        return $this->crawlerInstance;
    }

    /**
     * Assert the response contains the specified element.
     * @param string $selector
     * @return $this
     */
    public function assertElementExists(string $selector)
    {
        $elements = $this->crawler()->filter($selector);
        PHPUnit::assertTrue(
            $elements->count() > 0,
            'Unable to find element matching the selector: '.PHP_EOL.PHP_EOL.
            "[{$selector}]".PHP_EOL.PHP_EOL.
            'within'.PHP_EOL.PHP_EOL.
            "[{$this->getContent()}]."
        );
        return $this;
    }

    /**
     * Assert the response does not contain the specified element.
     * @param string $selector
     * @return $this
     */
    public function assertElementNotExists(string $selector)
    {
        $elements = $this->crawler()->filter($selector);
        PHPUnit::assertTrue(
            $elements->count() === 0,
            'Found elements matching the selector: '.PHP_EOL.PHP_EOL.
            "[{$selector}]".PHP_EOL.PHP_EOL.
            'within'.PHP_EOL.PHP_EOL.
            "[{$this->getContent()}]."
        );
        return $this;
    }

    /**
     * Assert the response includes a specific element containing the given text.
     * @param string $selector
     * @param string $text
     * @return $this
     */
    public function assertElementContains(string $selector, string $text)
    {
        $elements = $this->crawler()->filter($selector);
        $matched = false;
        $pattern = $this->getEscapedPattern($text);
        foreach ($elements as $element) {
            $element = new Crawler($element);
            if (preg_match("/$pattern/i", $element->html())) {
                $matched = true;
                break;
            }
        }

        PHPUnit::assertTrue(
            $matched,
            'Unable to find element of selector: '.PHP_EOL.PHP_EOL.
            "[{$selector}]".PHP_EOL.PHP_EOL.
            'containing text'.PHP_EOL.PHP_EOL.
            "[{$text}]".PHP_EOL.PHP_EOL.
            'within'.PHP_EOL.PHP_EOL.
            "[{$this->getContent()}]."
        );

        return $this;
    }

    /**
     * Assert the response does not include a specific element containing the given text.
     * @param string $selector
     * @param string $text
     * @return $this
     */
    public function assertElementNotContains(string $selector, string $text)
    {
        $elements = $this->crawler()->filter($selector);
        $matched = false;
        $pattern = $this->getEscapedPattern($text);
        foreach ($elements as $element) {
            $element = new Crawler($element);
            if (preg_match("/$pattern/i", $element->html())) {
                $matched = true;
                break;
            }
        }

        PHPUnit::assertTrue(
            !$matched,
            'Found element of selector: '.PHP_EOL.PHP_EOL.
            "[{$selector}]".PHP_EOL.PHP_EOL.
            'containing text'.PHP_EOL.PHP_EOL.
            "[{$text}]".PHP_EOL.PHP_EOL.
            'within'.PHP_EOL.PHP_EOL.
            "[{$this->getContent()}]."
        );

        return $this;
    }

    /**
     * Get the escaped text pattern for the constraint.
     * @param  string  $text
     * @return string
     */
    protected function getEscapedPattern($text)
    {
        $rawPattern = preg_quote($text, '/');
        $escapedPattern = preg_quote(e($text), '/');
        return $rawPattern == $escapedPattern
            ? $rawPattern : "({$rawPattern}|{$escapedPattern})";
    }

}
