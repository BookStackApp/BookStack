<?php

namespace Tests\Commands;

use BookStack\Search\SearchTerm;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegenerateSearchCommandTest extends TestCase
{
    public function test_command_regenerates_index()
    {
        DB::rollBack();
        $page = $this->entities->page();
        SearchTerm::truncate();

        $this->assertDatabaseMissing('search_terms', ['entity_id' => $page->id]);

        $this->artisan('bookstack:regenerate-search')
            ->expectsOutput('Search index regenerated!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('search_terms', [
            'entity_type' => 'page',
            'entity_id' => $page->id
        ]);
        DB::beginTransaction();
    }
}
