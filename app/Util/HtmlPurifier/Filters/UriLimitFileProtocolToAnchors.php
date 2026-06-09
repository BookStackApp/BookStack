<?php

namespace BookStack\Util\HtmlPurifier\Filters;

use HTMLPurifier_Config;
use HTMLPurifier_Context;
use HTMLPurifier_URI;
use HTMLPurifier_URIFilter;

/**
 * Limits file:// URIs to only be used on anchor tags href attributes.
 * This prevents use on iframes/embeds/images where they can be used to load external
 * content on the network, triggering calls which may include NTLM auth hashes when in
 * certain windows based environments.
 */
class UriLimitFileProtocolToAnchors extends HTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'LimitFileProtocolToAnchors';

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
    public function filter(&$uri, $config, $context)
    {
        // Ensure we're only filtering file:// URIs'
        if ($uri->scheme !== 'file') {
            return true;
        }

        $token = $context->get('CurrentToken', true);
        $attr = $context->get('CurrentAttr', true);

        // Only allow if used on hrefs on anchor tags
        $isAnchor = $token && $token->name === 'a';
        $isHref = $attr === 'href';
        if ($isAnchor && $isHref) {
            return true;
        }

        return false;
    }
}

// vim: et sw=4 sts=4
