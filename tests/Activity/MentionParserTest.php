<?php

namespace Tests\Activity;

use BookStack\Activity\Tools\MentionParser;
use Tests\TestCase;

class MentionParserTest extends TestCase
{
    public function test_it_extracts_mentions()
    {
        $parser = new MentionParser();

        // Test basic mention extraction
        $html = '<p>Hello <a href="/user/5" data-mention-user-id="5">@User</a></p>';
        $result = $parser->parseUserIdsFromHtml($html);
        $this->assertEquals([5], $result);

        // Test multiple mentions
        $html = '<p><a data-mention-user-id="1">@Alice</a> and <a data-mention-user-id="2">@Bob</a></p>';
        $result = $parser->parseUserIdsFromHtml($html);
        $this->assertEquals([1, 2], $result);

        // Test filtering out invalid IDs (zero and negative)
        $html = '<p><a data-mention-user-id="0">@Invalid</a> <a data-mention-user-id="-5">@Negative</a> <a data-mention-user-id="3">@Valid</a></p>';
        $result = $parser->parseUserIdsFromHtml($html);
        $this->assertEquals([3], $result);

        // Test non-mention links are ignored
        $html = '<p><a href="/page/1">Normal Link</a> <a data-mention-user-id="7">@User</a></p>';
        $result = $parser->parseUserIdsFromHtml($html);
        $this->assertEquals([7], $result);

        // Test empty HTML
        $result = $parser->parseUserIdsFromHtml('');
        $this->assertEquals([], $result);

        // Test duplicate user IDs
        $html = '<p><a data-mention-user-id="4">@User</a> mentioned <a data-mention-user-id="4">@User</a> again</p>';
        $result = $parser->parseUserIdsFromHtml($html);
        $this->assertEquals([4], $result);
    }
}
