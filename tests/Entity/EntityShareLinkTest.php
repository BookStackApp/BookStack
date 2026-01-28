<?php

namespace Tests\Entity;

use BookStack\Entities\EntityShareLinkService;
use BookStack\Entities\Models\EntityShareLink;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\Permission;
use Tests\TestCase;

class EntityShareLinkTest extends TestCase
{
    protected EntityShareLinkService $shareLinkService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shareLinkService = app(EntityShareLinkService::class);
    }

    public function test_user_with_permission_can_create_share_link()
    {
        $page = $this->entities->page();
        $user = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($user, ['content-share-manage']);

        $resp = $this->actingAs($user)->post($page->getUrl('/share-links'), [
            'name' => 'Test Share Link',
        ]);

        $resp->assertRedirect($page->getUrl('/share-links'));
        $this->assertDatabaseHas('entity_share_links', [
            'entity_id' => $page->id,
            'entity_type' => 'page',
            'name' => 'Test Share Link',
            'created_by' => $user->id,
        ]);
    }

    public function test_user_without_permission_cannot_create_share_link()
    {
        $page = $this->entities->page();
        $user = $this->users->viewer();

        $resp = $this->actingAs($user)->post($page->getUrl('/share-links'), [
            'name' => 'Test Share Link',
        ]);

        $this->assertPermissionError($resp);
        $this->assertDatabaseMissing('entity_share_links', [
            'entity_id' => $page->id,
        ]);
    }

    public function test_share_link_generates_unique_token()
    {
        $page = $this->entities->page();
        $user = $this->users->editor();
        $this->permissions->grantUserRolePermissions($user, ['content-share-manage']);

        $this->actingAs($user);
        $shareLink1 = $this->shareLinkService->createShareLink($page, 'Link 1');
        $shareLink2 = $this->shareLinkService->createShareLink($page, 'Link 2');

        $this->assertNotEquals($shareLink1->token, $shareLink2->token);
        $this->assertEquals(32, strlen($shareLink1->token));
        $this->assertEquals(32, strlen($shareLink2->token));
    }

    public function test_public_can_access_shared_content_via_token()
    {
        $page = $this->entities->page();
        $user = $this->users->editor();
        $this->actingAs($user);
        $shareLink = $this->shareLinkService->createShareLink($page);

        $resp = $this->get('/share/' . $shareLink->token);

        $resp->assertStatus(200);
        $resp->assertSee($page->name);
    }

    public function test_invalid_token_returns_404()
    {
        $resp = $this->get('/share/invalidtoken123456789012345678');

        $resp->assertStatus(404);
    }

    public function test_user_can_delete_own_share_link()
    {
        $page = $this->entities->page();
        $user = $this->users->editor();
        $this->permissions->grantUserRolePermissions($user, ['content-share-manage']);
        
        $this->actingAs($user);
        $shareLink = $this->shareLinkService->createShareLink($page);

        $resp = $this->delete('/share-links/' . $shareLink->id);

        $resp->assertRedirect();
        $this->assertDatabaseMissing('entity_share_links', [
            'id' => $shareLink->id,
        ]);
    }

    public function test_share_link_works_for_book()
    {
        $book = $this->entities->book();
        $user = $this->users->editor();
        $this->actingAs($user);
        $shareLink = $this->shareLinkService->createShareLink($book);

        $resp = $this->get('/share/' . $shareLink->token);

        $resp->assertStatus(200);
        $resp->assertSee($book->name);
    }

    public function test_share_link_works_for_chapter()
    {
        $chapter = $this->entities->chapter();
        $user = $this->users->editor();
        $this->actingAs($user);
        $shareLink = $this->shareLinkService->createShareLink($chapter);

        $resp = $this->get('/share/' . $shareLink->token);

        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
    }

    public function test_share_links_show_in_entity_actions_menu_with_permission()
    {
        $page = $this->entities->page();
        $user = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($user, ['content-share-manage']);

        $resp = $this->actingAs($user)->get($page->getUrl());

        $this->withHtml($resp)->assertElementContains('a[href="' . $page->getUrl('/share-links') . '"]', 'Share Links');
    }

    public function test_share_links_hidden_in_entity_actions_menu_without_permission()
    {
        $page = $this->entities->page();
        $user = $this->users->viewer();

        $resp = $this->actingAs($user)->get($page->getUrl());

        $this->withHtml($resp)->assertElementNotExists('a[href="' . $page->getUrl('/share-links') . '"]');
    }

    public function test_share_link_warning_shows_in_permission_view_when_links_exist()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($editor, ['restrictions-manage-all']);
        $this->actingAs($editor);
        $this->shareLinkService->createShareLink($page);

        $resp = $this->actingAs($editor)->get($page->getUrl('/permissions'));

        $this->withHtml($resp)->assertElementContains('.text-warn', 'active share link');
    }

    public function test_admin_can_view_all_share_links_in_settings()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();
        $this->actingAs($editor);
        $shareLink = $this->shareLinkService->createShareLink($page, 'Admin Test Link');

        $resp = $this->asAdmin()->get('/settings/share-links');

        $resp->assertStatus(200);
        $resp->assertSee('Admin Test Link');
        $resp->assertSee($shareLink->getUrl());
    }

    public function test_attachment_can_be_downloaded_with_valid_share_token()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();
        $this->actingAs($editor);
        $shareLink = $this->shareLinkService->createShareLink($page);
        
        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, 'test-file.txt', $page->id);
        $upload->assertStatus(200);
        $attachment = \BookStack\Uploads\Attachment::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('/attachments/' . $attachment->id . '?share_token=' . $shareLink->token);

        $resp->assertStatus(200);
        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_cannot_be_downloaded_with_share_token_for_different_page()
    {
        $page1 = $this->entities->page();
        $page2 = $this->entities->page();
        $editor = $this->users->editor();
        $this->actingAs($editor);
        
        $shareLink = $this->shareLinkService->createShareLink($page1);
        
        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, 'test-file.txt', $page2->id);
        $upload->assertStatus(200);
        $attachment = \BookStack\Uploads\Attachment::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('/attachments/' . $attachment->id . '?share_token=' . $shareLink->token);

        $resp->assertStatus(404);
        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_can_be_downloaded_with_share_token_for_parent_book()
    {
        $book = $this->entities->book();
        $page = $this->entities->page(function ($query) use ($book) {
            $query->where('book_id', '=', $book->id);
        });
        $editor = $this->users->editor();
        $this->actingAs($editor);
        
        $shareLink = $this->shareLinkService->createShareLink($book);
        
        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, 'test-file.txt', $page->id);
        $upload->assertStatus(200);
        $attachment = \BookStack\Uploads\Attachment::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('/attachments/' . $attachment->id . '?share_token=' . $shareLink->token);

        $resp->assertStatus(200);
        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_can_be_downloaded_with_share_token_for_parent_chapter()
    {
        $book = $this->entities->book();
        $chapter = $this->entities->chapter(function ($query) use ($book) {
            $query->where('book_id', '=', $book->id);
        });
        $page = $this->entities->page(function ($query) use ($book, $chapter) {
            $query->where('book_id', '=', $book->id)->where('chapter_id', '=', $chapter->id);
        });
        $editor = $this->users->editor();
        $this->actingAs($editor);
        
        $shareLink = $this->shareLinkService->createShareLink($chapter);
        
        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, 'test-file.txt', $page->id);
        $upload->assertStatus(200);
        $attachment = \BookStack\Uploads\Attachment::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('/attachments/' . $attachment->id . '?share_token=' . $shareLink->token);

        $resp->assertStatus(200);
        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_cannot_be_downloaded_with_invalid_share_token()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();
        $this->actingAs($editor);
        
        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, 'test-file.txt', $page->id);
        $upload->assertStatus(200);
        $attachment = \BookStack\Uploads\Attachment::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('/attachments/' . $attachment->id . '?share_token=invalidtoken123456789012345678');

        $resp->assertStatus(404);
        $this->files->deleteAllAttachmentFiles();
    }
}
