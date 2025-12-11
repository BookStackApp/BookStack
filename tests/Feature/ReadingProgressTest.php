<?php

namespace Tests\Feature;

use App\Entities\Models\ReadingProgress;
use App\Entities\Models\User;
use App\Entities\Models\Book;
use App\Entities\Models\Chapter;
use App\Entities\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingProgressTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $book;
    protected $chapter;
    protected $page;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create();
        $this->chapter = Chapter::factory()->create(['book_id' => $this->book->id]);
        $this->page = Page::factory()->create([
            'book_id' => $this->book->id,
            'chapter_id' => $this->chapter->id
        ]);
    }

    /** @test */
    public function user_can_update_reading_progress()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/api/pages/{$this->page->id}/reading-progress", [
                'progress_percentage' => 75,
                'current_scroll_position' => 500,
                'time_spent_seconds' => 300
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'progress_percentage' => 75,
                    'current_scroll_position' => 500,
                    'time_spent_seconds' => 300
                ]
            ]);

        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $this->user->id,
            'page_id' => $this->page->id,
            'progress_percentage' => 75,
            'current_scroll_position' => 500,
            'time_spent_seconds' => 300
        ]);
    }

    /** @test */
    public function user_can_get_reading_progress()
    {
        ReadingProgress::factory()->create([
            'user_id' => $this->user->id,
            'page_id' => $this->page->id,
            'progress_percentage' => 60,
            'current_scroll_position' => 400,
            'time_spent_seconds' => 200
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/pages/{$this->page->id}/reading-progress");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'progress_percentage' => 60,
                    'current_scroll_position' => 400,
                    'time_spent_seconds' => 200
                ]
            ]);
    }

    /** @test */
    public function user_can_delete_reading_progress()
    {
        ReadingProgress::factory()->create([
            'user_id' => $this->user->id,
            'page_id' => $this->page->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/pages/{$this->page->id}/reading-progress");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Reading progress deleted successfully'
            ]);

        $this->assertDatabaseMissing('reading_progress', [
            'user_id' => $this->user->id,
            'page_id' => $this->page->id
        ]);
    }

    /** @test */
    public function user_can_get_reading_stats()
    {
        // Create test data
        $pages = Page::factory()->count(5)->create([
            'book_id' => $this->book->id,
            'chapter_id' => $this->chapter->id
        ]);

        foreach ($pages as $index => $page) {
            ReadingProgress::factory()->create([
                'user_id' => $this->user->id,
                'page_id' => $page->id,
                'progress_percentage' => ($index + 1) * 20,
                'time_spent_seconds' => ($index + 1) * 100
            ]);
        }

        $response = $this->actingAs($this->user)
            ->getJson("/api/users/me/reading-stats");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total_pages',
                    'completed_pages',
                    'total_reading_time_seconds',
                    'average_reading_time_seconds',
                    'completion_rate',
                    'streak_days',
                    'favorite_book',
                    'favorite_chapter'
                ]
            ]);
    }

    /** @test */
    public function user_can_get_all_reading_progress()
    {
        $pages = Page::factory()->count(3)->create([
            'book_id' => $this->book->id,
            'chapter_id' => $this->chapter->id
        ]);

        foreach ($pages as $page) {
            ReadingProgress::factory()->create([
                'user_id' => $this->user->id,
                'page_id' => $page->id,
                'progress_percentage' => 50,
                'time_spent_seconds' => 200
            ]);
        }

        $response = $this->actingAs($this->user)
            ->getJson("/api/users/me/reading-progress");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'page_id',
                        'progress_percentage',
                        'current_scroll_position',
                        'time_spent_seconds',
                        'is_completed',
                        'last_read_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function progress_percentage_is_validated()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/api/pages/{$this->page->id}/reading-progress", [
                'progress_percentage' => 150, // Invalid value
                'current_scroll_position' => 500,
                'time_spent_seconds' => 300
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['progress_percentage']);
    }

    /** @test */
    public function unauthorized_user_cannot_access_reading_progress()
    {
        $response = $this->getJson("/api/pages/{$this->page->id}/reading-progress");
        $response->assertStatus(401);

        $response = $this->putJson("/api/pages/{$this->page->id}/reading-progress", [
            'progress_percentage' => 50
        ]);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/pages/{$this->page->id}/reading-progress");
        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_only_access_own_reading_progress()
    {
        $otherUser = User::factory()->create();
        
        ReadingProgress::factory()->create([
            'user_id' => $otherUser->id,
            'page_id' => $this->page->id,
            'progress_percentage' => 75
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/pages/{$this->page->id}/reading-progress");

        $response->assertStatus(404);
    }

    /** @test */
    public function reading_progress_is_updated_when_progress_increases()
    {
        ReadingProgress::factory()->create([
            'user_id' => $this->user->id,
            'page_id' => $this->page->id,
            'progress_percentage' => 30,
            'current_scroll_position' => 200,
            'time_spent_seconds' => 100
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/pages/{$this->page->id}/reading-progress", [
                'progress_percentage' => 60,
                'current_scroll_position' => 400,
                'time_spent_seconds' => 200
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $this->user->id,
            'page_id' => $this->page->id,
            'progress_percentage' => 60,
            'current_scroll_position' => 400,
            'time_spent_seconds' => 300 // Should be cumulative
        ]);
    }

    /** @test */
    public function reading_progress_marks_page_as_completed_at_100_percent()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/api/pages/{$this->page->id}/reading-progress", [
                'progress_percentage' => 100,
                'current_scroll_position' => 1000,
                'time_spent_seconds' => 500
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $this->user->id,
            'page_id' => $this->page->id,
            'progress_percentage' => 100,
            'is_completed' => true
        ]);
    }
}