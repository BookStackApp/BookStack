<?php


class PageUpdateDraftTest extends TestCase
{
    protected $page;
    protected $pageRepo;

    public function setUp()
    {
        parent::setUp();
        $this->page = \BookStack\Page::first();
        $this->pageRepo = app('\BookStack\Repos\PageRepo');
    }

    public function test_draft_content_shows_if_available()
    {
        $addedContent = '<p>test message content</p>';
        $this->asAdmin()->visit($this->page->getUrl() . '/edit')
            ->dontSeeInField('html', $addedContent);

        $newContent = $this->page->html . $addedContent;
        $this->pageRepo->saveUpdateDraft($this->page, ['html' => $newContent]);
        $this->asAdmin()->visit($this->page->getUrl() . '/edit')
            ->seeInField('html', $newContent);
    }

    public function test_draft_not_visible_by_others()
    {
        $addedContent = '<p>test message content</p>';
        $this->asAdmin()->visit($this->page->getUrl() . '/edit')
            ->dontSeeInField('html', $addedContent);

        $newContent = $this->page->html . $addedContent;
        $newUser = $this->getNewUser();
        $this->pageRepo->saveUpdateDraft($this->page, ['html' => $newContent]);
        $this->actingAs($newUser)->visit($this->page->getUrl() . '/edit')
            ->dontSeeInField('html', $newContent);
    }

    public function test_alert_message_shows_if_editing_draft()
    {
        $this->asAdmin();
        $this->pageRepo->saveUpdateDraft($this->page, ['html' => 'test content']);
        $this->asAdmin()->visit($this->page->getUrl() . '/edit')
            ->see('You are currently editing a draft');
    }

    public function test_alert_message_shows_if_someone_else_editing()
    {
        $addedContent = '<p>test message content</p>';
        $this->asAdmin()->visit($this->page->getUrl() . '/edit')
            ->dontSeeInField('html', $addedContent);

        $newContent = $this->page->html . $addedContent;
        $newUser = $this->getNewUser();
        $this->pageRepo->saveUpdateDraft($this->page, ['html' => $newContent]);
        $this->actingAs($newUser)->visit($this->page->getUrl() . '/edit')
            ->see('Admin has started editing this page');
    }

}
