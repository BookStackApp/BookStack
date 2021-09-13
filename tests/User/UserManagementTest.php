<?php

namespace Tests\User;

use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_delete()
    {
        $editor = $this->getEditor();
        $resp = $this->asAdmin()->delete("settings/users/{$editor->id}");
        $resp->assertRedirect('/settings/users');
        $resp = $this->followRedirects($resp);

        $resp->assertSee('User successfully removed');
        $this->assertActivityExists(ActivityType::USER_DELETE);

        $this->assertDatabaseMissing('users', ['id' => $editor->id]);
    }

    public function test_delete_offers_migrate_option()
    {
        $editor = $this->getEditor();
        $resp = $this->asAdmin()->get("settings/users/{$editor->id}/delete");
        $resp->assertSee('Migrate Ownership');
        $resp->assertSee('new_owner_id');
    }

    public function test_delete_with_new_owner_id_changes_ownership()
    {
        $page = Page::query()->first();
        $owner = $page->ownedBy;
        $newOwner = User::query()->where('id', '!=', $owner->id)->first();

        $this->asAdmin()->delete("settings/users/{$owner->id}", ['new_owner_id' => $newOwner->id]);
        $this->assertDatabaseHas('pages', [
            'id'       => $page->id,
            'owned_by' => $newOwner->id,
        ]);
    }

    public function test_guest_profile_shows_limited_form()
    {
        $guest = User::getDefault();
        $resp = $this->asAdmin()->get('/settings/users/' . $guest->id);
        $resp->assertSee('Guest');
        $resp->assertElementNotExists('#password');
    }

    public function test_guest_profile_cannot_be_deleted()
    {
        $guestUser = User::getDefault();
        $resp = $this->asAdmin()->get('/settings/users/' . $guestUser->id . '/delete');
        $resp->assertSee('Delete User');
        $resp->assertSee('Guest');
        $resp->assertElementContains('form[action$="/settings/users/' . $guestUser->id . '"] button', 'Confirm');

        $resp =  $this->delete('/settings/users/' . $guestUser->id);
        $resp->assertRedirect('/settings/users/' . $guestUser->id);
        $resp = $this->followRedirects($resp);
        $resp->assertSee('cannot delete the guest user');
    }
}
