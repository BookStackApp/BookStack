<?php

namespace Laravel\BrowserKitTesting\Constraints;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class IsSelected extends FormFieldConstraint
{
    /**
     * Get the valid elements.
     *
     * @return string
     */
    protected function validElements()
    {
        return 'select,input[type="radio"]';
    }

    /**
     * Determine if the select or radio element is selected.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler|string  $crawler
     * @return bool
     */
    protected function matches($crawler)
    {
        $crawler = $this->crawler($crawler);

        return in_array($this->value, $this->getSelectedValue($crawler));
    }

    /**
     * Get the selected value of a select field or radio group.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return array
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function getSelectedValue(Crawler $crawler)
    {
        $field = $this->field($crawler);

        return $field->nodeName() == 'select'
            ? $this->getSelectedValueFromSelect($field)
            : [$this->getCheckedValueFromRadioGroup($field)];
    }

    /**
     * Get the selected value from a select field.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $select
     * @return array
     */
    protected function getSelectedValueFromSelect(Crawler $select)
    {
        $selected = [];

        foreach ($select->children() as $option) {
            if ($option->nodeName === 'optgroup') {
                foreach ($option->childNodes as $child) {
                    if ($child->hasAttribute('selected')) {
                        $selected[] = $this->getOptionValue($child);
                    }
                }
            } elseif ($option->hasAttribute('selected')) {
                $selected[] = $this->getOptionValue($option);
            }
        }

        return $selected;
    }

    /**
     * Get the selected value from an option element.
     *
     * @param  \DOMElement  $option
     * @return string
     */
    protected function getOptionValue(DOMElement $option)
    {
        if ($option->hasAttribute('value')) {
            return $option->getAttribute('value');
        }

        return $option->textContent;
    }

    /**
     * Get the checked value from a radio group.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $radioGroup
     * @return string|null
     */
    protected function getCheckedValueFromRadioGroup(Crawler $radioGroup)
    {
        foreach ($radioGroup as $radio) {
            if ($radio->hasAttribute('checked')) {
                return $radio->getAttribute('value');
            }
        }
    }

    /**
     * Returns the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return sprintf(
            'the element [%s] has the selected value [%s]',
            $this->selector, $this->value
        );
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        return sprintf(
            'the element [%s] does not have the selected value [%s]',
            $this->selector, $this->value
        );
    }
}
