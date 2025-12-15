<?php

namespace BookStack\Activity\Tools;

use BookStack\Util\HtmlDocument;
use DOMElement;

class MentionParser
{
    public function parseUserIdsFromHtml(string $html): array
    {
        $doc = new HtmlDocument($html);

        $ids = [];
        $mentionLinks = $doc->queryXPath('//a[@data-mention-user-id]');

        foreach ($mentionLinks as $link) {
            if ($link instanceof DOMElement) {
                $id = intval($link->getAttribute('data-mention-user-id'));
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }

        return array_values(array_unique($ids));
    }
}
