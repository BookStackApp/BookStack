<?php

namespace Laravel\BrowserKitTesting\Constraints;

class ReversePageConstraint extends PageConstraint
{
    /**
     * The page constraint instance.
     *
     * @var \Laravel\BrowserKitTesting\Constraints\PageConstraint
     */
    protected $pageConstraint;

    /**
     * Create a new reverse page constraint instance.
     *
     * @param  \Laravel\BrowserKitTesting\Constraints\PageConstraint  $pageConstraint
     * @return void
     */
    public function __construct(PageConstraint $pageConstraint)
    {
        $this->pageConstraint = $pageConstraint;
    }

    /**
     * Reverse the original page constraint result.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return bool
     */
    public function matches($crawler)
    {
        return ! $this->pageConstraint->matches($crawler);
    }

    /**
     * Get the description of the failure.
     *
     * This method will attempt to negate the original description.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return $this->pageConstraint->getReverseFailureDescription();
    }

    /**
     * Get a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return $this->pageConstraint->toString();
    }
}
