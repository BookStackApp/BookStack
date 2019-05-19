<?php
namespace Tests;

use BookStack\Entities\Repos\PageRepo;

class PageRepoTest extends TestCase
{
    /**
     * @var PageRepo $pageRepo
     */
    protected $pageRepo;

    protected function setUp()
    {
        parent::setUp();
        $this->pageRepo = app()->make(PageRepo::class);
    }

    public function test_get_page_nav_does_not_show_empty_titles()
    {
        $content = '<h1 id="testa">Hello</h1><h2 id="testb">&nbsp;</h2><h3 id="testc"></h3>';
        $navMap = $this->pageRepo->getPageNav($content);

        $this->assertCount(1, $navMap);
        $this->assertArraySubset([
            'nodeName' => 'h1',
            'link' => '#testa',
            'text' => 'Hello'
        ], $navMap[0]);
    }

}