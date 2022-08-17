<?php

namespace Tests\References;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Model;
use BookStack\References\Reference;
use Tests\TestCase;

class ReferencesTest extends TestCase
{

    public function test_references_created_on_page_update()
    {
        /** @var Page $pageA */
        /** @var Page $pageB */
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();

        $this->assertDatabaseMissing('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);

        $this->asEditor()->put($pageA->getUrl(), [
            'name' => 'Reference test',
            'html' => '<a href="' . $pageB->getUrl() . '">Testing</a>'
        ]);

        $this->assertDatabaseHas('references', [
            'from_id' => $pageA->id,
            'from_type' => $pageA->getMorphClass(),
            'to_id' => $pageB->id,
            'to_type' => $pageB->getMorphClass(),
        ]);
    }

    public function test_references_deleted_on_entity_delete()
    {
        /** @var Page $pageA */
        /** @var Page $pageB */
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();

        $this->createReference($pageA, $pageB);
        $this->createReference($pageB, $pageA);

        $this->assertDatabaseHas('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);
        $this->assertDatabaseHas('references', ['to_id' => $pageA->id, 'to_type' => $pageA->getMorphClass()]);

        app(PageRepo::class)->destroy($pageA);
        app(TrashCan::class)->empty();

        $this->assertDatabaseMissing('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);
        $this->assertDatabaseMissing('references', ['to_id' => $pageA->id, 'to_type' => $pageA->getMorphClass()]);
    }

    protected function createReference(Model $from, Model $to)
    {
        (new Reference())->forceFill([
            'from_type' => $from->getMorphClass(),
            'from_id' => $from->id,
            'to_type' => $to->getMorphClass(),
            'to_id' => $to->id,
        ])->save();
    }

}