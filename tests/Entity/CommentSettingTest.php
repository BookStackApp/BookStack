<?php

namespace Tests\Entity;

use Tests\TestCase;

class CommentSettingTest extends TestCase
{
    public function test_comment_disable()
    {
        $page = $this->entities->page();
        $this->setSettings(['app-disable-comments' => 'true']);
        $this->asAdmin();

        $resp = $this->asAdmin()->get($page->getUrl());
        $this->withHtml($resp)->assertElementNotExists('.comments-list');
    }

    public function test_comment_enable()
    {
        $page = $this->entities->page();
        $this->setSettings(['app-disable-comments' => 'false']);
        $this->asAdmin();

        $resp = $this->asAdmin()->get($page->getUrl());
        $this->withHtml($resp)->assertElementExists('.comments-list');
    }
}
