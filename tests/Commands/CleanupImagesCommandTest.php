<?php

namespace Tests\Commands;

use BookStack\Uploads\Image;
use Tests\TestCase;

class CleanupImagesCommandTest extends TestCase
{
    public function test_command_defaults_to_dry_run()
    {
        $page = $this->entities->page();
        $image = Image::factory()->create(['uploaded_to' => $page->id]);

        $this->artisan('bookstack:cleanup-images -v')
            ->expectsOutput('Dry run, no images have been deleted')
            ->expectsOutput('1 images found that would have been deleted')
            ->expectsOutputToContain($image->path)
            ->assertExitCode(0);

        $this->assertDatabaseHas('images', ['id' => $image->id]);
    }

    public function test_command_force_run()
    {
        $page = $this->entities->page();
        $image = Image::factory()->create(['uploaded_to' => $page->id]);

        $this->artisan('bookstack:cleanup-images --force')
            ->expectsOutputToContain('This operation is destructive and is not guaranteed to be fully accurate')
            ->expectsConfirmation('Are you sure you want to proceed?', 'yes')
            ->expectsOutput('1 images deleted')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_command_force_run_negative_confirmation()
    {
        $page = $this->entities->page();
        $image = Image::factory()->create(['uploaded_to' => $page->id]);

        $this->artisan('bookstack:cleanup-images --force')
            ->expectsConfirmation('Are you sure you want to proceed?', 'no')
            ->assertExitCode(0);

        $this->assertDatabaseHas('images', ['id' => $image->id]);
    }
}
