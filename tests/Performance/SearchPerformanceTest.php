<?php

namespace Tests\Performance;

use App\Search\SearchOptions;
use App\Search\SearchRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private SearchRunner $searchRunner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchRunner = app(SearchRunner::class);
    }

    /**
     * Test basic search performance.
     */
    public function test_basic_search_performance(): void
    {
        $this->createTestData(10000);

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $options = SearchOptions::fromString('test search');
        $result = $this->searchRunner->searchEntities($options, 'page', 1, 20);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $duration = $endTime - $startTime;
        $memoryUsage = $endMemory - $startMemory;

        $this->assertLessThan(1.0, $duration, "Search took too long: {$duration}s");
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsage, "Memory usage too high: {$memoryUsage} bytes");
    }

    /**
     * Test complex search performance.
     */
    public function test_complex_search_performance(): void
    {
        $this->createTestData(10000);

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $options = SearchOptions::fromString('test search tag:important created_by:admin');
        $result = $this->searchRunner->searchEntities($options, 'all', 1, 50);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $duration = $endTime - $startTime;
        $memoryUsage = $endMemory - $startMemory;

        $this->assertLessThan(1.5, $duration, "Complex search took too long: {$duration}s");
        $this->assertLessThan(75 * 1024 * 1024, $memoryUsage, "Memory usage too high: {$memoryUsage} bytes");
    }

    /**
     * Test search with caching.
     */
    public function test_search_with_caching_performance(): void
    {
        $this->createTestData(10000);

        $options = SearchOptions::fromString('cached search');
        
        // 第一次搜索（缓存未命中）
        $startTime = microtime(true);
        $result1 = $this->searchRunner->searchEntities($options, 'page', 1, 20);
        $firstDuration = microtime(true) - $startTime;

        // 第二次搜索（缓存命中）
        $startTime = microtime(true);
        $result2 = $this->searchRunner->searchEntities($options, 'page', 1, 20);
        $cachedDuration = microtime(true) - $startTime;

        $this->assertEquals($result1['total'], $result2['total']);
        $this->assertLessThan($firstDuration * 0.3, $cachedDuration, 
            "Cached search should be much faster: {$cachedDuration}s vs {$firstDuration}s");
    }

    /**
     * Test memory usage with large result sets.
     */
    public function test_memory_usage_with_large_results(): void
    {
        $this->createTestData(50000);

        $startMemory = memory_get_usage(true);
        
        $options = SearchOptions::fromString('common');
        $result = $this->searchRunner->searchEntities($options, 'page', 1, 100);

        $endMemory = memory_get_usage(true);
        $memoryUsage = $endMemory - $startMemory;

        $this->assertLessThan(100 * 1024 * 1024, $memoryUsage, 
            "Memory usage too high for large results: {$memoryUsage} bytes");
    }

    /**
     * Create test data for performance testing.
     */
    private function createTestData(int $count): void
    {
        // 创建测试页面数据
        $pages = [];
        for ($i = 0; $i < $count; $i++) {
            $pages[] = [
                'name' => 'Test Page ' . $i,
                'slug' => 'test-page-' . $i,
                'type' => 'page',
                'text' => 'This is test content for page ' . $i . ' containing search terms like test, search, performance, optimization.',
                'description' => 'Description for test page ' . $i,
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ];
        }

        // 分批插入以避免内存问题
        foreach (array_chunk($pages, 1000) as $chunk) {
            \App\Entities\Models\Entity::insert($chunk);
        }

        // 创建对应的search_terms数据
        $entities = \App\Entities\Models\Entity::where('type', 'page')->get();
        $searchTerms = [];
        
        foreach ($entities as $entity) {
            $terms = ['test', 'search', 'performance', 'optimization', 'content', 'page'];
            foreach ($terms as $term) {
                if (rand(1, 100) <= 30) { // 30%的概率包含每个词
                    $searchTerms[] = [
                        'term' => $term,
                        'entity_type' => 'page',
                        'entity_id' => $entity->id,
                        'score' => rand(1, 100) / 100,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // 分批插入search_terms
        foreach (array_chunk($searchTerms, 1000) as $chunk) {
            \App\Search\SearchTerm::insert($chunk);
        }
    }
}