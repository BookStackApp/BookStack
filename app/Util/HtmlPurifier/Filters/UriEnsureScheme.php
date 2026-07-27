<?php

namespace BookStack\Util\HtmlPurifier\Filters;

use HTMLPurifier_Config;
use HTMLPurifier_Context;
use HTMLPurifier_URI;
use HTMLPurifier_URIFilter;

class UriEnsureScheme extends HTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'EnsureScheme';

    /**
     * @type bool
     */
    public $always_load = true;

    /**
     * @param HTMLPurifier_URI $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function filter(&$uri, $config, $context): bool
    {
        $def = $config->getDefinition('URI');
        $defaultScheme = $def->defaultScheme ?? '';

        if (empty($uri->scheme) && $defaultScheme) {
            $uri->scheme = $defaultScheme;
        }

        return true;
    }
}
