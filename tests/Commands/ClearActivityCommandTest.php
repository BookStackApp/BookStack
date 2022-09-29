<?php

namespace Tests\Commands;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Page;
use BookStack\Facades\Activity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClearActivityCommandTest extends TestCase
{
    public function test_clear_activity_command()
    {
        $this->asEditor();
        $page = $this->entities->page();
        Activity::add(ActivityType::PAGE_UPDATE, $page);

        $this->assertDatabaseHas('activities', [
            'type'      => 'page_update',
            'entity_id' => $page->id,
            'user_id'   => $this->getEditor()->id,
        ]);

        DB::rollBack();
        $exitCode = Artisan::call('bookstack:clear-activity');
        DB::beginTransaction();
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('activities', [
            'type' => 'page_update',
        ]);
    }
}
