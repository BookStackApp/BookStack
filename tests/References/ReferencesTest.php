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

    public function test_references_to_visible_on_references_page()
    {
        $entities = $this->getEachEntityType();
        $this->asEditor();
        foreach ($entities as $entity) {
            $this->createReference($entities['page'], $entity);
        }

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl('/references'));
            $resp->assertSee('References');
            $resp->assertSee($entities['page']->name);
            $resp->assertDontSee('There are no tracked references');
        }
    }

    public function test_reference_not_visible_if_view_permission_does_not_permit()
    {
        /** @var Page $page */
        /** @var Page $pageB */
        $page = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $page->id)->first();
        $this->createReference($pageB, $page);

        $this->setEntityRestrictions($pageB);

        $this->asEditor()->get($page->getUrl('/references'))->assertDontSee($pageB->name);
        $this->asAdmin()->get($page->getUrl('/references'))->assertSee($pageB->name);
    }

    public function test_reference_page_shows_empty_state_with_no_references()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $this->asEditor()
            ->get($page->getUrl('/references'))
            ->assertSee('There are no tracked references');
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