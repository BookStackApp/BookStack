<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Users\Models\Role;
use Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class BookSortingTest extends TestCase
{
    public function test_update_sort_preference()
    {
        // Step 1: Set up the test environment
        $editor = $this->users->editor();
        $this->actingAs($editor);

        // Step 2: Send a PATCH request to the endpoint
        $updateRequest = $this->patch('/preferences/change-sort/books', [
            'sort'  => 'created_at',
            'order' => 'desc',
        ]);

        // Step 3: Assert the response status
        $updateRequest->assertStatus(302);

        // Step 4: Verify database changes
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort',
            'value'       => 'created_at',
        ]);
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort_order',
            'value'       => 'desc',
        ]);

        // Step 5: Check user settings
        $this->assertEquals('created_at', setting()->getForCurrentUser('books_sort'));
        $this->assertEquals('desc', setting()->getForCurrentUser('books_sort_order'));
        
        // Step 6: Check the order of books on the page
        $response = $this->get('/books');
        $response->assertStatus(200);
        $allBooks = Book::all();
        $this->assertBooksOrder($response->content(), $allBooks->sortByDesc('created_at')->pluck('name')->toArray());
    }

    // Test with different sort fields and orders
    public function test_update_sort_preference_with_different_fields_and_orders()
    {
        // Step 1: Set up the test environment
        $editor = $this->users->editor();
        $this->actingAs($editor);
        
        // Step 2: Send a PATCH request to the endpoint with different sort field and order
        $updateRequest = $this->patch('/preferences/change-sort/books', [
            'sort'  => 'updated_at',
            'order' => 'asc',
        ]);
        
        // Step 3: Assert the response status
        $updateRequest->assertStatus(302);
        
        // Step 4: Verify database changes for the new sort field and order
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort',
            'value'       => 'updated_at',
        ]);
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort_order',
            'value'       => 'asc',
        ]);
        
        // Step 5: Check user settings for the updated sort field and order
        $this->assertEquals('updated_at', setting()->getForCurrentUser('books_sort'));
        $this->assertEquals('asc', setting()->getForCurrentUser('books_sort_order'));

        // Step 6: Check the order of books on the page
        $response = $this->get('/books');
        $response->assertStatus(200);
        $allBooks = Book::all();
        $this->assertBooksOrder($response->content(), $allBooks->sortBy('updated_at')->pluck('name')->toArray());
    }

    // Test the sort field popularity
    public function test_sort_by_popularity()
    {
        // Step 1: Set up the test environment
        $editor = $this->users->editor();
        $this->actingAs($editor);
        
        // Step 2: Send a PATCH request to the endpoint with sort field popularity
        $updateRequest = $this->patch('/preferences/change-sort/books', [
            'sort'  => 'view_count',
            'order' => 'desc',
        ]);
        
        // Step 3: Assert the response status
        $updateRequest->assertStatus(302);
        
        // Step 4: Verify database changes for the sort field popularity
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort',
            'value'       => 'view_count',
        ]);
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort_order',
            'value'       => 'desc',
        ]);
        
        // Step 5: Check user settings for the sort field popularity
        $this->assertEquals('view_count', setting()->getForCurrentUser('books_sort'));
        $this->assertEquals('desc', setting()->getForCurrentUser('books_sort_order'));

        // Step 6: Check the order of books on the page
        $response = $this->get('/books');
        $response->assertStatus(200);
        $allBooks = Book::all();
        $this->assertBooksOrder($response->content(), $allBooks->sortByDesc('view_count')->pluck('name')->toArray());
    }

    private function assertBooksOrder($htmlContent, $expectedOrder)
    {
        $crawler = new Crawler($htmlContent);
        $bookNames = $crawler->filter('.grid-card-content h2.text-limit-lines-2')->each(function (Crawler $node) {
            return $node->text();
        });
        $this->assertEquals($expectedOrder, $bookNames);
    }
}