<?php

namespace BookStack\Util;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;

/**
 * HtmlDocument is a thin wrapper around DOMDocument built
 * specifically for loading, querying and generating HTML content.
 */
class HtmlDocument
{
    protected DOMDocument $document;
    protected ?DOMXPath $xpath = null;
    protected int $loadOptions;

    public function __construct(string $partialHtml = '', int $loadOptions = 0)
    {
        libxml_use_internal_errors(true);
        $this->document = new DOMDocument();
        $this->loadOptions = $loadOptions;

        if ($partialHtml) {
            $this->loadPartialHtml($partialHtml);
        }
    }

    /**
     * Load some HTML content that's part of a document (e.g. body content)
     * into the current document.
     */
    public function loadPartialHtml(string $html): void
    {
        $html = '<?xml encoding="utf-8" ?><body>' . $html . '</body>';
        $this->document->loadHTML($html, $this->loadOptions);
        $this->xpath = null;
    }

    /**
     * Load a complete page of HTML content into the document.
     */
    public function loadCompleteHtml(string $html): void
    {
        $html = '<?xml encoding="utf-8" ?>' . $html;
        $this->document->loadHTML($html, $this->loadOptions);
        $this->xpath = null;
    }

    /**
     * Start an XPath query on the current document.
     */
    public function queryXPath(string $expression): DOMNodeList
    {
        if (is_null($this->xpath)) {
            $this->xpath = new DOMXPath($this->document);
        }

        $result = $this->xpath->query($expression);
        if ($result === false) {
            throw new \InvalidArgumentException("XPath query for expression [$expression] failed to execute");
        }

        return $result;
    }

    /**
     * Create a new DOMElement instance within the document.
     */
    public function createElement(string $localName, string $value = ''): DOMElement
    {
        $element = $this->document->createElement($localName, $value);

        if ($element === false) {
            throw new \InvalidArgumentException("Failed to create element of name [$localName] and value [$value]");
        }

        return $element;
    }

    /**
     * Get an element within the document of the given ID.
     */
    public function getElementById(string $elementId): ?DOMElement
    {
        return $this->document->getElementById($elementId);
    }

    /**
     * Get the DOMNode that represents the HTML body.
     */
    public function getBody(): DOMNode
    {
        return $this->document->getElementsByTagName('body')[0];
    }

    /**
     * Get the nodes that are a direct child of the body.
     * This is usually all the content nodes if loaded partially.
     */
    public function getBodyChildren(): DOMNodeList
    {
        return $this->getBody()->childNodes;
    }

    /**
     * Get the inner HTML content of the body.
     * This is usually all the content if loaded partially.
     */
    public function getBodyInnerHtml(): string
    {
        $html = '';
        foreach ($this->getBodyChildren() as $child) {
            $html .= $this->document->saveHTML($child);
        }

        return $html;
    }

    /**
     * Get the HTML content of the whole document.
     */
    public function getHtml(): string
    {
        return $this->document->saveHTML($this->document->documentElement);
    }

    /**
     * Get the inner HTML for the given node.
     */
    public function getNodeInnerHtml(DOMNode $node): string
    {
        $html = '';

        foreach ($node->childNodes as $childNode) {
            $html .= $this->document->saveHTML($childNode);
        }

        return $html;
    }

    /**
     * Get the outer HTML for the given node.
     */
    public function getNodeOuterHtml(DOMNode $node): string
    {
        return $this->document->saveHTML($node);
    }
}
