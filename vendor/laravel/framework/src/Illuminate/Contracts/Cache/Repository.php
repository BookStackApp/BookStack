<?php

namespace Illuminate\Contracts\Cache;

use Closure;

interface Repository
{
    /**
     * Determine if an item exists in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key);

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function pull($key, $default = null);

    /**
     * Store an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes);

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return bool
     */
    public function add($key, $value, $minutes);

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1);

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1);

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value);

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  \DateTime|float|int  $minutes
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember($key, $minutes, Closure $callback);

    /**
     * Get an item from the cache, or store the default value forever.
     *
     * @param  string   $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function sear($key, Closure $callback);

    /**
     * Get an item from the cache, or store the default value forever.
     *
     * @param  string   $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rememberForever($key, Closure $callback);

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key);
}
