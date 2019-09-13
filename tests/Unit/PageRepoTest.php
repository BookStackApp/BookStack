<?php
namespace Tests;

use BookStack\Entities\Repos\PageRepo;

class PageRepoTest extends TestCase
{
    /**
     * @var PageRepo $pageRepo
     */
    protected $pageRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageRepo = app()->make(PageRepo::class);
    }

    public function test_get_page_nav_sets_correct_properties()
    {
        $content = '<h1 id="testa">Hello</h1><h2 id="testb">There</h2><h3 id="testc">Donkey</h3>';
        $navMap = $this->pageRepo->getPageNav($content);

        $this->assertCount(3, $navMap);
        $this->assertArraySubset([
            'nodeName' => 'h1',
            'link' => '#testa',
            'text' => 'Hello',
            'level' => 1,
        ], $navMap[0]);
        $this->assertArraySubset([
            'nodeName' => 'h2',
            'link' => '#testb',
            'text' => 'There',
            'level' => 2,
        ], $navMap[1]);
        $this->assertArraySubset([
            'nodeName' => 'h3',
            'link' => '#testc',
            'text' => 'Donkey',
            'level' => 3,
        ], $navMap[2]);
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

    public function test_get_page_nav_shifts_headers_if_only_smaller_ones_are_used()
    {
        $content = '<h4 id="testa">Hello</h4><h5 id="testb">There</h5><h6 id="testc">Donkey</h6>';
        $navMap = $this->pageRepo->getPageNav($content);

        $this->assertCount(3, $navMap);
        $this->assertArraySubset([
            'nodeName' => 'h4',
            'level' => 1,
        ], $navMap[0]);
        $this->assertArraySubset([
            'nodeName' => 'h5',
            'level' => 2,
        ], $navMap[1]);
        $this->assertArraySubset([
            'nodeName' => 'h6',
            'level' => 3,
        ], $navMap[2]);
    }

}