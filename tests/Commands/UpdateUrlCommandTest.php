<?php

namespace Tests\Commands;

use BookStack\Entities\Models\Entity;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class UpdateUrlCommandTest extends TestCase
{
    public function test_command_updates_page_content()
    {
        $page = $this->entities->page();
        $page->html = '<a href="https://example.com/donkeys"></a>';
        $page->save();

        $this->artisan('bookstack:update-url https://example.com https://cats.example.com')
            ->expectsQuestion("This will search for \"https://example.com\" in your database and replace it with  \"https://cats.example.com\".\nAre you sure you want to proceed?", 'y')
            ->expectsQuestion('This operation could cause issues if used incorrectly. Have you made a backup of your existing database?', 'y');

        $this->assertDatabaseHas('pages', [
            'id'   => $page->id,
            'html' => '<a href="https://cats.example.com/donkeys"></a>',
        ]);
    }

    public function test_command_updates_description_html()
    {
        /** @var Entity[] $models */
        $models = [$this->entities->book(), $this->entities->chapter(), $this->entities->shelf()];

        foreach ($models as $model) {
            $model->description_html = '<a href="https://example.com/donkeys"></a>';
            $model->save();
        }

        $this->artisan('bookstack:update-url https://example.com https://cats.example.com')
            ->expectsQuestion("This will search for \"https://example.com\" in your database and replace it with  \"https://cats.example.com\".\nAre you sure you want to proceed?", 'y')
            ->expectsQuestion('This operation could cause issues if used incorrectly. Have you made a backup of your existing database?', 'y');

        foreach ($models as $model) {
            $this->assertDatabaseHas($model->getTable(), [
                'id'               => $model->id,
                'description_html' => '<a href="https://cats.example.com/donkeys"></a>',
            ]);
        }
    }

    public function test_command_requires_valid_url()
    {
        $badUrlMessage = 'The given urls are expected to be full urls starting with http:// or https://';
        $this->artisan('bookstack:update-url //example.com https://cats.example.com')->expectsOutput($badUrlMessage);
        $this->artisan('bookstack:update-url https://example.com htts://cats.example.com')->expectsOutput($badUrlMessage);
        $this->artisan('bookstack:update-url example.com https://cats.example.com')->expectsOutput($badUrlMessage);

        $this->expectException(RuntimeException::class);
        $this->artisan('bookstack:update-url https://cats.example.com');
    }

    public function test_command_force_option_skips_prompt()
    {
        $this->artisan('bookstack:update-url --force https://cats.example.com/donkey https://cats.example.com/monkey')
            ->expectsOutputToContain('URL update procedure complete')
            ->assertSuccessful();
    }

    public function test_command_updates_settings()
    {
        setting()->put('my-custom-item', 'https://example.com/donkey/cat');
        $this->runUpdate('https://example.com', 'https://cats.example.com');

        setting()->flushCache();

        $settingVal = setting('my-custom-item');
        $this->assertEquals('https://cats.example.com/donkey/cat', $settingVal);
    }

    public function test_command_updates_array_settings()
    {
        setting()->put('my-custom-array-item', [['name' => 'a https://example.com/donkey/cat url']]);
        $this->runUpdate('https://example.com', 'https://cats.example.com');

        setting()->flushCache();

        $settingVal = setting('my-custom-array-item');
        $this->assertEquals('a https://cats.example.com/donkey/cat url', $settingVal[0]['name']);
    }

    public function test_command_updates_page_revisions()
    {
        $page = $this->entities->page();

        for ($i = 0; $i < 2; $i++) {
            $this->entities->updatePage($page, [
                'name' => $page->name,
                'markdown' => "[A link {$i}](https://example.com/donkey/cat)"
            ]);
        }

        $this->runUpdate('https://example.com', 'https://cats.example.com');
        setting()->flushCache();

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'markdown' => '[A link 1](https://cats.example.com/donkey/cat)',
            'html' => '<p id="bkmrk-a-link-1"><a href="https://cats.example.com/donkey/cat">A link 1</a></p>' . "\n"
        ]);
    }

    protected function runUpdate(string $oldUrl, string $newUrl)
    {
        $this->artisan("bookstack:update-url {$oldUrl} {$newUrl}")
            ->expectsQuestion("This will search for \"{$oldUrl}\" in your database and replace it with  \"{$newUrl}\".\nAre you sure you want to proceed?", 'y')
            ->expectsQuestion('This operation could cause issues if used incorrectly. Have you made a backup of your existing database?', 'y');
    }
}
