<?php

namespace BookStack\Util\HtmlPurifier;

use HTMLPurifier_AttrDef;

/**
 * Custom attribute definition to filter out potentially dangerous
 * values from the srcset attribute.
 */
class SrcsetAttrDef extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $lower = strtolower($string);
        $nonAllowed = ['javascript:', 'vbscript:', 'data:', 'file:'];

        foreach ($nonAllowed as $nonAllowedString) {
            if (str_contains($lower, $nonAllowedString)) {
                return false;
            }
        }

        return $string;
    }
}
