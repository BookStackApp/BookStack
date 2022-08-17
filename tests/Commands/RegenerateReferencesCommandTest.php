<?php

namespace Tests\Commands;

use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegenerateReferencesCommandTest extends TestCase
{
    public function test_regenerate_references_command()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $book = $page->book;

        $page->html = '<a href="' . $book->getUrl() . '">Book Link</a>';
        $page->save();

        DB::table('references')->delete();

        $this->artisan('bookstack:regenerate-references')
            ->assertExitCode(0);

        $this->assertDatabaseHas('references', [
            'from_id' => $page->id,
            'from_type' => $page->getMorphClass(),
            'to_id' => $book->id,
            'to_type' => $book->getMorphClass(),
        ]);
    }
}
