<?php namespace Tests;

class CommentSettingTest extends BrowserKitTest {
  protected $page;

  public function setUp(): void {
      parent::setUp();
      $this->page = \BookStack\Entities\Page::first();
  }

  public function test_comment_disable () {
    $this->asAdmin();

    $this->setSettings(['app-disable-comments' => 'true']);

    $this->asAdmin()->visit($this->page->getUrl())
    ->pageNotHasElement('.comments-list');
  }

  public function test_comment_enable () {
    $this->asAdmin();

    $this->setSettings(['app-disable-comments' => 'false']);

    $this->asAdmin()->visit($this->page->getUrl())
    ->pageHasElement('.comments-list');
  }
}