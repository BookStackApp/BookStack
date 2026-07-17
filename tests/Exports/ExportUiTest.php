<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Book;
use Tests\TestCase;

class ExportUiTest extends TestCase
{
    public function test_export_option_only_visible_and_accessible_with_permission()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->first();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();
        $entities = [$book, $chapter, $page];
        $user = $this->users->viewer();
        $this->actingAs($user);

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $resp->assertSee('/export/pdf');
        }

        $this->permissions->removeUserRolePermissions($user, ['content-export']);

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $resp->assertDontSee('/export/pdf');
            $resp = $this->get($entity->getUrl('/export/pdf'));
            $this->assertPermissionError($resp);
        }
    }
}
