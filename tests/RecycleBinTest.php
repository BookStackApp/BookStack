<?php namespace Tests;

use BookStack\Entities\Book;
use BookStack\Entities\Deletion;
use BookStack\Entities\Page;

class RecycleBinTest extends TestCase
{
    // TODO - Test activity updating on destroy

    public function test_recycle_bin_routes_permissions()
    {
        $page = Page::query()->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $deletion = Deletion::query()->firstOrFail();

        $routes = [
            'GET:/settings/recycle-bin',
            'POST:/settings/recycle-bin/empty',
            "GET:/settings/recycle-bin/{$deletion->id}/destroy",
            "GET:/settings/recycle-bin/{$deletion->id}/restore",
            "POST:/settings/recycle-bin/{$deletion->id}/restore",
            "DELETE:/settings/recycle-bin/{$deletion->id}",
        ];

        foreach($routes as $route) {
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['restrictions-manage-all']);

        foreach($routes as $route) {
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['settings-manage']);

        foreach($routes as $route) {
            \DB::beginTransaction();
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertNotPermissionError($resp);
            \DB::rollBack();
        }

    }

    public function test_recycle_bin_view()
    {
        $page = Page::query()->first();
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->withCount(['pages', 'chapters'])->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $this->actingAs($editor)->delete($book->getUrl());

        $viewReq = $this->asAdmin()->get('/settings/recycle-bin');
        $viewReq->assertElementContains('table.table', $page->name);
        $viewReq->assertElementContains('table.table', $editor->name);
        $viewReq->assertElementContains('table.table', $book->name);
        $viewReq->assertElementContains('table.table', $book->pages_count . ' Pages');
        $viewReq->assertElementContains('table.table', $book->chapters_count . ' Chapters');
    }
}