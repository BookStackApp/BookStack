<?php

namespace BookStack\Util\HtmlPurifier\Filters;

use HTMLPurifier_Config;
use HTMLPurifier_Context;
use HTMLPurifier_URI;
use HTMLPurifier_URIFilter_MakeAbsolute;

/**
 * A custom version of HTMLPurifier's MakeAbsolute filter which leaves fragment-based
 * anchors alone without adding a base.
 */
class UriMakeAbsolute extends HTMLPurifier_URIFilter_MakeAbsolute
{
    /**
     * @type string
     */
    public $name = 'CustomMakeAbsolute';

    /**
     * @type bool
     */
    public $always_load = true;

    /**
     * @param HTMLPurifier_URI $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     */
    public function filter(&$uri, $config, $context): bool
    {
        if (str_starts_with($uri->toString(), '#')) {
            return true;
        }

        return parent::filter($uri, $config, $context);
    }
}
