<?php

namespace Tests\Commands;

use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClearViewsCommandTest extends TestCase
{
    public function test_clear_views_command()
    {
        $this->asEditor();
        $page = Page::first();

        $this->get($page->getUrl());

        $this->assertDatabaseHas('views', [
            'user_id'     => $this->users->editor()->id,
            'viewable_id' => $page->id,
            'views'       => 1,
        ]);

        DB::rollBack();
        $exitCode = \Artisan::call('bookstack:clear-views');
        DB::beginTransaction();
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('views', [
            'user_id' => $this->users->editor()->id,
        ]);
    }
}
