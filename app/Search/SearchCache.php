<?php

namespace BookStack\Search;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SearchCache
{
    /**
     * Cache search results for 15 minutes
     */
    protected const CACHE_TTL = 900; // 15 minutes

    /**
     * Cache popular search terms for 1 hour
     */
    protected const POPULAR_CACHE_TTL = 3600; // 1 hour

    /**
     * Cache term adjustments for 24 hours
     */
    protected const TERM_ADJUSTMENT_TTL = 86400; // 24 hours

    /**
     * Get cached search results
     */
    public function getSearchResults(string $cacheKey): ?array
    {
        return Cache::get($this->getCacheKey('search', $cacheKey));
    }

    /**
     * Cache search results
     */
    public function cacheSearchResults(string $cacheKey, array $results): void
    {
        Cache::put(
            $this->getCacheKey('search', $cacheKey),
            $results,
            self::CACHE_TTL
        );
    }

    /**
     * Get cached term adjustments
     */
    public function getTermAdjustments(string $cacheKey): ?array
    {
        return Cache::get($this->getCacheKey('term_adjustments', $cacheKey));
    }

    /**
     * Cache term adjustments
     */
    public function cacheTermAdjustments(string $cacheKey, array $adjustments): void
    {
        Cache::put(
            $this->getCacheKey('term_adjustments', $cacheKey),
            $adjustments,
            self::TERM_ADJUSTMENT_TTL
        );
    }

    /**
     * Get cached popular search terms
     */
    public function getPopularTerms(): ?array
    {
        return Cache::get($this->getCacheKey('popular', 'terms'));
    }

    /**
     * Cache popular search terms
     */
    public function cachePopularTerms(array $terms): void
    {
        Cache::put(
            $this->getCacheKey('popular', 'terms'),
            $terms,
            self::POPULAR_CACHE_TTL
        );
    }

    /**
     * Clear search cache
     */
    public function clearSearchCache(): void
    {
        $prefix = 'search_';
        $keys = Cache::get($prefix . 'keys', []);
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        Cache::forget($prefix . 'keys');
        Log::info('Search cache cleared');
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $type, string $key): string
    {
        $sanitizedKey = md5($key);
        return "search_{$type}_{$sanitizedKey}";
    }

    /**
     * Track cache key for cleanup
     */
    protected function trackCacheKey(string $key): void
    {
        $prefix = 'search_';
        $keys = Cache::get($prefix . 'keys', []);
        $keys[] = $key;
        
        // Limit to 1000 keys to prevent memory issues
        if (count($keys) > 1000) {
            $keys = array_slice($keys, -500);
        }
        
        Cache::put($prefix . 'keys', $keys, self::CACHE_TTL);
    }
}