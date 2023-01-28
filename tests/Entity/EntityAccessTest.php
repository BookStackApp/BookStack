<?php

namespace Tests\Entity;

use BookStack\Auth\UserRepo;
use BookStack\Entities\Models\Entity;
use Tests\TestCase;

class EntityAccessTest extends TestCase
{
    public function test_entities_viewable_after_creator_deletion()
    {
        // Create required assets and revisions
        $creator = $this->users->editor();
        $updater = $this->users->viewer();
        $entities = $this->entities->createChainBelongingToUser($creator, $updater);
        app()->make(UserRepo::class)->destroy($creator);
        $this->entities->updatePage($entities['page'], ['html' => '<p>hello!</p>>']);

        $this->checkEntitiesViewable($entities);
    }

    public function test_entities_viewable_after_updater_deletion()
    {
        // Create required assets and revisions
        $creator = $this->users->viewer();
        $updater = $this->users->editor();
        $entities = $this->entities->createChainBelongingToUser($creator, $updater);
        app()->make(UserRepo::class)->destroy($updater);
        $this->entities->updatePage($entities['page'], ['html' => '<p>Hello there!</p>']);

        $this->checkEntitiesViewable($entities);
    }

    /**
     * @param array<string, Entity> $entities
     */
    private function checkEntitiesViewable(array $entities)
    {
        // Check pages and books are visible.
        $this->asAdmin();
        foreach ($entities as $entity) {
            $this->get($entity->getUrl())
                ->assertStatus(200)
                ->assertSee($entity->name);
        }

        // Check revision listing shows no errors.
        $this->get($entities['page']->getUrl('/revisions'))->assertStatus(200);
    }
}
